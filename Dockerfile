FROM php:8.1-cli

RUN apt-get update && apt-get install -y curl

WORKDIR /app
COPY . /app

CMD ["php", "-S", "0.0.0.0:10000", "api.php"]
