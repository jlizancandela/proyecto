<?php

/**
 * Unit tests for AuthService authentication logic.
 */

use Usuarios\Application\AuthService;
use Usuarios\Application\UserService;
use Usuarios\Infrastructure\UserRepository;
use Usuarios\Infrastructure\PasswordResetRepository;
use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;

beforeEach(function () {
    $this->userRepository = Mockery::mock(UserRepository::class)->shouldIgnoreMissing();
    $this->userService = Mockery::mock(UserService::class)->shouldIgnoreMissing();
    $this->passwordResetRepo = Mockery::mock(PasswordResetRepository::class)->shouldIgnoreMissing();
    $this->authService = new AuthService($this->userRepository, $this->userService, $this->passwordResetRepo);
});

afterEach(function () {
    Mockery::close();
});

test('register creates user successfully', function () {
    $userData = [
        'nombre' => 'Juan',
        'apellidos' => 'Pérez',
        'email' => 'juan@example.com',
        'password' => 'Password123!',
        'telefono' => '123456789'
    ];

    $this->userRepository->shouldReceive('getUserByEmail')
        ->with('juan@example.com')
        ->andReturn(null);

    $this->userService->shouldReceive('setUser')->once();

    $result = $this->authService->register($userData);

    expect($result)->toBeInstanceOf(Usuario::class);
});

test('register throws exception for duplicate email', function () {
    $userData = [
        'nombre' => 'Juan',
        'apellidos' => 'Pérez',
        'email' => 'existing@example.com',
        'password' => 'Password123!',
    ];

    $mockUser = Mockery::mock(Usuario::class);
    $this->userRepository->shouldReceive('getUserByEmail')
        ->with('existing@example.com')
        ->andReturn($mockUser);

    $this->authService->register($userData);
})->throws(\RuntimeException::class, 'El email ya está registrado');

test('register throws exception for invalid password', function () {
    $userData = [
        'nombre' => 'Juan',
        'apellidos' => 'Pérez',
        'email' => 'juan@example.com',
        'password' => 'weak',
    ];

    $this->authService->register($userData);
})->throws(\RuntimeException::class);

test('login returns user on success', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getPassword')
        ->andReturn(password_hash('correct', PASSWORD_DEFAULT));

    $this->userRepository->shouldReceive('getUserByEmail')
        ->with('test@example.com')
        ->andReturn($mockUser);

    $result = $this->authService->login('test@example.com', 'correct');

    expect($result)->toBe($mockUser);
});

test('login returns null for invalid credentials', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getPassword')
        ->andReturn(password_hash('correct', PASSWORD_DEFAULT));

    $this->userRepository->shouldReceive('getUserByEmail')
        ->with('test@example.com')
        ->andReturn($mockUser);

    $result = $this->authService->login('test@example.com', 'wrong');

    expect($result)->toBeNull();
});

test('login returns null if user not found', function () {
    $this->userRepository->shouldReceive('getUserByEmail')
        ->with('notfound@example.com')
        ->andReturn(null);

    $result = $this->authService->login('notfound@example.com', 'password');

    expect($result)->toBeNull();
});

test('changePassword updates password successfully', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getPassword')
        ->andReturn(password_hash('oldpass', PASSWORD_DEFAULT));
    $mockUser->shouldReceive('setPassword')->once();

    $this->userRepository->shouldReceive('getUserById')
        ->with(1)
        ->andReturn($mockUser);

    $this->userService->shouldReceive('updateUser')->once();

    $result = $this->authService->changePassword(1, 'oldpass', 'NewPass123!');

    expect($result)->toBeTrue();
});

test('changePassword fails with wrong old password', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getPassword')
        ->andReturn(password_hash('oldpass', PASSWORD_DEFAULT));

    $this->userRepository->shouldReceive('getUserById')
        ->with(1)
        ->andReturn($mockUser);

    $result = $this->authService->changePassword(1, 'wrongpass', 'NewPass123!');

    expect($result)->toBeFalse();
});

test('generatePasswordResetToken creates token', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getId')->andReturn(1);

    $this->userRepository->shouldReceive('getUserByEmail')
        ->with('test@example.com')
        ->andReturn($mockUser);

    $this->passwordResetRepo->shouldReceive('savePasswordResetToken')
        ->with(1, Mockery::type('string'), Mockery::type('string'))
        ->once();

    $result = $this->authService->generatePasswordResetToken('test@example.com');

    expect($result)->toBeString();
    expect(strlen($result))->toBe(64);
});

test('generatePasswordResetToken throws if user not found', function () {
    $this->userRepository->shouldReceive('getUserByEmail')
        ->with('notfound@example.com')
        ->andReturn(null);

    $this->authService->generatePasswordResetToken('notfound@example.com');
})->throws(\RuntimeException::class, 'Usuario no encontrado');

test('validateResetToken returns user if valid', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getId')->andReturn(1);

    $this->passwordResetRepo->shouldReceive('getUserByResetToken')
        ->with('validtoken')
        ->andReturn($mockUser);

    $futureDate = date('Y-m-d H:i:s', time() + 3600);
    $this->userRepository->shouldReceive('getResetTokenExpiration')
        ->with(1)
        ->andReturn($futureDate);

    $result = $this->authService->validateResetToken('validtoken');

    expect($result)->toBe($mockUser);
});

test('validateResetToken returns null if expired', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getId')->andReturn(1);

    $this->passwordResetRepo->shouldReceive('getUserByResetToken')
        ->with('expiredtoken')
        ->andReturn($mockUser);

    $pastDate = date('Y-m-d H:i:s', time() - 3600);
    $this->userRepository->shouldReceive('getResetTokenExpiration')
        ->with(1)
        ->andReturn($pastDate);

    $result = $this->authService->validateResetToken('expiredtoken');

    expect($result)->toBeNull();
});

test('resetPassword updates password successfully', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getId')->andReturn(1);
    $mockUser->shouldReceive('setPassword')->once();

    $this->passwordResetRepo->shouldReceive('getUserByResetToken')
        ->with('validtoken')
        ->andReturn($mockUser);

    $futureDate = date('Y-m-d H:i:s', time() + 3600);
    $this->userRepository->shouldReceive('getResetTokenExpiration')
        ->with(1)
        ->andReturn($futureDate);

    $this->userService->shouldReceive('updateUser')->once();
    $this->passwordResetRepo->shouldReceive('clearResetToken')->with(1)->once();

    $result = $this->authService->resetPassword('validtoken', 'NewPass123!');

    expect($result)->toBeTrue();
});

test('resetPassword fails with invalid token', function () {
    $this->passwordResetRepo->shouldReceive('getUserByResetToken')
        ->with('invalidtoken')
        ->andReturn(null);

    $result = $this->authService->resetPassword('invalidtoken', 'NewPass123!');

    expect($result)->toBeFalse();
});
