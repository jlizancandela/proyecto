<?php

/**
 * Tests for EspecialistaRepository focusing on key methods.
 */

use Especialistas\Infrastructure\EspecialistaRepository;

beforeEach(function () {
    $this->pdo = Mockery::mock(PDO::class);
    $this->stmt = Mockery::mock(PDOStatement::class);
    $this->repository = new EspecialistaRepository($this->pdo);
});

test('getAllEspecialistasWithUserData returns active specialists', function () {
    $this->pdo->shouldReceive('query')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'WHERE u.activo = 1')
                && str_contains($sql, 'ORDER BY u.nombre, u.apellidos');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('fetchAll')
        ->with(PDO::FETCH_ASSOC)
        ->andReturn([
            ['id' => 1, 'nombre' => 'Juan', 'apellidos' => 'Pérez'],
            ['id' => 2, 'nombre' => 'María', 'apellidos' => 'García']
        ]);

    $result = $this->repository->getAllEspecialistasWithUserData();

    expect($result)->toBeArray();
    expect($result)->toHaveCount(2);
    expect($result[0]['nombre'])->toBe('Juan');
});

test('getEspecialistaIdByUserId returns specialist ID', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'SELECT id_especialista FROM ESPECIALISTA')
                && str_contains($sql, 'WHERE id_usuario = :id_usuario');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')->with(['id_usuario' => 123]);
    $this->stmt->shouldReceive('fetch')
        ->with(PDO::FETCH_ASSOC)
        ->andReturn(['id_especialista' => 456]);

    $result = $this->repository->getEspecialistaIdByUserId(123);

    expect($result)->toBe(456);
});

test('getEspecialistaIdByUserId returns null when not found', function () {
    $this->pdo->shouldReceive('prepare')->andReturn($this->stmt);
    $this->stmt->shouldReceive('execute');
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $result = $this->repository->getEspecialistaIdByUserId(999);

    expect($result)->toBeNull();
});

test('createBasicEspecialista inserts and returns ID', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'INSERT INTO ESPECIALISTA');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')
        ->with([
            'id_usuario' => 123,
            'descripcion' => 'Test description',
            'foto_url' => 'test.jpg'
        ]);

    $this->pdo->shouldReceive('lastInsertId')->andReturn('789');

    $result = $this->repository->createBasicEspecialista(123, 'test.jpg', 'Test description');

    expect($result)->toBe(789);
});

test('updateEspecialistaPhoto updates photo URL', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'UPDATE ESPECIALISTA SET foto_url = :foto_url')
                && str_contains($sql, 'WHERE id_especialista = :id');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')
        ->with([
            'foto_url' => 'new-photo.jpg',
            'id' => 123
        ]);

    $this->repository->updateEspecialistaPhoto(123, 'new-photo.jpg');

    // If no exception is thrown, the test passes
    expect(true)->toBeTrue();
});

test('updateEspecialistaDescription updates description', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'UPDATE ESPECIALISTA SET descripcion = :descripcion')
                && str_contains($sql, 'WHERE id_especialista = :id');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')
        ->with([
            'descripcion' => 'New description',
            'id' => 123
        ]);

    $this->repository->updateEspecialistaDescription(123, 'New description');

    expect(true)->toBeTrue();
});

test('getEspecialistaDataByUserId returns specialist data', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'SELECT id_especialista, descripcion, foto_url')
                && str_contains($sql, 'WHERE id_usuario = :id_usuario');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')->with(['id_usuario' => 123]);
    $this->stmt->shouldReceive('fetch')
        ->with(PDO::FETCH_ASSOC)
        ->andReturn([
            'id_especialista' => 456,
            'descripcion' => 'Test specialist',
            'foto_url' => 'photo.jpg'
        ]);

    $result = $this->repository->getEspecialistaDataByUserId(123);

    expect($result)->toBeArray();
    expect($result['id_especialista'])->toBe(456);
    expect($result['descripcion'])->toBe('Test specialist');
});

test('countEspecialistasDisponibles counts active specialists for service', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'COUNT(DISTINCT e.id_especialista)')
                && str_contains($sql, 'WHERE es.id_servicio = :id_servicio')
                && str_contains($sql, 'AND u.activo = 1');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')->with(['id_servicio' => 5]);
    $this->stmt->shouldReceive('fetch')
        ->with(PDO::FETCH_ASSOC)
        ->andReturn(['total' => 3]);

    $result = $this->repository->countEspecialistasDisponibles(5);

    expect($result)->toBe(3);
});
