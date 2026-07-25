<?php

namespace App\Orders\UI\Controller;

use App\Orders\Application\UseCase\AcceptOrderItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SellerOrderItemController
{
    public function __construct(
        private AcceptOrderItem $acceptOrder
    ) {
    }

    public function accept(
        int $id,
        Request $request
    ): Response {

        $this->acceptOrder->execute(
            $id,
            $request->query->get('token')
        );

        return new Response(
            'Paiement capturé.'
        );
    }
}