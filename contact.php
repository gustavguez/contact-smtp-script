<?php

use Gustavguez\Contact;

include_once('./vendor/autoload.php');

// Get config as array from ./config.php (You must rename config.dist.php and complete it)
$config = include './config.php';

// Response array
$response = [ 'success' => false, 'error' => '' ];

// Create Client class object from src/Contact.php 
$contact = new Contact($config);

// Process data
$contact->processPayload();

// Check allowed method and run minimal validation.
if( $contact->isValid() ){

    // Render mailbody
    $contact->renderBody();

    // Send mail and save result as response success value
    $response['success'] = $contact->send();
} else {
    $response['error'] = 'Form has errors!';
}

// Print response as json
echo json_encode($response);
