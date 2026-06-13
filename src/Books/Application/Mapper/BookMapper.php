<?php

namespace App\Books\Application\Mapper;

use App\Books\Application\DTO\BookDTO;
use App\Books\Domain\Entity\Book;
use App\Enum\BookFormat;




class BookMapper
{
 

   
    public function toEntity(BookDTO $bookDTO): Book
    {
        $book = new Book();
        $book->setTitle($bookDTO->title);     
        $book->setAuthorName($bookDTO->authorName);
        $book->setPrice($bookDTO->price);
        $book->setStock($bookDTO->stock);
        $book->setFormat(BookFormat::from($bookDTO->format));
        $book->setDescription($bookDTO->description);
        $book->setIsbn($bookDTO->isbn);
        $book->setPageCount($bookDTO->pageCount);
        $book->setCurrency($bookDTO->currency);
        $book->setExtract($bookDTO->extract);
       

        return $book;
    }

    public function toDTO(Book $book): BookDTO
    {
        $bookDTO = new BookDTO();
        $bookDTO->title = $book->getTitle();
        $bookDTO->authorName = $book->getAuthorName();
        $bookDTO->price = $book->getPrice();
        $bookDTO->stock = $book->getStock();
        $bookDTO->format = $book->getFormat()->value ;
        $bookDTO->description = $book->getDescription();
        $bookDTO->isbn = $book->getIsbn();
        $bookDTO->pageCount = $book->getPageCount();
        $bookDTO->currency = $book->getCurrency();

        return $bookDTO;
    }
}