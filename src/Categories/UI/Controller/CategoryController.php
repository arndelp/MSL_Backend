<?php

namespace App\Categories\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Categories\Application\UseCase\GetCategory;
use App\Components\HttpFoundation\JsonResponse;


final class CategoryController extends AbstractController
{
    private GetCategory $getCategory;

    public function __construct(GetCategory $getCategory)
    {
        $this->getCategory = $getCategory;
    }

    public function listAll(): Response
    {
       $category = $this->getCategory->execute();

        return $this->json($category,  200, [], ['groups' => 'category:read']);
        
    }
}
