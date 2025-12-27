<?php

/**
 * Tests for UserRepository focusing on filtering, sorting, and the new buildOrderBy method.
 */

use Usuarios\Infrastructure\UserRepository;

beforeEach(function () {
    $this->pdo = Mockery::mock(PDO::class);
    $this->stmt = Mockery::mock(PDOStatement::class);
    $this->repository = new UserRepository($this->pdo);
});

test('getAllUsers applies default sorting', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'ORDER BY id_usuario DESC');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindParam')->with(':limit', 10, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindParam')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $results = $this->repository->getAllUsers();
    expect($results)->toBeArray();
});

test('getAllUsers applies custom sorting by nombre', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'ORDER BY nombre ASC, apellidos ASC');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindParam')->with(':limit', 10, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindParam')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $this->repository->getAllUsers(10, 0, 'nombre', 'asc');
});

test('getAllUsers applies descending sort order', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'ORDER BY email DESC');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindParam')->with(':limit', 10, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindParam')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $this->repository->getAllUsers(10, 0, 'email', 'desc');
});

test('searchUsers applies search filter correctly', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'WHERE nombre LIKE :search1')
                && str_contains($sql, 'OR apellidos LIKE :search2')
                && str_contains($sql, 'OR email LIKE :search3')
                && str_contains($sql, 'OR telefono LIKE :search4');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':search1', '%John%');
    $this->stmt->shouldReceive('bindValue')->with(':search2', '%John%');
    $this->stmt->shouldReceive('bindValue')->with(':search3', '%John%');
    $this->stmt->shouldReceive('bindValue')->with(':search4', '%John%');
    $this->stmt->shouldReceive('bindValue')->with(':limit', 10, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindValue')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $this->repository->searchUsers('John');
});

test('getUsersByRole filters by role correctly', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'WHERE rol = :rol');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':rol', 'Cliente');
    $this->stmt->shouldReceive('bindValue')->with(':limit', 10, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindValue')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $this->repository->getUsersByRole('Cliente');
});

test('findAllFiltered applies role filter', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'WHERE 1=1')
                && str_contains($sql, 'AND rol = :rol');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':rol', 'Especialista');
    $this->stmt->shouldReceive('bindValue')->with(':limit', 50, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindValue')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $this->repository->findAllFiltered(['rol' => 'Especialista']);
});

test('findAllFiltered applies search and role filters together', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'WHERE 1=1')
                && str_contains($sql, 'AND (nombre LIKE :search1')
                && str_contains($sql, 'AND rol = :rol');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':search1', '%Maria%');
    $this->stmt->shouldReceive('bindValue')->with(':search2', '%Maria%');
    $this->stmt->shouldReceive('bindValue')->with(':search3', '%Maria%');
    $this->stmt->shouldReceive('bindValue')->with(':search4', '%Maria%');
    $this->stmt->shouldReceive('bindValue')->with(':rol', 'Cliente');
    $this->stmt->shouldReceive('bindValue')->with(':limit', 50, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindValue')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $this->repository->findAllFiltered([
        'search' => 'Maria',
        'rol' => 'Cliente'
    ]);
});

test('findAllFiltered applies estado filter', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'AND activo = :estado');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':estado', 1);
    $this->stmt->shouldReceive('bindValue')->with(':limit', 50, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindValue')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $this->repository->findAllFiltered(['estado' => '1']);
});

test('findAllFiltered applies custom sorting', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'ORDER BY fecha_registro DESC');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':limit', 50, PDO::PARAM_INT);
    $this->stmt->shouldReceive('bindValue')->with(':offset', 0, PDO::PARAM_INT);
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(false);

    $this->repository->findAllFiltered([
        'sort' => 'fecha',
        'order' => 'desc'
    ]);
});

test('getTotalUsers counts all users', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with('SELECT COUNT(*) as total FROM USUARIO')
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(['total' => 42]);

    $count = $this->repository->getTotalUsers();
    expect($count)->toBe(42);
});

test('getTotalUsersByRole counts users by role', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'SELECT COUNT(*) as total FROM USUARIO WHERE rol = :rol');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':rol', 'Admin');
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(['total' => 3]);

    $count = $this->repository->getTotalUsersByRole('Admin');
    expect($count)->toBe(3);
});

test('countAllFiltered counts with filters', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with(Mockery::on(function ($sql) {
            return str_contains($sql, 'SELECT COUNT(*) as total FROM USUARIO WHERE 1=1')
                && str_contains($sql, 'AND rol = :rol');
        }))
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('bindValue')->with(':rol', 'Especialista');
    $this->stmt->shouldReceive('execute')->once();
    $this->stmt->shouldReceive('fetch')->andReturn(['total' => 15]);

    $count = $this->repository->countAllFiltered(['rol' => 'Especialista']);
    expect($count)->toBe(15);
});
