<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "La date de fin est obligatoire.")]
    #[Assert\GreaterThan(propertyPath: "startDate", message: "La date de fin doit être après la date de début.")]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // --- Getters & Setters ---
    public function getId(): ?int { return $this->id; }

    public function getStartDate(): ?\DateTimeInterface { return $this->startDate; }
    public function setStartDate(\DateTimeInterface $startDate): static { $this->startDate = $startDate; return $this; }

    public function getEndDate(): ?\DateTimeInterface { return $this->endDate; }
    public function setEndDate(\DateTimeInterface $endDate): static { $this->endDate = $endDate; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getRoom(): ?Room { return $this->room; }
    public function setRoom(?Room $room): static { $this->room = $room; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
}