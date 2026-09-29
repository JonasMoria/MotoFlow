#!/bin/sh

set -eu

DESTINATION_BRANCH=${1:-main}

GIT_ROOT=$(git rev-parse --show-toplevel)

cd "$GIT_ROOT"

CHANGED_FILES=$(
    git -C "$GIT_ROOT" diff --name-only --diff-filter=ACMRTUXB "origin/${DESTINATION_BRANCH}" \
    | grep '^api/.*\.php$' \
    | sed 's#^api/##' \
    || true
)

if [ -z "$CHANGED_FILES" ]; then
    echo "No PHP files changed."

    exit 0
fi

echo "Changed files:"
echo "$CHANGED_FILES"

docker compose \
    exec -T api \
    vendor/bin/php-cs-fixer fix \
    --config=php-cs-fixer.php \
    --using-cache=no \
    --path-mode=intersection \
    -- $CHANGED_FILES