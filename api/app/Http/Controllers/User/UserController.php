<?php

namespace App\Http\Controllers\User;

use App\DTOs\User\UserLoginDTO;
use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserLoginRequest;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller {
    private UserService $userService;
    public function __construct(
        ?UserService $userService = null,
    ) {
        $this->userService = $userService ?? new UserService();
    }

    public function login(UserLoginRequest $request): JsonResponse {
        $loginResponse = function () use ($request) {
            $userLoginData = UserLoginDTO::fromArray($request->validated());
            $login = $this->userService->loginUser($userLoginData);

            return self::makeHttpResponse(
                'USER.LOGIN.SUCCESS',
                HttpStatusCode::OK,
                $login,
            );
        };

        return $this->makeRequest('USER.LOGIN', $loginResponse);
    }
}
