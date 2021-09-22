<?php

namespace App\Controller\Api\v1;

use App\Entity\Flight;
use App\Http\Request\CallbackRequestDTO;
use App\Repository\FlightRepository;
use App\Service\FlightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CallbackController extends AbstractController
{

    public function __construct(
        private FlightRepository $flightRepository,
        private FlightService $flightService,
        private SerializerInterface $serializer,
    )
    {
    }


    #[Route('/api/v1/callback/event', name: 'callback')]
    public function index(CallbackRequestDTO $request): JsonResponse
    {
        $flight = $this->flightRepository->find($request->getFlightId())
            ?? throw new BadRequestHttpException("Unknown flight number");
        if ($flight->getSecretKey() !== $request->getSecretKey()) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid secret key');
        }
        $result = match ($request->getEvent()) {
            'flight_ticket_sales_completed' => $this->flightService->stopSales($flight),
            'flight_ticket_sales_started' => $this->flightService->startSales($flight),
            'flight_canceled' => $this->flightService->cancelFlight($flight),
        };

        return new JsonResponse(
            $this->serializer->serialize(
                $result,
                'json',
                Flight::ATTRIBUTES
            ),
            json: true
        );
    }
}
