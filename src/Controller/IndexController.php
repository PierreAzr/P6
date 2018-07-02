<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class IndexController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {

    //   if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
    //   throw $this->createAccessDeniedException();
    // }
    //  $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        return $this->render('index.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
     //* @IsGranted("ROLE_ADMIN")
     //* @Security("has_role('ROLE_ADMIN')") necessite symfony/expression-language
    public function admin()
    {
        return $this->render('Admin/index.html.twig');
    }

}
