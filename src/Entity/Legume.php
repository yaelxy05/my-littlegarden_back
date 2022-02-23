<?php

namespace App\Entity;

use App\Repository\LegumeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LegumeRepository::class)
 */
class Legume
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
    private $date_semis;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $variete;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $family;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateSemis(): ?\DateTimeInterface
    {
        return $this->date_semis;
    }

    public function setDateSemis(\DateTimeInterface $date_semis): self
    {
        $this->date_semis = $date_semis;

        return $this;
    }

    public function getVariete(): ?string
    {
        return $this->variete;
    }

    public function setVariete(string $variete): self
    {
        $this->variete = $variete;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(string $family): self
    {
        $this->family = $family;

        return $this;
    }
}
