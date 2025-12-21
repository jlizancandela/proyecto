<?php

namespace Usuarios\Application;

use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;
use Usuarios\Infrastructure\UserRepository;
use Respect\Validation\Validator as v;

/**
 * Servicio de autenticación y gestión de sesiones
 * 
 * Maneja el registro de usuarios, login, logout y validación de credenciales.
 * Implementa reglas de validación de contraseñas y gestión de sesiones PHP.
 */
class AuthService
{
    private UserRepository $userRepository;
    private UserService $userService;

    public function __construct(
        UserRepository $userRepository,
        UserService $userService
    ) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * Registra un nuevo usuario validando datos y verificando email único
     * 
     * Valida formato de datos, verifica que el email no esté registrado,
     * hashea la contraseña y crea el usuario en la base de datos.
     * 
     * @param array $userData Datos del usuario (nombre, apellidos, email, password, telefono, rol)
     * @return Usuario Usuario creado
     * @throws \RuntimeException Si los datos son inválidos o el email ya existe
     */
    public function register(array $userData): Usuario
    {
        $this->validateUserData($userData);

        $result = $this->userRepository->getUserByEmail($userData['email']);
        if ($result) {
            throw new \RuntimeException("El email ya está registrado");
        }

        $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);

        $user = new Usuario(
            $userData['rol'] ?? UserRole::Cliente->value,
            $userData['nombre'],
            $userData['apellidos'],
            $userData['email'],
            $passwordHash,
            $userData['telefono'] ?? null
        );

        $this->userService->setUser($user);

        return $user;
    }

    /**
     * Valida el formato y contenido de los datos del usuario
     * 
     * Verifica que nombre, apellidos, email y contraseña cumplan con los requisitos.
     * Delega la validación de contraseña a validatePassword().
     * 
     * @param array $userData Datos a validar
     * @return void
     * @throws \RuntimeException Si algún dato no cumple las reglas de validación
     */
    private function validateUserData(array $userData): void
    {
        $validator = v::key('nombre', v::stringType()->notEmpty()->length(2, 50))
            ->key('apellidos', v::stringType()->notEmpty()->length(2, 100))
            ->key('email', v::email())
            ->key('password', v::stringType()->notEmpty())
            ->key('telefono', v::optional(v::phone()), false)
            ->key('rol', v::optional(v::in(['Admin', 'Especialista', 'Cliente'])), false);

        try {
            $validator->assert($userData);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $this->validatePassword($userData['password']);
    }

    /**
     * Valida que la contraseña cumpla con los requisitos de seguridad
     * 
     * Requisitos: mínimo 8 caracteres, una mayúscula, una minúscula,
     * un número y un carácter especial.
     * 
     * @param string $password Contraseña a validar
     * @return void
     * @throws \RuntimeException Si la contraseña no cumple los requisitos
     */
    private function validatePassword(string $password): void
    {
        $passwordValidator = v::allOf(
            v::length(8, null),
            v::regex('/[A-Z]/'),
            v::regex('/[a-z]/'),
            v::regex('/[0-9]/'),
            v::regex('/[@$!%*?&#.,;:\-_+]/')
        );

        try {
            $passwordValidator->assert($password);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new \RuntimeException(
                "La contraseña debe tener al menos 8 caracteres, " .
                    "una letra mayúscula, una letra minúscula, un número " .
                    "y un carácter especial (@$!%*?&#.,;:-_+)"
            );
        }
    }

    /**
     * Autentica un usuario verificando email y contraseña
     * 
     * Busca el usuario por email y verifica que la contraseña coincida
     * usando password_verify().
     * 
     * @param string $email Email del usuario
     * @param string $password Contraseña en texto plano
     * @return Usuario|null Usuario si las credenciales son correctas, null si no
     */
    public function login(string $email, string $password): ?Usuario
    {
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            return null;
        }

        return password_verify($password, $user->getPassword()) ? $user : null;
    }

    /**
     * Inicia una sesión PHP para el usuario autenticado
     * 
     * Guarda datos del usuario en $_SESSION y regenera el ID de sesión
     * por seguridad.
     * 
     * @param Usuario $user Usuario autenticado
     * @return void
     */
    public function startSession(Usuario $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['role'] = $user->getRol()->value;
        $_SESSION['name'] = $user->getNombre();

        session_regenerate_id(true);
    }

    /**
     * Cierra la sesión del usuario actual
     * 
     * Limpia todas las variables de sesión y destruye la sesión PHP.
     * 
     * @return void
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();
    }

    /**
     * Obtiene el usuario actualmente autenticado
     * 
     * Busca el usuario en la base de datos usando el ID almacenado en sesión.
     * 
     * @return Usuario|null Usuario actual o null si no hay sesión activa
     */
    public function getCurrentUser(): ?Usuario
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id'])
            ? $this->userRepository->getUserById($_SESSION['user_id'])
            : null;
    }

    /**
     * Verifica si hay un usuario autenticado
     * 
     * @return bool True si existe una sesión activa con user_id
     */
    public function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    /**
     * Cambia la contraseña de un usuario verificando la contraseña actual
     * 
     * Valida que la contraseña actual sea correcta antes de actualizar.
     * La nueva contraseña se hashea antes de guardar.
     * 
     * @param int $userId ID del usuario
     * @param string $oldPassword Contraseña actual en texto plano
     * @param string $newPassword Nueva contraseña en texto plano
     * @return bool True si se cambió correctamente, false si la contraseña actual es incorrecta
     */
    public function changePassword(
        int $userId,
        string $oldPassword,
        string $newPassword
    ): bool {
        $user = $this->userRepository->getUserById($userId);

        if (!$user || !password_verify($oldPassword, $user->getPassword())) {
            return false;
        }

        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->setPassword($newPasswordHash);
        $this->userService->updateUser($user);

        return true;
    }

    /**
     * Verifica si el usuario actual tiene un rol específico
     * 
     * @param UserRole $role Rol a verificar
     * @return bool True si el usuario tiene el rol especificado
     */
    public function hasRole(UserRole $role): bool
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return false;
        }

        return $user->getRol() === $role;
    }

    /**
     * Genera un token de recuperación de contraseña para un usuario
     * 
     * Crea un token único usando random_bytes y establece una expiración
     * de 1 hora. El token se guarda en la base de datos.
     * 
     * @param string $email Email del usuario
     * @return string Token generado
     * @throws \RuntimeException Si el usuario no existe
     */
    public function generatePasswordResetToken(string $email): string
    {
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            throw new \RuntimeException("Usuario no encontrado");
        }

        // Generar token único de 32 bytes (64 caracteres hexadecimales)
        $token = bin2hex(random_bytes(32));

        // Establecer expiración en 1 hora
        $expiration = date('Y-m-d H:i:s', time() + 3600);

        // Guardar token y expiración en la base de datos
        $this->userRepository->savePasswordResetToken($user->getId(), $token, $expiration);

        return $token;
    }

    /**
     * Valida un token de recuperación de contraseña
     * 
     * Verifica que el token exista y no haya expirado.
     * 
     * @param string $token Token de recuperación
     * @return Usuario|null Usuario si el token es válido, null si no
     */
    public function validateResetToken(string $token): ?Usuario
    {
        if (empty($token)) {
            return null;
        }

        $user = $this->userRepository->getUserByResetToken($token);

        if (!$user) {
            return null;
        }

        // Obtener expiración directamente de BD
        $db = $this->userRepository->getConnection();
        $query = "SELECT reset_expiration FROM USUARIO WHERE id_usuario = :id";
        $stmt = $db->prepare($query);
        $userId = $user->getId();
        $stmt->bindParam(":id", $userId);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $expiration = $result['reset_expiration'] ?? null;

        // Verificar si ha expirado
        if (!$expiration || strtotime($expiration) < time()) {
            return null;
        }

        return $user;
    }

    /**
     * Resetea la contraseña de un usuario usando un token válido
     * 
     * Valida el token, valida la nueva contraseña, la hashea,
     * actualiza en base de datos y limpia el token.
     * 
     * @param string $token Token de recuperación
     * @param string $newPassword Nueva contraseña en texto plano
     * @return bool True si se reseteo correctamente, false si el token es inválido
     * @throws \RuntimeException Si la contraseña no cumple los requisitos
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $user = $this->validateResetToken($token);

        if (!$user) {
            return false;
        }

        // Validar nueva contraseña
        $this->validatePassword($newPassword);

        // Hashear y actualizar contraseña
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->setPassword($newPasswordHash);
        $this->userService->updateUser($user);

        // Limpiar token para que no pueda reutilizarse
        $this->userRepository->clearResetToken($user->getId());

        return true;
    }
}
