<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;

use App\Form\EmailSendType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use App\Entity\User;
use App\Entity\ResetPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Service\ResetPasswordMailer;
use App\Service\UserConnection;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $helper): Response
    {
        return $this->render('Security/login.html.twig', [
            // dernier username saisi (si il y en a un)
            'last_username' => $helper->getLastUsername(),
            // La derniere erreur de connexion (si il y en a une)
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * @Route("/resetpassmailer", name="security_resetpass_mailer")
     */
    public function resetPassMailer(Request $request,ResetPasswordMailer $resetpasswordmailer): Response
    {

      $resetPassword = new ResetPassword();
      $form = $this->createForm(EmailSendType::class, $resetPassword);

      $form->handleRequest($request);

      if ($form->isSubmitted()) {

          $email = $resetPassword->getEmail();

          //on verifie si une demande de changement de mot de passe est deja en cours
          $repository = $this->getDoctrine()->getRepository('App\Entity\ResetPassword');
          $resetPasswordTest = $repository->findOneBy(['email' => $email]);

          // si c'est le cas on prend la demande dans la basse de donée pour la mettre a jours
          if (!empty($resetPasswordTest)) {
          $resetPassword = $resetPasswordTest;
          }

          $repository = $this->getDoctrine()->getRepository('App\Entity\User');
          $user = $repository->findOneBy(['email' => $email]);

          if (!empty($user)){

              $token = $resetPassword->getToken();
              $link = "http://127.0.0.1:8000/resetpass?token=$token"; //changer lien en prod (mettre en paramettre)
              $resetPassword->setEmail($email);
              $em = $this->getDoctrine()->getManager();
              $em->persist($resetPassword);
              $em->flush();

              // echo "cliquez sur le <a href=$link>lien</a>";
              // exit;

              // Envoie email contenant le lien
              $resetpasswordmailer->sendMail($resetPassword, $link);


              return $this->render('Security/resetpassmailer.html.twig', [
                  'sendmail' => "$link" //pendant le dev
              ]);
          }

        $this->addFlash(
            'notice',
            'Nous n\'avons pas pu trouver votre compte'
        );
      }

          return $this->render(
              'Security/resetpassmailer.html.twig',
              array('form' => $form->createView(),
              'sendmail' => ''
              )
          );
    }


    /**
     * @Route("/resetpass", name="security_resetpass")
     */
    public function resetPass(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // $userResetPassword = new User();
        // $form = $this->createForm(ResetPasswordType::class);
        // $userResetPassword->setUsername('userResetPassword');
        // $userResetPassword->setEmail('userResetPassword@userResetPassword.com');
        $form = $this->createFormBuilder()
        ->add('password', RepeatedType::class, array(
            'type' => PasswordType::class,
            'first_options'  => array('label' => 'Password'),
            'second_options' => array('label' => 'Repeat Password'),
            'invalid_message' => 'The password fields must match.',
            'constraints' => array(
                new NotBlank(),)
        ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $token = $request->request->get('token');
            $repository = $this->getDoctrine()->getRepository('App\Entity\ResetPassword');
            $resetPassword = $repository->findOneBy(['token' => $token]);

            if (empty($resetPassword)) {
                $this->addFlash('notice',
                    'La demande pour réinitialisé le mot de passe n\'a pas pu aboutir veuillez réessayer'
                );
                return $this->redirectToRoute('security_resetpass_mailer');
            }

            $interval= $resetPassword->getDate()->diff(new \Datetime());

            if ($token == $resetPassword->getToken() && $interval->format('%H')<24) {

                $pass = $form->getData()['password'];
                $email = $resetPassword->getEmail();

                $repositoryUser = $this->getDoctrine()->getRepository('App\Entity\User');
                $user = $repositoryUser->findOneBy(['email' => $email]);

                $password = $passwordEncoder->encodePassword($user, $pass);
                $user->setPassword($password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->remove($resetPassword);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'Votre mot de passe a été réinitialisé'
                );
                return $this->redirectToRoute('security_login');
            }

            $this->addFlash(
                'notice',
                'La demande pour réinitialisé le mot de passe a plus de 24h veuillez recommencer'
            );

          return $this->redirectToRoute('security_resetpass_mailer');

        }

      return $this->render(
          'Security/resetpass.html.twig',
          array('form' => $form->createView(),
        ));
    }


}
