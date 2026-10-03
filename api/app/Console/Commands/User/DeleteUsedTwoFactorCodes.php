<?php

namespace App\Console\Commands\User;

use App\Repositories\User\UserTwoFactorCodeRepository;
use Illuminate\Console\Command;

class DeleteUsedTwoFactorCodes extends Command {
    protected $signature = 'user-two-factor-codes:delete-used';
    protected $description = 'Remove códigos de autenticação de dois fatores já utilizados';

    public function __construct(
        private readonly UserTwoFactorCodeRepository $userTwoFactorCodeRepository,
    ) {
        return parent::__construct();
    }

    public function handle(): int {
        $deletedCodesQuantity = $this->userTwoFactorCodeRepository->deleteUsedCodes();

        $this->info(
            "Códigos 2FA utilizados removidos: {$deletedCodesQuantity}",
        );

        return self::SUCCESS;
    }
}
