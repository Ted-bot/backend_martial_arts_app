FROM caddy:2.8.4-builder-alpine as builder
# FROM caddy:2-builder-alpine as builder

RUN xcaddy build \
    --with github.com/dunglas/mercure \
    --with github.com/dunglas/mercure/caddy

FROM caddy:2.8.4 AS caddy

WORKDIR /app

COPY --from=dunglas/mercure:v0.11 /srv/public /srv/mercure-assets/
COPY --from=builder /usr/bin/caddy /usr/bin/caddy
COPY caddy/Caddyfile /etc/caddy/Caddyfile