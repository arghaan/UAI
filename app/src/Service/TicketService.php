<?php

namespace App\Service;

use App\Entity\Flight;
use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Util\Token;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TicketService
{
    public function __construct(
        private EntityManagerInterface $em,
        private TicketRepository $ticketRepository,
        private Token $token,
        private FlightService $flightService
    )
    {
    }

    public function book(Flight $flight, string $email): Ticket
    {
        $this->checkFlight($flight);
        $ticket = $this->getFirstFree($flight);
        if ($ticket) {
            $ticket
                ->setStatus(1)
                ->setBookingKey($this->token->generateToken())
                ->setCustomerEmail($email);
            $this->em->persist($ticket);
            $this->em->flush();
        } else {
            $this->flightService->stopSales($flight); // Sales is closing up because have no available tickets
            throw new BadRequestHttpException('Sales completed');
        }

        return $ticket;
    }

    private function checkFlight($flight): void
    {
        if (!$flight) {
            throw new BadRequestHttpException('Unknown flight');
        } elseif ($flight->getStatus() === 1) {
            throw new BadRequestHttpException('Sales completed');
        } elseif ($flight->getStatus() === 2) {
            throw new BadRequestHttpException('Flight is canceled');
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

    public function buy(Flight $flight, string $email, ?string $bookingKey = null): Ticket
    {
        $this->checkFlight($flight);
        if (!is_null($bookingKey)) {
            $ticket = $this->ticketRepository->findOneBy(['bookingKey' => $bookingKey, 'status' => 1]);
            if ($ticket) {
                if ($ticket->getStatus() === 2) {
                    throw new BadRequestHttpException('The ticket is already sold out');
                }
            } else {
                throw new BadRequestHttpException('Unknown booking key');
            }
        } else {
            $ticket = $this->getFirstFree($flight);
            if (is_null($ticket)) {
                $this->flightService->stopSales($flight); // Sales is closing up because have no available tickets
                throw new BadRequestHttpException('Sales completed');
            }
        }
        $ticket
            ->setStatus(2)
            ->setPurchaseKey($this->token->generateToken())
            ->setCustomerEmail($email);
        $this->em->persist($ticket);
        $this->em->flush();
        return $ticket;
    }

    public function cancelBooking(string $bookingKey): Ticket
    {
        $ticket = $this->ticketRepository->findOneBy(['bookingKey' => $bookingKey]);

        if (!is_null($ticket)) {
            if ($ticket->getStatus() === 2) {
                throw new BadRequestHttpException('The ticket is already sold out');
            }
            $ticket
                ->setBookingKey(null)
                ->setStatus(0)
                ->setCustomerEmail(null);
            $this->em->persist($ticket);
            $this->em->flush();
            if ($ticket->getFlight()->getStatus() === 1) { // Sales is opening because the ticket became available
                $this->flightService->startSales($ticket->getFlight());
            }
            return $ticket;
        } else {
            throw new BadRequestHttpException('Unknown booking key');
        }
    }

    public function cancelPurchase(string $purchaseKey): Ticket
    {
        $ticket = $this->ticketRepository->findOneBy(['purchaseKey' => $purchaseKey]);

        if (!is_null($ticket)) {
            $ticket
                ->setPurchaseKey(null)
                ->setBookingKey(null)
                ->setStatus(0)
                ->setCustomerEmail(null);
            $this->em->persist($ticket);
            $this->em->flush();
            if ($ticket->getFlight()->getStatus() === 1) { // Sales is opening because the ticket became available
                $this->flightService->startSales($ticket->getFlight());
            }
            return $ticket;
        } else {
            throw new BadRequestHttpException('Unknown purchase key');
        }
    }

}