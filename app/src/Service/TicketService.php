<?php

namespace App\Service;

use App\Entity\Flight;
use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Util\Token;
use Doctrine\ORM\EntityManagerInterface;

class TicketService
{
    private const CUSTOMER_EMAIL = 'arghaan@gmail.com';

    public function __construct(
        private EntityManagerInterface $em,
        private TicketRepository $ticketRepository,
        private Token $token
    )
    {
    }

    public function book(Flight $flight): array|Ticket
    {
        $flightStatus = $this->checkFlight($flight);
        if (!is_null($flightStatus)) return $flightStatus;
        $ticket = $this->getFirstFree($flight);
        if ($ticket) {
            $ticket
                ->setStatus(1)
                ->setBookingKey($this->token->generateToken())
                ->setCustomerEmail(self::CUSTOMER_EMAIL)
            ;
            $this->em->persist($ticket);
            $this->em->flush();
        } else {
            // TODO: Question! Set flight status "Sales completed" if needed
            return ['status' => 'error', 'message' => 'Sales completed'];
        }

        return $ticket;
    }

    public function buy(Flight $flight, ?string $bookingKey = null): array|Ticket
    {
        $flightStatus = $this->checkFlight($flight);
        if (!is_null($flightStatus)) return $flightStatus;
        if (!is_null($bookingKey)) {
            $ticket = $this->ticketRepository->findOneBy(['bookingKey' => $bookingKey, 'status' => 1]);
            if ($ticket) {
                if ($ticket->getStatus() === 2) {
                    return ['status' => 'error', 'message' => 'The ticket is already sold out'];
                }
                $ticket
                    ->setStatus(2)
                    ->setPurchaseKey($this->token->generateToken())
                    ->setCustomerEmail(self::CUSTOMER_EMAIL)
                ;
                $this->em->persist($ticket);
                $this->em->flush();
            } else {
                return ['status' => 'error', 'message' => 'Unknown booking key'];
            }
        } else {
            $ticket = $this->getFirstFree($flight);
            if ($ticket) {
                $ticket
                    ->setStatus(2)
                    ->setPurchaseKey($this->token->generateToken())
                    ->setCustomerEmail(self::CUSTOMER_EMAIL)
                ;
                $this->em->persist($ticket);
                $this->em->flush();
            } else {
                // TODO: Question! Set flight status "Sales completed" if needed
                return ['status' => 'error', 'message' => 'Sales completed'];
            }
        }
        return $ticket;
    }

    public function cancelBooking(string $bookingKey): array|Ticket
    {
        $ticket = $this->ticketRepository->findOneBy(['bookingKey' => $bookingKey]);

        if (!is_null($ticket)) {
            if ($ticket->getStatus() === 2) {
                return ['status' => 'error', 'message' => 'The ticket is already sold out'];
            }
            $ticket
                ->setBookingKey(null)
                ->setStatus(0)
                ->setCustomerEmail(null)
            ;
            $this->em->persist($ticket);
            $this->em->flush();
            return $ticket;
        } else {
            return ['status' => 'error', 'message' => 'Unknown booking key'];
        }
    }

    public function cancelPurchase(string $purchaseKey): array|Ticket
    {
        $ticket = $this->ticketRepository->findOneBy(['purchaseKey' => $purchaseKey]);

        if (!is_null($ticket)) {
            $ticket
                ->setPurchaseKey(null)
                ->setBookingKey(null)
                ->setStatus(0)
                ->setCustomerEmail(null)
            ;
            $this->em->persist($ticket);
            $this->em->flush();
            return $ticket;
        } else {
            return ['status' => 'error', 'message' => 'Unknown purchase key'];
        }
    }

    private function getFirstFree(Flight $flight): ?Ticket
    {
        return $this->ticketRepository->findOneBy(
            [
                'flight' => $flight,
                'status' => 0
            ],
            ['placeNumber' => 'ASC']
        );
    }

    private function checkFlight($flight): ?array
    {
        if (!$flight) {
            return ['status' => 'error', 'message' => 'Unknown flight'];
        } elseif ($flight->getStatus() === 1) {
            return ['status' => 'error', 'message' => 'Sales completed'];
        } elseif ($flight->getStatus() === 2) {
            return ['status' => 'error', 'message' => 'Flight is canceled'];
        } else {
            return null;
        }
    }

}