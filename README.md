# Contact SMTP Script
Scrip for contact forms, email is sent via SMTP.

## Requirements
- PHP
- Composer (https://getcomposer.org/download/)
- Valid mail account

## Running script
Copy the `config.dist.php` file to `config.php` and complete value with valid values.

Then run this comands in terminal:
``` sh
$ composer install
$ php -S localhost:8000 contact.php
```