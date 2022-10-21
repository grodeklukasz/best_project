<?php

namespace App\Entity;

use App\Repository\TerminTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TerminTypeRepository::class)
 */
class TerminType
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
    private $terminName;

    public function __toString()
    {
        return $this->terminName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTerminName(): ?string
    {
        return $this->terminName;
    }

    public function setTerminName(string $terminName): self
    {
        $this->terminName = $terminName;

        return $this;
    }
}
