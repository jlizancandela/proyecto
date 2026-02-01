<?php

/**
 * Unit tests for ServicioService
 */

use Servicios\Application\ServicioService;
use Servicios\Infrastructure\ServicioRepository;
use Servicios\Domain\Servicio;

beforeEach(function () {
    $this->repository = Mockery::mock(ServicioRepository::class);
    $this->service = new ServicioService($this->repository);
});

afterEach(function () {
    Mockery::close();
});

test('createService creates service successfully', function () {
    $data = [
        'nombre_servicio' => 'Corte',
        'duracion_minutos' => 30,
        'descripcion' => 'Corte de pelo',
        'precio' => 15.00
    ];

    $this->repository->shouldReceive('save')
        ->once()
        ->andReturn(1);

    $result = $this->service->createService($data);

    expect($result)->toBeInstanceOf(Servicio::class);
});

test('createService throws exception for invalid data', function () {
    $data = [
        'nombre_servicio' => 'AB', // Too short
        'duracion_minutos' => 30,
        'descripcion' => 'Test',
        'precio' => 15.00
    ];

    $this->service->createService($data);
})->throws(\Exception::class);

test('updateService updates existing service', function () {
    $data = [
        'nombre_servicio' => 'Corte Updated',
        'duracion_minutos' => 45,
        'descripcion' => 'Updated description',
        'precio' => 20.00
    ];

    $mockServicio = Mockery::mock(Servicio::class);
    $mockServicio->shouldReceive('isActivo')->andReturn(true);

    $this->repository->shouldReceive('getServicioById')
        ->once()
        ->with(1)
        ->andReturn($mockServicio);

    $this->repository->shouldReceive('update')
        ->once()
        ->andReturn(true);

    $result = $this->service->updateService(1, $data);

    expect($result)->toBeInstanceOf(Servicio::class);
});

test('updateService throws exception when service not found', function () {
    $data = [
        'nombre_servicio' => 'Test',
        'duracion_minutos' => 30,
        'descripcion' => 'Test',
        'precio' => 15.00
    ];

    $this->repository->shouldReceive('getServicioById')
        ->once()
        ->with(999)
        ->andReturn(null);

    $this->service->updateService(999, $data);
})->throws(\Exception::class, 'Service not found');

test('deactivateService deactivates successfully', function () {
    $mockServicio = Mockery::mock(Servicio::class);

    $this->repository->shouldReceive('getServicioById')
        ->once()
        ->with(1)
        ->andReturn($mockServicio);

    $this->repository->shouldReceive('deactivate')
        ->once()
        ->with(1)
        ->andReturn(true);

    $this->service->deactivateService(1);
});

test('deactivateService throws exception when service not found', function () {
    $this->repository->shouldReceive('getServicioById')
        ->once()
        ->with(999)
        ->andReturn(null);

    $this->service->deactivateService(999);
})->throws(\Exception::class, 'Service not found');

test('activateService activates successfully', function () {
    $mockServicio = Mockery::mock(Servicio::class);

    $this->repository->shouldReceive('getServicioById')
        ->once()
        ->with(1)
        ->andReturn($mockServicio);

    $this->repository->shouldReceive('activate')
        ->once()
        ->with(1)
        ->andReturn(true);

    $this->service->activateService(1);
});

test('getServiceById returns service when found', function () {
    $mockServicio = Mockery::mock(Servicio::class);

    $this->repository->shouldReceive('getServicioById')
        ->once()
        ->with(1)
        ->andReturn($mockServicio);

    $result = $this->service->getServiceById(1);

    expect($result)->toBeInstanceOf(Servicio::class);
});

test('getServiceById returns null when not found', function () {
    $this->repository->shouldReceive('getServicioById')
        ->once()
        ->with(999)
        ->andReturn(null);

    $result = $this->service->getServiceById(999);

    expect($result)->toBeNull();
});

test('getAllServices returns all services', function () {
    $mockServicio = Mockery::mock(Servicio::class);

    $this->repository->shouldReceive('getAllServicios')
        ->once()
        ->with(null)
        ->andReturn([$mockServicio, $mockServicio]);

    $result = $this->service->getAllServices();

    expect($result)->toBeArray()
        ->toHaveCount(2);
});

test('getAllServices filters by active status', function () {
    $mockServicio = Mockery::mock(Servicio::class);

    $this->repository->shouldReceive('getAllServicios')
        ->once()
        ->with(true)
        ->andReturn([$mockServicio]);

    $result = $this->service->getAllServices(true);

    expect($result)->toBeArray()
        ->toHaveCount(1);
});
