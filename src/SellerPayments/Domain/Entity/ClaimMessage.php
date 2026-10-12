<?php

namespace App\SellerPayments\Domain\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\SellerPayments\Infrastructure\Repository\ClaimMessageRepository;
use App\SellerPayments\Domain\Entity\Claim;
use App\Users\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClaimMessageRepository::class)]
#[ApiResource]
class ClaimMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'claimMessages')]
    private ?Claim $claim = null;

    #[ORM\ManyToOne(inversedBy: 'claimMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $isRead = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClaim(): ?Claim
    {
        return $this->claim;
    }

    public function setClaim(?Claim $claim): static
    {
        $this->claim = $claim;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

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

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }
}
