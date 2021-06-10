<?php

namespace App\Service;

use App\Repository\FlightRepository;
use App\Repository\TicketRepository;
use JetBrains\PhpStorm\ArrayShape;

class TicketService
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private FlightRepository $flightRepository
    )
    {
    }

    #[ArrayShape(['status' => "string", 'message' => "string"])]
    public function book(int $flight_id): array
    {
        $flight = $this->flightRepository->find($flight_id);
        if (!$flight){
            return ['status' => 'error', 'message' => 'Unknown flight'];
        } elseif ($flight->getStatus() === 1){
            return ['status' => 'error', 'message' => 'Sales completed'];
        } elseif ($flight->getStatus() === 2){
            return ['status' => 'error', 'message' => 'Flight is canceled'];
        }



    }


}