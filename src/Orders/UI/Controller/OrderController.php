<?php

namespace App\Orders\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Orders\Application\UseCase\RecordOrderByApi;
use Symfony\Component\HttpFoundation\Response;
use App\Orders\Application\DTO\OrderDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Users\Domain\Entity\User;

final class OrderController extends AbstractController
{
    public function recordOrder(
        Request $request,
        LoggerInterface $logger,
        ValidatorInterface $validator,
        RecordOrderByApi $recordOrderByApi,
    ): Response
    {
        

        $user = $this->getUser();

            if (!$user instanceof User) {
                return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
            }  

        $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new Response('Données reçues vides', 400);
            }

            if (empty($data['order_items'])) {
                return new Response("La commande doit contenir au moins un article.", 400);
            }

        // Normalisation des items
        foreach ($data['order_items'] as &$item) {

            if (isset($item['id']) && !isset($item['book_id'])) {
                $item['book_id'] = $item['id'];
            }
            
        }

        $dto = new OrderDTO(
            shipping_firstname: $data['shipping_firstname'] ?? null,
            shipping_lastname: $data['shipping_lastname'] ?? null,
            shipping_phone_number: $data['shipping_phone_number'] ?? null,
            shipping_address_line_1: $data['shipping_address_line_1'] ?? null,
            shipping_address_line_2: $data['shipping_address_line_2'] ?? null,
            shipping_postal_code: $data['shipping_postal_code'] ?? null,
            shipping_city: $data['shipping_city'] ?? null,
            shipping_country: $data['shipping_country'] ?? "France",            
            order_items: $data['order_items']
        );

        // Validation du DTO complet
        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return new Response('Données invalides: ' . (string) $errors, 400);
        }

        try {

            $result = $recordOrderByApi->execute($dto);
            
        } catch (\Exception $e) {
            $logger->error('Erreur lors de l\'enregistrement de la commande', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return new Response('Erreur lors de l\'enregistrement de la commande', 500);
        }

       return new JsonResponse([
            'success' => true,
            'url' => $result['url']
        ], 201);
    }
}
