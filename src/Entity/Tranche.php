<?php

namespace App\Entity;

use App\Repository\TrancheRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrancheRepository::class)
 */
class Tranche
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity=Loan::class, inversedBy="tranches")
     */
    private $credit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVAlid;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCredit(): ?Loan
    {
        return $this->credit;
    }

    public function setCredit(?Loan $credit): self
    {
        $this->credit = $credit;

        return $this;
    }

    public function getIsVAlid(): ?bool
    {
        return $this->isVAlid;
    }

    public function setIsVAlid(?bool $isVAlid): self
    {
        $this->isVAlid = $isVAlid;

        return $this;
    }
}
