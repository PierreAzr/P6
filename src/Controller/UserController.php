<?php

namespace App\Controller;

use App\Form\UserType;
use App\Form\EditUserType;
use App\Form\ChangePasswordType;
use App\Entity\User;
use App\Events;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
// use Symfony\Component\EventDispatcher\GenericEvent;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

use App\Events\UserRegisterEvent;
use App\Events\AppEvents;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Service\UserConnection;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EventDispatcherInterface $eventDispatcher, UserConnection $userconnection)
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $pass = $user->getPassword();
            $name = $user->getUsername();
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // Par defaut l'utilisateur aura toujours le rôle ROLE_USER
            $user->setRoles(['ROLE_USER']);

            // On enregistre l'utilisateur dans la base
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $event = new UserRegisterEvent($user);
            //on déclenche l'évènement
            $eventDispatcher->dispatch(AppEvents::USER_REGISTER, $event);
           // $this->get('event_dispatcher')->dispatch(AppEvents::USER_REGISTER,$event);

            //Reconnection utilisateur direct
            // $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            // $this->get('security.token_storage')->setToken($token);
            // $this->get('session')->set('_security_main', serialize($token));
            //Reconnection utilisateur avec le service
            $userconnection =  $this->container->get('user.connection');
            $userconnection->connect($user);

          return $this->redirectToRoute('user');
        }

        return $this->render(
            'User/register.html.twig',
            array('form' => $form->createView())
            );
    }

    /**
     * @Route("/user", name="user")
     */
    public function user()
    {
        return $this->render('User/index.html.twig',
        array('user'=> $this->getUser())
      );
    }

    /**
     * @Route("/user/edit", name="user_edit")
     */
    public function editUser(Request $request, UserPasswordEncoderInterface $passwordEncoder){

      $user = $this->getUser();
      $form = $this->createForm(EditUserType::class, $user);

      $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pass = $user->getPlainpassword();
            $password = $passwordEncoder->encodePassword($user, $pass);
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $userconnection =  $this->container->get('user.connection');
            $userconnection->connect($user);

          return $this->redirectToRoute('user');
        }

      return $this->render(
          'User/edituser.html.twig',
          array('form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/changepassword", name="user_change_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder){

      $user = $this->getUser();
      $form = $this->createForm(ChangePasswordType::class, $user);

      $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pass = $user->getPlainpassword();
            $password = $passwordEncoder->encodePassword($user, $pass);
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $userconnection =  $this->container->get('user.connection');
            $userconnection->connect($user);

          return $this->redirectToRoute('user');
        }

      return $this->render(
            'User/changepassword.html.twig',
              array('form' => $form->createView(),
              ));
    }

    /**
     * @Route("/user/remove", name="user_remove")
     */
    public function removeUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $user = $this->getUser();
        $form = $this->createFormBuilder()
            ->add('password', PasswordType::class, array(
              'label' => 'Mot de passe',        
               'constraints' => array(
                   new NotBlank(),
                   new UserPassword(['message'=>'mauvais mot de passe actuelle'])),
           ))
            ->getForm();

        $form->handleRequest($request);

          if ($form->isSubmitted() && $form->isValid()){
              $em = $this->getDoctrine()->getManager();
              $em->remove($user);
              $em->flush();
              return $this->redirectToRoute('index');

          }
      return $this->render(
          'User/removeuser.html.twig',
          array('form' => $form->createView(),
        ));
    }

}
