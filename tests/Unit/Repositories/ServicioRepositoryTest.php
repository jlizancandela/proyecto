<?php

/**
 * Unit tests for ServicioRepository
 */

use Servicios\Infrastructure\ServicioRepository;
use Servicios\Domain\Servicio;

beforeEach(function () {
    $this->db = Mockery::mock(PDO::class);
    $this->repository = new ServicioRepository($this->db);
});

afterEach(function () {
    Mockery::close();
});

test('getAllServicios returns all services when no filter', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('query')
        ->once()
        ->with(Mockery::pattern('/SELECT \* FROM SERVICIO.*ORDER BY nombre_servicio/is'))
        ->andReturn($stmt);

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
                'descripcion' => 'Tinte de pelo',
                'activo' => 1
            ],
            false
        );

    $result = $this->repository->getAllServicios();

    expect($result)->toBeArray();
    expect(count($result))->toBe(2);
    expect($result[0])->toBeInstanceOf(Servicio::class);
});

test('getAllServicios filters by active status', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('query')
        ->once()
        ->with(Mockery::pattern('/WHERE activo = 1/'))
        ->andReturn($stmt);

    $stmt->shouldReceive('fetch')
        ->once()
        ->andReturn(false);

    $result = $this->repository->getAllServicios(true);

    expect($result)->toBeArray();
});

test('getServicioById returns service when found', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/SELECT \* FROM SERVICIO WHERE id_servicio/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with(['id' => 1])
        ->andReturn(true);

    $stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturn([
            'id_servicio' => 1,
            'nombre_servicio' => 'Corte',
            'duracion_minutos' => 30,
            'precio' => 15.00,
            'descripcion' => 'Corte de pelo',
            'activo' => 1
        ]);

    $result = $this->repository->getServicioById(1);

    expect($result)->toBeInstanceOf(Servicio::class);
});

test('getServicioById returns null when not found', function () {
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

    $result = $this->repository->getServicioById(999);

    expect($result)->toBeNull();
});

test('save inserts new service and returns ID', function () {
    $stmt = Mockery::mock(PDOStatement::class);
    $servicio = Mockery::mock(Servicio::class);

    $servicio->shouldReceive('getNombreServicio')->andReturn('Corte');
    $servicio->shouldReceive('getDuracionMinutos')->andReturn(30);
    $servicio->shouldReceive('getPrecio')->andReturn(15.00);
    $servicio->shouldReceive('getDescripcion')->andReturn('Corte de pelo');
    $servicio->shouldReceive('isActivo')->andReturn(true);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/INSERT INTO SERVICIO/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->andReturn(true);

    $this->db->shouldReceive('lastInsertId')
        ->once()
        ->andReturn('1');

    $result = $this->repository->save($servicio);

    expect($result)->toBe(1);
});

test('update modifies existing service', function () {
    $stmt = Mockery::mock(PDOStatement::class);
    $servicio = Mockery::mock(Servicio::class);

    $servicio->shouldReceive('getIdServicio')->andReturn(1);
    $servicio->shouldReceive('getNombreServicio')->andReturn('Corte Updated');
    $servicio->shouldReceive('getDuracionMinutos')->andReturn(45);
    $servicio->shouldReceive('getPrecio')->andReturn(20.00);
    $servicio->shouldReceive('getDescripcion')->andReturn('Updated');
    $servicio->shouldReceive('isActivo')->andReturn(true);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/UPDATE SERVICIO/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->andReturn(true);

    $result = $this->repository->update($servicio);

    expect($result)->toBeTrue();
});

test('deactivate sets service as inactive', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/UPDATE SERVICIO SET activo = 0/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with(['id' => 1])
        ->andReturn(true);

    $result = $this->repository->deactivate(1);

    expect($result)->toBeTrue();
});

test('activate sets service as active', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->with(Mockery::pattern('/UPDATE SERVICIO SET activo = 1/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->with(['id' => 1])
        ->andReturn(true);

    $result = $this->repository->activate(1);

    expect($result)->toBeTrue();
});

test('getTotalCount returns total number of services', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('query')
        ->once()
        ->with(Mockery::pattern('/SELECT COUNT/i'))
        ->andReturn($stmt);

    $stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturn(['count' => 5]);

    $result = $this->repository->getTotalCount();

    expect($result)->toBe(5);
});
