<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Categories\Domain\Entity\Category;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\User;
use App\Enum\BookFormat;



#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['book:read', 'category:read']],
    denormalizationContext: ['groups' => ['book:write', 'category:write']]    
)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['book:read', 'category:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Groups(['book:read', 'category:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 150, unique: true)]
    #[Groups(['book:read'])]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?int $stock = null;

    #[ORM\Column(enumType: BookFormat::class)]
    private ?BookFormat $format = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $isbn = null;

    #[ORM\Column(nullable: true)]
    private ?int $pageCount = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(nullable: true)]
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

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    // ---------- GETTERS & SETTERS ----------

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(string $slug): static { $this->slug = $slug; return $this; }

    public function getPrice(): ?float { return $this->price; }
    public function setPrice(?float $price): static { $this->price = $price; return $this; }

    public function getStock(): ?int { return $this->stock; }
    public function setStock(?int $stock): static { $this->stock = $stock; return $this; }

    public function getFormat(): ?BookFormat
    {
        return $this->format;
    }

    public function setFormat(BookFormat $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

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


    public function getAuthor(): ?User { return $this->author; } // retourne l'auteur de ce livre (ManyToOne)
    public function setAuthor(?User $author): static { $this->author = $author; return $this;   } // définit l'auteur de ce livre (ManyToOne)
}