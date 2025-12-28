<?php

/**
 * Unit tests for PasswordResetRepository
 */

use Usuarios\Infrastructure\PasswordResetRepository;
use Usuarios\Domain\Usuario;

beforeEach(function () {
    $this->db = Mockery::mock(PDO::class);
    $this->repository = new PasswordResetRepository($this->db);
});

afterEach(function () {
    Mockery::close();
});

test('savePasswordResetToken saves token successfully', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/UPDATE usuarios/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with([
            ':token' => 'test-token',
            ':expiration' => '2024-12-31 23:59:59',
            ':userId' => 1
        ])
        ->andReturn(true);

    $this->repository->savePasswordResetToken(1, 'test-token', '2024-12-31 23:59:59');

    expect(true)->toBeTrue();
});

test('getUserByResetToken returns user when token is valid', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/SELECT \* FROM usuarios.*WHERE reset_token/is'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with([':token' => 'valid-token'])
        ->andReturn(true);

    $stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturn([
            'id_usuario' => 1,
            'nombre' => 'Test',
            'apellidos' => 'User',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password', PASSWORD_DEFAULT),
            'rol' => 'Cliente',
            'activo' => 1,
            'telefono' => '123456789',
            'fecha_registro' => '2024-01-01'
        ]);

    $result = $this->repository->getUserByResetToken('valid-token');

    expect($result)->toBeInstanceOf(Usuario::class);
});

test('getUserByResetToken returns null when token not found', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->andReturn(true);

    $stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturn(false);

    $result = $this->repository->getUserByResetToken('invalid-token');

    expect($result)->toBeNull();
});

test('clearResetToken clears token successfully', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/UPDATE usuarios.*SET reset_token = NULL/is'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with([':userId' => 1])
        ->andReturn(true);

    $this->repository->clearResetToken(1);

    expect(true)->toBeTrue();
});
