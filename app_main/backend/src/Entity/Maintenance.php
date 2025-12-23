<?php

namespace App\Entity;

use App\Repository\MaintenanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MaintenanceRepository::class)]
class Maintenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "La date prévue est obligatoire.")]
    #[Assert\GreaterThan(propertyPath: "startDate", message: "La date de fin prévue doit être après la date de début.")]
    private ?\DateTimeInterface $endDatePlanned = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $endDateReal = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    private ?string $status = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'maintenances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    // --- Getters & Setters ---
    public function getId(): ?int { return $this->id; }
    public function getStartDate(): ?\DateTimeInterface { return $this->startDate; }
    public function setStartDate(\DateTimeInterface $startDate): static { $this->startDate = $startDate; return $this; }

    public function getEndDatePlanned(): ?\DateTimeInterface { return $this->endDatePlanned; }
    public function setEndDatePlanned(\DateTimeInterface $endDatePlanned): static { $this->endDatePlanned = $endDatePlanned; return $this; }

    public function getEndDateReal(): ?\DateTimeInterface { return $this->endDateReal; }
    public function setEndDateReal(?\DateTimeInterface $endDateReal): static { $this->endDateReal = $endDateReal; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getRoom(): ?Room { return $this->room; }
    public function setRoom(?Room $room): static { $this->room = $room; return $this; }
}