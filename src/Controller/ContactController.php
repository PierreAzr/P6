<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;
use App\Form\ContactType;

class ContactController extends Controller
{
  /**
   * @Route("/contact", name="contact", methods="GET|POST")
   */
  public function contactAction(Request $request)
  {
      $contact = new Contact();
      $form = $this->createForm(ContactType::class, $contact);

      $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

          $email = $contact->getEmail();
          $object = $contact->getObject();

          $content = $this->renderView(
            'Contact/emailcontact.html.twig',
            array('contact' => $contact
            ));

          $emailsite = $this->container->getParameter('mail_contact');

          $message = (new \Swift_Message($object))
            ->setTo($emailsite)
            ->setFrom($email,'Contact | Olikin')
            ->setBody($content, 'text/html')
            ;
          $this->get('mailer')->send($message);

          $this->addFlash('info', 'Votre message a bien été envoyé');
          return $this->redirectToRoute('home');
        }

      return $this->render('Contact/contact.html.twig', array(
          'form' => $form->createView()
      ));
  }


}
