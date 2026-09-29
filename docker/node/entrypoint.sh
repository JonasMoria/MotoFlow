#!/bin/sh

set -e

if [ -d "/app/node_modules" ]; then
    chown -R node:node /app/node_modules
else
    mkdir -p /app/node_modules
    chown -R node:node /app/node_modules
fi

exec su-exec node "$@"