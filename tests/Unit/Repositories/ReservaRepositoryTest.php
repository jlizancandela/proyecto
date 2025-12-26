<?php

use Reservas\Infrastructure\ReservaRepository;
use Reservas\Application\ReservaCompletaDTO;

beforeEach(function () {
    $this->pdo = Mockery::mock(PDO::class);
    $this->stmt = Mockery::mock(PDOStatement::class);
    $this->repository = new ReservaRepository($this->pdo);
});

test('findAllFiltered applies client filter correctly', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'WHERE 1=1')
                && str_contains($sql, 'AND r.id_cliente = :id_cliente');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':id_cliente', 123);
    $this->stmt->shouldReceive('bindValue')->with(':limit', 50, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindValue')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $results = $this->repository->findAllFiltered(['cliente' => 123]);
    expect($results)->toBeArray();
});

test('findAllFiltered applies date range filters correctly', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'fecha_reserva >= :fecha_desde')
                && str_contains($sql, 'fecha_reserva <= :fecha_hasta');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':fecha_desde', '2024-01-01');
    $this->stmt->shouldReceive('bindValue')->with(':fecha_hasta', '2024-12-31');
    $this->stmt->shouldReceive('bindValue')->with(':limit', 50, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindValue')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $this->repository->findAllFiltered([
        'fecha_desde' => '2024-01-01',
        'fecha_hasta' => '2024-12-31'
    ]);
});

test('findByUserIdWithFilters applies status filter', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'WHERE r.id_cliente = :userId')
                && str_contains($sql, 'AND r.estado = :estado');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':userId', 1);
    $this->stmt->shouldReceive('bindValue')->with(':estado', 'confirmada');
    $this->stmt->shouldReceive('bindValue')->with(':limit', 50, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindValue')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $this->repository->findByUserIdWithFilters(1, 50, 0, null, null, 'confirmada');
});
