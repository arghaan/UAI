<?php


namespace App\Service;


use App\Entity\Flight;
use App\Message\SendEmailMessage;
use App\Repository\FlightRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class FlightService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
        private TicketRepository $ticketRepository,
        private FlightRepository $flightRepository,
    )
    {
    }

    public function stopSales(Flight $flight): array|Flight
    {
        if ($flight->getStatus() === 2)
            return ['status' => 'error', 'message' => 'Flight is canceled'];
        $flight->setStatus(1);
        $this->em->persist($flight);
        $this->em->flush();
        return $flight;
    }

    public function startSales(Flight $flight): array|Flight
    {
        if ($flight->getStatus() === 2)
            return ['status' => 'error', 'message' => 'Flight is canceled'];
        $flight->setStatus(1);
        $this->em->persist($flight);
        $this->em->flush();
        return $flight;
    }

    public function cancelFlight(Flight $flight): Flight
    {
        $flight->setStatus(2);
        $this->em->persist($flight);
        $this->em->flush();
        $tickets = $this->ticketRepository->findAllNotFree($flight);
        foreach ($tickets as $ticket) {
            $this->bus->dispatch(new SendEmailMessage($flight, $ticket));
        }
        return $flight;
    }

    public function startFlight(Flight $flight): Flight
    {
        $flight->setStatus(0);
        $this->em->persist($flight);
        $this->em->flush();
        return $flight;
    }

    public function getFlightIds(): array
    {
        $flights = $this->flightRepository->findBy(['status' => 0]);
        $ids = [];
        foreach ($flights as $flight){
            $ids[] = $flight->getId();
        }
        return $ids;
    }
}