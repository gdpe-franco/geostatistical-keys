FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends git libzip-dev unzip \
    && docker-php-ext-install pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY --from=node:22-bookworm-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-bookworm-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/corepack/dist/corepack.js /usr/local/bin/corepack

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

ENTRYPOINT ["entrypoint"]
