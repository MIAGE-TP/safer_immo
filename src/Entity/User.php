<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Carbon\Carbon;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Un compte avec la même adresse mail existe déjà.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Good::class)]
    private Collection $goods;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Fav::class, orphanRemoval: false)]
    private Collection $favs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: NewsLetter::class)]
    private Collection $newsLetters;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->createdAt = Carbon::now()->toDateTimeImmutable();
        $this->goods = new ArrayCollection();
        $this->favs = new ArrayCollection();
        $this->newsLetters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Good>
     */
    public function getGoods(): Collection
    {
        return $this->goods;
    }

    public function addGood(Good $good): self
    {
        if (!$this->goods->contains($good)) {
            $this->goods->add($good);
            $good->setUser($this);
        }

        return $this;
    }

    public function removeGood(Good $good): self
    {
        if ($this->goods->removeElement($good)) {
            // set the owning side to null (unless already changed)
            if ($good->getUser() === $this) {
                $good->setUser(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, Fav>
     */
    public function getFavs(): Collection
    {
        return $this->favs;
    }

    public function getFavNumber(): int
    {
        return count($this->getFavs());
    }

    /**
     * @return Array<int, Good>
     */
    public function getGoodsWithFavOnly(): array
    {
        $tab = [];
        foreach ($this->getFavs() as $fav) {
            array_push($tab, $fav->getGood());
        }
        return $tab;
    }

    /**
     * @return Array of good id with fav only
     */
    public function getFavGoodIds() : array
    {
       $tab = [];
       foreach ($this->getFavs() as $fav) {
            array_push($tab, $fav->getGood()->getId());
       }
       return $tab;
    }

    /**
     * @return int user fav id matching with a specific good
     */
    public function getFavId($goodId) : int
    {
        foreach ($this->getFavs() as $fav) {
           if ($fav->getGood()->getId() == $goodId) {
                return $fav->getId();
           }
        }
      
    }

    public function addFav(Fav $fav): self
    {
        if (!$this->favs->contains($fav)) {
            $this->favs->add($fav);
            $fav->setUser($this);
        }

        return $this;
    }

    public function removeFav(Fav $fav): self
    {
        if ($this->favs->removeElement($fav)) {
            // set the owning side to null (unless already changed)
            if ($fav->getUser() === $this) {
                $fav->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, NewsLetter>
     */
    public function getNewsLetters(): Collection
    {
        return $this->newsLetters;
    }

    public function addNewsLetter(NewsLetter $newsLetter): self
    {
        if (!$this->newsLetters->contains($newsLetter)) {
            $this->newsLetters->add($newsLetter);
            $newsLetter->setUser($this);
        }

        return $this;
    }

    public function removeNewsLetter(NewsLetter $newsLetter): self
    {
        if ($this->newsLetters->removeElement($newsLetter)) {
            // set the owning side to null (unless already changed)
            if ($newsLetter->getUser() === $this) {
                $newsLetter->setUser(null);
            }
        }

        return $this;
    }
}
