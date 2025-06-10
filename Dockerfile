FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y libzip-dev unzip --no-install-recommends
RUN docker-php-ext-install pdo pdo_mysql mysqli zip
RUN a2enmod rewrite

COPY . /var/www/html/

RUN chmod -R 766 /var/www/html/

# Konfigurasi Virtual Host
RUN cat <<EOF > /etc/apache2/sites-available/simple-recipe.conf
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html
    ServerName localhost

    <Directory /var/www/html/>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF


RUN a2ensite simple-recipe.conf
RUN a2dissite 000-default.conf

RUN service apache2 restart

EXPOSE 80