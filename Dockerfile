FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y libzip-dev unzip --no-install-recommends
RUN docker-php-ext-install pdo pdo_mysql mysqli zip
RUN a2enmod rewrite

COPY . /var/www/html/

# Konfigurasi Virtual Host
RUN echo "<VirtualHost *:80>\n" > /etc/apache2/sites-available/simple-recipe.conf
RUN echo "    ServerAdmin webmaster@localhost\n" >> /etc/apache2/sites-available/simple-recipe.conf
RUN echo "    DocumentRoot /var/www/html\n" >> /etc/apache2/sites-available/simple-recipe.conf
RUN echo "    ServerName localhost\n" >> /etc/apache2/sites-available/simple-recipe.conf
RUN echo "    <Directory /var/www/html/>\n" >> /etc/apache2/sites-available/simple-recipe.conf
RUN echo "        Options Indexes FollowSymLinks MultiViews\n" >> /etc/apache2/sites-available/simple-recipe.conf
RUN echo "        AllowOverride All\n" >> /etc/apache2/sites-available/simple-recipe.conf
RUN echo "        Require all granted\n" >> /etc/apache2/sites-available/simple-recipe.conf
RUN echo "    </Directory>\n" >> /etc/apache2/sites-available/simple-recipe.conf
RUN echo "    ErrorLog ${APACHE_LOG_DIR}/error.log\n" >> /etc/apache2/sites-available/simple-recipe.conf
RUN echo "    CustomLog ${APACHE_LOG_DIR}/access.log combined\n" >> /etc/apache2/sites-available/simple-recipe.conf
RUN echo "</VirtualHost>\n" >> /etc/apache2/sites-available/simple-recipe.conf

RUN a2ensite simple-recipe.conf
RUN a2dissite 000-default.conf

RUN service apache2 restart

EXPOSE 80