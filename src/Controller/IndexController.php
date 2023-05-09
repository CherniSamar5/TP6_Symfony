<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\ArticleType;
use App\Entity\Category;
use App\Form\CategoryType;

class IndexController extends AbstractController
{
    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/home', name: 'article_list')]
    public function home(ManagerRegistry $doctrine) #($name)
    {
        #return new Response('<h1>Ma première page Symfony</h1>');
        #return $this->render('index/index.html.twig' , ['name'=>$name]);
        #return $this->render('articles/index.html.twig');
        #$articles = ['Artcile1', 'Article 2','Article 3'];
        #return $this->render('articles/index.html.twig',['articles' => $articles]);
        $articles = $doctrine->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    #[Route('/article/save', name: 'app-save')]
    public function save(ManagerRegistry $doctrine): Response {
            $entityManager = $doctrine->getManager();
            $article = new Article();
            $article->setNom('Article 3');
            $article->setPrix(2500);
            $entityManager->persist($article);
            $entityManager->flush();
        return new Response('Article enregisté avec id '.$article->getId());
    }


    #[Route('/article/new', name:'new_article')]
    public function new(Request $request, ManagerRegistry $doctrine) {
            $article = new Article();
            $form = $this->createForm(ArticleType::class,$article);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                    $article = $form->getData();
                    $entityManager = $doctrine->getManager();
                    $entityManager->persist($article);
                    $entityManager->flush();
                return $this->redirectToRoute('article_list');
        }
        return $this->render('articles/new.html.twig',['form' => $form->createView()]);
        }

    #[Route('/article/{id}', name:'article_show')]
    public function show($id,ManagerRegistry $doctrine): Response {
        $article = $doctrine->getRepository(Article::class)->find($id);
        return $this->render('articles/show.html.twig', array('article' =>$article));
    }

    #[Route('/article/edit/{id}', name: 'edit_article')]
    public function edit(Request $request, $id ,ManagerRegistry $doctrine)
    {
            $article = new Article();
            $article = $doctrine->getRepository(Article::class)->find($id);
            $form = $this->createForm(ArticleType::class,$article);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $entityManager=$doctrine->getManager();
                $entityManager->flush();
            return $this->redirectToRoute('article_list');
            }
        return $this->render('articles/edit.html.twig', ['form' =>$form->createView()]);
    }

    #[Route('/article/delete/{id}', name: 'delete_article')]
    public function delete(Request $request, $id, ManagerRegistry $doctrine)
    {
        $article = $doctrine->getRepository(Article::class)->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        $response = new Response();
        $response->send();
        return $this->redirectToRoute('article_list');
    }

    #[Route('/category/newCat', name: 'new_category')]
    public function newCategory(Request $request, ManagerRegistry $doctrine)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
        }
        return $this->render('articles/newCategory.html.twig', ['form' =>$form->createView()]);
    }

}
