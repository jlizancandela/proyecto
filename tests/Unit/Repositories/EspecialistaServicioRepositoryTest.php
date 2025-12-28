<?php

/**
 * Unit tests for EspecialistaServicioRepository
 */

use Especialistas\Infrastructure\EspecialistaServicioRepository;
use Especialistas\Domain\EspecialistaServicio;
use Servicios\Domain\Servicio;
use Especialistas\Domain\Especialista;

beforeEach(function () {
    $this->db = Mockery::mock(PDO::class);
    $this->repository = new EspecialistaServicioRepository($this->db);
});

afterEach(function () {
    Mockery::close();
});

test('getEspecialistaServicio returns link when found', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/SELECT \* FROM ESPECIALISTA_SERVICIO/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with([
            'id_especialista' => 1,
            'id_servicio' => 1
        ])
        ->andReturn(true);

    $stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturn([
            'id_especialista' => 1,
            'id_servicio' => 1
        ]);

    $result = $this->repository->getEspecialistaServicio(1, 1);

    expect($result)->toBeInstanceOf(EspecialistaServicio::class);
});

test('getEspecialistaServicio returns null when not found', function () {
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

    $result = $this->repository->getEspecialistaServicio(1, 999);

    expect($result)->toBeNull();
});

test('getServiciosForEspecialista returns services array', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/SELECT s\.\* FROM SERVICIO/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with(['id_especialista' => 1])
        ->andReturn(true);

    $stmt->shouldReceive('fetch')
        ->times(3)
        ->with(PDO::FETCH_ASSOC)
        ->andReturn(
            [
                'id_servicio' => 1,
                'nombre_servicio' => 'Corte',
                'duracion_minutos' => 30,
                'precio' => 15.00,
                'descripcion' => 'Corte de pelo',
                'activo' => 1
            ],
            [
                'id_servicio' => 2,
                'nombre_servicio' => 'Tinte',
                'duracion_minutos' => 60,
                'precio' => 40.00,
                'descripcion' => 'Tinte',
                'activo' => 1
            ],
            false
        );

    $result = $this->repository->getServiciosForEspecialista(1);

    expect($result)->toBeArray()
        ->toHaveCount(2);
    expect($result[0])->toBeInstanceOf(Servicio::class);
});

test('getEspecialistasForServicio returns specialists array', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/SELECT e\.\* FROM ESPECIALISTA/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with(['id_servicio' => 1])
        ->andReturn(true);

    $stmt->shouldReceive('fetch')
        ->times(2)
        ->with(PDO::FETCH_ASSOC)
        ->andReturn(
            [
                'id_especialista' => 1,
                'id_usuario' => 1,
                'rol' => 'Especialista',
                'nombre' => 'John',
                'apellidos' => 'Doe',
                'email' => 'john@example.com',
                'password_hash' => 'hashed',
                'telefono' => '123456789',
                'fecha_registro' => '2024-01-01',
                'activo' => 1,
                'descripcion' => 'Especialista 1',
                'foto_url' => null
            ],
            false
        );

    $result = $this->repository->getEspecialistasForServicio(1);

    expect($result)->toBeArray()
        ->toHaveCount(1);
    expect($result[0])->toBeInstanceOf(Especialista::class);
});

test('addEspecialistaServicio inserts link successfully', function () {
    $stmt = Mockery::mock(PDOStatement::class);
    $link = Mockery::mock(EspecialistaServicio::class);

    $link->shouldReceive('getIdEspecialista')->andReturn(1);
    $link->shouldReceive('getIdServicio')->andReturn(1);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/INSERT INTO ESPECIALISTA_SERVICIO/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with([
            'id_especialista' => 1,
            'id_servicio' => 1
        ])
        ->andReturn(true);

    $result = $this->repository->addEspecialistaServicio($link);

    expect($result)->toBeNull();
});

test('deleteEspecialistaServicio removes link successfully', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/DELETE FROM ESPECIALISTA_SERVICIO/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with([
            'id_especialista' => 1,
            'id_servicio' => 1
        ])
        ->andReturn(true);

    $result = $this->repository->deleteEspecialistaServicio(1, 1);

    expect($result)->toBeNull();
});

test('deleteAllServiciosForEspecialista removes all links', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/DELETE FROM ESPECIALISTA_SERVICIO WHERE id_especialista/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with(['id_especialista' => 1])
        ->andReturn(true);

    $result = $this->repository->deleteAllServiciosForEspecialista(1);

    expect($result)->toBeNull();
});
