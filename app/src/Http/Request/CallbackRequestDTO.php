<?php


namespace App\Http\Request;


use App\Service\FlightService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class CallbackRequestDTO implements RequestDTOInterface
{


    #[Assert\NotBlank]
    #[Assert\Choice(choices: FlightService::EVENTS, message: 'Incorrect event')]
    private ?string $event;

    #[Assert\NotBlank]
    private ?string $flightId;

    #[Assert\NotBlank]
    private ?string $secretKey;

    public function __construct(Request $request)
    {
        $data = json_decode($request->getContent(), true)['data'] ?? [];
        $this->event = $data['event'] ?? null;
        $this->flightId = $data['flight_id'] ?? null;
        $this->secretKey = $data['secret_key'] ?? null;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function getFlightId(): ?string
    {
        return $this->flightId;
    }

    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }


}