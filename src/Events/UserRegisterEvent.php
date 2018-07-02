<?php
namespace App\Events;


use Symfony\Component\EventDispatcher\Event;
use App\Entity\User;

class UserRegisterEvent extends Event

{
  protected $user;


  public function __construct(User $user)
  {
    $this->user = $user;
  }

  // Le listener doit avoir accès à l'utilisateur'
  public function getUser()
  {
    return $this->user;
  }

}


 ?>
