<?php

namespace App\Books\Domain\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Books\Infrastructure\DoctrineBookRepository;
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




#[ORM\Entity(repositoryClass: DoctrineBookRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            processor: BookProcessor::class,
            inputFormats: [
                'multipart' => ['multipart/form-data']
            ],
            deserialize: false //API Platform ne transforme pas la requête en entité (manuel avec le processor)
        )
    ],
    normalizationContext: ['groups' => ['book:read', 'category:read']],
    denormalizationContext: ['groups' => ['book:write', 'category:write']]    
)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['book:read', 'category:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?string $authorName = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?int $stock = null;

    #[ORM\Column(type: "string", length: 20, nullable: true)]    
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?string $format = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?string $extract = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?string $isbn = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?int $pageCount = null;

    #[ORM\Column(length: 3, nullable: true)]
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?string $currency = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read','book:write', 'category:read'])]
    private ?bool $isPublished = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]    
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2, nullable: true)]
    private ?string $averageRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $reviewCount = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'books', cascade: ['persist'])]
    private Collection $categories;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'books')] // un livre a un seul auteur, mais un auteur peut avoir plusieurs livres
    #[ORM\JoinColumn(nullable: false)] // la colonne author_id dans la table book ne peut pas être nulle, un livre doit toujours avoir un auteur
    private ?User $author = null; // author = auteur du livre (ManyToOne)

    #[ORM\Column(nullable: true)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?string $cover = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $images = [];

    #[Vich\UploadableField(mapping: 'book_cover', fileNameProperty: 'cover')]
    private ?File $coverFile = null;

   







    public function __construct()
    {
        $this->categories = new ArrayCollection();
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

    public function getStock(): ?int { return $this->stock; }
    public function setStock(?int $stock): static { $this->stock = $stock; return $this; }

    public function getFormat(): ?string  {  return $this->format;   }
    public function setFormat(?string $format): static  {  $this->format = $format;  return $this; }

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

    public function getCover(): ?string { return $this->cover; }
    public function setCover(?string $cover): static { $this->cover = $cover; return $this; } 

    public function getCoverFile(): ?File { return $this->coverFile; }
    public function setCoverFile(?File $coverFile ): static 
        { 
            $this->coverFile = $coverFile;

            if ($coverFile) {
                $this->updatedAt = new \DateTimeImmutable();
            }

            return $this;
        }

    public function getImages(): array { return $this->images ?? [];}
    public function setImages(?array $images): static { $this->images = $images ?? []; return $this;}


    /**
     * @return Collection<int, Category> // retourne les catégories associées à ce livre (ManyToMany)
     */
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


    public function getAuthor(): ?User { return $this->author; } // retourne l'auteur de ce livre (ManyToOne)
    public function setAuthor(?User $author): static { $this->author = $author; return $this;   } // définit l'auteur de ce livre (ManyToOne)
}