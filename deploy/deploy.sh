#!/usr/bin/env bash
# تحديث نسخة البرنامج على سيرفر واحد.
# يُشغَّل من GitHub Actions عبر SSH، ويمكن تشغيله يدوياً من داخل مجلد البرنامج:
#   APP_DIR=~/public_html bash deploy/deploy.sh
#
# المتغيرات (كلها اختيارية):
#   APP_DIR       مجلد البرنامج على السيرفر (الافتراضي: المجلد الحالي)
#   BRANCH        الفرع (الافتراضي main)
#   TARGET_SHA    الـ commit المطلوب بالضبط (الافتراضي آخر commit في الفرع)
#   PHP_BIN       أمر php (مثلاً /opt/cpanel/ea-php82/root/usr/bin/php)
#   COMPOSER_BIN  أمر composer
#   KEEP_BACKUPS  عدد النسخ الاحتياطية المحفوظة (الافتراضي 10)
set -euo pipefail

APP_DIR="${APP_DIR:-$PWD}"
BRANCH="${BRANCH:-main}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
KEEP_BACKUPS="${KEEP_BACKUPS:-10}"
BACKUP_DIR="${BACKUP_DIR:-$HOME/db-backups}"

log()  { echo "[$(date '+%F %T')] $*"; }
fail() { echo "::error::$*"; log "فشل: $*"; exit 1; }

APP_DIR="${APP_DIR/#\~/$HOME}"
cd "$APP_DIR" || fail "المجلد غير موجود: $APP_DIR"
[ -d .git ] || fail "$APP_DIR ليس مستودع git"

# منع تشغيل تحديثين في نفس الوقت
mkdir -p runtime
LOCK="runtime/deploy.lock"
mkdir "$LOCK" 2>/dev/null || fail "يوجد تحديث آخر يعمل الآن (احذف $APP_DIR/$LOCK إن كان عالقاً)"
trap 'rmdir "$APP_DIR/$LOCK" 2>/dev/null || true' EXIT

# ملفات عُدّلت يدوياً على السيرفر توقف التحديث بدل أن تضيع
if ! git diff --quiet || ! git diff --cached --quiet; then
    git status --short
    fail "توجد ملفات معدّلة يدوياً على هذا السيرفر، لم يتم التحديث"
fi

log "جلب التحديثات من GitHub..."
git fetch --quiet origin "$BRANCH"
OLD=$(git rev-parse HEAD)
NEW="${TARGET_SHA:-$(git rev-parse "origin/$BRANCH")}"
git cat-file -e "$NEW^{commit}" 2>/dev/null || fail "الـ commit $NEW غير موجود"

if [ "$OLD" = "$NEW" ]; then
    log "السيرفر محدّث مسبقاً على $(git log -1 --format='%h %s')"
else
    git merge --ff-only --quiet "$NEW" || fail "لا يمكن التحديث المباشر (السيرفر عليه commits ليست في GitHub)"
    log "تم التحديث: ${OLD:0:7} -> ${NEW:0:7}"
fi

# مكتبات composer عند تغيّر composer.lock أو غياب vendor
if [ ! -f vendor/autoload.php ] || { [ "$OLD" != "$NEW" ] && git diff --name-only "$OLD" "$NEW" | grep -qx 'composer.lock'; }; then
    command -v "$COMPOSER_BIN" >/dev/null || fail "composer غير موجود على السيرفر (حدّد COMPOSER_BIN)"
    log "تثبيت مكتبات composer..."
    "$COMPOSER_BIN" install --no-interaction --prefer-dist --optimize-autoloader
fi

# هل توجد migrations جديدة؟
PENDING=$("$PHP_BIN" yii migrate/new all --interactive=0 2>&1 || true)
if echo "$PENDING" | grep -q 'No new migrations found'; then
    log "لا توجد migrations جديدة"
else
    echo "$PENDING"

    # نسخة احتياطية لقاعدة البيانات قبل التنفيذ (من بيانات .env)
    envval() { grep -E "^$1=" .env | tail -1 | cut -d= -f2- | sed -E "s/^['\"]//; s/['\"]\$//"; }
    DB_HOST=$(envval DB_HOST); DB_NAME=$(envval DB_NAME); DB_USER=$(envval DB_USER); DB_PASS=$(envval DB_PASS)
    [ -n "$DB_NAME" ] || fail "لم أجد DB_NAME في .env"
    mkdir -p "$BACKUP_DIR"; chmod 700 "$BACKUP_DIR"
    CNF=$(mktemp); chmod 600 "$CNF"
    printf '[client]\nhost=%s\nuser=%s\npassword="%s"\n' "${DB_HOST:-localhost}" "$DB_USER" "$DB_PASS" > "$CNF"
    BACKUP="$BACKUP_DIR/${DB_NAME}-$(date +%Y%m%d-%H%M%S)-${OLD:0:7}.sql.gz"
    log "نسخة احتياطية: $BACKUP"
    if ! mysqldump --defaults-extra-file="$CNF" --single-transaction --routines --triggers --no-tablespaces "$DB_NAME" | gzip > "$BACKUP"; then
        rm -f "$CNF" "$BACKUP"
        fail "فشلت النسخة الاحتياطية، لم يتم تنفيذ migrate"
    fi
    rm -f "$CNF"
    ls -1t "$BACKUP_DIR/${DB_NAME}-"*.sql.gz 2>/dev/null | tail -n +"$((KEEP_BACKUPS + 1))" | xargs -r rm -f

    log "تنفيذ migrate..."
    if ! "$PHP_BIN" yii migrate --interactive=0; then
        fail "فشل migrate. الكود الآن على ${NEW:0:7}، والكود السابق ${OLD:0:7}، ونسخة القاعدة قبل التنفيذ: $BACKUP"
    fi
fi

log "مسح الذاكرة المؤقتة..."
"$PHP_BIN" yii cache/flush-all --interactive=0

log "تم بنجاح على $(git log -1 --format='%h %s')"
