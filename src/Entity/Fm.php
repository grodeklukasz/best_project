<?php

namespace App\Entity;

use App\Repository\FmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FmRepository::class)
 */
class Fm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nachname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vorname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zw;

    /**
     * @ORM\OneToMany(targetEntity=Tn::class, mappedBy="Fm")
     */
    private $tns;

    public function __construct()
    {
        $this->tns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNachname(): ?string
    {
        return $this->nachname;
    }

    public function setNachname(string $nachname): self
    {
        $this->nachname = $nachname;

        return $this;
    }

    public function getVorname(): ?string
    {
        return $this->vorname;
    }

    public function setVorname(?string $vorname): self
    {
        $this->vorname = $vorname;

        return $this;
    }

    public function getZw(): ?string
    {
        return $this->zw;
    }

    public function setZw(?string $zw): self
    {
        $this->zw = $zw;

        return $this;
    }

    /**
     * @return Collection<int, Tn>
     */
    public function getTns(): Collection
    {
        return $this->tns;
    }

    public function addTn(Tn $tn): self
    {
        if (!$this->tns->contains($tn)) {
            $this->tns[] = $tn;
            $tn->setFm($this);
        }

        return $this;
    }

    public function removeTn(Tn $tn): self
    {
        if ($this->tns->removeElement($tn)) {
            // set the owning side to null (unless already changed)
            if ($tn->getFm() === $this) {
                $tn->setFm(null);
            }
        }

        return $this;
    }
}
