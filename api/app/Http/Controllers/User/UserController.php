<?php

namespace App\Http\Controllers\User;

use App\DTOs\User\UserLoginDTO;
use App\DTOs\User\UserLoginVerifyDTO;
use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserLoginVerifyRequest;
use App\Services\User\UserLoginService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller {
    private UserLoginService $userLoginService;
    public function __construct(
        ?UserLoginService $userLoginService = null,
    ) {
        $this->userLoginService = $userLoginService ?? new UserLoginService();
    }

    public function login(UserLoginRequest $request): JsonResponse {
        $loginResponse = function () use ($request) {
            $userLoginData = UserLoginDTO::fromArray(
                $request->validated(),
            );

            $login = $this->userLoginService->loginUser($userLoginData);

            return self::makeHttpResponse(
                'USER.LOGIN.SUCCESS',
                HttpStatusCode::OK,
                $login,
            );
        };

        return $this->makeRequest('USER.LOGIN', $loginResponse);
    }

    public function verifyLogin(UserLoginVerifyRequest $request): JsonResponse {
        $verifyLoginResponse = function () use ($request) {
            $userLoginData = UserLoginVerifyDTO::fromArray(
                $request->validated(),
            );

            $tokenLogin = $this->userLoginService->verifyLogin($userLoginData);

            return self::makeHttpResponse(
                'USER.CODE.VERIFIED',
                HttpStatusCode::CREATED,
                $tokenLogin,
            );
        };

        return $this->makeRequest('USER.VERIFY.LOGIN', $verifyLoginResponse);
    }
}
