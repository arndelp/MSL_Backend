<?php

namespace App\Books\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Books\Domain\Entity\Book;
use App\Categories\Domain\Entity\Category;
use App\Users\Domain\Entity\User;

final class BookController extends AbstractController
{
   
   

    public function record(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Si title n’est pas défini ou vide → mettre “A définir”
        $title = $data['title'] ?? '';
        if (trim($title) === '') {$title = 'A définir';}

        $book = new Book();
        $book->setTitle($title);

        // Pour le reste, mettre des valeurs par défaut simples
        $book->setPrice(isset($data['price']) ? (float)$data['price'] : 0);
        $book->setStock(isset($data['stock']) ? (int)$data['stock'] : 1);
        $book->setDescription($data['description'] ?? null);
        // Author : si pas défini, mettre un user par défaut (ex: id=1)
        // Author temporaire : null ou user existant
        $user = $em->getRepository(User::class)->find(1);
        $book->setAuthor($this->getUser());

        $book->setFormat($data['format'] ?? null);

        // Catégories
        if (!empty($data['categories'])) {
            foreach ($data['categories'] as $catId) {
                $category = $em->getRepository(Category::class)->find($catId);
                if ($category) {
                    $book->addCategory($category);
                }
            }
        }
      

        // Listener gère automatiquement : slug + createdAt + updatedAt
        $em->persist($book);
        $em->flush();

        return $this->json([
            
            'title' => $book->getTitle(),            
            'author' => $book->getAuthor(),
            'price' => $book->getPrice(),
            'stock' => $book->getStock(),
            'description' => $book->getDescription(),
            'format' => $book->getFormat(),

            'categories' => array_map(
                fn($c) => ['id' => $c->getId(), 'name' => $c->getName()],
                $book->getCategories()->toArray()
            ),
        ]);
    }
};
    

