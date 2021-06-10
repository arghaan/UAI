<?php

namespace App\DataFixtures;

use App\Entity\Flight;
use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $flight = new Flight();
        $flight->setFlightVolume(150);
        $manager->persist($flight);

        for ($i = 0; $i < $flight->getFlightVolume(); $i++) {
            $ticket = new Ticket();
            $ticket->setFlight($flight)->setPlaceNumber($i + 1);
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}
