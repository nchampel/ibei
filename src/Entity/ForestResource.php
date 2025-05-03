<?php

namespace App\Entity;

use App\Repository\ForestResourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForestResourceRepository::class)]
class ForestResource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'forestResources')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'forestResources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ForestResourceInfos $forestResource = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $claimedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?int $cooldown = null;

    #[ORM\Column]
    private ?int $gain = null;

    #[ORM\Column(nullable: true)]
    private ?int $x = null;

    #[ORM\Column(nullable: true)]
    private ?int $y = null;

    #[ORM\OneToMany(mappedBy: 'forestResource', targetEntity: ForestField::class)]
    private Collection $forestFields;

    public function __construct()
    {
        $this->forestFields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getForestResource(): ?ForestResourceInfos
    {
        return $this->forestResource;
    }

    public function setForestResource(?ForestResourceInfos $forestResource): static
    {
        $this->forestResource = $forestResource;

        return $this;
    }

    public function getClaimedAt(): ?\DateTimeInterface
    {
        return $this->claimedAt;
    }

    public function setClaimedAt(?\DateTimeInterface $claimedAt): static
    {
        $this->claimedAt = $claimedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCooldown(): ?int
    {
        return $this->cooldown;
    }

    public function setCooldown(int $cooldown): static
    {
        $this->cooldown = $cooldown;

        return $this;
    }

    public function getGain(): ?int
    {
        return $this->gain;
    }

    public function setGain(int $gain): static
    {
        $this->gain = $gain;

        return $this;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(?int $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(?int $y): static
    {
        $this->y = $y;

        return $this;
    }

    /**
     * @return Collection<int, ForestField>
     */
    public function getForestFields(): Collection
    {
        return $this->forestFields;
    }

    public function addForestField(ForestField $forestField): static
    {
        if (!$this->forestFields->contains($forestField)) {
            $this->forestFields->add($forestField);
            $forestField->setForestResource($this);
        }

        return $this;
    }

    public function removeForestField(ForestField $forestField): static
    {
        if ($this->forestFields->removeElement($forestField)) {
            // set the owning side to null (unless already changed)
            if ($forestField->getForestResource() === $this) {
                $forestField->setForestResource(null);
            }
        }

        return $this;
    }
}
