<?php

namespace App\Mail;

use App\Events\UserRegisterEvent;

class MailListener
{
  /**
    * @var MailRegister
    */
  protected $mailRegister;

  public function __construct(MailRegister $mailRegister)
  {
    $this->mailRegister = $mailRegister;
  }

  public function processMail(UserRegisterEvent $event)
  {
    $this->mailRegister->SendMail($event);
  }

}

 ?>
