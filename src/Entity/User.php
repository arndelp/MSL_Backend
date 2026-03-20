<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Book;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{ 

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'book:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['user:read', 'book:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['user:read', 'book:read'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 150, unique: true)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[ORM\Column]    
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private ?string $stripeAccount = null;

    #[ORM\Column(nullable: true)]
    private ?bool $stripeOnboarded = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    
// ---------- GETTERS & SETTERS ----------

    public function __construct()
    {
        $this->books = new ArrayCollection();   //books = livres écrits par cet utilisateur (OneToMany)

        // création automatique du profile
        $profile = new AuthorProfile();
        $profile->setUser($this);
        $this->authorProfile = $profile;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    // public function getPassword(): ?string
    //{
    //    return $this->password;
    //}

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

    public function getStripeOnboarded(): ?bool
    {
        return $this->stripeOnboarded;
    }

    public function setStripeOnboarded(?bool $stripeOnboarded): static
    {
        $this->stripeOnboarded = $stripeOnboarded;

        return $this;
    }

    // public function getRoles(): array
    //{
    //    return $this->roles;
    //}

    public function setRoles(?array $roles): static
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