<?php
namespace Gustavguez;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use GuzzleHttp\Client;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Contact {

    public static $ALLOWED_METHOD = 'POST';
    public static $RECAPTCHA_URL = 'https://www.google.com/recaptcha/api/siteverify';

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

    //Services
    protected $mailer;
    protected $guzzle;
    
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

        //Init services
        $this->mailer = new PHPMailer(true);
        $this->guzzle = new Client();
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
        $response = false;

        try {
            //Server settings
            $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $this->mailer->Host       = $this->mailHost;                    // Set the SMTP server to send through
            $this->mailer->Username   = $this->mailFrom;                     // SMTP username
            $this->mailer->Password   = $this->mailFromPassword;                               // SMTP password
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $this->mailer->Port       = 465;                                    // TCP port to connect to

            //Recipients
            $this->mailer->setFrom($this->mailFrom, $this->mailFromName);
            $this->mailer->addAddress($this->mailTo);

            // Content
            $this->mailer->isHTML(true);                                  // Set email format to HTML
            $this->mailer->Subject = $this->mailSubject;
            $this->mailer->Body    = $this->bodyHTML;

            $response = $this->mailer->send();
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
        //Check recpatcha 
        if(!empty($this->recaptchaToken)){
            //Do CURL request to google
            $responseObj = $this->guzzle->post(self::$RECAPTCHA_URL, [
                'form_params' => [
                    'secret' => $this->recaptchaSecret,
                    'response' => $this->recaptchaToken
                ]
            ]);

            //Check response
            $response = json_decode($responseObj->getBody());
            
            //return success
            return $response->success;
        }
        return true;
    }
}