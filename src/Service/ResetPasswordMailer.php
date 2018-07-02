<?php
namespace App\Service;

use App\Entity\ResetPassword;
use Twig_Environment;

class ResetPasswordMailer {

    protected $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendMail(ResetPassword $ResetPassword, $link){

        //$mess = $this->twig->render('seurity/emailresetpassword.html.twig');

        $message = (new \Swift_Message())
            ->setSubject('Mot de passe oubliée')
            ->setFrom(array('contact@site.com' => "site" ))
            ->setTo($ResetPassword->getEmail())
            ->setContentType('text/html')
            ->setBody("
            <h2>Voici le lien</h2>
            <p>$link</p>
            ")
        ;
        echo "Resetpaswordmailer ok modif en prod";
        // dump($message);
        // exit;
        //$this->mailer->send($message);

    }

}
