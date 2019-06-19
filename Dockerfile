FROM php:7.3-fpm

WORKDIR /app

RUN apt-get update -y && apt-get install -y sendmail libpng-dev

RUN docker-php-ext-install pdo_mysql gd bcmath pcntl

# imagick
#RUN apk add --update --no-cache autoconf g++ imagemagick-dev imagemagick libtool make pcre-dev \
#    && pecl install imagick \
#    && docker-php-ext-enable imagick \
#    && apk del autoconf g++ libtool make pcre-dev

ARG HOST_USER_ID=1000
ARG HOST_GROUP_ID=1000

ENV HOST_USER_ID=$HOST_USER_ID
ENV HOST_GROUP_ID=$HOST_GROUP_ID

RUN \
  if [ $(getent group ${HOST_GROUP_ID}) ]; then \
    useradd  -r -u ${HOST_USER_ID} dockeruser; \
  else \
    groupadd -g ${HOST_GROUP_ID} dockergroup && \
    useradd -r -u ${HOST_USER_ID} -g dockergroup dockeruser; \
  fi

RUN curl -sS https://getcomposer.org/installer | \
  php -- --install-dir=/usr/local/bin --filename=composer

COPY . .

RUN composer install

RUN chown -R dockeruser:dockergroup /app

USER dockeruser

CMD ["php-fpm"]
