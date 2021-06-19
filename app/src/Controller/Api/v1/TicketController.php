<?php

namespace App\Controller\Api\v1;

use App\Entity\Flight;
use App\Http\Request\TicketRequestDTO;
use App\Service\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TicketController extends AbstractController
{
    private const TICKET = [
        AbstractNormalizer::ATTRIBUTES => [
            'id',
            'flight' => [
                'id'
            ],
            'placeNumber',
            'status',
            'bookingKey',
            'purchaseKey',
            'customerEmail',
        ]
    ];

    public function __construct(
        private TicketService $ticketService,
        private SerializerInterface $serializer,
    )
    {
    }

    #[Route('/api/v1/{flight<\d+>}/ticket/book', name: 'book_ticket', methods: 'POST')]
    public function book(TicketRequestDTO $request, ?Flight $flight = null): JsonResponse
    {
        $flight ?? throw new BadRequestHttpException("Unknown flight number");
        $ticket = $this->ticketService->book($flight, $request->getEmail());
        return new JsonResponse(
            $this->serializer->serialize(
                $ticket,
                'json',
                self::TICKET
            ),
            json: true
        );
    }

    /** @noinspection PhpUnused */
    #[Route('/api/v1/{flight<\d+>}/ticket/book', name: 'cancel_booking', methods: 'DELETE')]
    public function cancelBooking(TicketRequestDTO $request): JsonResponse
    {
        $request->getBookingKey() ?? throw new BadRequestHttpException('Incorrect request format');
        $this->ticketService->cancelBooking($request->getBookingKey());
        return new JsonResponse();
    }

    /** @noinspection PhpUnused */
    #[Route('/api/v1/{flight<\d+>}/ticket/buy', name: 'buy', methods: 'POST')]
    public function buy(TicketRequestDTO $request, ?Flight $flight = null): JsonResponse
    {
        $flight ?? throw new BadRequestHttpException("Unknown flight number");
        $ticket = $this->ticketService->buy(
            $flight,
            $request->getEmail(),
            $request->getBookingKey() ?? null
        );
        return new JsonResponse(
            $this->serializer->serialize(
                $ticket,
                'json',
                self::TICKET
            ),
            json: true
        );
    }

    /** @noinspection PhpUnused */
    #[Route('/api/v1/{flight<\d+>}/ticket/buy', name: 'cancel_purchase', methods: 'DELETE')]
    public function cancelPurchase(TicketRequestDTO $request): JsonResponse
    {
        $request->getPurchaseKey() ?? throw new BadRequestHttpException('Incorrect request format');
        $this->ticketService->cancelPurchase($request->getPurchaseKey());
        return new JsonResponse();
    }
}
