FROM php:8.3-cli

# Install MySQL PDO driver
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy application files
COPY . /app
WORKDIR /app

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/app"]
