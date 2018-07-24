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
        $emailcontact = $this->container->getParameter('mail_contact');
        $message = (new \Swift_Message())
            ->setSubject('Olikin: réinitialisation de mot de passe')
            ->setFrom($emailcontact, 'Olikin')
            ->setTo($ResetPassword->getEmail())
            ->setContentType('text/html')
            ->setBody("
            <h2>Demande de réinitialisation de mot de passe</h2>
            <p>Voici le lien: $link</p>
            <p>A très vite,</p>
            <p>Olikin</p>
            ")
        ;
        //echo "Resetpaswordmailer ok modif en prod";
        // dump($message);
        // exit;
        $this->mailer->send($message);

    }

}
