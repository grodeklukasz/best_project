<?php

namespace App\Entity;

use App\Repository\TnRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TnRepository::class)
 */
class Tn
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
     * @ORM\Column(type="string", length=255)
     */
    private $telefonnummer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="date")
     */
    private $gebdatum;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pseudonym;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $starttermin;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $ausgeschieden;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $grund_ausgeschieden;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bemerkung;

    /**
     * @ORM\ManyToOne(targetEntity=Jobcoach::class, inversedBy="tns")
     * @ORM\JoinColumn(nullable=false)
     */
    private $jobcoach;

    /**
     * @ORM\ManyToOne(targetEntity=Fm::class, inversedBy="tns")
     */
    private $fm;


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

    public function setTelefonnummer(string $telefonnummer): self
    {
        $this->telefonnummer = $telefonnummer;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getGebdatum(): ?\DateTimeInterface
    {
        return $this->gebdatum;
    }

    public function setGebdatum(\DateTimeInterface $gebdatum): self
    {
        $this->gebdatum = $gebdatum;

        return $this;
    }

    public function getPseudonym(): ?string
    {
        return $this->pseudonym;
    }

    public function setPseudonym(?string $pseudonym): self
    {
        $this->pseudonym = $pseudonym;

        return $this;
    }

    public function getStarttermin(): ?\DateTimeInterface
    {
        return $this->starttermin;
    }

    public function getStartterminAsDiff(?\DateTime $targetObject): ?\DateInterval
    {
        return $this->starttermin->diff($targetObject);
    }

    public function setStarttermin(?\DateTimeInterface $starttermin): self
    {
        $this->starttermin = $starttermin;

        return $this;
    }

    public function getAusgeschieden(): ?\DateTimeInterface
    {
        return $this->ausgeschieden;
    }

    public function setAusgeschieden(?\DateTimeInterface $ausgeschieden): self
    {
        $this->ausgeschieden = $ausgeschieden;

        return $this;
    }

    public function getGrundAusgeschieden(): ?string
    {
        return $this->grund_ausgeschieden;
    }

    public function setGrundAusgeschieden(?string $grund_ausgeschieden): self
    {
        $this->grund_ausgeschieden = $grund_ausgeschieden;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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

    public function getJobcoach(): ?Jobcoach
    {
        return $this->jobcoach;
    }

    public function setJobcoach(?Jobcoach $jobcoach): self
    {
        $this->jobcoach = $jobcoach;

        return $this;
    }

    public function getFm(): ?Fm
    {
        return $this->fm;
    }

    public function setFm(?Fm $fm): self
    {
        $this->fm = $fm;

        return $this;
    }

 

}
