<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)*
 * @UniqueEntity(fields={"email"}, message="Il y a déjà un compte avec cette email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     *
       * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("user_read")
     * @Groups("legume_read")
     */
    private $id;

    /**
    * @ORM\Column(type="string", length=49, unique=true)
    * @Groups("user_read")
    * @Assert\Email(
    *     message="Veuillez saisir un email valide."
    * )
    */
    private $email;

    /**
     * @ORM\Column(type="json")
     *
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=Legume::class, mappedBy="user")
     * @Groups("user_read")
     */
    private $legume;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updated_at;

    /**
     * @Assert\Regex(
     *     pattern     = "/^[A-Za-zÀ-úœ'\-\s]+$/i",
     *     htmlPattern = "[A-Za-zÀ-úœ'\-\s]+",
     *     message="Veuillez saisir un prénom valide."
     * )
     * @Assert\Length(
     *      min = 2,
     *      max = 25,
     *      minMessage = "Le nom doit avoir au minimum {{ limit }} caractères",
     *      maxMessage = "Le nom ne peut avoir plus de {{ limit }} caractères"
     * )
     * @ORM\Column(type="string", length=50)
     * @Groups("user_read")
     */
    private $lastname;

    /**
     * @Assert\Regex(
     *     pattern     = "/^[A-Za-zÀ-úœ'\-\s]+$/i",
     *     htmlPattern = "[A-Za-zÀ-úœ'\-\s]+",
     *     message="Veuillez saisir un prénom valide."
     * )
     * @Assert\Length(
     *      min = 2,
     *      max = 25,
     *      minMessage = "Le prénom doit avoir au minimum {{ limit }} caractères",
     *      maxMessage = "Le prénom ne peut avoir plus de {{ limit }} caractères"
     * )
     * @ORM\Column(type="string", length=50)
     * @Groups("user_read")
     */
    private $firstname;

    /**
     * @ORM\OneToMany(targetEntity=Potager::class, mappedBy="user")
     */
    private $potagers;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups("user_read")
     */
    private $avatar;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $imageSize;


    public function __construct()
    {
        $this->legume = new ArrayCollection();
        $this->potagers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    /**
     * @return Collection<int, Legume>
     */
    public function getLegume(): Collection
    {
        return $this->legume;
    }

    public function addLegume(Legume $legume): self
    {
        if (!$this->legume->contains($legume)) {
            $this->legume[] = $legume;
            $legume->setUser($this);
        }

        return $this;
    }

    public function removeLegume(Legume $legume): self
    {
        if ($this->legume->removeElement($legume)) {
            // set the owning side to null (unless already changed)
            if ($legume->getUser() === $this) {
                $legume->setUser(null);
            }
        }

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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return Collection<int, Potager>
     */
    public function getPotagers(): Collection
    {
        return $this->potagers;
    }

    public function addPotager(Potager $potager): self
    {
        if (!$this->potagers->contains($potager)) {
            $this->potagers[] = $potager;
            $potager->setUser($this);
        }

        return $this;
    }

    public function removePotager(Potager $potager): self
    {
        if ($this->potagers->removeElement($potager)) {
            // set the owning side to null (unless already changed)
            if ($potager->getUser() === $this) {
                $potager->setUser(null);
            }
        }

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): self
    {
        $this->imageSize = $imageSize;

        return $this;
    }
}
