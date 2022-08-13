FROM php:8.0-apache

# Update & Upgrade OS
RUN apt-get update
RUN apt upgrade -y

# Install some base extensions
RUN apt-get install -y \
		curl \
		libcurl4-openssl-dev \
		pkg-config \
		libssl-dev \
		libzip-dev \
		zip \
		libonig-dev \
		libicu-dev \
		libmagickwand-dev --no-install-recommends \
	&& curl -sL https://deb.nodesource.com/setup_16.x | bash - \
	&& apt-get install -y nodejs \
	&& docker-php-ext-configure zip \
	&& docker-php-ext-install zip \
	&& docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-enable intl \
	&& pecl install imagick \
	&& docker-php-ext-enable imagick

#XML PHP Extension
RUN apt-get install -y libxml2-dev \
    && docker-php-ext-install xml

# BCMath PHP Extension
RUN docker-php-ext-install bcmath

# Ctype PHP Extension
RUN docker-php-ext-install ctype

# Fileinfo PHP extension
RUN docker-php-ext-install fileinfo

# JSON PHP extension (The JSON extension is already included in PHP)
#RUN docker-php-ext-install json

# Mbstring PHP Extension
RUN docker-php-ext-install mbstring

# PDO PHP Extension
RUN docker-php-ext-install pdo

# Tokenizer PHP Extension
RUN docker-php-ext-install tokenizer

# PDO_MYSQL PHP Extension
RUN docker-php-ext-install pdo_mysql

# Exif PHP Extension
RUN docker-php-ext-install exif

# OpenSSL PHP Extension
RUN apt-get install -y openssl

#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

#set our application folder as an environment variable
ENV APP_HOME /var/www/html

#change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# 2. apache configs + document root
RUN sed -i  's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i  's|#ServerName www.example.com|#ServerName www.example.com\n	LimitRequestFieldSize 400000|'  /etc/apache2/sites-available/000-default.conf

# Enable apache module rewrite
RUN a2enmod rewrite headers

# Copy source files
COPY . $APP_HOME

# Install all PHP dependencies
RUN cd /var/www/html/ && composer install --optimize-autoloader  --no-dev  --prefer-dist

# Clear cache
RUN php artisan cache:clear
RUN php artisan route:clear
RUN php artisan config:clear
RUN php artisan view:clear
RUN php artisan optimize:clear
RUN php artisan storage:link

ENV TZ='Europe/Paris'
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Change ownership of our applications
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/*

CMD service apache2 restart && php artisan queue:work --verbose --tries=3 --timeout=90
