<?php

namespace App\SellerPayments\Domain\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\SellerPayments\Domain\Entity\ClaimMessage;
use App\Orders\Domain\Entity\OrderItem;
use App\Enum\ClaimReason;
use App\Enum\ClaimStatus;
use App\SellerPayments\Domain\Entity\SellerPayment;
use App\Users\Domain\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\SellerPayments\Infrastructure\Repository\ClaimRepository;

#[ORM\Entity(repositoryClass: ClaimRepository::class)]
#[ApiResource]
class Claim
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\ManyToOne(inversedBy: 'claims')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SellerPayment $sellerPayment = null;   

    #[ORM\ManyToOne(inversedBy: 'buyerClaims')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $buyer = null;

    #[ORM\ManyToOne(inversedBy: 'sellerClaims')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $seller = null;

    #[ORM\Column(enumType: ClaimStatus::class)]
    private ?ClaimStatus $status = null;

    #[ORM\Column(enumType: ClaimReason::class)]
    private ?ClaimReason $reason = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resolvedAt = null;
   

    /**
     * @var Collection<int, ClaimMessage>
     */
    #[ORM\OneToMany(
        targetEntity: ClaimMessage::class, 
        mappedBy: 'claim',
        cascade:['persist'],    //Pour que l'enregistrement auto du message dans claimMessage puisse se faire lors de l'enregistrement d'un Claim
        orphanRemoval: true)]
    private Collection $claimMessages;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\ManyToMany(
        targetEntity: OrderItem::class, 
        inversedBy: 'claims',
        cascade:['persist'],
        )]    
    private Collection $orderItems;

    

    public function __construct()
    {
        $this->claimMessages = new ArrayCollection();
        $this->orderItems = new ArrayCollection();

    }   

    public function getId(): ?int
    {
        return $this->id;
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

  

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): static
    {
        $this->buyer = $buyer;

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

    public function getStatus(): ?ClaimStatus
    {
        return $this->status;
    }

    public function setStatus(ClaimStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getReason(): ?ClaimReason
    {
        return $this->reason;
    }

    public function setReason(ClaimReason $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getResolvedAt(): ?\DateTimeImmutable
    {
        return $this->resolvedAt;
    }

    public function setResolvedAt(?\DateTimeImmutable $resolvedAt): static
    {
        $this->resolvedAt = $resolvedAt;

        return $this;
    }

    /**
     * @return Collection<int, ClaimMessage>
     */
    public function getClaimMessages(): Collection
    {
        return $this->claimMessages;
    }

    public function addClaimMessage(ClaimMessage $claimMessage): static
    {
        if (!$this->claimMessages->contains($claimMessage)) {
            $this->claimMessages->add($claimMessage);
            $claimMessage->setClaim($this);
        }

        return $this;
    }

    public function removeClaimMessage(ClaimMessage $claimMessage): static
    {
        if ($this->claimMessages->removeElement($claimMessage)) {
            // set the owning side to null (unless already changed)
            if ($claimMessage->getClaim() === $this) {
                $claimMessage->setClaim(null);
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
            $orderItem->addClaim($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            $orderItem->removeClaim($this);
        }

        return $this;
    }

  
}
