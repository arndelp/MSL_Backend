<?php

namespace App\Addresses\Domain\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Addresses\Infrastructure\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Users\Domain\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['address:read']],
    denormalizationContext: ['groups' => ['address:write']],
    operations: [
        new Get(
            security: "object.getUser() == user"
        ),
        new GetCollection(
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        // Les opérations de mise à jour et de suppression sont sécurisées pour que seuls les propriétaires de l'adresse puissent les effectuer
        new Patch(
            security: "object.getUser() == user"
        ),
        new Delete(
            security: "object.getUser() == user"
        )
    ]
)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['address:read'])]
    private ?int $id = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['address:read'])]
    private bool $isDefault = false;

    #[ORM\Column(length: 255)]
    #[Groups(['address:read', 'address:write'])]
    private ?string $addressLine1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['address:read', 'address:write'])]
    private ?string $addressLine2 = null;

    #[ORM\Column(length: 20)]
    #[Groups(['address:read', 'address:write'])]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100)]
    #[Groups(['address:read', 'address:write'])]
    private ?string $city = null;

    #[ORM\Column(length: 100)]
    #[Groups(['address:read', 'address:write'])]
    private ?string $country = 'France';

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): static
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(?string $addressLine1): static
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): static
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

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
}
