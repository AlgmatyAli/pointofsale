#!/usr/bin/env bash
# تحديث نسخة البرنامج على سيرفر واحد، يُشغَّل من Cron كل 5 دقائق.
# السيرفر يسحب التحديث بنفسه من GitHub، فلا يحتاج أي منفذ مفتوح.
#
# سطر Cron (في cPanel > Cron Jobs):
#   */5 * * * * BRANCH=main APP_DIR=$HOME/public_html/pointofsale /bin/bash $HOME/public_html/pointofsale/deploy/deploy.sh >> $HOME/deploy.log 2>&1
#   السيرفر التجريبي: BRANCH=main ، بقية السيرفرات: BRANCH=production
#
# المتغيرات (كلها اختيارية):
#   APP_DIR       مجلد البرنامج (الافتراضي: المجلد الذي فيه deploy/)
#   BRANCH        الفرع الذي يتبعه هذا السيرفر (الافتراضي main)
#   PHP_BIN       أمر php (مثلاً /opt/cpanel/ea-php82/root/usr/bin/php)
#   COMPOSER_BIN  أمر composer
#   KEEP_BACKUPS  عدد نسخ ما قبل migrate المحفوظة (الافتراضي 10)
#   BACKUP_DIR    مجلد النسخ (الافتراضي ~/db-backups)
#   FORCE=1       ينفّذ migrate و cache حتى لو لم يوجد تحديث
#   SKIP_DAILY=1  بدون النسخة اليومية backup_DB.sql.gz
#
# عند أي فشل يُنشأ الملف runtime/deploy.failed ويتوقف التحديث التلقائي
# حتى تصلح المشكلة وتحذفه. السجل في ~/deploy.log.

# كل السكربت داخل دالة: bash يقرأه كاملاً قبل التنفيذ، فلا يتأثر عندما يحدّث git هذا الملف نفسه
main() {
set -euo pipefail
export PATH="/usr/local/bin:/usr/bin:/bin:/opt/cpanel/composer/bin:$PATH"

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
BRANCH="${BRANCH:-main}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
KEEP_BACKUPS="${KEEP_BACKUPS:-10}"
BACKUP_DIR="${BACKUP_DIR:-$HOME/db-backups}"

log() { echo "[$(date '+%F %T')] $*"; }

APP_DIR="${APP_DIR/#\~/$HOME}"
cd "$APP_DIR" || { log "المجلد غير موجود: $APP_DIR"; exit 1; }
[ -d .git ] || { log "$APP_DIR ليس مستودع git"; exit 1; }
mkdir -p runtime

# ---- قاعدة البيانات: النسخ الاحتياطية -------------------------------------
envval() { grep -E "^$1=" .env | tail -1 | cut -d= -f2- | tr -d '\r' | sed -E "s/^['\"]//; s/['\"]\$//"; }
# dump_db <ملف.sql.gz> : يكتب في ملف مؤقت ثم يستبدل، فالنسخة القديمة لا تضيع إن فشل التصدير
dump_db() {
    local out="$1" cnf tmp
    local DB_HOST DB_NAME DB_USER DB_PASS
    DB_HOST=$(envval DB_HOST); DB_NAME=$(envval DB_NAME); DB_USER=$(envval DB_USER); DB_PASS=$(envval DB_PASS)
    [ -n "$DB_NAME" ] || { log "لم أجد DB_NAME في .env"; return 1; }
    mkdir -p "$BACKUP_DIR"; chmod 700 "$BACKUP_DIR"
    cnf=$(mktemp); chmod 600 "$cnf"
    printf '[client]\nhost=%s\nuser=%s\npassword="%s"\n' "${DB_HOST:-localhost}" "$DB_USER" "$DB_PASS" > "$cnf"
    tmp="$out.tmp"
    if mysqldump --defaults-extra-file="$cnf" --single-transaction --routines --triggers --no-tablespaces "$DB_NAME" | gzip > "$tmp"; then
        rm -f "$cnf"; chmod 600 "$tmp"; mv -f "$tmp" "$out"
    else
        rm -f "$cnf" "$tmp"; return 1
    fi
}
# مكان النسخ لشاشة "النسخ الاحتياطية" في المنظومة
echo "$BACKUP_DIR" > runtime/backup.dir

# نسخة يومية واحدة باسم ثابت backup_DB.sql.gz (تُستبدل كل يوم)
DAILY="$BACKUP_DIR/backup_DB.sql.gz"
if [ "${SKIP_DAILY:-0}" != "1" ] && { [ ! -f "$DAILY" ] || [ -n "$(find "$DAILY" -mmin +1200)" ]; }; then
    if dump_db "$DAILY"; then
        log "النسخة اليومية: $DAILY ($(du -h "$DAILY" | cut -f1))"
    else
        log "فشلت النسخة اليومية لقاعدة البيانات"
    fi
fi

FAILED="runtime/deploy.failed"
fail() {
    log "فشل: $*"
    { date '+%F %T'; echo "$*"; } > "$APP_DIR/$FAILED"
    exit 1
}
if [ -f "$FAILED" ]; then
    # لا نكرر المحاولة كل 5 دقائق؛ نسجّل مرة في الساعة فقط
    [ "$(date +%M)" -lt 5 ] && log "التحديث متوقف بسبب فشل سابق: $(tail -1 "$FAILED") (احذف $APP_DIR/$FAILED بعد الإصلاح)"
    exit 0
fi

# منع تشغيل تحديثين معاً (القفل الأقدم من 30 دقيقة يُعتبر عالقاً)
LOCK="runtime/deploy.lock"
if [ -d "$LOCK" ] && [ -n "$(find "$LOCK" -maxdepth 0 -mmin +30)" ]; then rmdir "$LOCK"; fi
mkdir "$LOCK" 2>/dev/null || exit 0
trap 'rmdir "$APP_DIR/$LOCK" 2>/dev/null || true' EXIT

git fetch --quiet origin "$BRANCH" || { log "تعذر الاتصال بـ GitHub (سيُعاد لاحقاً)"; exit 0; }
OLD=$(git rev-parse HEAD)
NEW=$(git rev-parse "origin/$BRANCH")

if [ "$OLD" = "$NEW" ] && [ "${FORCE:-0}" != "1" ]; then
    exit 0   # لا جديد، بصمت
fi
if [ "$OLD" != "$NEW" ] && git merge-base --is-ancestor "$NEW" "$OLD"; then
    exit 0   # السيرفر أحدث من الفرع (مثلاً سُحب يدوياً)، لا نرجع للخلف
fi

log "== تحديث جديد على $BRANCH: ${OLD:0:7} -> ${NEW:0:7}"

# ملفات عُدّلت يدوياً على السيرفر توقف التحديث بدل أن تضيع
if ! git diff --quiet || ! git diff --cached --quiet; then
    git status --short
    fail "توجد ملفات معدّلة يدوياً على السيرفر، لم يتم التحديث"
fi

if [ "$OLD" != "$NEW" ]; then
    git merge --ff-only --quiet "$NEW" || fail "لا يمكن التحديث المباشر (على السيرفر commits ليست في GitHub)"
    log "تم سحب الكود: $(git log -1 --format='%h %s')"
fi

# مكتبات composer عند تغيّر composer.lock أو غياب vendor
if [ ! -f vendor/autoload.php ] || { [ "$OLD" != "$NEW" ] && git diff --name-only "$OLD" "$NEW" | grep -qx 'composer.lock'; }; then
    command -v "$COMPOSER_BIN" >/dev/null || fail "composer غير موجود على السيرفر (حدّد COMPOSER_BIN)"
    log "تثبيت مكتبات composer..."
    "$COMPOSER_BIN" install --no-interaction --prefer-dist --optimize-autoloader || fail "فشل composer install"
fi

# هل توجد migrations جديدة؟
PENDING=$("$PHP_BIN" yii migrate/new all --interactive=0 2>&1 || true)
if echo "$PENDING" | grep -q 'No new migrations found'; then
    log "لا توجد migrations جديدة"
else
    echo "$PENDING"

    # نسخة احتياطية قبل التنفيذ
    BACKUP="$BACKUP_DIR/before-migrate-$(date +%Y%m%d-%H%M%S)-${OLD:0:7}.sql.gz"
    log "نسخة احتياطية: $BACKUP"
    dump_db "$BACKUP" || fail "فشلت النسخة الاحتياطية، لم يتم تنفيذ migrate (الكود الآن ${NEW:0:7})"
    ls -1t "$BACKUP_DIR/before-migrate-"*.sql.gz 2>/dev/null | tail -n +"$((KEEP_BACKUPS + 1))" | xargs -r rm -f

    log "تنفيذ migrate..."
    "$PHP_BIN" yii migrate --interactive=0 \
        || fail "فشل migrate. الكود الآن ${NEW:0:7} والسابق ${OLD:0:7}، ونسخة القاعدة قبل التنفيذ: $BACKUP"
fi

"$PHP_BIN" yii cache/flush-all --interactive=0 >/dev/null || fail "فشل مسح الذاكرة المؤقتة"
date '+%F %T' > runtime/deploy.last
log "تم بنجاح على $(git log -1 --format='%h %s')"
}
main "$@"
exit
