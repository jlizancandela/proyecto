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

describe('disponibilidad con SQLite en memoria', function () {
    beforeEach(function () {
        $this->sqlitePdo = new PDO('sqlite::memory:');
        $this->sqlitePdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->sqlitePdo->exec("
            CREATE TABLE USUARIO (
                id_usuario INTEGER PRIMARY KEY,
                nombre TEXT,
                apellidos TEXT,
                email TEXT,
                telefono TEXT,
                activo INTEGER DEFAULT 1
            );
            CREATE TABLE SERVICIO (
                id_servicio INTEGER PRIMARY KEY,
                duracion_minutos INTEGER
            );
            CREATE TABLE ESPECIALISTA (
                id_especialista INTEGER PRIMARY KEY,
                id_usuario INTEGER,
                descripcion TEXT,
                foto_url TEXT
            );
            CREATE TABLE ESPECIALISTA_SERVICIO (
                id_especialista INTEGER,
                id_servicio INTEGER
            );
            CREATE TABLE HORARIO_ESPECIALISTA (
                id_especialista INTEGER,
                dia_semana INTEGER,
                hora_inicio TEXT,
                hora_fin TEXT
            );
            CREATE TABLE RESERVA (
                id_especialista INTEGER,
                fecha_reserva TEXT,
                hora_inicio TEXT,
                hora_fin TEXT,
                estado TEXT
            );
        ");

        $this->sqlitePdo->exec("INSERT INTO SERVICIO (id_servicio, duracion_minutos) VALUES (1, 60)");
        $this->sqliteRepo = new EspecialistaRepository($this->sqlitePdo);
    });

    test('countEspecialistasDisponibles counts only active specialists with available slots', function () {
        $fecha = '2030-12-09'; // Monday (dia_semana = 1)

        $this->sqlitePdo->exec("
            INSERT INTO USUARIO (id_usuario, nombre, apellidos, email, telefono, activo) VALUES
                (1, 'Elena', 'Ruiz', 'elena@test.com', '600000001', 1),
                (2, 'Marcos', 'Sanz', 'marcos@test.com', '600000002', 1),
                (3, 'Inactivo', 'Uno', 'inactivo@test.com', '600000003', 0);
            INSERT INTO ESPECIALISTA (id_especialista, id_usuario, descripcion, foto_url) VALUES
                (1, 1, 'Colorista', '/a.jpg'),
                (2, 2, 'Barbero', '/b.jpg'),
                (3, 3, 'Inactivo', '/c.jpg');
            INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio) VALUES (1, 1), (2, 1), (3, 1);
            INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES
                (1, 1, '09:00:00', '11:00:00'),
                (2, 1, '09:00:00', '11:00:00'),
                (3, 1, '09:00:00', '11:00:00');
            INSERT INTO RESERVA (id_especialista, fecha_reserva, hora_inicio, hora_fin, estado) VALUES
                (1, '$fecha', '09:00:00', '10:00:00', 'Confirmada'),
                (2, '$fecha', '09:00:00', '11:00:00', 'Confirmada');
        ");

        expect($this->sqliteRepo->countEspecialistasDisponibles(1, $fecha))->toBe(1);
    });

    test('getEspecialistasDisponibles excludes specialists without available slots and paginates filtered results', function () {
        $fecha = '2030-12-09'; // Monday (dia_semana = 1)

        $this->sqlitePdo->exec("
            INSERT INTO USUARIO (id_usuario, nombre, apellidos, email, telefono, activo) VALUES
                (1, 'Completo', 'Ocupado', 'uno@test.com', '600000001', 1),
                (2, 'Parcial', 'Libre', 'dos@test.com', '600000002', 1),
                (3, 'Cancelado', 'Libre', 'tres@test.com', '600000003', 1);
            INSERT INTO ESPECIALISTA (id_especialista, id_usuario, descripcion, foto_url) VALUES
                (1, 1, 'Sin huecos', '/1.jpg'),
                (2, 2, 'Un hueco', '/2.jpg'),
                (3, 3, 'Todo libre', '/3.jpg');
            INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio) VALUES (1, 1), (2, 1), (3, 1);
            INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES
                (1, 1, '09:00:00', '11:00:00'),
                (2, 1, '09:00:00', '11:00:00'),
                (3, 1, '09:00:00', '11:00:00');
            INSERT INTO RESERVA (id_especialista, fecha_reserva, hora_inicio, hora_fin, estado) VALUES
                (1, '$fecha', '09:00:00', '11:00:00', 'Confirmada'),
                (2, '$fecha', '09:00:00', '10:00:00', 'Confirmada'),
                (3, '$fecha', '09:00:00', '11:00:00', 'Cancelada');
        ");

        $todos = $this->sqliteRepo->getEspecialistasDisponibles(1, $fecha);
        expect($todos)->toHaveCount(2);
        expect(array_column($todos, 'id_especialista'))->toEqual([2, 3]);
        expect($todos[0]['horas_disponibles'])->toBe(['10:00']);
        expect($todos[1]['horas_disponibles'])->toBe(['09:00', '09:30', '10:00']);

        $pagina2 = $this->sqliteRepo->getEspecialistasDisponibles(1, $fecha, 1, 1);
        expect($pagina2)->toHaveCount(1);
        expect($pagina2[0]['id_especialista'])->toBe(3);
    });

    test('getEspecialistasDisponibles excludes past slots when booking for today', function () {
        $hoy = date('Y-m-d');
        $diaSemanaHoy = date('N', strtotime($hoy)) === '7' ? 0 : (int)date('N', strtotime($hoy));

        $this->sqlitePdo->exec("
            INSERT INTO USUARIO (id_usuario, nombre, apellidos, email, telefono, activo) VALUES
                (1, 'Turno', 'Pasado', 'hoy@test.com', '600000001', 1);
            INSERT INTO ESPECIALISTA (id_especialista, id_usuario, descripcion, foto_url) VALUES
                (1, 1, 'Solo madrugada', '/1.jpg');
            INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio) VALUES (1, 1);
            INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES
                (1, $diaSemanaHoy, '00:00:00', '01:00:00');
        ");

        expect($this->sqliteRepo->getEspecialistasDisponibles(1, $hoy))->toBe([]);
        expect($this->sqliteRepo->countEspecialistasDisponibles(1, $hoy))->toBe(0);
    });
});
