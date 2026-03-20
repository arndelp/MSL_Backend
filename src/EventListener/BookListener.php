<?php

namespace App\EventListener;

use App\Entity\Book;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookListener
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Book $book, LifecycleEventArgs $args)
    {
        if (!$book->getSlug() && $book->getTitle()) {
            $book->setSlug($this->slugger->slug($book->getTitle())->lower());
        }

        $now = new \DateTimeImmutable();
        $book->setCreatedAt($now);
        $book->setUpdatedAt($now);
    }

    public function preUpdate(Book $book, LifecycleEventArgs $args)
    {
        $book->setUpdatedAt(new \DateTimeImmutable());
    }
}