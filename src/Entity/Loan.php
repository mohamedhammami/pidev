<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoanRepository::class)
 */
class Loan
{
    public const STATUS_EN_COURS = 1;
    public const STATUS_ACCEPT   = 2;
    public const STATUS_REFUED   = 3;

    public const STATUS = [
        self::STATUS_EN_COURS => "En cours",
        self::STATUS_ACCEPT   => "Accepté",
        self::STATUS_REFUED   => "Refusé"
    ];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="loans")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Tranche::class, mappedBy="credit")
     */
    private $tranches;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateOfSend;

    public function __construct()
    {
        $this->tranches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string  $status): void
    {
        if (!array_key_exists($status, self::STATUS) && $status !== null) {
            throw new \Exception('status' .$status. 'n\'est pas authorisé!');
        }

        $this->status = $status;
    }



    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

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

    /**
     * @return Collection<int, Tranche>
     */
    public function getTranches(): Collection
    {
        return $this->tranches;
    }

    public function addTranch(Tranche $tranch): self
    {
        if (!$this->tranches->contains($tranch)) {
            $this->tranches[] = $tranch;
            $tranch->setCredit($this);
        }

        return $this;
    }

    public function removeTranch(Tranche $tranch): self
    {
        if ($this->tranches->removeElement($tranch)) {
            // set the owning side to null (unless already changed)
            if ($tranch->getCredit() === $this) {
                $tranch->setCredit(null);
            }
        }

        return $this;
    }

    public function getDateOfSend(): ?\DateTimeInterface
    {
        return $this->dateOfSend;
    }

    public function setDateOfSend(?\DateTimeInterface $dateOfSend): self
    {
        $this->dateOfSend = $dateOfSend;

        return $this;
    }
}
