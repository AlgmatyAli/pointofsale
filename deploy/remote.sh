#!/usr/bin/env bash
# يُشغَّل داخل GitHub Actions: يتصل بسيرفر واحد (SERVER بصيغة JSON) وينفّذ deploy.sh عليه.
# REMOTE_CMD اختياري لتنفيذ أمر قصير داخل مجلد البرنامج بدلاً من التحديث.
set -euo pipefail

get() { echo "$SERVER" | jq -r --arg k "$1" --arg d "${2:-}" '.[$k] // $d'; }
NAME=$(get name); HOST=$(get host); PORT=$(get port 22); USER_=$(get user)
APP=$(get path); PHPB=$(get php php); COMP=$(get composer composer)
[ -n "$HOST" ] && [ -n "$USER_" ] && [ -n "$APP" ] || { echo "::error::بيانات السيرفر ناقصة: $SERVER"; exit 1; }

mkdir -p ~/.ssh; chmod 700 ~/.ssh
printf '%s\n' "$SSH_KEY" > ~/.ssh/deploy_key; chmod 600 ~/.ssh/deploy_key
SSH=(ssh -i ~/.ssh/deploy_key -p "$PORT" -o StrictHostKeyChecking=accept-new -o ConnectTimeout=20 -o ServerAliveInterval=30 "$USER_@$HOST")

q() { printf '%q' "$1"; }
if [ -n "${REMOTE_CMD:-}" ]; then
    "${SSH[@]}" "cd $APP && $REMOTE_CMD"
    exit
fi

echo "== $NAME ($USER_@$HOST:$APP) -> ${TARGET_SHA:0:7}"
"${SSH[@]}" "APP_DIR=$APP TARGET_SHA=$(q "$TARGET_SHA") PHP_BIN=$(q "$PHPB") COMPOSER_BIN=$(q "$COMP") bash -s" < deploy/deploy.sh
