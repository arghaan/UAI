<?php

namespace App\Controller;

use App\Entity\Flight;
use App\Entity\Ticket;
use App\Service\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    public function __construct(
        private TicketService $ticketService
    )
    {
    }

    #[Route('/api/v1/ticket/book/{flight<\d+>}', name: 'book_ticket', methods: 'POST')]
    public function book(Flight $flight): Response
    {
        $result = $this->ticketService->book($flight);
        if ($result instanceof Ticket) {
            return $this->json([
                'message' => 'You successfully book the ticket.',
                'status' => 'OK',
                'data' => [
                    'flight_id' => $result->getFlight()->getId(),
                    'place_number' => $result->getPlaceNumber(),
                    'booking_key' => $result->getBookingKey(),
                ]
            ], Response::HTTP_OK);
        } else {
            return $this->checkErrors($result);
        }
    }

    #[Route('/api/v1/ticket/book', name: 'cancel_booking', methods: 'DELETE')]
    public function cancelBooking(Request $request): Response
    {
        $data = json_decode($request->getContent(), true)['data'];
        if (!is_array($data) || !isset($data['booking_key'])) {
            return $this->json([
                'message' => 'Incorrect request format',
                'status' => 'error',
                'data' => []
            ], Response::HTTP_BAD_REQUEST);
        }
        $result = $this->ticketService->cancelBooking($data['booking_key']);
        if ($result instanceof Ticket) {
            return $this->json([
                'message' => 'You successfully cancel booking.',
                'status' => 'OK',
                'data' => [
                    'flight_id' => $result->getFlight()->getId(),
                    'place_number' => $result->getPlaceNumber(),
                ]
            ], Response::HTTP_OK);
        } else {
            return $this->checkErrors($result);
        }
    }

    #[Route('/api/v1/ticket/buy/{flight<\d+>}', name: 'buy', methods: 'POST')]
    public function buy(Request $request, Flight $flight): Response
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['data'])) {
            if (!is_array($data) || !isset($data['booking_key'])) {
                return $this->json([
                    'message' => 'Incorrect request format',
                    'status' => 'error',
                    'data' => []
                ], Response::HTTP_BAD_REQUEST);
            }
        }
        $result = $this->ticketService->buy($flight, $data['booking_key'] ?? null);
        if ($result instanceof Ticket) {
            return $this->json([
                'message' => 'You successfully buy the ticket.',
                'status' => 'OK',
                'data' => [
                    'flight_id' => $result->getFlight()->getId(),
                    'place_number' => $result->getPlaceNumber(),
                    'purchase_key' => $result->getPurchaseKey(),
                ]
            ], Response::HTTP_OK);
        } else {
            return $this->checkErrors($result);
        }
    }

    #[Route('/api/v1/ticket/buy', name: 'cancel_purchase', methods: 'DELETE')]
    public function cancelPurchase(Request $request): Response
    {
        $data = json_decode($request->getContent(), true)['data'];
        if (!is_array($data) || !isset($data['purchase_key'])) {
            return $this->json([
                'message' => 'Incorrect request format',
                'status' => 'error',
                'data' => []
            ], Response::HTTP_BAD_REQUEST);
        }
        $result = $this->ticketService->cancelPurchase($data['purchase_key']);
        if ($result instanceof Ticket) {
            return $this->json([
                'message' => 'You successfully cancel purchase.',
                'status' => 'OK',
                'data' => [
                    'flight_id' => $result->getFlight()->getId(),
                    'place_number' => $result->getPlaceNumber(),
                ]
            ], Response::HTTP_OK);
        } else {
            return $this->checkErrors($result);
        }
    }

    private function checkErrors(array|Ticket $result): Response
    {
        if (is_array($result) && $result['status'] === 'error') {
            return $this->json([
                'message' => $result['message'],
                'status' => 'error',
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            return $this->json([
                'message' => 'Unknown Error',
                'status' => 'error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
