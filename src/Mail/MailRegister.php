<?php

namespace App\Mail;

use App\Events\UserRegisterEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class MailRegister
{

protected $mailer;
protected $twig;

  public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
  {
    $this->mailer = $mailer;
    $this->twig = $twig;
  }

  // Méthode pour envoyer l'email
  public function SendMail(UserRegisterEvent $event)
  {

    // on récupére la mise en forme de l'email
    $content = $this->twig->render(
                'Mail/userregister.html.twig',
                array('user' => $user
                ));

    // on récupere l'email du destinataire
    $email = $event->getUser()->getEmail();
    //$emailsite = $this->getParameter('mail_site');
    $message =  (new \Swift_Message())
      ->setSubject('Olikin')
      ->setTo($user->getEmail())
      ->setFrom('olikin@mail.com' , 'Olikin')
      ->setBody($content, 'text/html')
      ;

//echo "register mail ok modif en prod";
// dump($message);
// exit;
       $this->mailer->send($message);

  }

}


 ?>
