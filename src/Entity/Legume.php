<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=App\Repository\LegumeRepository::class)
 */
class Legume
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("legume_read")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups("legume_read")
     * @Assert\NotBlank
     */
    private $date_semis;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("legume_read")
     * @Assert\NotBlank
     */
    private $variete;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("legume_read")
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("legume_read")
     * @Assert\NotBlank
     */
    private $family;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="legume")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups("legume_read")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("legume_read")
     * @Assert\NotBlank
     */
    private $modeSemis;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("legume_read")
     */
    private $comments;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups("legume_read")
     */
    private $dateArrosage;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getModeSemis(): ?string
    {
        return $this->modeSemis;
    }

    public function setModeSemis(string $modeSemis): self
    {
        $this->modeSemis = $modeSemis;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getDateArrosage(): ?\DateTimeInterface
    {
        return $this->dateArrosage;
    }

    public function setDateArrosage(?\DateTimeInterface $dateArrosage): self
    {
        $this->dateArrosage = $dateArrosage;

        return $this;
    }
}
