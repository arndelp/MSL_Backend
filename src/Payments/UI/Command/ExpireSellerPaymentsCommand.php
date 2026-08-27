<?php

namespace App\Payments\UI\Command;

use App\Payments\Application\UseCase\ExpireSellerPayments;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:expire-seller-payments',
    description: 'Annule automatiquement les SellerPayments expirés.'
)]
final class ExpireSellerPaymentsCommand extends Command
{
    public function __construct(
        private ExpireSellerPayments $expireSellerPayments
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $this->expireSellerPayments->execute();

        $output->writeln(
            'SellerPayments expirés traités.'
        );

        return Command::SUCCESS;
    }
}