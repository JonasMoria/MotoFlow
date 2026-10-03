<?php

namespace App\Services\User;

use App\DTOs\User\UserLoginDTO;
use App\DTOs\User\UserLoginVerifyDTO;
use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Mail\User\UserTwoFactorCodeMail;
use App\Models\User\User;
use App\Models\User\UserTwoFactorCode;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserTwoFactorCodeRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserLoginService {
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

    public function verifyLogin(UserLoginVerifyDTO $userLoginInformations): array {
        $user = $this->userRepository->findByEmail(
            $userLoginInformations->email,
        );

        $this->validateUser($user);

        $twoFactorCode = $this->userTwoFactorCodeRepository->findLatestValidByUserId(
            $user->id,
        );

        $this->validateTwoFactorCode($twoFactorCode, $userLoginInformations);
        $this->userTwoFactorCodeRepository->markAsUsed(
            $twoFactorCode->id,
        );

        $token = $user->createToken(config('app.name'))->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
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

    protected function validateUser(?User $user): void {
        if ($user === null) {
            throw new AppException('USER.LOGIN.INVALID_CREDENTIALS', HttpStatusCode::UNAUTHORIZED);
        }
    }

    protected function validateTwoFactorCode(
        ?UserTwoFactorCode $twoFactorCode,
        UserLoginVerifyDTO $userLoginInformations,
    ): void {
        if ($twoFactorCode === null) {
            throw new AppException('USER.LOGIN.CODE.EXPIRED', HttpStatusCode::UNAUTHORIZED);
        }

        if ($twoFactorCode->attempts >= 5) {
            throw new AppException('USER.LOGIN.CODE.EXPIRED', HttpStatusCode::UNAUTHORIZED);
        }

        $hashValidated = Hash::check(
            $userLoginInformations->code,
            $twoFactorCode->code_hash,
        );

        if (!$hashValidated) {
            $this->userTwoFactorCodeRepository->incrementAttempts(
                $twoFactorCode->id,
            );

            throw new AppException('USER.LOGIN.CODE.INVALID', HttpStatusCode::UNAUTHORIZED);
        }
    }
}
