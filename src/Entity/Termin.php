<?php

namespace App\Entity;

use App\Repository\TerminRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TerminRepository::class)
 */
class Termin
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $termindatum;

    /**
     * @ORM\ManyToOne(targetEntity=Tn::class, inversedBy="termins")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tn;

    /**
     * @ORM\ManyToOne(targetEntity=TerminType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $termintype;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bemerkung;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTermindatum(): ?\DateTimeInterface
    {
        return $this->termindatum;
    }

    public function setTermindatum(\DateTimeInterface $termindatum): self
    {
        $this->termindatum = $termindatum;

        return $this;
    }

    public function getTn(): ?Tn
    {
        return $this->tn;
    }

    public function setTn(?Tn $tn): self
    {
        $this->tn = $tn;

        return $this;
    }

    public function getTermintype(): ?TerminType
    {
        return $this->termintype;
    }

    public function setTermintype(?TerminType $termintype): self
    {
        $this->termintype = $termintype;

        return $this;
    }

    public function getBemerkung(): ?string
    {
        return $this->bemerkung;
    }

    public function setBemerkung(?string $bemerkung): self
    {
        $this->bemerkung = $bemerkung;

        return $this;
    }
}
