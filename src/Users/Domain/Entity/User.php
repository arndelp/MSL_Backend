<?php

namespace App\Users\Domain\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\ApiResource;
use App\Orders\Domain\Entity\OrderItem;
use App\Orders\Domain\Entity\Order;
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
use App\Addresses\Domain\Entity\Address;
use App\Contacts\Domain\Entity\Contact;
use App\SellerPayments\Domain\Entity\SellerPayment;

#[ORM\Entity(repositoryClass: DoctrineUserRepository::class)]
#[ApiResource(
    // Création d'utilisateur
    input: CreateUserDTO::class, 
    normalizationContext: ['groups' => ['user:read', 'book:read', 'contact:read']], // groupes de sérialisation pour la lecture
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
    #[Groups(['user:read', 'book:read', 'contact:read'])]
    private ?int $id = null;

    #[ORM\Column(name: 'firstname',length: 50, nullable: true)]
    #[Groups(['user:read', 'book:read', 'contact:read'])]
    private ?string $firstname = null;

    #[ORM\Column(name: 'lastname',length: 50, nullable: true)]
    #[Groups(['user:read', 'book:read', 'contact:read'])]
    private ?string $lastname = null;

    #[ORM\Column(name: 'email',length: 150, unique: true)]
    #[Groups(['user:read', 'book:read', 'contact:read'])]
    private ?string $email = null;

    #[ORM\Column(name: 'password')]   
    private ?string $password = null;

     #[ORM\Column(name:'type',length: 20, nullable: true)]
    #[Groups(['user:read', 'book:read'])]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:private', 'book:private'])]
    private ?string $stripeConnectAccountId = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:private', 'book:private'])]
    private ?bool $stripeOnboarded = null;

    #[ORM\Column(name:'roles',type: 'json')]
    #[Groups(['user:private', 'book:private', 'contact:private'])]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Book::class)] // un utilisateur peut être l'auteur de plusieurs livres, mais un livre n'a qu'un seul auteur
    private Collection $books;   

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?AuthorProfile $authorProfile = null;

    //Collections

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Address::class)]
    private Collection $addresses;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Contact::class)]
    private Collection $contacts;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $orders;

    
 
    /**
     * @var Collection<int, SellerPayment>
     */
    #[ORM\OneToMany(
        targetEntity: SellerPayment::class,
        mappedBy: 'seller'
    )]
    private Collection $sellerPayments;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'seller')]
    private Collection $orderItems;   // adresses de livraison de l'utilisateur (OneToMany)

    


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

   
    


// Constructeur
    public function __construct()
    {
        $this->books = new ArrayCollection();   //books = livres écrits par cet utilisateur (OneToMany)
        $this->addresses = new ArrayCollection();   //addresses = adresses de livraison de l'utilisateur (OneToMany)
        // création automatique du profile
        //$profile = new AuthorProfile();
        //$profile->setUser($this);
        //$this->authorProfile = $profile;
        $this->orders = new ArrayCollection();      
        $this->contacts = new ArrayCollection();
        $this->sellerPayments = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
    }


// ---------- GETTERS & SETTERS ----------
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

    public function getStripeConnectAccountId(): ?string
    {
        return $this->stripeConnectAccountId;
    }

    public function setStripeConnectAccountId(?string $stripeConnectAccountId): static
    {
        $this->stripeConnectAccountId = $stripeConnectAccountId;

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
            $book->setUser($this); // 🔥 IMPORTANT
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            if ($book->getUser() === $this) {
                $book->setUser(null);
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

    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setUser($this); // 🔥 IMPORTANT
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUserId($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUserId() === $this) {
                $order->setUserId(null);
            }
        }

        return $this;
    }

   

    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setUser($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            if ($contact->getUser() === $this) {
                $contact->setUser(null);
            }
        }

        return $this;
    }


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
            $sellerPayment->setSeller($this);
        }

        return $this;
    }

    public function removeSellerPayment(SellerPayment $sellerPayment): static
    {
        if ($this->sellerPayments->removeElement($sellerPayment)) {
            // set the owning side to null (unless already changed)
            if ($sellerPayment->getSeller() === $this) {
                $sellerPayment->setSeller(null);
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
            $orderItem->setSeller($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getSeller() === $this) {
                $orderItem->setSeller(null);
            }
        }

        return $this;
    }

}