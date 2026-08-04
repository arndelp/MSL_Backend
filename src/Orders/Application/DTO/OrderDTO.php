<?php

namespace App\Orders\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Orders\Application\DTO\OrderItemDTO;




final class OrderDTO
{
    #[Assert\NotBlank(message: "Vous devez remplir ce champ.")]
    #[Assert\Length(max: 255)]
    public ?string $shipping_firstname = null;

    #[Assert\NotBlank(message: "Vous devez remplir ce champ.")]
    #[Assert\Length(max: 255)]
    public ?string $shipping_lastname = null;

    #[Assert\NotBlank(message: "Vous devez remplir ce champ.")]
    #[Assert\Length(max: 255)]
    public ?string $shipping_phone_number = null;

    #[Assert\NotBlank(message: "Vous devez remplir ce champ.")]
    #[Assert\Length(max: 255)]
    public ?string $shipping_address_line_1 = null;

    #[Assert\Length(max: 255)]
    public ?string $shipping_address_line_2 = null;

    #[Assert\NotBlank(message: "Vous devez remplir ce champ.")]
    #[Assert\Length(max: 10)]
    public ?string $shipping_postal_code = null;

    #[Assert\NotBlank(message: "Vous devez remplir ce champ.")]
    #[Assert\Length(max: 255)]
    public ?string $shipping_city = null;

    #[Assert\NotBlank(message: "Vous devez remplir ce champ.")]
    #[Assert\Length(max: 255)]
    public ?string $shipping_country = "France";

    #[Assert\Choice(choices: ['pending_payment', 'paid', 'cancelled'])]
    public ?string $status = "pending_payment";

    /** @var OrderItemDTO[] */
    #[Assert\Count(min: 1, minMessage: "La commande doit contenir au moins un article.")]
    #[Assert\Valid] 
    public array $order_items = [];

    public function setOrderItems(array $items): void
{
    $this->order_items = array_map(function ($item) {
        return new OrderItemDTO(
            $item['book_id'] ?? $item['id'] ?? null,
            $item['quantity'] ?? null
        );
    }, $items);
}


    public function __construct(
        ?string $shipping_firstname = null,
        ?string $shipping_lastname = null,
        ?string $shipping_phone_number = null,
        ?string $shipping_address_line_1 = null,
        ?string $shipping_address_line_2 = null,
        ?string $shipping_postal_code = null,
        ?string $shipping_city = null,
        ?string $shipping_country = "France",
        ?string $status = null,
        array $order_items = []
    ) {
        $this->shipping_firstname = $shipping_firstname;
        $this->shipping_lastname = $shipping_lastname;
        $this->shipping_phone_number = $shipping_phone_number;
        $this->shipping_address_line_1 = $shipping_address_line_1;
        $this->shipping_address_line_2 = $shipping_address_line_2;
        $this->shipping_postal_code = $shipping_postal_code;
        $this->shipping_city = $shipping_city;
        $this->shipping_country = $shipping_country;
        $this->status = $status;
        $this->setOrderItems($order_items);
    }
}
