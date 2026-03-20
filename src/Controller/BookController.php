<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Entity\Category;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    public function createBook(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $book = new Book();

        // Titre, prix, stock...
        $book->setTitle($request->request->get('title'));
        $book->setPrice((float)$request->request->get('price'));
        $book->setStock((int)$request->request->get('stock'));

        // Récupération des catégories par leur id envoyées depuis le front
        $categoryIds = $request->request->get('categories', []); // tableau d'id
        foreach ($categoryIds as $catId) {
            $category = $em->getRepository(Category::class)->find($catId);
            if ($category) {
                $book->addCategory($category); // ManyToMany
            }
        }

        // Listener gère automatiquement : slug + createdAt + updatedAt
        $em->persist($book);
        $em->flush();

        return $this->json([
            'id' => $book->getId(),
            'slug' => $book->getSlug(),
            'title' => $book->getTitle(),
            'categories' => array_map(fn($c) => ['id' => $c->getId(), 'name' => $c->getName(), 'slug' => $c->getSlug()], $book->getCategories()->toArray())
        ]);
    }
}
