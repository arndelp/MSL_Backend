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

    public function accept(int $id, Request $request ): Response {

     

    $data = json_decode($request->getContent(), true);

  

    $confirmationToken = $data['confirmationToken'] ?? null;

        if (!$confirmationToken) {
            return new Response(
                'Token de confirmation manquant',
                400
            );
        }

        $this->acceptOrder->execute(
            $id,
            $confirmationToken
        );

        return new Response(
            'Paiement capturé.'
        );
    }
}