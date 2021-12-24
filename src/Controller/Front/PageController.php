<?php

namespace App\Controller\Front;

use App\Repository\ArticlesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(ArticlesRepository $articlesRepository)
    {
        $articles = $articlesRepository->findAll();

        return $this->render('Front/page/index.html.twig', [
            'articles' => $articles
        ]);
    }
}
