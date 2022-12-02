<?php

namespace App\Entity;

use App\Repository\JobcoachRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JobcoachRepository::class)
 */
class Jobcoach
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
     * @ORM\Column(type="string", length=255)
     */
    private $vorname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telefonnummer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Tn::class, mappedBy="jobcoach")
     */
    private $tns;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $kennwort;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $role;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->tns = new ArrayCollection();
    }

    public function __toString(): String 
    {
        return $this->nachname . " " . $this->vorname;
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

    public function setVorname(string $vorname): self
    {
        $this->vorname = $vorname;

        return $this;
    }

    public function getTelefonnummer(): ?string
    {
        return $this->telefonnummer;
    }

    public function setTelefonnummer(?string $telefonnummer): self
    {
        $this->telefonnummer = $telefonnummer;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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
            $tn->setJobcoach($this);
        }

        return $this;
    }

    public function removeTn(Tn $tn): self
    {
        if ($this->tns->removeElement($tn)) {
            // set the owning side to null (unless already changed)
            if ($tn->getJobcoach() === $this) {
                $tn->setJobcoach(null);
            }
        }

        return $this;
    }

    public function getKennwort(): ?string
    {
        return $this->kennwort;
    }

    public function setKennwort(string $kennwort): self
    {
        $this->kennwort = $kennwort;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
