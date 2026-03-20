<?php

namespace App\Categories\Domain\Entity;

use App\Categories\Infrastructure\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Book;
use Doctrine\ORM\EntityManager;
;


#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['category:read', 'book:read']],
    denormalizationContext: ['groups' => ['category:write', 'book:write']]    
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:read', 'book:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['category:read', 'book:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['category:read', 'book:read'])]
    private ?string $slug = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')] // chaque catégorie peut avoir une seule catégorie parente, inversedBy: 'children' indique que la relation inverse est la propriété $children de la même classe
    #[Groups(['category:read'])]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['persist'])]// une catégorie peut avoir plusieurs catégories enfants. MappedBy: 'parent' indique que la relation est définie par la propriété $parent de la même classe. cascade: ['persist'] permet de persister automatiquement les sous-catégories lorsque la catégorie parente est persistée
    #[Groups(['category:read'])]
    private Collection $children;

    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'categories')] // une catégorie peut être associée à plusieurs livres, et un livre peut être associé à plusieurs catégories. mappedby: 'categories' indique que la relation est définie par la propriété $categories de la classe Book
    #[Groups(['category:read'])]
    private Collection $books;

    public function __construct()
    {
        $this->children = new ArrayCollection();    //children = sous-catégories
        $this->books = new ArrayCollection();   //books = livres associés à cette catégorie (ManyToMany)
    }

    // ---------- GETTERS & SETTERS ----------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getParent(): ?self // retourne la catégorie parente de cette catégorie, ou null si c'est une catégorie racine
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static // définit la catégorie parente de cette catégorie, ou null si c'est une catégorie racine
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection<int, self> // retourne les sous-catégories de cette catégorie
     */
    public function getChildren(): Collection // retourne les sous-catégories de cette catégorie
    {
        return $this->children;
    }

    public function addChild(self $child): static // ajoute une sous-catégorie à cette catégorie, et définit cette catégorie comme parent de la sous-catégorie pour garder la cohérence de la relation bidirectionnelle
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
        return $this;
    }

    public function removeChild(self $child): static // supprime une sous-catégorie de cette catégorie, et si la sous-catégorie avait cette catégorie comme parent, on met à null pour garder la cohérence de la relation bidirectionnelle
    {
        if ($this->children->removeElement($child)) {
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Book> // retourne les livres associés à cette catégorie (ManyToMany)
     */
    public function getBooks(): Collection // retourne les livres associés à cette catégorie (ManyToMany)
    {
        return $this->books;
    }

    public function addBook(Book $book): static // ajoute un livre à cette catégorie, et ajoute cette catégorie au livre pour garder la cohérence de la relation bidirectionnelle
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->addCategory($this);
        }
        return $this;
    }

    public function removeBook(Book $book): static // supprime un livre de cette catégorie, et supprime cette catégorie du livre pour garder la cohérence de la relation bidirectionnelle
    {
        if ($this->books->removeElement($book)) {
            $book->removeCategory($this);
        }
        return $this;
    }
}