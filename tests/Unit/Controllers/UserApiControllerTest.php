<?php

/**
 * Tests for UserApiController focusing on validation and key methods.
 */

use Usuarios\Presentation\UserApiController;
use Usuarios\Application\UserService;
use Especialistas\Application\EspecialistaService;
use Latte\Engine;

beforeEach(function () {
    $this->userService = Mockery::mock(UserService::class);
    $this->latte = Mockery::mock(Engine::class);
    $this->especialistaService = Mockery::mock(EspecialistaService::class);

    $this->controller = new UserApiController(
        $this->latte,
        $this->userService,
        $this->especialistaService
    );
});

afterEach(function () {
    Mockery::close();
});

test('validateUserData accepts valid user data for creation', function () {
    $validData = [
        'nombre' => 'Juan',
        'apellidos' => 'Pérez García',
        'email' => 'juan@example.com',
        'password' => 'password123',
        'telefono' => '+34600123456',
        'rol' => 'Cliente'
    ];

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('validateUserData');
    $method->setAccessible(true);

    // Should not throw exception
    $method->invoke($this->controller, $validData, true);

    expect(true)->toBeTrue();
});

test('validateUserData throws exception for invalid email', function () {
    $invalidData = [
        'nombre' => 'Juan',
        'apellidos' => 'Pérez',
        'email' => 'invalid-email',
        'password' => 'password123'
    ];

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('validateUserData');
    $method->setAccessible(true);

    $method->invoke($this->controller, $invalidData, true);
})->throws(\Respect\Validation\Exceptions\ValidationException::class);

test('validateUserData throws exception for short nombre', function () {
    $invalidData = [
        'nombre' => 'J',
        'apellidos' => 'Pérez',
        'email' => 'juan@example.com',
        'password' => 'password123'
    ];

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('validateUserData');
    $method->setAccessible(true);

    $method->invoke($this->controller, $invalidData, true);
})->throws(\Respect\Validation\Exceptions\ValidationException::class);

test('validateUserData accepts valid rol values', function () {
    $validData = [
        'nombre' => 'Juan',
        'apellidos' => 'Pérez',
        'email' => 'juan@example.com',
        'password' => 'password123',
        'rol' => 'Especialista'
    ];

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('validateUserData');
    $method->setAccessible(true);

    $method->invoke($this->controller, $validData, true);

    expect(true)->toBeTrue();
});

test('validateUserData throws exception for invalid rol', function () {
    $invalidData = [
        'nombre' => 'Juan',
        'apellidos' => 'Pérez',
        'email' => 'juan@example.com',
        'password' => 'password123',
        'rol' => 'InvalidRole'
    ];

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('validateUserData');
    $method->setAccessible(true);

    $method->invoke($this->controller, $invalidData, true);
})->throws(\Respect\Validation\Exceptions\ValidationException::class);

test('validateUserData allows optional password for updates', function () {
    $validData = [
        'nombre' => 'Juan',
        'apellidos' => 'Pérez',
        'email' => 'juan@example.com'
    ];

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('validateUserData');
    $method->setAccessible(true);

    // Should not throw exception when password not required
    $method->invoke($this->controller, $validData, false);

    expect(true)->toBeTrue();
});

test('enrichUsersWithServices adds services for specialists', function () {
    $usersArray = [
        ['id' => 1, 'rol' => 'Especialista', 'nombre' => 'Juan'],
        ['id' => 2, 'rol' => 'Cliente', 'nombre' => 'María']
    ];

    $this->especialistaService->shouldReceive('getEspecialistaIdByUserId')
        ->with(1)
        ->andReturn(10);

    $mockServicio = Mockery::mock();
    $mockServicio->shouldReceive('getNombreServicio')->andReturn('Corte de pelo');

    $this->especialistaService->shouldReceive('getServiciosForEspecialista')
        ->with(10)
        ->andReturn([$mockServicio]);

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('enrichUsersWithServices');
    $method->setAccessible(true);

    $method->invokeArgs($this->controller, [&$usersArray]);

    expect($usersArray[0])->toHaveKey('servicios');
    expect($usersArray[0]['servicios'])->toBe(['Corte de pelo']);
    expect($usersArray[1]['servicios'])->toBe([]);
});

test('enrichUsersWithServices handles specialist without services', function () {
    $usersArray = [
        ['id' => 1, 'rol' => 'Especialista', 'nombre' => 'Juan']
    ];

    $this->especialistaService->shouldReceive('getEspecialistaIdByUserId')
        ->with(1)
        ->andReturn(null);

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('enrichUsersWithServices');
    $method->setAccessible(true);

    $method->invokeArgs($this->controller, [&$usersArray]);

    expect($usersArray[0])->toHaveKey('servicios');
    expect($usersArray[0]['servicios'])->toBe([]);
});

test('getRequestData returns POST data when no JSON input', function () {
    $_POST = ['nombre' => 'Juan', 'email' => 'juan@example.com'];

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('getRequestData');
    $method->setAccessible(true);

    $result = $method->invoke($this->controller);

    expect($result)->toBe($_POST);

    $_POST = [];
});
