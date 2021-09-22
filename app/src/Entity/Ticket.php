<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
{
    const ATTRIBUTES = [
        AbstractNormalizer::ATTRIBUTES => [
            'id',
            'flight' => [
                'id'
            ],
            'placeNumber',
            'status',
            'bookingKey',
            'purchaseKey',
            'customerEmail',
        ]
    ];

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
     *  free = 0
     *  booked = 1
     *  sold out = 2
     */
    private int $status = 0;

    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $bookingKey;

    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $purchaseKey;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $customerEmail;

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(?string $customerEmail): static
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFlight(): Flight
    {
        return $this->flight;
    }

    public function setFlight(Flight $flight): static
    {
        $this->flight = $flight;

        return $this;
    }

    public function getPlaceNumber(): int
    {
        return $this->placeNumber;
    }

    public function setPlaceNumber(int $placeNumber): static
    {
        $this->placeNumber = $placeNumber;

        return $this;
    }

    public function getPurchaseKey(): ?string
    {
        return $this->purchaseKey;
    }

    public function setPurchaseKey(?string $purchaseKey): static
    {
        $this->purchaseKey = $purchaseKey;

        return $this;
    }

    public function getBookingKey(): ?string
    {
        return $this->bookingKey;
    }

    public function setBookingKey(?string $bookingKey): static
    {
        $this->bookingKey = $bookingKey;

        return $this;
    }

}
