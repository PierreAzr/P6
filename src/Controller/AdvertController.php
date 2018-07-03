<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->render('advert/index.html.twig', ['adverts' => $advertRepository->findAll()]);
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
     * @Route("/{id}/edit", name="advert_edit", methods="GET|POST")
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Advert $advert): Response
    {
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('advert_edit', ['id' => $advert->getId()]);
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    // /**
    //  * @Route("/{id}", name="advert_delete", methods="DELETE")
    //  * @IsGranted("ROLE_USER")
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

    /**
     * @Route("/{id}", name="add_participation", methods="POST")
     * @IsGranted("ROLE_USER")
     */
    public function addparticipation(Request $request, Advert $advert): Response
    {


        return $this->redirectToRoute('advert_index');
    }

    /**
     * @Route("/{id}", name="remove participation_delete", methods="POST")
     * @IsGranted("ROLE_USER")
     */
    public function removeparticipation(Request $request, Advert $advert): Response
    {


        return $this->redirectToRoute('advert_index');
    }

}
