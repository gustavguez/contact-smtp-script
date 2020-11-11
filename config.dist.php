<?php 
return [
    'mailHost' => 'example.com',
    'mailFrom' => 'no-reply@example.com',
    'mailFromPassword' => '',
    'mailFromName' => 'Contact mailer',
    'mailTo' => 'exampleFrom@example.com',
    'mailSubject' => 'Contact from web',

    'templatesDir' => __DIR__ . '/src/templates/',
    'templatesFile' => 'contact.html',

    'recaptchaSecret' => '',
    'recaptchaKey' => ''
];