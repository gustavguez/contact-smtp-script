<?php
namespace Gustavguez;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Contact {

    public static $ALLOWED_METHOD = 'POST';

    protected $mailHost;
    protected $mailFrom;
    protected $mailFromPassword;
    protected $mailFromName;
    protected $mailTo;
    protected $mailSubject;
    protected $mailBody;
    
    public function __construct(array $config) {
        $this->mailHost = $config['mailHost'];
        $this->mailFrom = $config['mailFrom'];
        $this->mailFromPassword = $config['mailFromPassword'];
        $this->mailFromName = $config['mailFromName'];
        $this->mailTo = $config['mailTo'];
        $this->mailSubject = $config['mailSubject'];
        $this->mailBody = 'Contact mail body';
    }

    public function checkMethod(){
        return $_SERVER['REQUEST_METHOD'] === self::$ALLOWED_METHOD;
    }

    public function send(){
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $response = false;

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->Host       = $this->mailHost;                    // Set the SMTP server to send through
            $mail->Username   = $this->mailFrom;                     // SMTP username
            $mail->Password   = $this->mailFromPassword;                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = 465;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom($this->mailFrom, $this->mailFromName);
            $mail->addAddress($this->mailTo);

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $this->mailSubject;
            $mail->Body    = $this->mailBody;

            $response = $mail->send();
        } catch (\Exception $e) {
            //Do nothing
            var_dump($e->getMessage());
        }
        return $response;
    }
}