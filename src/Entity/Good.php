<?php

namespace App\Entity;

use App\Repository\GoodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Carbon\Carbon;
use App\EntityListener\GoodListener;

#[ORM\Entity(repositoryClass: GoodRepository::class)]
#[ORM\EntityListeners([GoodListener::class])]
class Good
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $intitule = null;

    #[ORM\Column(length: 510, nullable: true)]
    private ?string $descriptif = null;

    #[ORM\Column(length: 510, nullable: true)]
    private ?string $localisation = null;

    #[ORM\Column(length: 255)]
    private ?int $surface = null;

    #[ORM\Column(length: 255)]
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'goods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OfferType $offertype = null;

    #[ORM\ManyToOne(inversedBy: 'goods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GoodCategory $goodcategory = null;

    #[ORM\ManyToOne(inversedBy: 'goods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'goods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    #[ORM\OneToMany(mappedBy: 'good', targetEntity: OfferGalery::class, orphanRemoval: true)]
    private Collection $offerGaleries;

    #[ORM\Column(length: 1020)]
    private ?string $slug;

    #[ORM\Column]
    private ?bool $hidden;

    #[ORM\Column(length: 255)]
    private ?string $unit = null;

    #[ORM\Column(length: 1020, nullable: true)]
    private ?string $street = null;

    #[ORM\OneToMany(mappedBy: 'good', targetEntity: Fav::class)]
    private Collection $favs;

    public function __construct()
    {
        $this->createdAt = Carbon::now()->toDateTimeImmutable();
        $this->offerGaleries = new ArrayCollection();
        $this->hidden = false;
        $this->favs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(?string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): self
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getSurface(): ?string
    {
        return $this->surface;
    }

    public function setSurface(string $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOffertype(): ?OfferType
    {
        return $this->offertype;
    }

    public function setOffertype(?OfferType $offertype): self
    {
        $this->offertype = $offertype;

        return $this;
    }

    public function getGoodcategory(): ?GoodCategory
    {
        return $this->goodcategory;
    }

    public function setGoodcategory(?GoodCategory $goodcategory): self
    {
        $this->goodcategory = $goodcategory;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection<int, OfferGalery>
     */
    public function getOfferGaleries(): Collection
    {
        return $this->offerGaleries;
    }

    public function addOfferGalery(OfferGalery $offerGalery): self
    {
        if (!$this->offerGaleries->contains($offerGalery)) {
            $this->offerGaleries->add($offerGalery);
            $offerGalery->setGood($this);
        }

        return $this;
    }

    public function removeOfferGalery(OfferGalery $offerGalery): self
    {
        if ($this->offerGaleries->removeElement($offerGalery)) {
            // set the owning side to null (unless already changed)
            if ($offerGalery->getGood() === $this) {
                $offerGalery->setGood(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return Collection<int, Fav>
     */
    public function getFavs(): Collection
    {
        return $this->favs;
    }

    /**
     * @return int number of fav for a given good
     */
    public function getFavNumber(): int
    {
        return count($this->getFavs());
    }

    public function addFav(Fav $fav): self
    {
        if (!$this->favs->contains($fav)) {
            $this->favs->add($fav);
            $fav->setGood($this);
        }

        return $this;
    }

    public function removeFav(Fav $fav): self
    {
        if ($this->favs->removeElement($fav)) {
            // set the owning side to null (unless already changed)
            if ($fav->getGood() === $this) {
                $fav->setGood(null);
            }
        }

        return $this;
    }
}
