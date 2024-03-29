FROM php:7.3.10-apache

LABEL maintainer="timo.schaefer@safisto.de" \
      description="PHP7 Apache including safisto LIB"

ENV TIMEZONE Europe/Berlin

RUN rm /bin/sh && ln -s /bin/bash /bin/sh && \
	\
	cp -vf /usr/share/zoneinfo/$TIMEZONE /etc/localtime && \ 
	\
	apt-get update && apt-get install -y \
		unzip \
		\
		libcurl4-gnutls-dev \
		libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
		libxml2-dev \	
		libzip-dev \
	&& \
	cd /etc/apache2/mods-enabled && \
	ln -s ../mods-available/rewrite.load && \
	ln -s ../mods-available/authz_groupfile.load
			
RUN docker-php-ext-configure \
		gd --with-jpeg-dir=/usr/lib/x86_64-linux-gnu --with-png-dir=/usr/lib/x86_64-linux-gnu --with-freetype-dir=/usr/lib/x86_64-linux-gnu \
	&& \
	docker-php-ext-install \
		curl \
		gd \
		hash \
		iconv \
		json \
		mbstring \
		mysqli \
		opcache \
		pdo \
		pdo_mysql \
		simplexml \
		soap \
		zip \
	&& \
	mkdir -p /var/www/html && \
	echo "<?php phpinfo(); ?>" > /var/www/html/index.php

RUN pear update-channels && pear upgrade-all --ignore-errors && \
	pear install Cache && \
	pear install Cache_Lite && \
	pear install Config && \
	pear install File_Passwd && \
    pear install HTTP && \
    pear install HTTP_Request && \
    pear install Log && \
	pear install Mail && \
	pear install Mail_Mime && \
	pear install Net_SMTP && \
    pear install MDB2 && \
    pear install --nodeps MDB2_Driver_mysql && \
	pear install Var_Dump

COPY src/main/docker/php.ini /usr/local/etc/php
COPY src/main/docker/php-additional.ini /usr/local/etc/php/conf.d

COPY src/main/php/ /usr/local/lib/php/LIB/

ARG SAVANT_VERSION=3.0.1

COPY lib/savant-$SAVANT_VERSION.zip /tmp/savant.zip
RUN cd /tmp && \ 
	unzip -q /tmp/savant.zip && \
	Savant3/install.sh && \
	\
	rm -Rf /tmp/*
