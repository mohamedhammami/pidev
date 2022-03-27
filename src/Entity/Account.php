<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 */
class Account
{
    public const CEPARGNE = 2;
    public const CCOURANT = 1;
    public const COMPTE = [
        self::CEPARGNE => "Compte épargne",
        self::CCOURANT => "Compte courant",
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $Balance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rib;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $openingDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isClosed;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="accounts")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="compte")
     */
    private $motif;

    public function __construct()
    {
        $this->motif = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): ?float
    {
        return $this->Balance;
    }

    public function setBalance(?float $Balance): self
    {
        $this->Balance = $Balance;

        return $this;
    }

    public function getRib(): ?string
    {
        return $this->rib;
    }

    public function setRib(?string $rib): self
    {
        $this->rib = $rib;

        return $this;
    }

    public function getOpeningDate(): ?\DateTimeInterface
    {
        return $this->openingDate;
    }

    public function setOpeningDate(?\DateTimeInterface $openingDate): self
    {
        $this->openingDate = $openingDate;

        return $this;
    }

    public function getIsClosed(): ?bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(?bool $isClosed): self
    {
        $this->isClosed = $isClosed;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        if (!array_key_exists($type, self::COMPTE) && $type !== null) {
            throw new \Exception('type' .$type. 'n\'est pas authorisé!');
        }
        $this->type = $type;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getMotif(): Collection
    {
        return $this->motif;
    }

    public function addMotif(Transaction $motif): self
    {
        if (!$this->motif->contains($motif)) {
            $this->motif[] = $motif;
            $motif->setCompte($this);
        }

        return $this;
    }

    public function removeMotif(Transaction $motif): self
    {
        if ($this->motif->removeElement($motif)) {
            // set the owning side to null (unless already changed)
            if ($motif->getCompte() === $this) {
                $motif->setCompte(null);
            }
        }

        return $this;
    }
}
