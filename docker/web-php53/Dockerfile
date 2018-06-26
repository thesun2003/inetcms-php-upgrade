FROM eugeneware/php-5.3

COPY init/000-default.conf /etc/apache2/sites-available/

# RUN apt-get update
# RUN apt-get -y install vim

RUN echo "ServerName $(ip route get 8.8.8.8 | awk '{print $NF; exit}')" >> /etc/apache2/apache2.conf && \
    echo '127.0.0.1 website.com' >> /etc/hosts && \
    echo "date.timezone = 'Pacific/Auckland'" >> /usr/local/lib/conf.d/php.ini

RUN a2enmod rewrite
RUN service apache2 restart
