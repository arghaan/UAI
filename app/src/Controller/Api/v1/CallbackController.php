<?php

namespace App\Controller\Api\v1;

use App\Entity\Flight;
use App\Repository\FlightRepository;
use App\Service\FlightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CallbackController extends AbstractController
{
    private const EVENTS = [
        'flight_ticket_sales_completed',
        'flight_ticket_sales_started',
        'flight_canceled',
        'flight_started',
    ];

    public function __construct(
        private FlightRepository $flightRepository,
        private FlightService $flightService
    )
    {
    }


    #[Route('/api/v1/callback/event', name: 'callback')]
    public function index(Request $request): Response
    {
        $data = json_decode($request->getContent(), true)['data'];
        if (
            !is_array($data)
            || !isset($data['event'])
            || !in_array($data['event'], self::EVENTS)
        ) {
            return $this->json([
                'message' => 'Incorrect request format',
                'status' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }
        $flight = $this->flightRepository->find($data['flight_id']);
        if (is_null($flight)) {
            return $this->json([
                'message' => 'Unknown flight',
                'status' => 'error',
            ], Response::HTTP_NOT_FOUND);
        }
        if ($flight->getSecretKey() !== $data['secret_key']) {
            return $this->json([
                'message' => 'Invalid secret key',
                'status' => 'error',
            ], Response::HTTP_FORBIDDEN);
        }
        $result = match ($data['event']) {
            'flight_ticket_sales_completed' => $this->flightService->stopSales($flight),
            'flight_ticket_sales_started' => $this->flightService->startSales($flight),
            'flight_canceled' => $this->flightService->cancelFlight($flight),
            'flight_started' => $this->flightService->startFlight($flight)
        };

        if ($result instanceof Flight) {
            return $this->json(['status' => 'OK'], Response::HTTP_OK);
        } elseif (is_array($result)) {
            return $this->json($result, Response::HTTP_OK);
        } else {
            return $this->json(['status' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
