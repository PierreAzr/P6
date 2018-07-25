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

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\AutocompleteType;


/**
 * @Route("/advert")
 */
class AdvertController extends Controller
{
    /**
     * @Route("/ad/{page}/{city}", name="advert_index", methods="GET|POST")
     */
    public function index($page = 1, $city = null,Request $request,AdvertRepository $advertRepository): Response
    {

      if ($page < 1) {
              throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            }
      $nbPerPage = 3;

      $form = $this->createForm(AutocompleteType::class);
      $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $city = $form->get('city')->getData();
          $page = 1;
        }
            if ($city == null) {
              $adverts = $advertRepository->findbydate( $page, $nbPerPage);
            }else {

              $adverts = $advertRepository->findbycity($city, $page, $nbPerPage);
              if ($adverts->count() == 0) {
                $this->addFlash(
                    'notice',
                    'Aucun resultat pour la ville de '.$city
                );
                $adverts = $advertRepository->findbydate( $page, $nbPerPage);
              }
            }

      $allcity = $advertRepository->allcity();

      $nbPages = ceil(count($adverts) / $nbPerPage);
      if ($page > $nbPages) {
        throw $this->createNotFoundException("La page ".$page." n'existe pas.");
      }

      return $this->render('advert/index.html.twig', array(
        'adverts' => $adverts,
        'allcity' => $allcity,
        'city' => $city,
        'nbPages' => $nbPages,
        'page' => $page,
        'form' => $form->createView(),
      ));
        // return $this->render('advert/index.html.twig', ['adverts' => $advertRepository->findAll()]);
    }

    //Pour l'autocompletion du champ recherche utilisateur
  /**
  * @Route("/autocomplete", name="autocomplete", methods="GET|POST")
   */
  public function userAutoCompAction(Request $request, AdvertRepository $advertRepository)
  {
      if($request->isXmlHttpRequest())
      {
          $city = $request->get('city');
          $cities = $advertRepository->findcity($city);

          $response = new Response(json_encode($cities));
          return $response;
      }
  }

    // /**
    //  * @Route("/city/{city}/{page}", name="advert_city", methods="GET")
    //  */
    // public function cityshow($page = 1 ,Request $request, AdvertRepository $advertRepository): Response
    // {
    //   $city = $request->get('city');
    //   $d = $advertRepository->findbycity($city);
    //
    //   return $this->render('advert/index.html.twig', ['adverts' => $d, 'city' => $city ]);
    // }

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

          //date et heure
          $d = $form->get('date')->getData()->format('d-m-Y');
          $h = $form->get('hour')->getData()->format('H:i');
          $date = new \Datetime("$d $h");
          $advert->setAppointmentdate($date);

          //appel api https://api-adresse.data.gouv.fr
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

            $user = $this->getUser();
            $advert->setUsercreate($user);
            $advert->addParticipant($user);
            $em = $this->getDoctrine()->getManager();
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
     * @Route("/show/{id}", name="advert_show", methods="GET|POST")
     * @IsGranted("ROLE_USER")
     */
    public function show(Request $request, Advert $advert): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository("App\Entity\Comment")->findby(['advert' => $advert]);
          if ($form->isSubmitted() && $form->isValid()) {

              $comment->setAdvert($advert);
              $comment->setUser($this->getUser());
              $comment->setDate(new \DateTime());
              $comment->setLvl(0);

              $idreponse = $form->get('idreponse')->getData();
              //si le commentaire est une reponse
                if ($idreponse != "null") {
                  $com = $em->getRepository("App\Entity\Comment")->find($idreponse);
                  $comment->setParent($com);
                  $comment->setLvl($com->getLvl()+1);
                }

              $em->persist($comment);
              $em->persist($this->getUser());
              $em->persist($advert);
              $em->flush();

              return $this->redirectToRoute('advert_show', ['id' => $advert->getId()]);
          }

      return $this->render('advert/show.html.twig', [
        'advert' => $advert,
        'comments' => $comments,
        'form' => $form->createView(),
      ]);
    }

    /**
     * @Route("/show/{id}/add", name="add_participation", methods="POST")
     * @IsGranted("ROLE_USER")
     */
    public function addparticipation(Request $request, Advert $advert): Response
    {

          $user = $this->getUser();
          $advert->addParticipant($user);

          $em = $this->getDoctrine()->getManager();
          $em->persist($advert);
          $em->flush();

        return $this->redirectToRoute('advert_show', ['id' => $advert->getId()]);
    }

    /**
     * @Route("/show/{id}/remove", name="remove_participation", methods="POST")
     * @IsGranted("ROLE_USER")
     */
    public function removeparticipation(Request $request, Advert $advert): Response
    {

      $user = $this->getUser();
      $advert->removeParticipant($user);

      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();

        if ($advert->getParticipant()->isEmpty()) {
          // foreach ($advert->getComments() as $value) {
          //   $advert->removeComment($value);
          // }
          $em->remove($advert);
          $em->flush();
        }

    return $this->redirectToRoute('advert_index', ['advert' => $advert]);
    }

}
