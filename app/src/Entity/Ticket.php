<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Flight::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private Flight $flight;

    /**
     * @ORM\Column(type="integer")
     */
    private int $placeNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?int $bookingKey;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?int $purchaseKey;

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFlight(): Flight
    {
        return $this->flight;
    }

    public function setFlight(Flight $flight): self
    {
        $this->flight = $flight;

        return $this;
    }

    public function getPlaceNumber(): int
    {
        return $this->placeNumber;
    }

    public function setPlaceNumber(int $placeNumber): self
    {
        $this->placeNumber = $placeNumber;

        return $this;
    }

    public function getPurchaseKey(): ?int
    {
        return $this->purchaseKey;
    }

    public function setPurchaseKey(?int $purchaseKey): void
    {
        $this->purchaseKey = $purchaseKey;
    }

    public function getBookingKey(): ?int
    {
        return $this->bookingKey;
    }

    public function setBookingKey(?int $bookingKey): void
    {
        $this->bookingKey = $bookingKey;
    }

}
