<?php

namespace App\Orders\Domain\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Books\Domain\Entity\Book;
use App\Enum\PayoutStatus;
use App\Orders\Infrastructure\Repository\OrderItemRepository;
use App\Users\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\OrderItemStatus;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ApiResource]
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
    private ?User $author = null;

    #[ORM\Column(enumType: OrderItemStatus::class)]
    private ?OrderItemStatus $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $book_title = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $unit_price = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $total_price = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $platform_fee = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $author_amount = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $refund_amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripe_transfer_id = null;

    #[ORM\Column(nullable: true, enumType: PayoutStatus::class)]
    private ?PayoutStatus $payout_status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $paid_to_author_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $author_confirmed_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $shipped_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $cancelled_at = null;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

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

    public function getTotalPrice(): ?string
    {
        return $this->total_price;
    }

    public function setTotalPrice(string $total_price): static
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function getPlatformFee(): ?string
    {
        return $this->platform_fee;
    }

    public function setPlatformFee(?string $platform_fee): static
    {
        $this->platform_fee = $platform_fee;

        return $this;
    }

    public function getAuthorAmount(): ?string
    {
        return $this->author_amount;
    }

    public function setAuthorAmount(?string $author_amount): static
    {
        $this->author_amount = $author_amount;

        return $this;
    }

    public function getRefundAmount(): ?string
    {
        return $this->refund_amount;
    }

    public function setRefundAmount(?string $refund_amount): static
    {
        $this->refund_amount = $refund_amount;

        return $this;
    }

    public function getStripeTransferId(): ?string
    {
        return $this->stripe_transfer_id;
    }

    public function setStripeTransferId(?string $stripe_transfer_id): static
    {
        $this->stripe_transfer_id = $stripe_transfer_id;

        return $this;
    }

    public function getPayoutStatus(): ?PayoutStatus
    {
        return $this->payout_status;
    }

    public function setPayoutStatus(?PayoutStatus $payout_status): static
    {
        $this->payout_status = $payout_status;

        return $this;
    }

    public function getPaidToAuthorAt(): ?\DateTimeImmutable
    {
        return $this->paid_to_author_at;
    }

    public function setPaidToAuthorAt(?\DateTimeImmutable $paid_to_author_at): static
    {
        $this->paid_to_author_at = $paid_to_author_at;

        return $this;
    }

    public function getAuthorConfirmedAt(): ?\DateTimeImmutable
    {
        return $this->author_confirmed_at;
    }

    public function setAuthorConfirmedAt(?\DateTimeImmutable $author_confirmed_at): static
    {
        $this->author_confirmed_at = $author_confirmed_at;

        return $this;
    }

    public function getShippedAt(): ?\DateTimeImmutable
    {
        return $this->shipped_at;
    }

    public function setShippedAt(?\DateTimeImmutable $shipped_at): static
    {
        $this->shipped_at = $shipped_at;

        return $this;
    }

    public function getCancelledAt(): ?\DateTimeImmutable
    {
        return $this->cancelled_at;
    }

    public function setCancelledAt(?\DateTimeImmutable $cancelled_at): static
    {
        $this->cancelled_at = $cancelled_at;

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
