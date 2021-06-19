<?php


namespace App\Http\Request;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class TicketRequestDTO implements RequestDTOInterface
{

    private ?string $booking_key;
    private ?string $purchase_key;

    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    public function __construct(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $this->booking_key = $data['booking_key'] ?? null;
        $this->purchase_key = $data['purchase_key'] ?? null;
        $this->email = $data['email'] ?? '';
    }

    public function getBookingKey(): ?string
    {
        return $this->booking_key;
    }

    public function getPurchaseKey(): ?string
    {
        return $this->purchase_key;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
}