<?php

namespace App\Categories\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Categories\Application\UseCase\GetCategory;
use App\Categories\Application\UseCase\GetAllCategoriesWithBookCount;
use App\Books\Application\UseCase\GetAuthorNamesWithBookCount;
use Symfony\Component\HttpFoundation\JsonResponse;


final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly GetAllCategoriesWithBookCount $getAllCategoriesWithBookCount,
        private readonly GetAuthorNamesWithBookCount $getAuthorNamesWithBookCount,
        private GetCategory $getCategory,
    ) {}   

    public function listAll(): Response
    {
       $category = $this->getCategory->execute();

        return $this->json($category,  200, [], ['groups' => 'category:read']);
        
    }

     public function getBookFiltersWithCount(): Response
    {
        $categories = $this->getAllCategoriesWithBookCount->execute();
        $authors = $this->getAuthorNamesWithBookCount->execute();

        return $this->json([
            'categories' => $categories,
            'authors' => $authors,
        ]);
           
            
        
    } 
}
