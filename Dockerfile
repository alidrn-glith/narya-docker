FROM php:8.1-cli

WORKDIR /var/www/html

COPY Api.php .

CMD [ "php", "-S", "0.0.0.0:8080", "Api.php" ]
