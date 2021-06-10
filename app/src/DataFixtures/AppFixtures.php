<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Flight;
use App\Entity\Ticket;
use App\Util\Token;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{

    public function __construct(
        private Token $token
    )
    {
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $flight = new Flight();
        $flight->setFlightVolume(150);
        $flight->setSecretKey($this->token->generateToken(32));
        $manager->persist($flight);

        for ($i = 0; $i < $flight->getFlightVolume(); $i++) {
            $ticket = new Ticket();
            $ticket
                ->setFlight($flight)
                ->setPlaceNumber($i + 1);
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}
