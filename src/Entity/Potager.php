<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PotagerRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PotagerRepository::class)
 */
class Potager
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("potager_read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("potager_read")
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 49,
     *      maxMessage = "Le nom ne peut avoir plus de {{ limit }} caractères"
     * )
     * @Assert\Regex(
     *     pattern     = "[\D][a-zA-Z]",
     *     message="Veuillez saisir un nom valide, pas de chiffre."
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     * @Groups("potager_read")
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 49,
     *      maxMessage = "Le nom ne peut avoir plus de {{ limit }} caractères"
     * )
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="potagers")
     * @ORM\JoinColumn(nullable=false)
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
     * @ORM\OneToMany(targetEntity=Plant::class, mappedBy="potager")
     * @Groups("potager_read")
     */
    private $plants;

    public function __construct()
    {
        $this->plants = new ArrayCollection();
    }

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

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

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

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Plant>
     */
    public function getPlants(): Collection
    {
        return $this->plants;
    }

    public function addPlant(Plant $plant): self
    {
        if (!$this->plants->contains($plant)) {
            $this->plants[] = $plant;
            $plant->setPotager($this);
        }

        return $this;
    }

    public function removePlant(Plant $plant): self
    {
        if ($this->plants->removeElement($plant)) {
            // set the owning side to null (unless already changed)
            if ($plant->getPotager() === $this) {
                $plant->setPotager(null);
            }
        }

        return $this;
    }
}
