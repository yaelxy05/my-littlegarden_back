<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PlantRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlantRepository::class)
 */
class Plant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("plant_read")
     * @Groups("potager_read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("plant_read")
     * @Groups("potager_read")
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 49,
     *      maxMessage = "Le nom ne peut avoir plus de {{ limit }} caractères"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("plant_read")
     * @Groups("potager_read")
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 49,
     *      maxMessage = "Le nom ne peut avoir plus de {{ limit }} caractères"
     * )
     */
    private $family;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("plant_read")
     * @Groups("potager_read")
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 49,
     *      maxMessage = "Le nom ne peut avoir plus de {{ limit }} caractères"
     * )
     */
    private $variete;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=Potager::class, inversedBy="plants")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $potager;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getVariete(): ?string
    {
        return $this->variete;
    }

    public function setVariete(string $variete): self
    {
        $this->variete = $variete;

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

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPotager(): ?Potager
    {
        return $this->potager;
    }

    public function setPotager(?Potager $potager): self
    {
        $this->potager = $potager;

        return $this;
    }
}
