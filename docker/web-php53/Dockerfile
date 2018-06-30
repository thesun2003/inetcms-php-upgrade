FROM eugeneware/php-5.3

COPY init/000-default.conf /etc/apache2/sites-available/
COPY init/docker-php-ext-enable /usr/local/bin

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libmcrypt-dev \
        libpng12-dev \
        libjpeg-dev \
        libpng-dev \
    && docker-php-ext-install iconv mcrypt

RUN mkdir /usr/include/freetype2/freetype && ln -s /usr/include/freetype2/freetype.h /usr/include/freetype2/freetype/freetype.h

RUN docker-php-ext-configure gd \
        --enable-gd-native-ttf \
        --with-freetype-dir=/usr/include/freetype2 \
        --with-png-dir=/usr/include \
        --with-jpeg-dir=/usr/include \
    && docker-php-ext-install gd \
    && docker-php-ext-install mbstring \
    && docker-php-ext-enable gd

RUN echo "ServerName $(ip route get 8.8.8.8 | awk '{print $NF; exit}')" >> /etc/apache2/apache2.conf && \
    echo '127.0.0.1 website.com' >> /etc/hosts && \
    echo "date.timezone = 'Pacific/Auckland'" >> /usr/local/lib/conf.d/php.ini

RUN a2enmod rewrite
RUN service apache2 restart
