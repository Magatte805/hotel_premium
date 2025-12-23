<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le numéro de chambre est obligatoire.")]
    #[Assert\Positive(message: "Le numéro doit être positif.")]
    private ?int $number = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix par nuit est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être supérieur à 0.")]
    private ?float $pricePerNight = null;

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hotel $hotel = null;

    /**
     * @var Collection<int, Maintenance>
     */
    #[ORM\OneToMany(targetEntity: Maintenance::class, mappedBy: 'room', orphanRemoval: true)]
    private Collection $maintenances;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'room', orphanRemoval: true)]
    private Collection $reservations;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'rooms')]
    private Collection $services;

    public function __construct()
    {
        $this->maintenances = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    // --- Getters & Setters ---
    public function getId(): ?int { return $this->id; }

    public function getNumber(): ?int { return $this->number; }
    public function setNumber(int $number): static { $this->number = $number; return $this; }

    public function getPricePerNight(): ?float { return $this->pricePerNight; }
    public function setPricePerNight(float $pricePerNight): static { $this->pricePerNight = $pricePerNight; return $this; }

    public function getHotel(): ?Hotel { return $this->hotel; }
    public function setHotel(?Hotel $hotel): static { $this->hotel = $hotel; return $this; }

    public function getMaintenances(): Collection { return $this->maintenances; }
    public function addMaintenance(Maintenance $maintenance): static { 
        if (!$this->maintenances->contains($maintenance)) {
            $this->maintenances->add($maintenance);
            $maintenance->setRoom($this);
        }
        return $this;
    }
    public function removeMaintenance(Maintenance $maintenance): static { 
        if ($this->maintenances->removeElement($maintenance)) {
            if ($maintenance->getRoom() === $this) {
                $maintenance->setRoom(null);
            }
        }
        return $this;
    }

    public function getReservations(): Collection { return $this->reservations; }
    public function addReservation(Reservation $reservation): static {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setRoom($this);
        }
        return $this;
    }
    public function removeReservation(Reservation $reservation): static {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getRoom() === $this) {
                $reservation->setRoom(null);
            }
        }
        return $this;
    }

    public function getServices(): Collection { return $this->services; }
    public function addService(Service $service): static {
        if (!$this->services->contains($service)) $this->services->add($service);
        return $this;
    }
    public function removeService(Service $service): static {
        $this->services->removeElement($service);
        return $this;
    }
}