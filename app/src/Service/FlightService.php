<?php


namespace App\Service;


use App\Repository\FlightRepository;

class FlightService
{

    /**
     * FlightService constructor.
     */
    public function __construct(
        private FlightRepository $flightRepository
    )
    {
    }
}