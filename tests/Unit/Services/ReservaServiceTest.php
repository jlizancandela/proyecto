<?php

/**
 * Unit tests for ReservaService business logic.
 */

use Reservas\Application\ReservaService;
use Reservas\Infrastructure\ReservaRepository;

beforeEach(function () {
    $this->repository = Mockery::mock(ReservaRepository::class)->shouldIgnoreMissing();
    $this->service = new ReservaService($this->repository);
});

afterEach(function () {
    Mockery::close();
});

test('createReserva creates booking successfully', function () {
    $data = [
        'id_cliente' => 1,
        'especialista_id' => 2,
        'servicio_id' => 3,
        'fecha' => date('Y-m-d', strtotime('+1 day')),
        'hora' => '10:00:00',
        'duracion' => 60
    ];

    $this->repository->shouldReceive('findConflicts')->andReturn(false);
    $this->repository->shouldReceive('findClientConflicts')->andReturn(false);
    $this->repository->shouldReceive('findAllFiltered')->andReturn([]);
    $this->repository->shouldReceive('addReserva')->andReturn(123);

    $result = $this->service->createReserva($data);

    expect($result)->toBe(123);
});

test('createReserva throws exception for past date', function () {
    $data = [
        'id_cliente' => 1,
        'especialista_id' => 2,
        'servicio_id' => 3,
        'fecha' => date('Y-m-d', strtotime('-1 day')),
        'hora' => '10:00:00',
        'duracion' => 60
    ];

    $this->service->createReserva($data);
})->throws(\RuntimeException::class, 'La fecha de reserva debe ser futura');

test('createReserva throws exception for specialist conflict', function () {
    $data = [
        'id_cliente' => 1,
        'especialista_id' => 2,
        'servicio_id' => 3,
        'fecha' => date('Y-m-d', strtotime('+1 day')),
        'hora' => '10:00:00',
        'duracion' => 60
    ];

    $this->repository->shouldReceive('findConflicts')->andReturn(true);

    $this->service->createReserva($data);
})->throws(\RuntimeException::class, 'El horario seleccionado ya no está disponible');

test('createReserva throws exception for client conflict', function () {
    $data = [
        'id_cliente' => 1,
        'especialista_id' => 2,
        'servicio_id' => 3,
        'fecha' => date('Y-m-d', strtotime('+1 day')),
        'hora' => '10:00:00',
        'duracion' => 60
    ];

    $this->repository->shouldReceive('findConflicts')->andReturn(false);
    $this->repository->shouldReceive('findClientConflicts')->andReturn(true);

    $this->service->createReserva($data);
})->throws(\RuntimeException::class, 'Ya tienes otra reserva en ese horario');

test('createReserva throws exception for weekly limit exceeded', function () {
    $data = [
        'id_cliente' => 1,
        'especialista_id' => 2,
        'servicio_id' => 3,
        'fecha' => date('Y-m-d', strtotime('+1 day')),
        'hora' => '10:00:00',
        'duracion' => 60
    ];

    $mockReserva = new stdClass();
    $mockReserva->estado = 'Pendiente';

    $this->repository->shouldReceive('findConflicts')->andReturn(false);
    $this->repository->shouldReceive('findClientConflicts')->andReturn(false);
    $this->repository->shouldReceive('findAllFiltered')->andReturn([$mockReserva]);

    $this->service->createReserva($data);
})->throws(\RuntimeException::class, 'Ya tienes una reserva de este servicio en esta semana');


test('updateReserva throws exception if booking not found', function () {
    $this->repository->shouldReceive('findById')->with(999)->andReturn(null);

    $this->service->updateReserva(999, []);
})->throws(\RuntimeException::class, 'Reserva no encontrada');

test('updateReservaStatus updates status successfully', function () {
    $this->repository->shouldReceive('updateStatus')
        ->with(123, 'Confirmada')
        ->andReturn(true);

    $result = $this->service->updateReservaStatus(123, 'Confirmada');

    expect($result)->toBeTrue();
});

test('updateReservaStatus throws exception for invalid status', function () {
    $this->service->updateReservaStatus(123, 'InvalidStatus');
})->throws(\RuntimeException::class, 'Estado de reserva inválido');

test('deleteReserva deletes booking successfully', function () {
    $this->repository->shouldReceive('deleteReserva')->with(123)->andReturn(true);

    $result = $this->service->deleteReserva(123);

    expect($result)->toBeTrue();
});

test('getReservasByClient returns bookings', function () {
    $mockBookings = [new stdClass(), new stdClass()];

    $this->repository->shouldReceive('findByClient')
        ->with(1, 50, 0)
        ->andReturn($mockBookings);

    $result = $this->service->getReservasByClient(1);

    expect($result)->toBe($mockBookings);
    expect(count($result))->toBe(2);
});


test('getReservaById returns null if not found', function () {
    $this->repository->shouldReceive('getReservaCompletaById')
        ->with(999)
        ->andReturn(null);

    $result = $this->service->getReservaById(999);

    expect($result)->toBeNull();
});

test('getAllReservasByFilter returns filtered bookings', function () {
    $mockBookings = [new stdClass()];

    $this->repository->shouldReceive('findByUserIdWithFilters')
        ->with(1, 50, 0, '2024-01-01', '2024-12-31', 'Pendiente')
        ->andReturn($mockBookings);

    $result = $this->service->getAllReservasByFilter(
        1,
        50,
        0,
        '2024-01-01',
        '2024-12-31',
        'Pendiente'
    );

    expect($result)->toBe($mockBookings);
});

test('countReservasByFilter returns count', function () {
    $this->repository->shouldReceive('countByUserIdWithFilters')
        ->with(1, null, null, null)
        ->andReturn(5);

    $result = $this->service->countReservasByFilter(1);

    expect($result)->toBe(5);
});


test('getAllReservasWithFilters returns all bookings with filters', function () {
    $filters = ['estado' => 'Pendiente'];
    $mockBookings = [new stdClass(), new stdClass()];

    $this->repository->shouldReceive('findAllFiltered')
        ->with($filters, 50, 0)
        ->andReturn($mockBookings);

    $result = $this->service->getAllReservasWithFilters($filters);

    expect($result)->toBe($mockBookings);
});

test('countAllReservasWithFilters returns total count', function () {
    $filters = ['estado' => 'Confirmada'];

    $this->repository->shouldReceive('countAllFiltered')
        ->with($filters)
        ->andReturn(10);

    $result = $this->service->countAllReservasWithFilters($filters);

    expect($result)->toBe(10);
});
