<?php

namespace App\Users\Domain\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\ApiResource;
use App\Users\Infrastructure\Repository\DoctrineUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Books\Domain\Entity\Book;
use App\AuthorProfiles\Domain\Entity\AuthorProfile;
use App\Users\UI\Controller\UserController;
use App\Users\Application\DTO\CreateUserDTO;
use App\Users\Application\DTO\LoginDTO;

#[ORM\Entity(repositoryClass: DoctrineUserRepository::class)]
#[ApiResource(
    // Création d'utilisateur
    input: CreateUserDTO::class, 
    normalizationContext: ['groups' => ['user:read', 'book:read']],
    formats: ['json'],
    operations: [
        new Post(
            controller: UserController::class . '::receiveNewUser',
            input: CreateUserDTO::class,
            output: false,
            name: 'custom_create_user',
            denormalizationContext: ['groups' => ['user']]
        ),
        // Login utilisateur
        new Post(
            controller: UserController::class . '::login',
            input: LoginDTO::class,
            output: false,
            name: 'login_user',
            denormalizationContext: ['groups' => ['user']]
        ),
        new GetCollection(),
        new Get(),
    ]    
    )]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{ 

    #[ORM\Id]
    #[ORM\GeneratedValue]
     #[ORM\Column]
    #[Groups(['user:read', 'book:read'])]
    private ?int $id = null;

    #[ORM\Column(name: 'firstname',length: 50, nullable: true)]
    #[Groups(['user:read', 'book:read'])]
    private ?string $firstname = null;

    #[ORM\Column(name: 'lastname',length: 50, nullable: true)]
    #[Groups(['user:read', 'book:read'])]
    private ?string $lastname = null;

    #[ORM\Column(name: 'email',length: 150, unique: true)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[ORM\Column(name: 'password')]   
    private ?string $password = null;

     #[ORM\Column(name:'type',length: 20, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?string $stripeAccount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?bool $stripeOnboarded = null;

    #[ORM\Column(name:'roles',type: 'json')]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Book::class)] // un utilisateur peut être l'auteur de plusieurs livres, mais un livre n'a qu'un seul auteur
    private Collection $books;

   

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?AuthorProfile $authorProfile = null;


 

// ---------- SECURITY USER INTERFACE METHODS ---------- // méthodes requises par l'interface UserInterface pour la sécurité //
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function eraseCredentials(): void
    {
        // rien à effacer
    }

   
    
// ---------- GETTERS & SETTERS ----------

    public function __construct()
    {
        $this->books = new ArrayCollection();   //books = livres écrits par cet utilisateur (OneToMany)

        // création automatique du profile
        //$profile = new AuthorProfile();
        //$profile->setUser($this);
        //$this->authorProfile = $profile;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

   
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getStripeAccount(): ?string
    {
        return $this->stripeAccount;
    }

    public function setStripeAccount(?string $stripeAccount): static
    {
        $this->stripeAccount = $stripeAccount;

        return $this;
    }

    public function isIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getStripeOnboarded(): ?bool
    {
        return $this->stripeOnboarded;
    }

    public function setStripeOnboarded(?bool $stripeOnboarded): static
    {
        $this->stripeOnboarded = $stripeOnboarded;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Book> // retourne les livres écrits par cet utilisateur (OneToMany)
     */
    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setAuthor($this); // 🔥 IMPORTANT
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }

    public function getBooks(): Collection
    {
        return $this->books;
    }

    

    public function getAuthorProfile(): ?AuthorProfile
    {
        return $this->authorProfile;
    }

    public function setAuthorProfile(?AuthorProfile $authorProfile): static
    {
        // unset the owning side of the relation if necessary
        if ($authorProfile === null && $this->authorProfile !== null) {
            $this->authorProfile->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($authorProfile !== null && $authorProfile->getUser() !== $this) {
            $authorProfile->setUser($this);
        }

        $this->authorProfile = $authorProfile;

        return $this;
    }

    
}