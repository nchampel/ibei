<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: true)]
    private ?ProductInfos $product = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $claimedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?int $cooldown = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?int $gain = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $boughtAt = null;

    private bool $isClaimable = true;
    private int $remainedSeconds;

    public function updateRemainedSeconds(){
        $claimedAtBDD = $this->getClaimedAt();

    if ($claimedAtBDD) {
        // Convertir 'claimedAt' en UTC si ce n'est pas déjà le cas
        $claimedAtBDD->setTimezone(new \DateTimeZone('UTC'));
        $claimedAt = $claimedAtBDD->getTimestamp();

        // Obtenir la date actuelle avec le fuseau horaire 'Europe/Paris'
        $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $nowSecondes = $now->getTimestamp();

        $this->setRemainedSeconds($this->getCooldown() - ($nowSecondes - $claimedAt));
    } else {
        $this->setRemainedSeconds(0);
    }
    }

    public function updateIsClaimable(): void
{
    // Récupérer la date 'claimedAt' en UTC depuis la base de données
    $claimedAtBDD = $this->getClaimedAt();

    if ($claimedAtBDD) {
        // Convertir 'claimedAt' en UTC si ce n'est pas déjà le cas
        $claimedAtBDD->setTimezone(new \DateTimeZone('UTC'));
        $claimedAt = $claimedAtBDD->getTimestamp();

        // Obtenir la date actuelle avec le fuseau horaire 'Europe/Paris'
        $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $nowSecondes = $now->getTimestamp();

        // Comparer la différence entre les timestamps (en secondes)
        $this->setIsClaimable($nowSecondes - $claimedAt >= $this->getCooldown());
    } else {
        // Si pas de 'claimedAt', c'est claimable
        $this->setIsClaimable(true);
    }
}

//     public function getIsClaimable(): bool
// {
//     $claimedAtBDD = $this->getClaimedAt();
//     if ($claimedAtBDD) {
//         $claimedAt = $claimedAtBDD->getTimestamp();

//         $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
//         $nowSecondes = $now->getTimestamp();
        
//         // Comparaison de la différence entre le timestamp actuel et celui de 'claimedAt'
//         return ($nowSecondes - $claimedAt >= $this->getCooldown());
//     }
    
//     return true; // Si pas de claimedAt, c'est claimable
// }
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getProduct(): ?ProductInfos
    {
        return $this->product;
    }

    public function setProduct(?ProductInfos $product): static
    {
        $this->product = $product;

        return $this;
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

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

    public function getBoughtAt(): ?\DateTimeImmutable
    {
        return $this->boughtAt;
    }

    public function setBoughtAt(\DateTimeImmutable $boughtAt): static
    {
        $this->boughtAt = $boughtAt;

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
     * Get the value of remainedSeconds
     */ 
    public function getRemainedSeconds()
    {
        return $this->remainedSeconds;
    }

    /**
     * Set the value of remainedSeconds
     *
     * @return  self
     */ 
    public function setRemainedSeconds($remainedSeconds)
    {
        $this->remainedSeconds = $remainedSeconds;

        return $this;
    }
}
