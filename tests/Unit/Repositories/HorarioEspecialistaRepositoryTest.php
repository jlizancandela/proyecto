<?php

/**
 * Unit tests for HorarioEspecialistaRepository - Simplified
 */

use Especialistas\Infrastructure\HorarioEspecialistaRepository;
use Especialistas\Domain\HorarioEspecialista;

beforeEach(function () {
    $this->db = Mockery::mock(PDO::class);
    $this->repository = new HorarioEspecialistaRepository($this->db);
});

afterEach(function () {
    Mockery::close();
});

test('getHorarioById returns schedule when found', function () {
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
        ->andReturn([
            'id_horario' => 1,
            'id_especialista' => 1,
            'dia_semana' => 1,
            'hora_inicio' => '09:00:00',
            'hora_fin' => '17:00:00'
        ]);

    $result = $this->repository->getHorarioById(1);

    expect($result)->toBeInstanceOf(HorarioEspecialista::class);
});

test('getHorarioById returns null when not found', function () {
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

    $result = $this->repository->getHorarioById(999);

    expect($result)->toBeNull();
});

test('getHorariosByEspecialista returns schedules array', function () {
    $stmt = Mockery::mock(PDOStatement::class);

    $this->db->shouldReceive('prepare')
        ->once()
        ->andReturn($stmt);

    $stmt->shouldReceive('execute')
        ->once()
        ->andReturn(true);

    $stmt->shouldReceive('fetch')
        ->times(3)
        ->with(PDO::FETCH_ASSOC)
        ->andReturn(
            [
                'id_horario' => 1,
                'id_especialista' => 1,
                'dia_semana' => 1,
                'hora_inicio' => '09:00:00',
                'hora_fin' => '17:00:00'
            ],
            [
                'id_horario' => 2,
                'id_especialista' => 1,
                'dia_semana' => 2,
                'hora_inicio' => '09:00:00',
                'hora_fin' => '17:00:00'
            ],
            false
        );

    $result = $this->repository->getHorariosByEspecialista(1);

    expect($result)->toBeArray()
        ->toHaveCount(2);
    expect($result[0])->toBeInstanceOf(HorarioEspecialista::class);
});
