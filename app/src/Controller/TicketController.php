<?php

namespace App\Controller;

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

    #[Route('/api/v1/ticket/book', name: 'book_ticket', methods: 'POST')]
    public function book(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $result = $this->ticketService->book($data['flight']);
        return $this->json([
            'message' => 'You successfully book the ticket.',
            'data' => ['flight' => 1, 'place' => 1]
        ], Response::HTTP_OK);
    }
}
