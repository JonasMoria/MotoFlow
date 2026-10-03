<?php

namespace Tests\Unit\Services\User;

use App\DTOs\User\UserLoginDTO;
use App\DTOs\User\UserLoginVerifyDTO;
use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Models\User\User;
use App\Models\User\UserTwoFactorCode;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserTwoFactorCodeRepository;
use App\Services\User\UserLoginService;
use stdClass;
use Tests\TestCase;

class UserLoginServiceTest extends TestCase {
    public function testLoginUserSuccessfully(): void {
        $userModel = new User();
        $userModel->id = 123;

        $userLoginInformations = new UserLoginDTO('email@test.com', 'password_test');

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($userLoginInformations->email)
            ->willReturn($userModel);

        $userTwoFactorCode = new UserTwoFactorCode();
        $userTwoFactorCodeRepository = $this->createMock(UserTwoFactorCodeRepository::class);
        $userTwoFactorCodeRepository
            ->expects($this->once())
            ->method('invalidatePreviousCodes')
            ->with(123);
        $userTwoFactorCodeRepository
            ->expects($this->once())
            ->method('create')
            ->with(123, $this->isString())
            ->willReturn($userTwoFactorCode);

        $service = $this->getMockBuilder(UserLoginService::class)
            ->setConstructorArgs([
                $userRepository,
                $userTwoFactorCodeRepository,
            ])
            ->onlyMethods([
                'validateLogin',
                'generateAuthCode',
                'sendTwoFactorCodeEmail',
            ])
            ->getMock();
        $service
            ->expects($this->once())
            ->method('validateLogin')
            ->with($userModel, $userLoginInformations);
        $service
            ->expects($this->once())
            ->method('generateAuthCode')
            ->willReturn('code123');
        $service
            ->expects($this->once())
            ->method('sendTwoFactorCodeEmail')
            ->with($userModel, 'code123');

        $actual = $service->loginUser($userLoginInformations);
        $expected = [
            'two_factor_code_generated' => true,
        ];

        $this->assertEquals($expected, $actual, 'Fluxo de login ocorre como o esperado');
    }

    public function testLoginUserThrowsExceptionWithInvalidLogin(): void {
        $userModel = new User();
        $userModel->id = 123;

        $userLoginInformations = new UserLoginDTO('email@test.com', 'password_test');

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($userLoginInformations->email)
            ->willReturn($userModel);

        $userTwoFactorCodeRepository = $this->createMock(UserTwoFactorCodeRepository::class);
        $userTwoFactorCodeRepository
            ->expects($this->never())
            ->method('invalidatePreviousCodes');

        $userTwoFactorCodeRepository
            ->expects($this->never())
            ->method('create');

        $service = $this->getMockBuilder(UserLoginService::class)
            ->setConstructorArgs([
                $userRepository,
                $userTwoFactorCodeRepository,
            ])
            ->onlyMethods([
                'validateLogin',
            ])
            ->getMock();
        $service
            ->expects($this->once())
            ->method('validateLogin')
            ->with($userModel, $userLoginInformations)
            ->willThrowException(
                new AppException(
                    'MESSAGE.TEST',
                    HttpStatusCode::UNPROCESSABLE_ENTITY,
                ),
            );

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('MESSAGE.TEST');
        $this->expectExceptionCode(
            HttpStatusCode::UNPROCESSABLE_ENTITY->value,
        );

        $service->loginUser($userLoginInformations);
    }

    public function testVerifyLoginSuccessfully(): void {
        $userLoginInformations = new UserLoginVerifyDTO('email@test.com', '123456');

        $userModel = $this->getMockBuilder(User::class)
            ->onlyMethods(['createToken'])
            ->getMock();

        $userModel->id = 123;
        $userModel->email = 'email@test.com';

        $accessToken = new stdClass();
        $accessToken->plainTextToken = 'token123';

        $userModel
            ->expects($this->once())
            ->method('createToken')
            ->with($userModel->email)
            ->willReturn($accessToken);

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($userLoginInformations->email)
            ->willReturn($userModel);

        $userTwoFactorCode = new UserTwoFactorCode();
        $userTwoFactorCode->id = 321;

        $userTwoFactorCodeRepository = $this->createMock(UserTwoFactorCodeRepository::class);
        $userTwoFactorCodeRepository
            ->expects($this->once())
            ->method('findLatestValidByUserId')
            ->with($userModel->id)
            ->willReturn($userTwoFactorCode);
        $userTwoFactorCodeRepository
            ->expects($this->once())
            ->method('markAsUsed')
            ->with($userTwoFactorCode->id);

        $service = $this->getMockBuilder(UserLoginService::class)
            ->setConstructorArgs([
                $userRepository,
                $userTwoFactorCodeRepository,
            ])
            ->onlyMethods([
                'validateUser',
                'validateTwoFactorCode',
            ])
            ->getMock();
        $service
            ->expects($this->once())
            ->method('validateUser')
            ->with($userModel);
        $service
            ->expects($this->once())
            ->method('validateTwoFactorCode')
            ->with($userTwoFactorCode, $userLoginInformations);

        $actual = $service->verifyLogin($userLoginInformations);
        $expected = [
            'token' => $accessToken->plainTextToken,
            'token_type' => UserLoginService::DEFAULT_TOKEN_TYPE,
        ];

        $this->assertEquals($expected, $actual, 'Fluxo de verificação 2FA ocorre como o esperado');
    }

    public function testVerifyLoginThrowsExceptionWithInvalidUser(): void {
        $userLoginInformations = new UserLoginVerifyDTO('email@test.com', '123456');

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($userLoginInformations->email)
            ->willReturn(null);

        $service = new UserLoginService($userRepository);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('USER.LOGIN.INVALID_CREDENTIALS');
        $this->expectExceptionCode(HttpStatusCode::UNAUTHORIZED->value);

        $service->verifyLogin($userLoginInformations);
    }

    public function testVerifyLoginThrowsExceptionWithInvalidTwoFactorCode(): void {
        $userLoginInformations = new UserLoginVerifyDTO('email@test.com', '123456');

        $userModel = new User();
        $userModel->id = 123;

        $userTwoFactorCode = new UserTwoFactorCode();

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($userLoginInformations->email)
            ->willReturn($userModel);

        $userTwoFactorCodeRepository = $this->createMock(UserTwoFactorCodeRepository::class);
        $userTwoFactorCodeRepository
            ->expects($this->once())
            ->method('findLatestValidByUserId')
            ->with($userModel->id)
            ->willReturn($userTwoFactorCode);
        $userTwoFactorCodeRepository
            ->expects($this->never())
            ->method('markAsUsed');

        $service = $this->getMockBuilder(UserLoginService::class)
            ->setConstructorArgs([
                $userRepository,
                $userTwoFactorCodeRepository,
            ])
            ->onlyMethods([
                'validateUser',
                'validateTwoFactorCode',
            ])
            ->getMock();
        $service
            ->expects($this->once())
            ->method('validateUser')
            ->with($userModel);
        $service
            ->expects($this->once())
            ->method('validateTwoFactorCode')
            ->with($userTwoFactorCode, $userLoginInformations)
            ->willThrowException(new AppException('TEST.MESSAGE'));

        $this->expectException(AppException::class);

        $service->verifyLogin($userLoginInformations);
    }
}
