<?php

namespace App\Orders\Domain\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Orders\Infrastructure\Repository\OrderRepository;
use App\Users\Domain\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\OrderStatus;
use App\Orders\Domain\Entity\OrderItem;
use App\SellerPayments\Domain\Entity\SellerPayment;


#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ApiResource]
#[ORM\Table(name: "orders")] 
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: OrderStatus::class)]  
    private ?OrderStatus $status = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $total_amount = null;

    #[ORM\Column(length: 10)]
    private ?string $currency = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripe_session_id = null;

    #[ORM\Column(type: 'string',length: 255, nullable: true)]
    private ?string $stripe_payment_intent_id = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $stripe_fee_total = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shipping_firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shipping_lastname = null;   

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shipping_address_line_1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shipping_address_line_2 = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $shipping_postal_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shipping_city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shipping_country = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $shipping_phone_number = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $paid_at = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: true)]
    private ?User $user = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $orderNumber = null;

    //Relations
   
    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(
        targetEntity: OrderItem::class, 
        mappedBy: 'order',         
        cascade:['persist'], 
        orphanRemoval: true)]
    private Collection $orderItems;
    

    /**
     * @var Collection<int, SellerPayment>
     */
    #[ORM\OneToMany(
        targetEntity: SellerPayment::class,
        mappedBy: 'order',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $sellerPayments;



    //Constructeur
    public function __construct()
    {    
        $this->sellerPayments = new ArrayCollection();
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

    public function getStatus(): ?OrderStatus
    {
        return $this->status;
    }

    public function setStatus(?OrderStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->total_amount;
    }

    public function setTotalAmount(string $total_amount): static
    {
        $this->total_amount = $total_amount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripe_session_id;
    }

    public function setStripeSessionId(?string $stripe_session_id): static
    {
        $this->stripe_session_id = $stripe_session_id;

        return $this;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripe_payment_intent_id;
    }

    public function setStripePaymentIntentId(?string $stripe_payment_intent_id): static
    {
        $this->stripe_payment_intent_id = $stripe_payment_intent_id;

        return $this;
    }

    public function getStripeFeeTotal(): ?string
    {
        return $this->stripe_fee_total;
    }

    public function setStripeFeeTotal(?string $stripe_fee_total): static
    {
        $this->stripe_fee_total = $stripe_fee_total;

        return $this;
    }

    public function getShippingFirstname(): ?string
    {
        return $this->shipping_firstname;
    }

    public function setShippingFirstname(?string $shipping_Firstname):static
    {
        $this->shipping_firstname = $shipping_Firstname;

        return $this;
    }

    public function getShippingLastname(): ?string
    {
        return $this->shipping_lastname;
    }

    public function setShippingLastname(?string $shipping_lastname):static
    {
        $this->shipping_lastname = $shipping_lastname;

        return $this;
    }

    public function getShippingAddressLine1(): ?string
    {
        return $this->shipping_address_line_1;
    }

    public function setShippingAddressLine1(?string $shipping_address_line_1): static
    {
        $this->shipping_address_line_1 = $shipping_address_line_1;

        return $this;
    }

    public function getShippingAddressLine2(): ?string
    {
        return $this->shipping_address_line_2;
    }

    public function setShippingAddressLine2(?string $shipping_address_line_2): static
    {
        $this->shipping_address_line_2 = $shipping_address_line_2;

        return $this;
    }

    public function getShippingPostalCode(): ?string
    {
        return $this->shipping_postal_code;
    }

    public function setShippingPostalCode(?string $shipping_postal_code): static
    {
        $this->shipping_postal_code = $shipping_postal_code;

        return $this;
    }

    public function getShippingCity(): ?string
    {
        return $this->shipping_city;
    }

    public function setShippingCity(?string $shipping_city): static
    {
        $this->shipping_city = $shipping_city;

        return $this;
    }

    public function getShippingCountry(): ?string
    {
        return $this->shipping_country;
    }

    public function setShippingCountry(?string $shipping_country): static
    {
        $this->shipping_country = $shipping_country;

        return $this;
    }

    public function getShippingPhoneNumber(): ?string
    {
        return $this->shipping_phone_number;
    }

    public function setShippingPhoneNumber(?string $shipping_phone_number): static
    {
        $this->shipping_phone_number = $shipping_phone_number;

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

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paid_at;
    }

    public function setPaidAt(?\DateTimeImmutable $paid_at): static
    {
        $this->paid_at = $paid_at;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }


//Collections
   

     /**
     * @return Collection<int, SellerPayment>
     */
    public function getSellerPayments(): Collection
    {
        return $this->sellerPayments;
    }

    public function addSellerPayment(SellerPayment $sellerPayment): static
    {
        if (!$this->sellerPayments->contains($sellerPayment)) {
            $this->sellerPayments->add($sellerPayment);
            $sellerPayment->setOrder($this);
        }

        return $this;
    }

    public function removeSellerPayment(SellerPayment $sellerPayment): static
    {
        if ($this->sellerPayments->removeElement($sellerPayment)) {
            // set the owning side to null (unless already changed)
            if ($sellerPayment->getOrder() === $this) {
                $sellerPayment->setOrder(null);
            }
        }

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
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }


}
