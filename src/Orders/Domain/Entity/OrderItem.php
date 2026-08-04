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
use App\Enum\CancellationReason;


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
    #[ORM\JoinColumn(name: 'seller_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

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
    private ?string $user_amount = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $refund_amount = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $stripeSessionId = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $stripePaymentIntentId = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripe_transfer_id = null;

    #[ORM\Column(nullable: true, enumType: PayoutStatus::class)]
    private ?PayoutStatus $payout_status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $paid_to_user_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $user_confirmed_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $shipped_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $cancelled_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $buyer_user = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $confirmation_token = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $confirmation_token_expires_at = null;

    #[ORM\Column(enumType: CancellationReason::class, nullable: true)]
    private ?CancellationReason $cancellationReason = null;

    public function getCancellationReason(): ?CancellationReason
    {
        return $this->cancellationReason;
    }

    public function setCancellationReason(?CancellationReason  $cancellationReason): static
    {
        $this->cancellationReason = $cancellationReason;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmation_token;
    }

    public function setConfirmationToken(?string $confirmation_token): static
    {
        $this->confirmation_token = $confirmation_token;

        return $this;
    }

    public function getConfirmationTokenExpiresAt() : ?\DateTimeImmutable
    {
        return $this->confirmation_token_expires_at;
    }

    public function setConfirmationTokenExpiresAt(?\DateTimeImmutable $confirmation_token_expires_at) : static
    {
        $this->confirmation_token_expires_at = $confirmation_token_expires_at;
        return $this;
    }

    public function getBuyerUser(): ?User
    {
        return $this->buyer_user;
    }

    public function setBuyerUser(?User $buyer_user): self
    {
        $this->buyer_user = $buyer_user;
        return $this;
    }
   

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getUserAmount(): ?string
    {
        return $this->user_amount;
    }

    public function setUserAmount(?string $user_amount): static
    {
        $this->user_amount = $user_amount;

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

        public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(?string $stripeSessionId): self
    {
        $this->stripeSessionId = $stripeSessionId;
        return $this;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripePaymentIntentId;
    }

    public function setStripePaymentIntentId(?string $stripePaymentIntentId): self
    {
        $this->stripePaymentIntentId = $stripePaymentIntentId;
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

    public function getPaidToUserAt(): ?\DateTimeImmutable
    {
        return $this->paid_to_user_at;
    }

    public function setPaidToUserAt(?\DateTimeImmutable $paid_to_user_at): static
    {
        $this->paid_to_user_at = $paid_to_user_at;

        return $this;
    }

    public function getUserConfirmedAt(): ?\DateTimeImmutable
    {
        return $this->user_confirmed_at;
    }

    public function setUserConfirmedAt(?\DateTimeImmutable $user_confirmed_at): static
    {
        $this->user_confirmed_at = $user_confirmed_at;

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
