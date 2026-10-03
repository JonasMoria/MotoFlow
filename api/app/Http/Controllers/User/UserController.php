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
use Illuminate\Http\Request;

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

    public function logout(Request $request): JsonResponse {
        $logoutResponse = function () use ($request) {
            $user = $request->user();
            $token = $user->currentAccessToken();

            $this->userLoginService->logout($token);

            return self::makeHttpResponse(
                'USER.LOGOUT.DONE',
                HttpStatusCode::OK,
            );
        };

        return $this->makeRequest('USER.LOGOUT', $logoutResponse);
    }

    public function logoutAll(Request $request): JsonResponse {
        $logoutAllResponse = function () use ($request) {
            $user = $request->user();

            $this->userLoginService->logoutAll($user);

            return self::makeHttpResponse(
                'USER.LOGOUT.ALL.DONE',
                HttpStatusCode::OK,
            );
        };

        return $this->makeRequest('USER.LOGOUT', $logoutAllResponse);
    }
}
