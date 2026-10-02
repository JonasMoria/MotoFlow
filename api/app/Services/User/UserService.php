<?php

namespace App\Services\User;

use App\DTOs\User\UserLoginDTO;
use App\Exceptions\AppException;
use App\Mail\User\UserTwoFactorCodeMail;
use App\Models\User\User;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserTwoFactorCodeRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserService {
    private UserRepository $userRepository;
    private UserTwoFactorCodeRepository $userTwoFactorCodeRepository;

    public function __construct(
        ?UserRepository $userRepository = null,
        ?UserTwoFactorCodeRepository $userTwoFactorCodeRepository = null,
    ) {
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->userTwoFactorCodeRepository = $userTwoFactorCodeRepository ?? new UserTwoFactorCodeRepository();
    }

    public function loginUser(UserLoginDTO $userLoginInformations): array {
        $user = $this->userRepository->findByEmail(
            $userLoginInformations->email,
        );

        $this->validateLogin($user, $userLoginInformations);

        $authCode = $this->generateAuthCode();

        $this->userTwoFactorCodeRepository->invalidatePreviousCodes($user->id);
        $this->userTwoFactorCodeRepository->create(
            userId: $user->id,
            codeHash: Hash::make($authCode),
        );

        $this->sendTwoFactorCodeEmail($user, $authCode);

        return [
            'two_factor_code_generated' => true,
        ];
    }

    protected function validateLogin(?User $user, UserLoginDTO $userLoginInformations): void {
        if ($user === null) {
            throw new AppException('USER.LOGIN.INVALID_CREDENTIALS');
        }

        if (!Hash::check($userLoginInformations->password, $user->password)) {
            throw new AppException('USER.LOGIN.INVALID_CREDENTIALS');
        }
    }

    protected function generateAuthCode(): string {
        return (string) random_int(100000, 999999);
    }

    protected function sendTwoFactorCodeEmail(User $user, string $authCode): void {
        Mail::to($user->email)
            ->queue(
                new UserTwoFactorCodeMail(
                    user: $user,
                    code: $authCode,
                ),
            );
    }
}
