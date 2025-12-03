<?php

namespace Reservas\Application;

/**
 * DTO que combina los datos completos de una Reserva con información de Cliente, Especialista y Servicio
 * Útil para mostrar información completa de una reserva sin necesidad de múltiples consultas
 */
class ReservaCompletaDTO
{
    // Propiedades de Reserva
    private int $id_reserva;
    private int $id_cliente;
    private int $id_especialista;
    private int $id_servicio;
    private string $fecha_reserva;
    private string $hora_inicio;
    private string $hora_fin;
    private string $estado;
    private ?string $observaciones;
    private string $fecha_creacion;

    // Propiedades del Cliente (Usuario)
    private string $cliente_nombre;
    private string $cliente_apellidos;
    private string $cliente_email;
    private ?string $cliente_telefono;

    // Propiedades del Especialista (Usuario)
    private string $especialista_nombre;
    private string $especialista_apellidos;
    private string $especialista_email;
    private ?string $especialista_telefono;
    private ?string $especialista_descripcion;
    private ?string $especialista_foto_url;

    // Propiedades del Servicio
    private string $servicio_nombre;
    private int $servicio_duracion_minutos;
    private float $servicio_precio;
    private ?string $servicio_descripcion;

    public function __construct(
        int $id_reserva,
        int $id_cliente,
        int $id_especialista,
        int $id_servicio,
        string $fecha_reserva,
        string $hora_inicio,
        string $hora_fin,
        string $estado,
        ?string $observaciones,
        string $fecha_creacion,
        string $cliente_nombre,
        string $cliente_apellidos,
        string $cliente_email,
        ?string $cliente_telefono,
        string $especialista_nombre,
        string $especialista_apellidos,
        string $especialista_email,
        ?string $especialista_telefono,
        ?string $especialista_descripcion,
        ?string $especialista_foto_url,
        string $servicio_nombre,
        int $servicio_duracion_minutos,
        float $servicio_precio,
        ?string $servicio_descripcion,
    ) {
        $this->id_reserva = $id_reserva;
        $this->id_cliente = $id_cliente;
        $this->id_especialista = $id_especialista;
        $this->id_servicio = $id_servicio;
        $this->fecha_reserva = $fecha_reserva;
        $this->hora_inicio = $hora_inicio;
        $this->hora_fin = $hora_fin;
        $this->estado = $estado;
        $this->observaciones = $observaciones;
        $this->fecha_creacion = $fecha_creacion;
        $this->cliente_nombre = $cliente_nombre;
        $this->cliente_apellidos = $cliente_apellidos;
        $this->cliente_email = $cliente_email;
        $this->cliente_telefono = $cliente_telefono;
        $this->especialista_nombre = $especialista_nombre;
        $this->especialista_apellidos = $especialista_apellidos;
        $this->especialista_email = $especialista_email;
        $this->especialista_telefono = $especialista_telefono;
        $this->especialista_descripcion = $especialista_descripcion;
        $this->especialista_foto_url = $especialista_foto_url;
        $this->servicio_nombre = $servicio_nombre;
        $this->servicio_duracion_minutos = $servicio_duracion_minutos;
        $this->servicio_precio = $servicio_precio;
        $this->servicio_descripcion = $servicio_descripcion;
    }

    // ========================================================================
    // GETTERS - RESERVA
    // ========================================================================

    public function getIdReserva(): int
    {
        return $this->id_reserva;
    }

    public function getIdCliente(): int
    {
        return $this->id_cliente;
    }

    public function getIdEspecialista(): int
    {
        return $this->id_especialista;
    }

    public function getIdServicio(): int
    {
        return $this->id_servicio;
    }

    public function getFechaReserva(): \DateTime
    {
        return new \DateTime($this->fecha_reserva);
    }

    public function getFechaReservaString(): string
    {
        return $this->fecha_reserva;
    }

    public function getHoraInicio(): string
    {
        return $this->hora_inicio;
    }

    public function getHoraFin(): string
    {
        return $this->hora_fin;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function getFechaCreacion(): \DateTime
    {
        return new \DateTime($this->fecha_creacion);
    }

    public function getFechaCreacionString(): string
    {
        return $this->fecha_creacion;
    }

    // ========================================================================
    // GETTERS - CLIENTE
    // ========================================================================

    public function getClienteNombre(): string
    {
        return $this->cliente_nombre;
    }

    public function getClienteApellidos(): string
    {
        return $this->cliente_apellidos;
    }

    public function getClienteNombreCompleto(): string
    {
        return $this->cliente_nombre . " " . $this->cliente_apellidos;
    }

    public function getClienteEmail(): string
    {
        return $this->cliente_email;
    }

    public function getClienteTelefono(): ?string
    {
        return $this->cliente_telefono;
    }

    // ========================================================================
    // GETTERS - ESPECIALISTA
    // ========================================================================

    public function getEspecialistaNombre(): string
    {
        return $this->especialista_nombre;
    }

    public function getEspecialistaApellidos(): string
    {
        return $this->especialista_apellidos;
    }

    public function getEspecialistaNombreCompleto(): string
    {
        return $this->especialista_nombre . " " . $this->especialista_apellidos;
    }

    public function getEspecialistaEmail(): string
    {
        return $this->especialista_email;
    }

    public function getEspecialistaTelefono(): ?string
    {
        return $this->especialista_telefono;
    }

    public function getEspecialistaDescripcion(): ?string
    {
        return $this->especialista_descripcion;
    }

    public function getEspecialistaFotoUrl(): ?string
    {
        return $this->especialista_foto_url;
    }

    // ========================================================================
    // GETTERS - SERVICIO
    // ========================================================================

    public function getServicioNombre(): string
    {
        return $this->servicio_nombre;
    }

    public function getServicioDuracionMinutos(): int
    {
        return $this->servicio_duracion_minutos;
    }

    public function getServicioPrecio(): float
    {
        return $this->servicio_precio;
    }

    public function getServicioPrecioFormateado(): string
    {
        return number_format($this->servicio_precio, 2, ',', '.') . ' €';
    }

    public function getServicioDescripcion(): ?string
    {
        return $this->servicio_descripcion;
    }

    // ========================================================================
    // MÉTODOS DE UTILIDAD
    // ========================================================================

    /**
     * Verifica si la reserva está confirmada
     */
    public function isConfirmada(): bool
    {
        return strtolower($this->estado) === 'confirmada';
    }

    /**
     * Verifica si la reserva está pendiente
     */
    public function isPendiente(): bool
    {
        return strtolower($this->estado) === 'pendiente';
    }

    /**
     * Verifica si la reserva está cancelada
     */
    public function isCancelada(): bool
    {
        return strtolower($this->estado) === 'cancelada';
    }

    /**
     * Obtiene la duración de la reserva en formato legible
     */
    public function getDuracionFormateada(): string
    {
        $horas = floor($this->servicio_duracion_minutos / 60);
        $minutos = $this->servicio_duracion_minutos % 60;

        if ($horas > 0 && $minutos > 0) {
            return "{$horas}h {$minutos}min";
        } elseif ($horas > 0) {
            return "{$horas}h";
        } else {
            return "{$minutos}min";
        }
    }

    /**
     * Obtiene la fecha y hora de inicio en formato legible
     */
    public function getFechaHoraInicioFormateada(): string
    {
        $fecha = $this->getFechaReserva();
        return $fecha->format('d/m/Y') . ' a las ' . substr($this->hora_inicio, 0, 5);
    }

    /**
     * Creates a DTO from database row data (typically from a JOIN query)
     * 
     * Expected query structure:
     * SELECT 
     *     r.*,
     *     c.nombre as cliente_nombre, c.apellidos as cliente_apellidos, 
     *     c.email as cliente_email, c.telefono as cliente_telefono,
     *     u.nombre as especialista_nombre, u.apellidos as especialista_apellidos,
     *     u.email as especialista_email, u.telefono as especialista_telefono,
     *     e.descripcion as especialista_descripcion, e.foto_url as especialista_foto_url,
     *     s.nombre_servicio, s.duracion_minutos, s.precio, s.descripcion as servicio_descripcion
     * FROM RESERVA r
     * INNER JOIN USUARIO c ON r.id_cliente = c.id_usuario
     * INNER JOIN ESPECIALISTA e ON r.id_especialista = e.id_especialista
     * INNER JOIN USUARIO u ON e.id_usuario = u.id_usuario
     * INNER JOIN SERVICIO s ON r.id_servicio = s.id_servicio
     *
     * @param array $data Associative array from database
     * @return self
     */
    public static function fromDatabase(array $data): self
    {
        return new self(
            id_reserva: (int) $data["id_reserva"],
            id_cliente: (int) $data["id_cliente"],
            id_especialista: (int) $data["id_especialista"],
            id_servicio: (int) $data["id_servicio"],
            fecha_reserva: $data["fecha_reserva"],
            hora_inicio: $data["hora_inicio"],
            hora_fin: $data["hora_fin"],
            estado: $data["estado"] ?? 'Pendiente',
            observaciones: $data["observaciones"] ?? null,
            fecha_creacion: $data["fecha_creacion"],
            cliente_nombre: $data["cliente_nombre"],
            cliente_apellidos: $data["cliente_apellidos"],
            cliente_email: $data["cliente_email"],
            cliente_telefono: $data["cliente_telefono"] ?? null,
            especialista_nombre: $data["especialista_nombre"],
            especialista_apellidos: $data["especialista_apellidos"],
            especialista_email: $data["especialista_email"],
            especialista_telefono: $data["especialista_telefono"] ?? null,
            especialista_descripcion: $data["especialista_descripcion"] ?? null,
            especialista_foto_url: $data["especialista_foto_url"] ?? null,
            servicio_nombre: $data["nombre_servicio"],
            servicio_duracion_minutos: (int) $data["duracion_minutos"],
            servicio_precio: (float) $data["precio"],
            servicio_descripcion: $data["servicio_descripcion"] ?? null,
        );
    }

    /**
     * Convierte el DTO a un array asociativo
     * Útil para serialización JSON o respuestas de API
     */
    public function toArray(): array
    {
        return [
            'id_reserva' => $this->id_reserva,
            'fecha_reserva' => $this->fecha_reserva,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'fecha_creacion' => $this->fecha_creacion,
            'cliente' => [
                'id' => $this->id_cliente,
                'nombre' => $this->cliente_nombre,
                'apellidos' => $this->cliente_apellidos,
                'nombre_completo' => $this->getClienteNombreCompleto(),
                'email' => $this->cliente_email,
                'telefono' => $this->cliente_telefono,
            ],
            'especialista' => [
                'id' => $this->id_especialista,
                'nombre' => $this->especialista_nombre,
                'apellidos' => $this->especialista_apellidos,
                'nombre_completo' => $this->getEspecialistaNombreCompleto(),
                'email' => $this->especialista_email,
                'telefono' => $this->especialista_telefono,
                'descripcion' => $this->especialista_descripcion,
                'foto_url' => $this->especialista_foto_url,
            ],
            'servicio' => [
                'id' => $this->id_servicio,
                'nombre' => $this->servicio_nombre,
                'duracion_minutos' => $this->servicio_duracion_minutos,
                'duracion_formateada' => $this->getDuracionFormateada(),
                'precio' => $this->servicio_precio,
                'precio_formateado' => $this->getServicioPrecioFormateado(),
                'descripcion' => $this->servicio_descripcion,
            ],
        ];
    }
}
