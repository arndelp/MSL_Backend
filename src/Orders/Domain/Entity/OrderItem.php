<?php

namespace App\Orders\Domain\Entity;


use App\Books\Domain\Entity\Book;
use App\Orders\Infrastructure\Repository\OrderItemRepository;
use App\Users\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\OrderItemStatus;
use App\SellerPayments\Domain\Entity\SellerPayment;
use App\Orders\Domain\Entity\Order;


#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;       
   
    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;    

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: true)]                      //Attention nullable: true pour le DEBUG
    private ?SellerPayment $sellerPayment = null;  

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $book_title = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $unit_price = null;
   
    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(name: 'seller_id', referencedColumnName: 'id', nullable: false)]
    private ?User $seller = null;

    #[ORM\Column(enumType: OrderItemStatus::class)]
    private ?OrderItemStatus $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }   

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }    

    public function getSellerPayment(): ?SellerPayment
    {
        return $this->sellerPayment;
    }

    
    public function setSellerPayment(?SellerPayment $sellerPayment): static
    {
        $this->sellerPayment = $sellerPayment;

        return $this;
    }



    public function getBookTitle(): ?string
    {
        return $this->book_title;
    }

    public function setBookTitle(?string $book_title): static
    {
        $this->book_title = $book_title;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unit_price;
    }

    public function setUnitPrice(string $unit_price): static
    {
        $this->unit_price = $unit_price;

        return $this;
    }

     public function getSeller(): ?User
    {
        return $this->seller;
    }

    public function setSeller(?User $seller): static
    {
        $this->seller = $seller;

        return $this;
    }

    public function getStatus(): ?OrderItemStatus
    {
        return $this->status;
    }

    public function setStatus(OrderItemStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }



}
