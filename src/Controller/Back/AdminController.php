<?php

namespace App\Controller\Back;

use App\Entity\Articles;
use App\Entity\Categorie;
use App\Form\ArticleType;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/ajout_article", name="ajout_article")
     */
    public function ajout_article(Request $request, EntityManagerInterface $manager)
    {
        $article = new Articles();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) :
            $article->setDateCreation(new \DateTime('now'));
            $imageFile = $form->get('photo')->getData();

            if ($imageFile) :
                $nomImage = date("YmdHis") . "-" . uniqid() . "-" . $imageFile->getClientOriginalName();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $nomImage
                );
                $article->setPhoto($nomImage);
                $manager->persist($article);
                $manager->flush();
                $this->addFlash("success", "L'article a bien été ajouté");
                return $this->redirectToRoute("ajout_article");
            endif;
        endif;
        return $this->render('Back/ajout_article.html.twig', [
            'formu' => $form->createView()
        ]);
    }

    /**
     *@Route ("/modif_categorie/{id}", name="modif_categorie")
     *@Route("/ajout_categorie", name="ajout_categorie")
     */
    public function ajout_categorie(Request $request, EntityManagerInterface $manager, Categorie $categorie = null)
    {

        if (!$categorie) :
            $categorie = new Categorie();
        endif;

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) :

            $idCategorie = $categorie->getId();
            $manager->persist($categorie);
            $manager->flush();

            if ($idCategorie == null) :
                $this->addFlash('success', 'la catégorie a bien été ajoutée');
            else :
                $this->addFlash('success', 'la catégorie a bien été modifiée');
            endif;

            return $this->redirectToRoute("ajout_article");


        endif;

        return $this->render("Back/ajout_categorie.html.twig", [
            'formu' => $form->createView()
        ]);
    }
}
