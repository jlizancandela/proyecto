<?php

/**
 * Tests for UserApiController focusing on validation and key methods.
 */

use Usuarios\Presentation\UserApiController;
use Usuarios\Application\UserService;
use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;
use Latte\Engine;

beforeEach(function () {
    $this->userService = Mockery::mock(UserService::class);
    $this->latte = Mockery::mock(Engine::class);
    $this->especialistaServicioRepo = Mockery::mock(\Especialistas\Infrastructure\EspecialistaServicioRepository::class);
    $this->especialistaRepo = Mockery::mock(\Especialistas\Infrastructure\EspecialistaRepository::class);

    $this->controller = new UserApiController(
        $this->latte,
        $this->userService,
        $this->especialistaServicioRepo,
        $this->especialistaRepo
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

    $this->especialistaRepo->shouldReceive('getEspecialistaIdByUserId')
        ->with(1)
        ->andReturn(10);

    $mockServicio = Mockery::mock();
    $mockServicio->shouldReceive('getNombreServicio')->andReturn('Corte de pelo');

    $this->especialistaServicioRepo->shouldReceive('getServiciosForEspecialista')
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

    $this->especialistaRepo->shouldReceive('getEspecialistaIdByUserId')
        ->with(1)
        ->andReturn(null);

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('enrichUsersWithServices');
    $method->setAccessible(true);

    $method->invokeArgs($this->controller, [&$usersArray]);

    expect($usersArray[0])->toHaveKey('servicios');
    expect($usersArray[0]['servicios'])->toBe([]);
});

test('handleAvatarUpload returns null for invalid file type', function () {
    $invalidFile = [
        'error' => UPLOAD_ERR_OK,
        'type' => 'application/pdf',
        'name' => 'test.pdf',
        'tmp_name' => '/tmp/test'
    ];

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('handleAvatarUpload');
    $method->setAccessible(true);

    $result = $method->invoke($this->controller, $invalidFile);

    expect($result)->toBeNull();
});

test('handleAvatarUpload returns null for upload error', function () {
    $errorFile = [
        'error' => UPLOAD_ERR_NO_FILE,
        'type' => 'image/jpeg',
        'name' => 'test.jpg',
        'tmp_name' => ''
    ];

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('handleAvatarUpload');
    $method->setAccessible(true);

    $result = $method->invoke($this->controller, $errorFile);

    expect($result)->toBeNull();
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

test('handleEspecialistaCreation creates specialist with services', function () {
    $userId = 123;
    $data = [
        'descripcion' => 'Especialista en cortes',
        'servicios' => [1, 2, 3]
    ];

    $this->especialistaRepo->shouldReceive('createBasicEspecialista')
        ->with($userId, null, 'Especialista en cortes')
        ->andReturn(456);

    $this->especialistaServicioRepo->shouldReceive('addEspecialistaServicio')
        ->times(3);

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('handleEspecialistaCreation');
    $method->setAccessible(true);

    $method->invoke($this->controller, $userId, $data);

    // If no exception, test passes
    expect(true)->toBeTrue();
});

test('handleEspecialistaUpdate updates specialist data', function () {
    $userId = 123;
    $data = [
        'descripcion' => 'Nueva descripción',
        'servicios' => [4, 5]
    ];

    $this->especialistaRepo->shouldReceive('getEspecialistaIdByUserId')
        ->with($userId)
        ->andReturn(456);

    $this->especialistaRepo->shouldReceive('updateEspecialistaDescription')
        ->with(456, 'Nueva descripción');

    $this->especialistaServicioRepo->shouldReceive('deleteAllServiciosForEspecialista')
        ->with(456);

    $this->especialistaServicioRepo->shouldReceive('addEspecialistaServicio')
        ->times(2);

    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('handleEspecialistaUpdate');
    $method->setAccessible(true);

    $method->invoke($this->controller, $userId, $data);

    expect(true)->toBeTrue();
});
