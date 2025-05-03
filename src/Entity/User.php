<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cet email')]
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

    #[ORM\Column(length: 255)]
    private ?string $pseudo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Purchase::class, orphanRemoval: true)]
    private Collection $purchases;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Pot $pot = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $nature = null;

    #[ORM\Column(type: Types::BIGINT, options:["default" => 0])]
    private ?string $exp = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Nature::class, orphanRemoval: true)]
    private Collection $natureAnswers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Idle::class, orphanRemoval: true)]
    private Collection $idles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Log::class)]
    private Collection $logs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ressource::class, orphanRemoval: true)]
    private Collection $ressources;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Pot::class)]
    private Collection $pots;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ForestResource::class)]
    private Collection $forestResources;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?ForestField $forestField = null;

    public function __construct()
    {
        $this->purchases = new ArrayCollection();
        $this->natureAnswers = new ArrayCollection();
        $this->idles = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->ressources = new ArrayCollection();
        $this->pots = new ArrayCollection();
        $this->forestResources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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

    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): static
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setUser($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): static
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getUser() === $this) {
                $purchase->setUser(null);
            }
        }

        return $this;
    }

    public function getPot(): ?Pot
    {
        return $this->pot;
    }

    public function setPot(?Pot $pot): static
    {
        // unset the owning side of the relation if necessary
        if ($pot === null && $this->pot !== null) {
            $this->pot->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($pot !== null && $pot->getUser() !== $this) {
            $pot->setUser($this);
        }

        $this->pot = $pot;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(?string $nature): static
    {
        $this->nature = $nature;

        return $this;
    }

    public function getExp(): ?string
    {
        return $this->exp;
    }

    public function setExp(string $exp): static
    {
        $this->exp = $exp;

        return $this;
    }

    public function getRessourceByType(string $type)
{
    foreach ($this->ressources as $ressource) {
        if ($ressource->getType() === $type) {
            return $ressource;
        }
    }

}

    
    /**
     * @return Collection<int, Nature>
     */
    public function getNatureAnswers(): Collection
    {
        return $this->natureAnswers;
    }

    public function addNatureAnswer(Nature $natureAnswer): static
    {
        if (!$this->natureAnswers->contains($natureAnswer)) {
            $this->natureAnswers->add($natureAnswer);
            $natureAnswer->setUser($this);
        }

        return $this;
    }

    public function removeNatureAnswer(Nature $natureAnswer): static
    {
        if ($this->natureAnswers->removeElement($natureAnswer)) {
            // set the owning side to null (unless already changed)
            if ($natureAnswer->getUser() === $this) {
                $natureAnswer->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Idle>
     */
    public function getIdles(): Collection
    {
        return $this->idles;
    }

    public function addIdle(Idle $idle): static
    {
        if (!$this->idles->contains($idle)) {
            $this->idles->add($idle);
            $idle->setUser($this);
        }

        return $this;
    }

    public function removeIdle(Idle $idle): static
    {
        if ($this->idles->removeElement($idle)) {
            // set the owning side to null (unless already changed)
            if ($idle->getUser() === $this) {
                $idle->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Log>
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): static
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setUser($this);
        }

        return $this;
    }

    public function removeLog(Log $log): static
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getUser() === $this) {
                $log->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessource(Ressource $ressource): static
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources->add($ressource);
            $ressource->setUser($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): static
    {
        if ($this->ressources->removeElement($ressource)) {
            // set the owning side to null (unless already changed)
            if ($ressource->getUser() === $this) {
                $ressource->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pot>
     */
    public function getPots(): Collection
    {
        return $this->pots;
    }

    public function addPot(Pot $pot): static
    {
        if (!$this->pots->contains($pot)) {
            $this->pots->add($pot);
            $pot->setUser($this);
        }

        return $this;
    }

    public function removePot(Pot $pot): static
    {
        if ($this->pots->removeElement($pot)) {
            // set the owning side to null (unless already changed)
            if ($pot->getUser() === $this) {
                $pot->setUser(null);
            }
        }

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
            $forestResource->setUser($this);
        }

        return $this;
    }

    public function removeForestResource(ForestResource $forestResource): static
    {
        if ($this->forestResources->removeElement($forestResource)) {
            // set the owning side to null (unless already changed)
            if ($forestResource->getUser() === $this) {
                $forestResource->setUser(null);
            }
        }

        return $this;
    }

    public function getForestField(): ?ForestField
    {
        return $this->forestField;
    }

    public function setForestField(?ForestField $forestField): static
    {
        // unset the owning side of the relation if necessary
        if ($forestField === null && $this->forestField !== null) {
            $this->forestField->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($forestField !== null && $forestField->getUser() !== $this) {
            $forestField->setUser($this);
        }

        $this->forestField = $forestField;

        return $this;
    }

}
