<?php
namespace Gustavguez;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Contact {

    public static $ALLOWED_METHOD = 'POST';

    //Configs
    protected $mailHost;
    protected $mailFrom;
    protected $mailFromPassword;
    protected $mailFromName;
    protected $mailTo;
    protected $mailSubject;
    protected $templatesDir;
    protected $templatesFile;
    protected $recaptchaSecret;

    //Data
    protected $bodyHTML;
    protected $data;
    protected $recaptchaToken;
    
    public function __construct(array $config) {
        $this->mailHost = $config['mailHost'];
        $this->mailFrom = $config['mailFrom'];
        $this->mailFromPassword = $config['mailFromPassword'];
        $this->mailFromName = $config['mailFromName'];
        $this->mailTo = $config['mailTo'];
        $this->mailSubject = $config['mailSubject'];
        $this->templatesDir = $config['templatesDir'];
        $this->templatesFile = $config['templatesFile'];
        $this->recaptchaSecret = $config['recaptchaSecret'];

        $this->bodyHTML = '';
        $this->data = [];
    }

    public function isValid() {
        return $this->checkMethod() && $this->formFieldsAreValid() && $this->recaptchaAreValid(); 
    }

    public function processPayload() {
        //Load data.
        $this->data = [
            'email'   => filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL ),
            'message' => filter_var( $_POST['message'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW ),
        ];

        //Load recaptcha
        if(!empty($_POST['recaptchaToken'])){
            $this->recaptchaToken = filter_var( $_POST['recaptchaToken'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW );
        }
    }

    public function renderBody(){
        //Render html using TWIG
        $loader = new FilesystemLoader($this->templatesDir);
        $twig = new Environment($loader, []);
        $this->bodyHTML = $twig->render($this->templatesFile, $this->data);
    }

    public function send(){
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $response = false;

        // @@TODO: use recaptcha v3 to prevent spam

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
            $mail->Body    = $this->bodyHTML;

            $response = $mail->send();
        } catch (\Exception $e) {
            //Do nothing
        }
        return $response;
    }

    private function checkMethod(){
        return $_SERVER['REQUEST_METHOD'] === self::$ALLOWED_METHOD;
    }

    private function formFieldsAreValid() {
        return !empty($this->data['email']) && !empty($this->data['message']);
    }

    private function recaptchaAreValid() {
        //TODO: install Guzzle and finish https://developers.google.com/recaptcha/docs/verify
        return true;
    }
}