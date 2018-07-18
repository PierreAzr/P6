<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
/**
 * @Route("/advert")
 */
class AdvertController extends Controller
{
    /**
     * @Route("/", name="advert_index", methods="GET")
     */
    public function index(AdvertRepository $advertRepository): Response
    {
      $d = $advertRepository->findbydate();
      $c = $advertRepository->findbycity();
      //dump($c);
      // exit;
      return $this->render('advert/index.html.twig', ['adverts' => $d, 'city' => $c ]);
        // return $this->render('advert/index.html.twig', ['adverts' => $advertRepository->findAll()]);
    }


    /**
     * @Route("/city/{city}", name="advert_city", methods="GET")
     */
    public function cityshow(Request $request, AdvertRepository $advertRepository): Response
    {
      $city = $request->get('city');
      $d = $advertRepository->findbycityshow($city);

      return $this->render('advert/index.html.twig', ['adverts' => $d, 'city' => $city ]);
    }

    /**
     * @Route("/new", name="advert_new", methods="GET|POST")
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {
        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

          $d = $form->get('date')->getData()->format('d-m-Y');
          $h = $form->get('hour')->getData()->format('H:i');
          $date = new \Datetime("$d $h");
            $advert->setAppointmentdate($date);

            $lng = round($form->get('lng')->getData(), 2);
            $lat = round($form->get('lat')->getData(), 2);
            $url = "https://api-adresse.data.gouv.fr/reverse/?lon=".$lng."&lat=".$lat;

            $raw = @file_get_contents($url);
            $json = json_decode($raw);
                //test si erreur 404 ou conversion coordonée
                if ($raw === false || empty($json->features)) {

                  $this->addFlash(
                      'notice',
                      'Une erreur c\'est produite veuillez réesayer'
                  );
                  return $this->render('advert/new.html.twig', [
                      'advert' => $advert,
                      'form' => $form->createView(),
                  ]);
                }

            $city = $json->features[0]->properties->city;


            $advert->setCity($city);

            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $advert->setUsercreate($user);
            $advert->addParticipant($user);
            $em->persist($advert);
            $em->flush();

            return $this->redirectToRoute('advert_index');
        }

        return $this->render('advert/new.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advert_show", methods="GET")
     */
    public function show(Advert $advert): Response
    {
        return $this->render('advert/show.html.twig', ['advert' => $advert]);
    }

    /**
     * @Route("/{id}", name="add_participation", methods="POST")
     * @IsGranted("ROLE_USER")
     */
    public function addparticipation(Request $request, Advert $advert): Response
    {
          $user = $this->getUser();
          $advert->addParticipant($user);

          $em = $this->getDoctrine()->getManager();
          $em->persist($advert);
          $em->flush();

        return $this->redirectToRoute('advert_index', ['advert' => $advert]);
    }

    /**
     * @Route("/{id}", name="remove participation_delete", methods="POST")
     * @IsGranted("ROLE_USER")
     */
    public function removeparticipation(Request $request, Advert $advert): Response
    {
      $user = $this->getUser();
      $advert->removeParticipant($user);

      $em = $this->getDoctrine()->getManager();
        if ($advert->Participant == null) {
          $em->remove($advert);
        }else {
          $em->persist($advert);
        }
      $em->flush();

    return $this->redirectToRoute('advert_index', ['advert' => $advert]);
    }

    // /**
    //  * @Route("/{id}/edit", name="advert_edit", methods="GET|POST")
    //  * @IsGranted("ROLE_USER")
    //  */
    // public function edit(Request $request, Advert $advert): Response
    // {
    //     $form = $this->createForm(AdvertType::class, $advert);
    //     $form->handleRequest($request);
    //
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->flush();
    //
    //         return $this->redirectToRoute('advert_edit', ['id' => $advert->getId()]);
    //     }
    //
    //     return $this->render('advert/edit.html.twig', [
    //         'advert' => $advert,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("/{id}", name="advert_delete", methods="DELETE")
    //  * @IsGranted("ROLE_ADMIN")
    //  */
    // public function delete(Request $request, Advert $advert): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
    //         $em = $this->getDoctrine()->getManager();
    //         $em->remove($advert);
    //         $em->flush();
    //     }
    //
    //     return $this->redirectToRoute('advert_index');
    // }

}
