<?php

/**
 * Service layer for managing specialists and their schedules
 */

namespace Especialistas\Application;

use Especialistas\Infrastructure\EspecialistaRepository;
use Especialistas\Infrastructure\EspecialistaServicioRepository;
use Especialistas\Application\EspecialistaUsuarioDTO;
use Especialistas\Domain\EspecialistaServicio;

class EspecialistaService
{
    private EspecialistaRepository $especialistaRepository;
    private EspecialistaServicioRepository $especialistaServicioRepository;

    /**
     * EspecialistaService constructor.
     * @param EspecialistaRepository $especialistaRepository The specialist repository instance.
     * @param EspecialistaServicioRepository $especialistaServicioRepository The specialist-service repository instance.
     */
    public function __construct(
        EspecialistaRepository $especialistaRepository,
        EspecialistaServicioRepository $especialistaServicioRepository
    ) {
        $this->especialistaRepository = $especialistaRepository;
        $this->especialistaServicioRepository = $especialistaServicioRepository;
    }

    /**
     * Retrieves all specialists with their associated user data.
     * @return EspecialistaUsuarioDTO[] An array of EspecialistaUsuarioDTO objects.
     */
    public function getAllEspecialistas(): array
    {
        return $this->especialistaRepository->getAllEspecialistasConUsuario();
    }

    /**
     * Creates a specialist profile, handling avatar upload and services linking.
     *
     * @param int $userId The ID of the user.
     * @param array $data The data containing description and services.
     * @param array|null $file The uploaded file array (optional).
     */
    public function createEspecialistaProfile(int $userId, array $data, ?array $file): void
    {
        $avatarUrl = null;
        if ($file) {
            $avatarUrl = $this->handleAvatarUpload($file);
        }

        $descripcion = $data['descripcion'] ?? null;
        $especialistaId = $this->especialistaRepository->createBasicEspecialista($userId, $avatarUrl, $descripcion);

        if ($especialistaId && !empty($data['servicios'])) {
            foreach ($data['servicios'] as $servicioId) {
                $especialistaServicio = new EspecialistaServicio(
                    $especialistaId,
                    (int) $servicioId
                );
                $this->especialistaServicioRepository->addEspecialistaServicio($especialistaServicio);
            }
        }
    }

    /**
     * Updates a specialist profile, handling avatar upload and services linking.
     *
     * @param int $userId The ID of the user.
     * @param array $data The data containing description and services.
     * @param array|null $file The uploaded file array (optional).
     */
    public function updateEspecialistaProfile(int $userId, array $data, ?array $file): void
    {
        $especialistaId = $this->especialistaRepository->getEspecialistaIdByUserId($userId);

        if (!$especialistaId) {
            $especialistaId = $this->especialistaRepository->createBasicEspecialista($userId);
        }

        if ($especialistaId) {
            if ($file) {
                $avatarUrl = $this->handleAvatarUpload($file);
                if ($avatarUrl) {
                    $this->especialistaRepository->updateEspecialistaPhoto($especialistaId, $avatarUrl);
                }
            }

            if (isset($data['descripcion'])) {
                $this->especialistaRepository->updateEspecialistaDescription($especialistaId, $data['descripcion']);
            }

            if (isset($data['servicios'])) {
                $this->especialistaServicioRepository->deleteAllServiciosForEspecialista($especialistaId);

                foreach ($data['servicios'] as $servicioId) {
                    $especialistaServicio = new EspecialistaServicio(
                        $especialistaId,
                        (int) $servicioId
                    );
                    $this->especialistaServicioRepository->addEspecialistaServicio($especialistaServicio);
                }
            }
        }
    }

    /**
     * Handles avatar file upload.
     *
     * @param array|null $file Uploaded file data
     * @return string|null Avatar URL or null if upload failed
     */
    private function handleAvatarUpload(?array $file): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        try {
            $imageKit = new \ImageKit\ImageKit(
                $_ENV['IMAGEKIT_API_KEY'],
                $_ENV['IMAGEKIT_PRIVATE_KEY'],
                $_ENV['IMAGEKIT_ENDPOINT']
            );

            $upload = $imageKit->upload([
                'file' => fopen($file['tmp_name'], 'r'),
                'fileName' => $file['name'],
                'folder' => '/avatars'
            ]);

            if ($upload->error) {
                return null;
            }

            return $upload->result->url;
        } catch (\Exception $e) {
            error_log('Error uploading avatar to ImageKit: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Gets specialist ID by user ID.
     */
    public function getEspecialistaIdByUserId(int $userId): ?int
    {
        return $this->especialistaRepository->getEspecialistaIdByUserId($userId);
    }

    /**
     * Gets specialist data by user ID.
     */
    public function getEspecialistaDataByUserId(int $userId): ?array
    {
        return $this->especialistaRepository->getEspecialistaDataByUserId($userId);
    }

    /**
     * Gets services for a specialist.
     */
    public function getServiciosForEspecialista(int $especialistaId): array
    {
        return $this->especialistaServicioRepository->getServiciosForEspecialista($especialistaId);
    }
}
