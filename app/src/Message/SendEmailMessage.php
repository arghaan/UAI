<?php

namespace App\Message;

use App\Entity\Flight;
use App\Entity\Ticket;

final class SendEmailMessage
{

    public function __construct(
        private Flight $flight,
        private Ticket $ticket
    )
    {
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }


    public function getFlight(): Flight
    {
        return $this->flight;
    }

}
