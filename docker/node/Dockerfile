FROM node:22-alpine

ARG UID
ARG GID

RUN apk add --no-cache \
    su-exec

RUN deluser node 2>/dev/null || true \
    && delgroup node 2>/dev/null || true \
    && addgroup -S -g ${GID} node \
    && adduser -S -D -u ${UID} -G node node

RUN mkdir -p /home/node \
    && chown -R node:node /home/node

COPY entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /app

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]