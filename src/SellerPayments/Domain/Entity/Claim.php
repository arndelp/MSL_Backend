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
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\SellerPayments\UI\ApiPlatform\ClaimProcessor;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClaimRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            processor: ClaimProcessor::class,
            inputFormats: [
                'multipart' => ['multipart/form-data']
            ],
            deserialize: false
        )
    ],
    normalizationContext: ['groups' => ['claim:read']],
    denormalizationContext: ['groups' => ['claim:write']]
)]
class Claim
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['claim:read'])]
    private ?int $id = null;
    
    #[ORM\ManyToOne(inversedBy: 'claims')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['claim:read'])]
    private ?SellerPayment $sellerPayment = null;   

    #[ORM\ManyToOne(inversedBy: 'buyerClaims')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['claim:read'])]
    private ?User $buyer = null;

    #[ORM\ManyToOne(inversedBy: 'sellerClaims')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['claim:read'])]
    private ?User $seller = null;

    #[ORM\Column(enumType: ClaimStatus::class)]   
    #[Groups(['claim:read'])]
    private ?ClaimStatus $status = null;

    #[ORM\Column(enumType: ClaimReason::class)]
    #[Groups(['claim:read', 'claim:write'])]
    private ?ClaimReason $reason = null;

    #[ORM\Column]
    #[Groups(['claim:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['claim:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['claim:read'])]
    private ?\DateTimeImmutable $resolvedAt = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['claim:read'])]
    private ?array $images = [];
   

    /**
     * @var Collection<int, ClaimMessage>
     */
    #[ORM\OneToMany(
        targetEntity: ClaimMessage::class, 
        mappedBy: 'claim',
        cascade:['persist'],    //Pour que l'enregistrement auto du message dans claimMessage puisse se faire lors de l'enregistrement d'un Claim
        orphanRemoval: true)]
    #[Groups(['claim:read', 'claim:write'])]
    private Collection $claimMessages;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\ManyToMany(
        targetEntity: OrderItem::class, 
        inversedBy: 'claims',
        cascade:['persist'],
        )]    
    #[Groups(['claim:read', 'claim:write'])]
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


    public function getImages(): array { return $this->images ?? [];}
    public function setImages(?array $images): static { $this->images = $images ?? []; return $this;}

    public function addImage(string $image): static
    {
        if (!in_array($image, $this->images ?? [])) {
        $this->images[] = $image;
        }
        return $this;
    }

    public function removeImage(string $image): static
    {
        $this->images = array_values(array_filter(
            $this->images ?? [],
            fn($img) => $img !== $image
        ));

        return $this;
    }

    #[Groups(['claim:read'])]
    public function getImageUrls(): array
    {
        // si aucune image n'est définie, on retourne un tableau vide, sinon on retourne un tableau d'url complètes en préfixant le chemin d'accès aux images
        $urls = [];
        foreach ($this->images ?? [] as $image) { 
            $urls[] = 'http://localhost:8000/uploads/images/claims/' . $image;
        }
        return $urls;
    }
  
}
