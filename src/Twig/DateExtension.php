<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('european_date', [$this, 'formatEuropeanDate']),
        ];
    }

   public function formatEuropeanDate(?\DateTimeInterface $date): string
{
    if ($date === null) {
        return 'S/O';
    }

    return $date->format('d/m/Y H:i');
}
}