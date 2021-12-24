<?php

namespace App\Controller\Back;


use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/back")
 */
class PageController extends AbstractController
{
    /**
     * @Route("/", name="back_home")
     */
    public function index()
    {
        $user = new User();


        return $this->render('Back/page/index.html.twig', []);
    }
}
