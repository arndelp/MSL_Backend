<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

//Pour éviter d'avoir une syntaxe complexe à chaque prix affiché.
//sans l'extension:  {{ (order.totalAmount / 100) | number_format(2, '.', ',') }} 


class CentimeToEuroExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('centimes_to_euros', [$this, 'convertCentimesToEuros']),
        ];
    }

    public function convertCentimesToEuros(int $centimes): string
    {
        return number_format($centimes / 100, 2, ',', ' ');
    }
}