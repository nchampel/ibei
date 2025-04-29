<?php

namespace App\Entity;

use App\Repository\ForestResourceInfosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForestResourceInfosRepository::class)]
class ForestResourceInfos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $gain = null;

    #[ORM\Column(length: 50)]
    private ?string $gainType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $cooldown = null;

    #[ORM\Column]
    private ?bool $isDisplayed = null;

    #[ORM\OneToMany(mappedBy: 'forestResource', targetEntity: ForestResource::class, orphanRemoval: true)]
    private Collection $forestResources;

    #[ORM\Column]
    private ?int $factor = null;

    #[ORM\Column(length: 100)]
    private ?string $imageUrl = null;

    public function __construct()
    {
        $this->forestResources = new ArrayCollection();
    }

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

    public function getGain(): ?int
    {
        return $this->gain;
    }

    public function setGain(int $gain): static
    {
        $this->gain = $gain;

        return $this;
    }

    public function getGainType(): ?string
    {
        return $this->gainType;
    }

    public function setGainType(string $gainType): static
    {
        $this->gainType = $gainType;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function isIsDisplayed(): ?bool
    {
        return $this->isDisplayed;
    }

    public function setIsDisplayed(bool $isDisplayed): static
    {
        $this->isDisplayed = $isDisplayed;

        return $this;
    }

    /**
     * @return Collection<int, ForestResource>
     */
    public function getForestResources(): Collection
    {
        return $this->forestResources;
    }

    public function addForestResource(ForestResource $forestResource): static
    {
        if (!$this->forestResources->contains($forestResource)) {
            $this->forestResources->add($forestResource);
            $forestResource->setForestResource($this);
        }

        return $this;
    }

    public function removeForestResource(ForestResource $forestResource): static
    {
        if ($this->forestResources->removeElement($forestResource)) {
            // set the owning side to null (unless already changed)
            if ($forestResource->getForestResource() === $this) {
                $forestResource->setForestResource(null);
            }
        }

        return $this;
    }

    public function getFactor(): ?int
    {
        return $this->factor;
    }

    public function setFactor(int $factor): static
    {
        $this->factor = $factor;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
