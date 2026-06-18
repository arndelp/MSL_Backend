<?php

namespace App\Contacts\Domain\Entity;

use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Contacts\Application\DTO\ContactMessageInputDTO;
use App\Contacts\UI\Controller\ContactMessageController;
use App\Contacts\Domain\Repository\DoctrineContactRepository;
use App\Users\Domain\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DoctrineContactRepository::class)]
#[ApiResource(
    input: ContactMessageInputDTO::class,
    normalizationContext: ['groups' => ['contact:read']],
    formats: ['json'],
    operations: [
        new Post(
            uriTemplate: '/contacts',
            controller: ContactMessageController::class,
            input: ContactMessageInputDTO::class,
            output: false,
            name: 'custom_create_contact',
            denormalizationContext: ['groups' => ['contact:write']]
        )
    ]
)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]    
    #[Groups(['contact:read', 'contact:write'])]
    private ?int $id = null;    

    #[ORM\Column(length: 255, nullable: true)]   
    #[Groups(['contact:read', 'contact:write'])]
    private ?string $subject = null;   

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['contact:read', 'contact:write'])]   
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['contact:read', 'contact:write'])]
    private ?bool $isRead = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'contacts')] // un livre a un seul auteur, mais un auteur peut avoir plusieurs livres
    #[ORM\JoinColumn(nullable: false)] // la colonne user_id dans la table contact ne peut pas être nulle, un livre doit toujours avoir un auteur
    private ?User $user = null; // user = auteur du livre (ManyToOne)









    public function getId(): ?int
    {
        return $this->id;
    }    

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }    

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(?bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User { return $this->user;}
    public function setUser(?User $user): static { $this->user = $user; return $this;   } // définit l'auteur de ce livre (ManyToOne)

    }
