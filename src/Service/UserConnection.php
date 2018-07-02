<?php
namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class UserConnection{

    protected $session;
    protected $tokenstorage;

    public function __construct(SessionInterface $session, TokenStorageInterface $tokenstorage) {
        $this->session = $session;
        $this->tokenstorage = $tokenstorage;
    }

    public function connect($user){

      $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
      $this->tokenstorage->setToken($token);
      $this->session->set('_security_main', serialize($token));

    }

}
