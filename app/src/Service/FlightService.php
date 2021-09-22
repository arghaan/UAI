<?php


namespace App\Service;


use App\Entity\Flight;
use App\Message\SendEmailMessage;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

class FlightService
{
    const EVENTS = [
        'flight_ticket_sales_completed',
        'flight_ticket_sales_started',
        'flight_canceled',
    ];

    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
        private TicketRepository $ticketRepository,
    )
    {
    }

    public function stopSales(Flight $flight): Flight
    {
        if ($flight->getStatus() === 2)
            throw new BadRequestHttpException('Flight is canceled');
        $flight->setStatus(1);
        $this->em->persist($flight);
        $this->em->flush();
        return $flight;
    }

    public function startSales(Flight $flight): Flight
    {
        if ($flight->getStatus() === 2)
            throw new BadRequestHttpException('Flight is canceled');
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
}