<?php

/**
 * Unit tests for EspecialistaService
 */

use Especialistas\Application\EspecialistaService;
use Especialistas\Infrastructure\EspecialistaRepository;
use Especialistas\Application\EspecialistaUsuarioDTO;

beforeEach(function () {
    $this->repository = Mockery::mock(EspecialistaRepository::class);
    $this->service = new EspecialistaService($this->repository);
});

afterEach(function () {
    Mockery::close();
});

test('getAllEspecialistas returns all specialists with user data', function () {
    $mockDTO = Mockery::mock(EspecialistaUsuarioDTO::class);

    $this->repository->shouldReceive('getAllEspecialistasConUsuario')
        ->once()
        ->andReturn([$mockDTO, $mockDTO]);

    $result = $this->service->getAllEspecialistas();

    expect($result)->toBeArray();
    expect(count($result))->toBe(2);
});

test('getAllEspecialistas returns empty array when no specialists', function () {
    $this->repository->shouldReceive('getAllEspecialistasConUsuario')
        ->once()
        ->andReturn([]);

    $result = $this->service->getAllEspecialistas();

    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});
