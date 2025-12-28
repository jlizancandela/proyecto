<?php

/**
 * Unit tests for UserService
 */

use Usuarios\Application\UserService;
use Usuarios\Infrastructure\UserRepository;
use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;

beforeEach(function () {
    $this->repository = Mockery::mock(UserRepository::class);
    $this->service = new UserService($this->repository);
});

afterEach(function () {
    Mockery::close();
});

test('getAllUsers returns paginated users', function () {
    $mockUser = Mockery::mock(Usuario::class);

    $this->repository->shouldReceive('getAllUsers')
        ->once()
        ->with(10, 0, '', 'asc')
        ->andReturn([$mockUser, $mockUser]);

    $result = $this->service->getAllUsers();

    expect($result)->toBeArray();
    expect(count($result))->toBe(2);
});

test('getTotalUsers returns count', function () {
    $this->repository->shouldReceive('getTotalUsers')
        ->once()
        ->andReturn(42);

    $result = $this->service->getTotalUsers();

    expect($result)->toBe(42);
});

test('getUserById returns user when found', function () {
    $mockUser = Mockery::mock(Usuario::class);

    $this->repository->shouldReceive('getUserById')
        ->once()
        ->with(1)
        ->andReturn($mockUser);

    $result = $this->service->getUserById(1);

    expect($result)->toBeInstanceOf(Usuario::class);
});

test('getUserById returns null when not found', function () {
    $this->repository->shouldReceive('getUserById')
        ->once()
        ->with(999)
        ->andReturn(null);

    $result = $this->service->getUserById(999);

    expect($result)->toBeNull();
});

test('getUsersByRole returns users with specific role', function () {
    $mockUser = Mockery::mock(Usuario::class);

    $this->repository->shouldReceive('getUsersByRole')
        ->once()
        ->with('Cliente', 10, 0, '', 'asc')
        ->andReturn([$mockUser]);

    $result = $this->service->getUsersByRole('Cliente');

    expect($result)->toBeArray();
    expect(count($result))->toBe(1);
});

test('getTotalUsersByRole returns count for role', function () {
    $this->repository->shouldReceive('getTotalUsersByRole')
        ->once()
        ->with('Admin')
        ->andReturn(5);

    $result = $this->service->getTotalUsersByRole('Admin');

    expect($result)->toBe(5);
});

test('searchUsers returns matching users', function () {
    $mockUser = Mockery::mock(Usuario::class);

    $this->repository->shouldReceive('searchUsers')
        ->once()
        ->with('john', 10, 0, '', 'asc')
        ->andReturn([$mockUser]);

    $result = $this->service->searchUsers('john');

    expect($result)->toBeArray();
    expect(count($result))->toBe(1);
});

test('getTotalSearchResults returns search count', function () {
    $this->repository->shouldReceive('getTotalSearchResults')
        ->once()
        ->with('john')
        ->andReturn(3);

    $result = $this->service->getTotalSearchResults('john');

    expect($result)->toBe(3);
});

test('getAllUsersWithFilters applies filters', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $filters = ['rol' => 'Cliente', 'activo' => true];

    $this->repository->shouldReceive('findAllFiltered')
        ->once()
        ->with($filters, 50, 0)
        ->andReturn([$mockUser]);

    $result = $this->service->getAllUsersWithFilters($filters);

    expect($result)->toBeArray();
    expect(count($result))->toBe(1);
});

test('countAllUsersWithFilters returns filtered count', function () {
    $filters = ['rol' => 'Admin'];

    $this->repository->shouldReceive('countAllFiltered')
        ->once()
        ->with($filters)
        ->andReturn(10);

    $result = $this->service->countAllUsersWithFilters($filters);

    expect($result)->toBe(10);
});

test('setUser creates new user successfully', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getEmail')->andReturn('new@example.com');
    $mockUser->shouldReceive('getNombre')->andReturn('John');
    $mockUser->shouldReceive('getApellidos')->andReturn('Doe');
    $mockUser->shouldReceive('getTelefono')->andReturn('123456789');
    $mockUser->shouldReceive('setId')->once()->with(1);

    $this->repository->shouldReceive('getUserByEmail')
        ->once()
        ->with('new@example.com')
        ->andReturn(null);

    $this->repository->shouldReceive('addUser')
        ->once()
        ->andReturn(1);

    $this->service->setUser($mockUser);

    expect(true)->toBeTrue();
});

test('setUser throws exception for duplicate email', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $existingUser = Mockery::mock(Usuario::class);

    $mockUser->shouldReceive('getEmail')->andReturn('existing@example.com');
    $mockUser->shouldReceive('getNombre')->andReturn('John');
    $mockUser->shouldReceive('getApellidos')->andReturn('Doe');
    $mockUser->shouldReceive('getTelefono')->andReturn('123456789');

    $this->repository->shouldReceive('getUserByEmail')
        ->once()
        ->with('existing@example.com')
        ->andReturn($existingUser);

    $this->service->setUser($mockUser);
})->throws(\RuntimeException::class, 'El email ya está registrado');

test('setUser throws exception for invalid email', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getEmail')->andReturn('invalid-email');
    $mockUser->shouldReceive('getNombre')->andReturn('John');
    $mockUser->shouldReceive('getApellidos')->andReturn('Doe');
    $mockUser->shouldReceive('getTelefono')->andReturn(null);

    $this->service->setUser($mockUser);
})->throws(\RuntimeException::class);

test('updateUser updates existing user successfully', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $mockUser->shouldReceive('getId')->andReturn(1);
    $mockUser->shouldReceive('getEmail')->andReturn('updated@example.com');
    $mockUser->shouldReceive('getNombre')->andReturn('John');
    $mockUser->shouldReceive('getApellidos')->andReturn('Doe Updated');
    $mockUser->shouldReceive('getTelefono')->andReturn('987654321');

    $this->repository->shouldReceive('getUserByEmail')
        ->once()
        ->with('updated@example.com')
        ->andReturn(null);

    $this->repository->shouldReceive('updateUser')
        ->once();

    $this->service->updateUser($mockUser);

    expect(true)->toBeTrue();
});

test('updateUser allows same email for same user', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $existingUser = Mockery::mock(Usuario::class);

    $mockUser->shouldReceive('getId')->andReturn(1);
    $mockUser->shouldReceive('getEmail')->andReturn('same@example.com');
    $mockUser->shouldReceive('getNombre')->andReturn('John');
    $mockUser->shouldReceive('getApellidos')->andReturn('Doe');
    $mockUser->shouldReceive('getTelefono')->andReturn('123456789');

    $existingUser->shouldReceive('getId')->andReturn(1);

    $this->repository->shouldReceive('getUserByEmail')
        ->once()
        ->with('same@example.com')
        ->andReturn($existingUser);

    $this->repository->shouldReceive('updateUser')
        ->once();

    $this->service->updateUser($mockUser);

    expect(true)->toBeTrue();
});

test('updateUser throws exception for email used by another user', function () {
    $mockUser = Mockery::mock(Usuario::class);
    $existingUser = Mockery::mock(Usuario::class);

    $mockUser->shouldReceive('getId')->andReturn(1);
    $mockUser->shouldReceive('getEmail')->andReturn('taken@example.com');
    $mockUser->shouldReceive('getNombre')->andReturn('John');
    $mockUser->shouldReceive('getApellidos')->andReturn('Doe');
    $mockUser->shouldReceive('getTelefono')->andReturn('123456789');

    $existingUser->shouldReceive('getId')->andReturn(2); // Different user

    $this->repository->shouldReceive('getUserByEmail')
        ->once()
        ->with('taken@example.com')
        ->andReturn($existingUser);

    $this->service->updateUser($mockUser);
})->throws(\RuntimeException::class, 'El email ya está registrado');

test('deleteUser deletes user successfully', function () {
    $this->repository->shouldReceive('deleteUser')
        ->once()
        ->with(1);

    $this->service->deleteUser(1);

    expect(true)->toBeTrue();
});

test('deactivateUser sets user as inactive', function () {
    $this->repository->shouldReceive('setUserStatus')
        ->once()
        ->with(1, false);

    $this->service->deactivateUser(1);

    expect(true)->toBeTrue();
});

test('activateUser sets user as active', function () {
    $this->repository->shouldReceive('setUserStatus')
        ->once()
        ->with(1, true);

    $this->service->activateUser(1);

    expect(true)->toBeTrue();
});
