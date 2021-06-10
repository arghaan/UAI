<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;

final class SendEmailMessageHandler implements MessageHandlerInterface
{

    public function __construct(
        private MailerInterface $mailer
    )
    {
    }

    public function __invoke(SendEmailMessage $message)
    {
        $email = (new TemplatedEmail())
            ->from('dev.arghaan@gmail.com')
            ->to(new Address($message->getTicket()->getCustomerEmail()))
            ->subject("Flight #{$message->getFlight()->getId()} is canceled")
            ->htmlTemplate("emails/flight_canceled.html.twig")
            ->context([
                'flight_id' => $message->getFlight()->getId(),
                'place_number' => $message->getTicket()->getPlaceNumber()
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface) {
        }
    }
}
