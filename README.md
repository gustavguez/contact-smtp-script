# Contact SMTP Script for your personal web
Scrip for contact form, email is sent via SMTP to prevent SPAM folder.

## Requirements
- PHP
- Composer (https://getcomposer.org/download/)
- Valid mail account

## Running script
Copy the `config.dist.php` file to `config.php` and complete value with valid values.

Then run this comands in terminal:
``` sh
$ composer install
```

Then upload `vendor`, `src`, `config.php` and `contact.php` to your server.

## Example
In the folder `examples` you have an example of how to use this script :).

# Google Recaptcha v3
Steps to add google recaptcha v3 to script.

## Create keys
Go to https://www.google.com/recaptcha/admin/create and fill all fields, it will create a private and public keys.

## Configure secret in PHP script
The script must validate in PHP the request made by de form, you must setup inside the `config.php` the `recaptchaSecret` with the secret created in the previous step.

## Configure the HTML/ Form
Load the JavaScript API using the public key created in previous steps. (Doc: https://developers.google.com/recaptcha/docs/v3).

``` html
 <script src="https://www.google.com/recaptcha/api.js?render=PUBLIC_KEY"></script>
 ```

 Setup inside the `config.php` the `recaptchaKey` with the public created in the previous step, if you like to use the example in the examples folder.


