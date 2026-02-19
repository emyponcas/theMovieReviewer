<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ORM\Table(name: 'movie')]
#[ORM\HasLifecycleCallbacks]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'tmdb_id')]
    private ?int $tmdb_id = null;

    #[ORM\Column(name: 'title', length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(name: 'original_title', length: 255, nullable: true)]
    private ?string $original_title = null;

    #[ORM\Column(name: 'overview', type: Types::TEXT, nullable: true)]
    private ?string $overview = null;

    #[ORM\Column(name: 'release_date', type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $release_date = null;

    #[ORM\Column(name: 'poster_path', length: 255, nullable: true)]
    private ?string $poster_path = null;

    #[ORM\Column(name: 'backdrop_path', length: 255, nullable: true)]
    private ?string $backdrop_path = null;

    #[ORM\Column(name: 'popularity', type: Types::DECIMAL, precision: 10, scale: 4, nullable: true)]
    private ?string $popularity = null;

    #[ORM\Column(name: 'vote_average', type: Types::DECIMAL, precision: 3, scale: 1, nullable: true)]
    private ?string $vote_average = null;

    #[ORM\Column(name: 'vote_count', nullable: true)]
    private ?int $vote_count = null;

    #[ORM\Column(name: 'adult', nullable: true)]
    private ?bool $adult = null;

    #[ORM\Column(name: 'video', nullable: true)]
    private ?bool $video = null;

    #[ORM\Column(name: 'original_language', length: 255, nullable: true)]
    private ?string $original_language = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'movie')]
    private Collection $reviews;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'movies')]
    private Collection $categories;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTmdbId(): ?int
    {
        return $this->tmdb_id;
    }

    public function setTmdbId(int $tmdb_id): static
    {
        $this->tmdb_id = $tmdb_id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getOriginalTitle(): ?string
    {
        return $this->original_title;
    }

    public function setOriginalTitle(?string $original_title): static
    {
        $this->original_title = $original_title;
        return $this;
    }

    public function getOverview(): ?string
    {
        return $this->overview;
    }

    public function setOverview(?string $overview): static
    {
        $this->overview = $overview;
        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->release_date;
    }

    public function setReleaseDate(?\DateTime $release_date): static
    {
        $this->release_date = $release_date;
        return $this;
    }

    public function getPosterUrl(string $size = 'w500'): ?string
    {
        if (!$this->poster_path) {
            return null;
        }

        return sprintf('https://image.tmdb.org/t/p/%s%s', $size, $this->poster_path);
    }

    public function setPosterPath(?string $poster_path): static
    {
        $this->poster_path = $poster_path;
        return $this;
    }

    public function getBackdropUrl(string $size = 'w1280'): ?string
    {
        if (!$this->backdrop_path) {
            return null;
        }

        return sprintf('https://image.tmdb.org/t/p/%s%s', $size, $this->backdrop_path);
    }

    public function setBackdropPath(?string $backdrop_path): static
    {
        $this->backdrop_path = $backdrop_path;
        return $this;
    }

    public function getPopularity(): ?string
    {
        return $this->popularity;
    }

    public function setPopularity(?string $popularity): static
    {
        $this->popularity = $popularity;
        return $this;
    }

    public function getVoteAverage(): ?string
    {
        return $this->vote_average;
    }

    public function setVoteAverage(?string $vote_average): static
    {
        $this->vote_average = $vote_average;
        return $this;
    }

    public function getVoteCount(): ?int
    {
        return $this->vote_count;
    }

    public function setVoteCount(?int $vote_count): static
    {
        $this->vote_count = $vote_count;
        return $this;
    }

    public function isAdult(): ?bool
    {
        return $this->adult;
    }

    public function setAdult(?bool $adult): static
    {
        $this->adult = $adult;
        return $this;
    }

    public function isVideo(): ?bool
    {
        return $this->video;
    }

    public function setVideo(?bool $video): static
    {
        $this->video = $video;
        return $this;
    }

    public function getOriginalLanguage(): ?string
    {
        return $this->original_language;
    }

    public function setOriginalLanguage(?string $original_language): static
    {
        $this->original_language = $original_language;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setMovie($this);
        }
        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getMovie() === $this) {
                $review->setMovie(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addMovie($this);
        }
        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeMovie($this);
        }
        return $this;
    }
}
