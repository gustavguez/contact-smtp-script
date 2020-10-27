<?php

use Gustavguez\Contact;

include_once('./vendor/autoload.php');

// Get config as array from ./config.php (You must rename config.dist.php and complete it)
$config = include './config.php';

// Response array
$response = [ 'success' => false, 'error' => '' ];

// Create Client class object from src/Contact.php 
$contact = new Contact($config);

//Check allowed method
if($contact->checkMethod()){
    //Process data
    $contact->processBody();

    // Send mail and save result as response success value
    $response['success'] = $contact->send();
} else {
    $response['error'] = 'You must send mail using ' . Contact::$ALLOWED_METHOD . ' method';
}

// Print response as json
echo json_encode($response);
