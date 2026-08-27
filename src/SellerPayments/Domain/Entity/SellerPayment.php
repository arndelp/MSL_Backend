<?php

namespace App\SellerPayments\Domain\Entity;

use App\Orders\Domain\Entity\Order;
use App\Users\Domain\Entity\User;
use App\Enum\SellerPaymentStatus;
use App\Enum\ShippingMethod;
use App\Enum\ShippingStatus;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Orders\Domain\Entity\OrderItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Enum\CancellationReason;
use App\Enum\PayoutStatus;

#[ORM\Entity]
#[ORM\Table(name: 'seller_payments')]
class SellerPayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /*
     |--------------------------------------------------------------------------
     | Relations
     |--------------------------------------------------------------------------
     */

    #[ORM\ManyToOne(inversedBy: 'sellerPayments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\ManyToOne(inversedBy: 'sellerPayments')]
    #[ORM\JoinColumn(name: 'seller_id', nullable: false)]
    private ?User $seller = null;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(
        targetEntity: OrderItem::class,
        mappedBy: 'sellerPayment',
        cascade:['persist'], 
        orphanRemoval: true
    )]
    private Collection $orderItems;

    
    /*
    |--------------------------------------------------------------------------
    | numéro de payment
    |--------------------------------------------------------------------------
    */

    #[ORM\Column(length:40, unique: true)]
    private ?string $paymentNumber = null;

    /*
     |--------------------------------------------------------------------------
     | Statut
     |--------------------------------------------------------------------------
     */

    #[ORM\Column(enumType: SellerPaymentStatus::class)]
    private ?SellerPaymentStatus $status = null;

    #[ORM\Column(enumType: CancellationReason::class, nullable: true)]
    private ?CancellationReason $cancellationReason = null;

    /*
     |--------------------------------------------------------------------------
     | Montants (toujours en centimes)
     |--------------------------------------------------------------------------
     */

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $subtotal_amount = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $shipping_amount = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $total_amount = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $platform_fee = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $seller_amount = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $refund_amount = null;

    #[ORM\Column(length: 10)]
    private ?string $currency = null;

    /*
     |--------------------------------------------------------------------------
     | Stripe
     |--------------------------------------------------------------------------
     */
    
    #[ORM\Column(length:255, nullable:true)]
    private ?string $stripe_transfer_id = null;

    #[ORM\Column(enumType: PayoutStatus::class, nullable:true)]
    private ?PayoutStatus $payout_status = null;

    /*
     |--------------------------------------------------------------------------
     | Livraison (Boxtal)
     |--------------------------------------------------------------------------
     */

    #[ORM\Column(enumType: ShippingMethod::class, nullable:true)]
    private ?ShippingMethod $shipping_method = null;

    #[ORM\Column(enumType: ShippingStatus::class, nullable:true)]
    private ?ShippingStatus $shipping_status = null;

    #[ORM\Column(length:100, nullable:true)]
    private ?string $carrier_name = null;

    #[ORM\Column(length:150, nullable:true)]
    private ?string $carrier_service = null;

    #[ORM\Column(length:255, nullable:true)]
    private ?string $tracking_number = null;

    #[ORM\Column(length:255, nullable:true)]
    private ?string $shipping_label_id = null;

    #[ORM\Column(length:1000, nullable:true)]
    private ?string $shipping_label_url = null;

    /*
    |--------------------------------------------------------------------------
    | Token
    |--------------------------------------------------------------------------
    */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $confirmation_token = null;

    /*
     |--------------------------------------------------------------------------
     | Dates
     |--------------------------------------------------------------------------
     */


    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $estimated_shipping_date = null;

    #[ORM\Column(nullable:true)]
    private ?\DateTimeImmutable $authorized_at = null;

    #[ORM\Column(nullable:true)]
    private ?\DateTimeImmutable $captured_at = null;

    #[ORM\Column(nullable:true)]
    private ?\DateTimeImmutable $cancelled_at = null;

    #[ORM\Column(nullable:true)]
    private ?\DateTimeImmutable $transferred_at = null;

    #[ORM\Column(nullable:true)]
    private ?\DateTimeImmutable $fulfilled_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $confirmation_token_expires_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $paid_to_seller_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $seller_confirmed_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $shipped_at = null;

    
   



    //constructeur
    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $this->updated_at = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $this->status = SellerPaymentStatus::CREATED;
        $this->currency = 'EUR';
        $this->orderItems = new ArrayCollection();
        
    }

    //Getter/Setter

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

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getSeller(): ?User
    {
        return $this->seller;
    }

    public function setSeller(?User $seller): self
    {
        $this->seller = $seller;
        return $this;
    }
    
    public function getPaymentNumber(): ?string
    {
        return $this->paymentNumber;
    }

    public function setPaymentNumber(?string $paymentNumber): static
    {
        $this->paymentNumber = $paymentNumber;

        return $this;
    }   

    public function getStatus(): ?SellerPaymentStatus
    {
        return $this->status;
    }

    public function setStatus(?SellerPaymentStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCancellationReason(): ?CancellationReason
    {
        return $this->cancellationReason;
    }

    public function setCancellationReason(?CancellationReason  $cancellationReason): static
    {
        $this->cancellationReason = $cancellationReason;

        return $this;
    }   

    public function getSubtotalAmount(): ?string
    {
        return $this->subtotal_amount;
    }

    public function setSubtotalAmount(?string $subtotal_amount): self
    {
        $this->subtotal_amount = $subtotal_amount;
        return $this;
    }

    public function getShippingAmount(): ?string
    {
        return $this->shipping_amount;
    }

    public function setShippingAmount(?string $shipping_amount): self
    {
        $this->shipping_amount = $shipping_amount;
        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->total_amount;
    }

    public function setTotalAmount(?string $total_amount): self
    {
        $this->total_amount = $total_amount;
        return $this;
    }

    public function getPlatformFee(): ?string
    {
        return $this->platform_fee;
    }

    public function setPlatformFee(?string $platform_fee): self
    {
        $this->platform_fee = $platform_fee;
        return $this;
    }

    public function getSellerAmount(): ?string
    {
        return $this->seller_amount;
    }

    public function setSellerAmount(?string $seller_amount): self
    {
        $this->seller_amount = $seller_amount;
        return $this;
    }

    public function getRefundAmount(): ?string
    {
        return $this->refund_amount;
    }

    public function setRefundAmount(?string $refund_amount): self
    {
        $this->refund_amount = $refund_amount;
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    

    public function getStripeTransferId(): ?string
    {
        return $this->stripe_transfer_id;
    }

    public function setStripeTransferId(?string $stripe_transfer_id): self
    {
        $this->stripe_transfer_id = $stripe_transfer_id;
        return $this;
    }

    public function getPayoutStatus(): ?PayoutStatus
    {
        return $this->payout_status;
    }

    public function setPayoutStatus(?PayoutStatus $payout_status): self
    {
        $this->payout_status = $payout_status;
        return $this;
    }

    public function getShippingMethod(): ?ShippingMethod
    {
        return $this->shipping_method;
    }

    public function setShippingMethod(?ShippingMethod $shipping_method): self
    {
        $this->shipping_method = $shipping_method;
        return $this;
    }

    public function getShippingStatus(): ?ShippingStatus
    {
        return $this->shipping_status;
    }

    public function setShippingStatus(?ShippingStatus $shipping_status): self
    {
        $this->shipping_status = $shipping_status;
        return $this;
    }

    public function getCarrierName(): ?string
    {
        return $this->carrier_name;
    }

    public function setCarrierName(?string $carrier_name): self
    {
        $this->carrier_name = $carrier_name;
        return $this;
    }

    public function getCarrierService(): ?string
    {
        return $this->carrier_service;
    }

    public function setCarrierService(?string $carrier_service): self
    {
        $this->carrier_service = $carrier_service;
        return $this;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->tracking_number;
    }

    public function setTrackingNumber(?string $tracking_number): self
    {
        $this->tracking_number = $tracking_number;
        return $this;
    }

    public function getShippingLabelId(): ?string
    {
        return $this->shipping_label_id;
    }

    public function setShippingLabelId(?string $shipping_label_id): self
    {
        $this->shipping_label_id = $shipping_label_id;
        return $this;
    }

    public function getShippingLabelUrl(): ?string
    {
        return $this->shipping_label_url;
    }

    public function setShippingLabelUrl(?string $shipping_label_url): self
    {
        $this->shipping_label_url = $shipping_label_url;
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

    public function getEstimatedShippingDate(): ?\DateTimeImmutable
    {
        return $this->estimated_shipping_date;
    }

    public function setEstimatedShippingDate(?\DateTimeImmutable $estimated_shipping_date): self
    {
        $this->estimated_shipping_date = $estimated_shipping_date;
        return $this;
    }

    public function getAuthorizedAt(): ?\DateTimeImmutable
    {
        return $this->authorized_at;
    }

    public function setAuthorizedAt(?\DateTimeImmutable $authorized_at): self
    {
        $this->authorized_at = $authorized_at;
        return $this;
    }

    public function getCapturedAt(): ?\DateTimeImmutable
    {
        return $this->captured_at;
    }

    public function setCapturedAt(?\DateTimeImmutable $captured_at): self
    {
        $this->captured_at = $captured_at;
        return $this;
    }

    public function getCancelledAt(): ?\DateTimeImmutable
    {
        return $this->cancelled_at;
    }

    public function setCancelledAt(?\DateTimeImmutable $cancelled_at): self
    {
        $this->cancelled_at = $cancelled_at;
        return $this;
    }

    public function getTransferredAt(): ?\DateTimeImmutable
    {
        return $this->transferred_at;
    }

    public function setTransferredAt(?\DateTimeImmutable $transferred_at): self
    {
        $this->transferred_at = $transferred_at;
        return $this;
    }

    public function getFulfilledAt(): ?\DateTimeImmutable
    {
        return $this->fulfilled_at;
    }

    public function setFulfilledAt(?\DateTimeImmutable $fulfilled_at): self
    {
        $this->fulfilled_at = $fulfilled_at;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;
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

    public function getPaidToSellerAt() : ?\DateTimeImmutable
    {
        return $this->paid_to_seller_at;
    }

    public function setPaidToSellerAt(?\DateTimeImmutable $paid_to_seller_at) : static
    {
        $this->paid_to_seller_at = $paid_to_seller_at;
        return $this;
    }

    public function getSellerConfirmedAt() : ?\DateTimeImmutable
    {
        return $this->seller_confirmed_at;
    }

    public function setSellerConfirmedAt(?\DateTimeImmutable $seller_confirmed_at) : static
    {
        $this->seller_confirmed_at = $seller_confirmed_at;
        return $this;
    }

    public function getShippedAt() : ?\DateTimeImmutable
    {
        return $this->shipped_at;
    }

    public function setShippedAt(?\DateTimeImmutable $shipped_at) : static
    {
        $this->shipped_at = $shipped_at;
        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setSellerPayment($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getSellerPayment() === $this) {
                $orderItem->setSellerPayment(null);
            }
        }

        return $this;
    }

    

    

    
}