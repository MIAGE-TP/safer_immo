<?php

namespace App\Entity;

use App\Repository\OfferGaleryRepository;
use Doctrine\ORM\Mapping as ORM;
use Carbon\Carbon;

#[ORM\Entity(repositoryClass: OfferGaleryRepository::class)]
class OfferGalery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'offerGaleries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Good $offer = null;

    #[ORM\Column(length: 1275)]
    private ?string $name = null;

    #[ORM\Column(length: 2550)]
    private ?string $path = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct()
    {
        $this->createdAt = Carbon::now()->toDateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffer(): ?Good
    {
        return $this->offer;
    }

    public function setOffer(?Good $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

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

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
