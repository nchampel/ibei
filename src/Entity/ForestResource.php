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

    private bool $isClaimable = true;
    private int $remainedSecondsPop;
    private int $remainedSecondsGain;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $nextAvailableAt = null;

    // pour le gain de la ressource
    public function updateRemainedSecondsGain(){
        $claimedAtBDD = $this->getClaimedAt();

        if ($claimedAtBDD) {
            // Convertir 'claimedAt' en UTC si ce n'est pas déjà le cas
            $claimedAtBDD->setTimezone(new \DateTimeZone('UTC'));
            $claimedAt = $claimedAtBDD->getTimestamp();

            // Obtenir la date actuelle avec le fuseau horaire 'Europe/Paris'
            $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
            $nowSecondes = $now->getTimestamp();
            $difference = $this->getForestResource()->getHarvestTime() - ($nowSecondes - $claimedAt);
            if ($difference > 0) {

                $this->setRemainedSecondsGain($difference);
            } else {
                $this->setClaimedAt(null);

                // sauvegarder
                return "ressource récoltée";
            }
        } else {
            $this->setRemainedSecondsGain(0);
            return "";
        }
    }

    // pour le repop de la ressource
    public function updateRemainedSecondsPop()
    {
        $claimedAtBDD = $this->getClaimedAt();

        if ($claimedAtBDD) {
            // Convertir 'claimedAt' en UTC si ce n'est pas déjà le cas
            $claimedAtBDD->setTimezone(new \DateTimeZone('UTC'));
            $claimedAt = $claimedAtBDD->getTimestamp();

            // Obtenir la date actuelle avec le fuseau horaire 'Europe/Paris'
            $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
            $nowSecondes = $now->getTimestamp();
            $difference = $this->getForestResource()->getCooldown() - ($nowSecondes - $claimedAt);
            if ($difference > 0) {

                $this->setRemainedSecondsPop($difference);
            } else {
                $this->setClaimedAt(null);
                // sauvegarder
            }
        } else {
            $this->setRemainedSecondsPop(0);
        }
    }

    public function updateIsClaimable(): bool
    {
        // Récupérer la date 'claimedAt' en UTC depuis la base de données
        $claimedAtBDD = $this->getClaimedAt();
        
        $nextAvailableAtBDD = $this->getNextAvailableAt();

        if ($claimedAtBDD) {
            // Convertir 'claimedAt' en UTC si ce n'est pas déjà le cas
            $claimedAtBDD->setTimezone(new \DateTimeZone('UTC'));
            $claimedAt = $claimedAtBDD->getTimestamp();
            
            $nextAvailableAtBDD->setTimezone(new \DateTimeZone('UTC'));
            $nextAvailableAt = $nextAvailableAtBDD->getTimestamp();

            // Obtenir la date actuelle avec le fuseau horaire 'Europe/Paris'
            $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
            $nowSecondes = $now->getTimestamp();

            // Comparer la différence entre les timestamps (en secondes)
            $this->setIsClaimable($nowSecondes - $nextAvailableAt >= 0);
            return $nowSecondes - $nextAvailableAt >= 0;
        } else {
            // Si pas de 'claimedAt', c'est claimable
            $this->setIsClaimable(true);
            return true;
        }
    }

    /**
     * Get the value of isClaimable
     *
     * @return bool
     */
    public function getIsClaimable(): bool
    {
        return $this->isClaimable;
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

    /**
     * Set the value of isClaimable
     *
     * @param bool $isClaimable
     * @return self
     */
    public function setIsClaimable(bool $isClaimable): self
    {
        $this->isClaimable = $isClaimable;

        return $this;
    }

    /**
     * Get the value of remainedSecondsPop
     */
    public function getRemainedSecondsPop()
    {
        return $this->remainedSecondsPop;
    }

    /**
     * Set the value of remainedSecondsPop
     *
     * @return  self
     */
    public function setRemainedSecondsPop($remainedSecondsPop)
    {
        $this->remainedSecondsPop = $remainedSecondsPop;

        return $this;
    }
    /**
     * Get the value of remainedSecondsGain
     */
    public function getRemainedSecondsGain()
    {
        return $this->remainedSecondsGain;
    }

    /**
     * Set the value of remainedSecondsGain
     *
     * @return  self
     */
    public function setRemainedSecondsGain($remainedSecondsGain)
    {
        $this->remainedSecondsGain = $remainedSecondsGain;

        return $this;
    }

    public function getNextAvailableAt(): ?\DateTimeInterface
    {
        return $this->nextAvailableAt;
    }

    public function setNextAvailableAt(?\DateTimeInterface $nextAvailableAt): static
    {
        $this->nextAvailableAt = $nextAvailableAt;

        return $this;
    }
}
