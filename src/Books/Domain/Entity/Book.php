<?php

namespace App\Books\Domain\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Books\Infrastructure\Repository\BookRepository;
use App\Orders\Domain\Entity\OrderItem;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Categories\Domain\Entity\Category;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Users\Domain\Entity\User;
use Symfony\Component\String\Slugger\SluggerInterface;
use ApiPlatform\Metadata\Post;
use App\Books\UI\ApiPlatform\BookProcessor;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\BookFormat;





#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            processor: BookProcessor::class,
            inputFormats: [
                'multipart' => ['multipart/form-data']
            ],
            deserialize: false //API Platform ne transforme pas la requête en entité (manuel avec le processor)
        )
    ],
    normalizationContext: ['groups' => ['book:read', 'authorNames:read']], 
    denormalizationContext: ['groups' => ['book:write', 'authorNames:write']]    
)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['book:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Groups(['book:read','book:write'])]
    private ?string $title = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['book:read','book:write', 'authorNames:read'])]
    private ?string $authorName = null;



    #[ORM\Column(length: 255)]
    #[Groups(['book:read'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::BIGINT,nullable: true)]
    #[Groups(['book:read','book:write'])]
    private ?string $price = null; // en centimes

    
    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['book:read', 'book:write'])]
    private ?int $quantity = 0;

     #[ORM\Column(options: ['default' => 0])]
    #[Groups(['book:read', 'book:write'])]
    private ?int $quantityReserved = 0;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['book:read', 'book:write'])]
    private ?int $quantitySold = 0;

    #[ORM\Column(enumType: BookFormat::class)]   
    #[Groups(['book:read','book:write'])]
    private BookFormat $format;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['book:read','book:write'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['book:read','book:write'])]
    private ?string $extract = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['book:read','book:write'])]
    private ?string $isbn = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read','book:write'])]
    private ?int $pageCount = null;

    #[ORM\Column(length: 3, nullable: true)]
    #[Groups(['book:read','book:write'])]
    private ?string $currency = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read','book:write'])]
    private ?bool $isPublished = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]    
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2, nullable: true)]
    #[Groups(['book:read'])]
    private ?string $averageRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $reviewCount = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'books', cascade: ['persist'])]
    #[Groups(['book:read'])]
    private Collection $categories;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'books')] // un livre a un seul auteur, mais un auteur peut avoir plusieurs livres
    #[ORM\JoinColumn(nullable: false)] // la colonne user_id dans la table book ne peut pas être nulle, un livre doit toujours avoir un auteur
    #[Groups(['book:read'])]
    private ?User $user = null; // user = auteur du livre (ManyToOne)

    #[ORM\Column(nullable: true)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?int $weight = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?int $width = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?int $height = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?int $thickness = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?bool $isVerified = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?string $cover = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['book:read'])]
    private ?array $images = [];

    #[Vich\UploadableField(mapping: 'book_cover', fileNameProperty: 'cover')]
    private ?File $coverFile = null;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'book')]
    private Collection $orderItems;




    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->orderItems = new ArrayCollection();

        $this->quantityReserved = 0;
        $this->quantitySold = 0;
    }

    // ---------- GETTERS & SETTERS ----------

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getAuthorName(): ?string { return $this->authorName; }
    public function setAuthorName(?string $authorName): static { $this->authorName = $authorName; return $this; }

    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }

    public function getPrice(): ?float { return $this->price; }
    public function setPrice(?float $price): static { $this->price = $price; return $this; }
    

    public function getQuantity(): ?int { return $this->quantity; }

    public function setQuantity(?int $quantity): static { $this->quantity = $quantity; return $this; }

    public function getQuantityReserved(): ?int { return $this->quantityReserved; }    

    public function getQuantitySold(): ?int { return $this->quantitySold; }    

    public function getFormat(): ?BookFormat  {  return $this->format;   }
    public function setFormat(?BookFormat $format): static  {  $this->format = $format;  return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getExtract(): ?string { return $this->extract; }
    public function setExtract(?string $extract): static { $this->extract = $extract; return $this; }

    public function getIsbn(): ?string { return $this->isbn; }
    public function setIsbn(?string $isbn): static { $this->isbn = $isbn; return $this; }

    public function getPageCount(): ?int { return $this->pageCount; }
    public function setPageCount(?int $pageCount): static { $this->pageCount = $pageCount; return $this; }

    public function getCurrency(): ?string { return $this->currency; }
    public function setCurrency(?string $currency): static { $this->currency = $currency; return $this; }

    public function isPublished(): ?bool { return $this->isPublished; }
    public function setIsPublished(?bool $isPublished): static { $this->isPublished = $isPublished; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(?\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getAverageRating(): ?string { return $this->averageRating; }
    public function setAverageRating(?string $averageRating): static { $this->averageRating = $averageRating; return $this; }

    public function getReviewCount(): ?int { return $this->reviewCount; }
    public function setReviewCount(?int $reviewCount): static { $this->reviewCount = $reviewCount; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(?string $status): static { $this->status = $status; return $this;}

    public function getWeight(): ?int { return $this->weight; }
    public function setWeight(?int $weight): static  { $this->weight = $weight; return $this;}

    public function getWidth(): ?int { return $this->width; }
    public function setWidth(?int $width): static  { $this->width = $width; return $this;}

    public function getHeight(): ?int { return $this->height; }
    public function setHeight(?int $height): static  { $this->height = $height; return $this;}

    public function getThickness(): ?int { return $this->thickness; }
    public function setThickness(?int $thickness): static  { $this->thickness = $thickness; return $this;}

    public function isVerified(): ?bool { return $this->isVerified; }
    public function setIsVerified(?bool $isVerified): static { $this->isVerified = $isVerified; return $this; }
    
    public function getCover(): ?string { return $this->cover; }
    public function setCover(?string $cover): static { $this->cover = $cover; return $this; } 

    public function getCoverFile(): ?File { return $this->coverFile; }
    public function setCoverFile(?File $coverFile ): static 
        { 
            $this->coverFile = $coverFile;

            //if ($coverFile) {
             //   $this->updatedAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
            //}

            return $this;
        }

    public function getImages(): array { return $this->images ?? [];}
    public function setImages(?array $images): static { $this->images = $images ?? []; return $this;}


    /**
     * @return Collection<int, Category> // retourne les catégories associées à ce livre (ManyToMany)
     */

    #[Groups(['book:read'])]
    public function getCategories(): Collection { return $this->categories; } // retourne les catégories associées à ce livre (ManyToMany)

    public function addCategory(Category $category): static // add() ajoute la catégorie à ce livre, mais ne gère pas la relation inverse (ajout de ce livre à la catégorie), donc on le fait manuellement pour garder la cohérence de la relation bidirectionnelle
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addBook($this);
        }
        return $this;
    }

    public function removeCategory(Category $category): static // removeElement() retourne true si l'élément était présent et a été supprimé
    {
        if ($this->categories->removeElement($category)) { // si la catégorie a bien été supprimée de ce livre, alors on supprime aussi ce livre de la catégorie (relation bidirectionnelle)
            $category->removeBook($this);
        }
        return $this;
    }

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

    public function generateSlug(SluggerInterface $slugger): void
    {
        $this->slug = strtolower($slugger->slug($this->title)->toString());
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateSlug(): void
    {
        if ($this->title && !$this->slug) {
            $this->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->title)));
        }
    }


    public function getUser(): ?User { return $this->user; } // retourne l'auteur (user) de ce livre (ManyToOne)
    public function setUser(?User $user): static { $this->user = $user; return $this;   } // définit l'auteur de ce livre (ManyToOne)

    //envoi de l'url des images
    #[Groups(['book:read'])]
    public function getCoverUrl(): ?string
    {
        // si aucune couverture n'est définie, on retourne null, sinon on retourne l'url complète en préfixant le chemin d'accès aux couvertures
        if (!$this->cover) {
            return null;
        } else {
            return 'http://localhost:8000/uploads/covers/' . $this->cover;
        }
      
    }
    
    #[Groups(['book:read'])]
    public function getImageUrls(): array
    {
        // si aucune image n'est définie, on retourne un tableau vide, sinon on retourne un tableau d'url complètes en préfixant le chemin d'accès aux images
        $urls = [];
        foreach ($this->images ?? [] as $image) { 
            $urls[] = 'http://localhost:8000/uploads/images/' . $image;
        }
        return $urls;
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
            $orderItem->setBook($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getBook() === $this) {
                $orderItem->setBook(null);
            }
        }

        return $this;
    }



//fonction pour gérer les stocks (pas de setters publics)
        #[Groups(['book:read'])]
        public function getQuantityAvailable(): int
        {
            return max(0, ($this->quantity ?? 0) - ($this->quantityReserved ?? 0));
        }

        public function reserve(int $quantity): void
        {
            if ($this->quantity === null) {
                throw new \LogicException('Le stock n\'est pas défini.');
            }

            if ($this->getQuantityAvailable() < $quantity) {
                throw new \LogicException('Stock insuffisant.');
            }

            $this->quantityReserved += $quantity;
        }

        public function confirmReservation(int $quantity): void
        {     
            if ($this->quantityReserved < $quantity) {
                throw new \LogicException('Réservation insuffisante.');
            }

            $this->quantityReserved -= $quantity;
            $this->quantity -= $quantity;
            $this->quantitySold += $quantity;
        }
        
        public function cancelReservation(int $quantity): void
        {       

            $this->quantityReserved -= $quantity;
        }

   
  


}