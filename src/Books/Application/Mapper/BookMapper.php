<?php

namespace App\Books\Application\Mapper;

use App\Books\Application\DTO\BookDTO;
use App\Books\Domain\Entity\Book;

class BookMapper
{
    public function toEntity(BookDTO $bookDTO): Book
    {
        $book = new Book();
        $book->setTitle($bookDTO->title);
        $book->setPrice($bookDTO->price);
        $book->setStock($bookDTO->stock);
        $book->setFormat($bookDTO->format);
        $book->setDescription($bookDTO->description);
        $book->setIsbn($bookDTO->isbn);
        $book->setPageCount($bookDTO->pageCount);
        $book->setCurrency($bookDTO->currency);

        return $book;
    }

    public function toDTO(Book $book): BookDTO
    {
        $bookDTO = new BookDTO();
        $bookDTO->title = $book->getTitle();
        $bookDTO->price = $book->getPrice();
        $bookDTO->stock = $book->getStock();
        $bookDTO->format = $book->getFormat() ;
        $bookDTO->description = $book->getDescription();
        $bookDTO->isbn = $book->getIsbn();
        $bookDTO->pageCount = $book->getPageCount();
        $bookDTO->currency = $book->getCurrency();

        return $bookDTO;
    }
}