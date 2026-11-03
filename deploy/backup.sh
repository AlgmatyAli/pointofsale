#!/usr/bin/env bash
# نسخة احتياطية لقاعدة البيانات، مستقلة عن التحديث.
#
# سطر Cron (cPanel > Cron Jobs، Once Per Hour: 0 * * * *):
#   APP_DIR=$HOME/public_html/pointofsale /bin/bash $HOME/public_html/pointofsale/deploy/backup.sh >> $HOME/backup.log 2>&1
#
# بدون وسيط: يكتب ~/db-backups/backup_DB.sql.gz (ملف واحد يُستبدل كل مرة).
# مع وسيط:   bash deploy/backup.sh /المسار/ملف.sql.gz  (يستخدمه deploy.sh قبل migrate)
#
# يكتب أولاً في ملف مؤقت ثم يستبدل، فالنسخة السابقة لا تضيع إن فشل التصدير.
# لا يكتب في السجل إلا عند الفشل. الحالة في runtime/backup.last و runtime/backup.failed.

main() {
set -euo pipefail
export PATH="/usr/local/bin:/usr/bin:/bin:$PATH"

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
APP_DIR="${APP_DIR/#\~/$HOME}"
BACKUP_DIR="${BACKUP_DIR:-$HOME/db-backups}"
OUT="${1:-$BACKUP_DIR/backup_DB.sql.gz}"
PERIODIC=$([ $# -eq 0 ] && echo 1 || echo 0)   # 1 = النسخة الدورية backup_DB

log() { echo "[$(date '+%F %T')] $*"; }
cd "$APP_DIR" || { log "المجلد غير موجود: $APP_DIR"; exit 1; }
mkdir -p runtime "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"
echo "$BACKUP_DIR" > runtime/backup.dir

fail() {
    log "فشل النسخ الاحتياطي: $*"
    if [ "$PERIODIC" = 1 ]; then { date '+%F %T'; echo "$*"; } > "$APP_DIR/runtime/backup.failed"; fi
    exit 1
}

# منع نسختين دوريتين في نفس الوقت (القفل الأقدم من ساعتين يُعتبر عالقاً)
LOCK="runtime/backup.lock"
if [ "$PERIODIC" = 1 ]; then
if [ -d "$LOCK" ] && [ -n "$(find "$LOCK" -maxdepth 0 -mmin +120)" ]; then rmdir "$LOCK"; fi
mkdir "$LOCK" 2>/dev/null || fail "النسخة السابقة ما زالت تعمل"
trap 'rmdir "$APP_DIR/$LOCK" 2>/dev/null || true; rm -f "${CNF:-}" "${TMP:-}"' EXIT
else
trap 'rm -f "${CNF:-}" "${TMP:-}"' EXIT
fi

envval() { grep -E "^$1=" .env | tail -1 | cut -d= -f2- | tr -d '\r' | sed -E "s/^['\"]//; s/['\"]\$//"; }
DB_HOST=$(envval DB_HOST); DB_NAME=$(envval DB_NAME); DB_USER=$(envval DB_USER); DB_PASS=$(envval DB_PASS)
[ -n "$DB_NAME" ] || fail "لم أجد DB_NAME في .env"

CNF=$(mktemp); chmod 600 "$CNF"
printf '[client]\nhost=%s\nuser=%s\npassword="%s"\n' "${DB_HOST:-localhost}" "$DB_USER" "$DB_PASS" > "$CNF"
TMP="$OUT.tmp"
mysqldump --defaults-extra-file="$CNF" --single-transaction --quick --routines --triggers --no-tablespaces "$DB_NAME" | gzip > "$TMP" \
    || fail "تعذر تصدير قاعدة البيانات $DB_NAME"
[ -s "$TMP" ] || fail "ملف النسخة فارغ"
chmod 600 "$TMP"
mv -f "$TMP" "$OUT"

if [ "$PERIODIC" = 1 ]; then
    date '+%F %T' > runtime/backup.last
    rm -f runtime/backup.failed
fi
}
main "$@"
exit
