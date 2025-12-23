<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]


class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private ?string $email = null;

    // Nullable at DB level to allow schema updates on existing SQLite databases.
    // Still required at validation/controller level for real registrations.
    #[ORM\Column(length: 120, nullable: true)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(max: 120, maxMessage: "Le nom est trop long.")]
    private ?string $lastName = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(max: 120, maxMessage: "Le prénom est trop long.")]
    private ?string $firstName = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\NotBlank(message: "Le téléphone est obligatoire.")]
    #[Assert\Length(max: 30, maxMessage: "Le téléphone est trop long.")]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    // --- Getters & Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setUser($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }
        return $this;
    }
    public function eraseCredentials(): void
    {
        
    }

    public function getSalt(): ?string
    {
       
        return null;
    }
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }


}
