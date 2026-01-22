# Project Documentation: Hair Salon Booking System

## Class: `\Especialistas\Application\EspecialistaService`
> Application

### Methods

#### `public function __construct()`
EspecialistaService constructor.

**Parameters:**
- `\Especialistas\Infrastructure\EspecialistaRepository` **especialistaRepository** The specialist repository instance.

---

#### `public function getAllEspecialistas()`
Retrieves all specialists with their associated user data.

**Returns:** `\Especialistas\Application\EspecialistaUsuarioDTO[]` An array of EspecialistaUsuarioDTO objects.

---


## Class: `\Especialistas\Application\EspecialistaUsuarioDTO`
> Application

### Methods

#### `public function fromDatabase()`
Creates an EspecialistaUsuarioDTO instance from database an array of data.

**Parameters:**
- `array` **data** Associative array of data from the database.

**Returns:** `self` A new EspecialistaUsuarioDTO instance.

---

#### `public function getRol()`
Get the user role.

**Returns:** `\Usuarios\Domain\UserRole` The user's role as a UserRole enum.

---

#### `public function getFechaRegistro()`
Get the registration date.

**Returns:** `\DateTime` A DateTime object representing the registration date.

---


## Class: `\Especialistas\Domain\Especialista`
> The main user class with all basic profile information.

### Methods

#### `public function __construct()`
Creates a new Especialista instance

**Parameters:**
- `\Especialistas\Domain\EspecialistaDTO` **dto** Data transfer object with all required fields

---

#### `public function getIdEspecialista()`
Get the specialist's ID.

**Returns:** `int` The specialist's unique ID.

---

#### `public function getIdUsuario()`
Get the associated user's ID.

**Returns:** `int` The ID of the user associated with this specialist.

---

#### `public function getDescripcion()`
Get the specialist's description.

**Returns:** `string|null` The specialist's description, or null if not set.

---

#### `public function getFotoUrl()`
Get the URL of the specialist's profile photo.

**Returns:** `string|null` The URL of the profile photo, or null if not set.

---

#### `public function fromDatabase()`
Creates an Especialista instance from database row data

**Parameters:**
- `array` **data** Database row data

**Returns:** `self` 

---

#### `public function __construct()`
Usuario constructor.

**Parameters:**
- `string` **rol** The user's role (e.g., 'Admin', 'Cliente').
- `string` **nombre** The user's first name.
- `string` **apellidos** The user's last name.
- `string` **email** The user's email address.
- `string` **password_hash** The hashed password.
- `string|null` **telefono** The user's phone number (optional).
- `array` **options** Optional settings (fecha_registro, activo, id_usuario).

---

#### `public function getId()`
Gets the user's ID.

**Returns:** `int` The unique ID of the user.

---

#### `public function setId()`
Sets the user's ID.

**Parameters:**
- `int` **id** The unique ID of the user.

**Returns:** `void` 

---

#### `public function getNombre()`
Gets the user's first name.

**Returns:** `string` The first name of the user.

---

#### `public function getApellidos()`
Gets the user's last name.

**Returns:** `string` The last names of the user.

---

#### `public function getEmail()`
Gets the user's email address.

**Returns:** `string` The email address.

---

#### `public function getPassword()`
Gets the user's password hash.

**Returns:** `string` The hashed password.

---

#### `public function getTelefono()`
Gets the user's phone number.

**Returns:** `string|null` The phone number, or null if not set.

---

#### `public function getActivo()`
Checks if the user account is active.

**Returns:** `bool` True if active, false otherwise.

---

#### `public function getRol()`
Gets the user's role.

**Returns:** `\Usuarios\Domain\UserRole` The user's role enum.

---

#### `public function getFechaRegistro()`
Gets the user's registration date.

**Returns:** `\DateTime` The registration timestamp.

---

#### `public function setPassword()`
Sets the user's password hash.

**Parameters:**
- `string` **password** The new hashed password.

**Returns:** `void` 

---

#### `public function setNombre()`
Sets the user's first name.

**Parameters:**
- `string` **nombre** The new first name.

**Returns:** `void` 

---

#### `public function setApellidos()`
Sets the user's last name.

**Parameters:**
- `string` **apellidos** The new last names.

**Returns:** `void` 

---

#### `public function setEmail()`
Sets the user's email address.

**Parameters:**
- `string` **email** The new email address.

**Returns:** `void` 

---

#### `public function setTelefono()`
Sets the user's phone number.

**Parameters:**
- `string|null` **telefono** The new phone number.

**Returns:** `void` 

---

#### `public function fromDatabase()`
Hydrates a Usuario object from a database associative array.

**Parameters:**
- `array` **data** The raw record from the database.

**Returns:** `self` A new instance of Usuario.

---


## Class: `\Especialistas\Domain\EspecialistaDTO`
> Simple container for specialist data without much logic.

### Methods

#### `public function fromArray()`
Creates a new EspecialistaDTO instance

**Parameters:**
- `array` **data** Associative array with specialist data

**Returns:** `self` 

---


## Class: `\Especialistas\Domain\EspecialistaServicio`
> Application

### Methods

#### `public function __construct()`
EspecialistaServicio constructor.

**Parameters:**
- `int` **idEspecialista** The ID of the specialist.
- `int` **idServicio** The ID of the service.

---

#### `public function getIdEspecialista()`
Get the specialist's ID.

**Returns:** `int` The ID of the specialist.

---

#### `public function getIdServicio()`
Get the service's ID.

**Returns:** `int` The ID of the service.

---

#### `public function fromDatabase()`
Creates an EspecialistaServicio instance from database row data.

**Parameters:**
- `array` **data** Associative array of data from the database.

**Returns:** `self` A new EspecialistaServicio instance.

---


## Class: `\Especialistas\Domain\HorarioEspecialista`
> Application

### Methods

#### `public function __construct()`
HorarioEspecialista constructor.

**Parameters:**
- `int` **id_especialista** The ID of the specialist this schedule belongs to.
- `int` **diaSemana** The day of the week (1 for Monday, 7 for Sunday).
- `string` **hora_inicio** The start time of the schedule slot (e.g., "09:00").
- `string` **hora_fin** The end time of the schedule slot (e.g., "18:00").
- `int|null` **id_horario** The unique ID of the schedule slot (optional, for existing records).

---

#### `public function getIdHorario()`
Get the ID of the schedule entry.

**Returns:** `int` The unique ID of the schedule slot.

---

#### `public function getIdEspecialista()`
Get the specialist's ID.

**Returns:** `int` The ID of the specialist this schedule belongs to.

---

#### `public function getDiaSemana()`
Get the day of the week for this schedule.

**Returns:** `int` The day of the week (1-7).

---

#### `public function getHoraInicio()`
Get the start time of the schedule slot.

**Returns:** `string` The start time in "HH:MM" format.

---

#### `public function getHoraFin()`
Get the end time of the schedule slot.

**Returns:** `string` The end time in "HH:MM" format.

---

#### `public function fromDatabase()`
Creates a HorarioEspecialista instance from database row data.

**Parameters:**
- `array` **data** Associative array of data from the database.

**Returns:** `self` A new HorarioEspecialista instance.

---


## Class: `\Especialistas\Infrastructure\EspecialistaRepository`
> Application

### Methods

#### `public function __construct()`
EspecialistaRepository constructor.

**Parameters:**
- `\PDO` **db** The PDO database connection.

---

#### `public function getAllEspecialistasConUsuario()`
Retrieves all specialists along with their associated user data.

**Returns:** `\Especialistas\Application\EspecialistaUsuarioDTO[]` An array of EspecialistaUsuarioDTO objects.

---

#### `public function getAllEspecialistasWithUserData()`
Gets all especialistas with basic user data for selectors
Returns array with id_especialista and user name

**Returns:** `array` 

---

#### `public function addEspecialista()`
Adds a new specialist entry to the database.

**Parameters:**
- `\Especialistas\Domain\Especialista` **especialista** The Especialista object containing the data to be added.

**Returns:** `void` 

---

#### `public function createBasicEspecialista()`
Creates a basic especialista entry (without full Especialista object).

**Parameters:**
- `int` **userId** User ID.
- `string|null` **fotoUrl** Avatar URL.
- `string|null` **descripcion** Specialist description.

**Returns:** `int|null` The created especialista ID or null on failure.

---

#### `public function getEspecialistaIdByUserId()`
Gets especialista ID by user ID.

**Parameters:**
- `int` **userId** User ID.

**Returns:** `int|null` The specialist ID if found, otherwise null.

---

#### `public function updateEspecialista()`
Updates an existing especialista entry.

**Parameters:**
- `\Especialistas\Domain\Especialista` **especialista** The Especialista object with updated data.

**Returns:** `void` 

---

#### `public function updateEspecialistaPhoto()`
Updates the photo URL of a specialist.

**Parameters:**
- `int` **id** The specialist ID.
- `string` **fotoUrl** The new photo URL.

**Returns:** `void` 

---

#### `public function updateEspecialistaDescription()`
Updates the description of a specialist.

**Parameters:**
- `int` **id** The specialist ID.
- `string` **descripcion** The new description.

**Returns:** `void` 

---

#### `public function getEspecialistaDataByUserId()`
Gets basic especialista data by user ID

**Parameters:**
- `int` **userId** 

**Returns:** `array|null` ['id_especialista', 'descripcion', 'foto_url']

---

#### `public function getEspecialistasDisponibles()`
Retrieves available specialists for a given service and date, considering their booked times.

**Parameters:**
- `int` **idServicio** The ID of the service.
- `string` **fecha** The date for which to find available specialists (format 'YYYY-MM-DD').
- `int|null` **limit** The maximum number of results to return.
- `int|null` **offset** The number of results to skip.

**Returns:** `array` An array of available specialists, each with their available time slots.

---

#### `private function calcularHorasDisponibles()`
Calculates available time slots for a specialist based on their existing bookings and service duration.

**Parameters:**
- `array` **reservas** An array of existing bookings for the specialist on a given day.
- `int` **duracionMinutos** The duration of the service in minutes.
- `array` **horarios** The working hours of the specialist for this day.

**Returns:** `string[]` An array of available time slots in 'HH:MM' format.

---

#### `public function countEspecialistasDisponibles()`
Counts the total number of available specialists for a specific service and date.

**Parameters:**
- `int` **idServicio** The ID of the service.
- `string` **fecha** The date to check availability.

**Returns:** `int` The total count of available specialists.

---

#### `public function getEspecialistaProfileWithServices()`
Gets specialist profile with user data and services by user ID

**Parameters:**
- `int` **userId** User ID

**Returns:** `array|null` Profile data with services or null if not found

---


## Class: `\Especialistas\Infrastructure\EspecialistaServicioRepository`
> Application

### Methods

#### `public function __construct()`
EspecialistaServicioRepository constructor.

**Parameters:**
- `\PDO` **db** The PDO database connection.

---

#### `public function getEspecialistaServicio()`
Retrieves a specific specialist-service link.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.
- `int` **id_servicio** The ID of the service.

**Returns:** `\Especialistas\Domain\EspecialistaServicio|null` The EspecialistaServicio object if found, null otherwise.

---

#### `public function getServiciosForEspecialista()`
Retrieves all services offered by a specific specialist.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.

**Returns:** `\Servicios\Domain\Servicio[]` An array of Servicio objects.

---

#### `public function getEspecialistasForServicio()`
Retrieves all specialists who provide a specific service.

**Parameters:**
- `int` **id_servicio** The ID of the service.

**Returns:** `\Especialistas\Domain\Especialista[]` An array of Especialista objects.

---

#### `public function addEspecialistaServicio()`
Adds a new specialist-service link to the database.

**Parameters:**
- `\Especialistas\Domain\EspecialistaServicio` **especialistaServicio** The EspecialistaServicio object to add.

**Returns:** `void` 

---

#### `public function deleteEspecialistaServicio()`
Deletes a specific specialist-service link from the database.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.
- `int` **id_servicio** The ID of the service.

**Returns:** `void` 

---

#### `public function deleteAllServiciosForEspecialista()`
Deletes all service links for a specific specialist.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.

**Returns:** `void` 

---


## Class: `\Especialistas\Infrastructure\HorarioEspecialistaRepository`
> Application

### Methods

#### `public function __construct()`
HorarioEspecialistaRepository constructor.

**Parameters:**
- `\PDO` **db** The PDO database connection.

---

#### `public function getAllHorarios()`
Retrieves all specialist schedules from the database.

**Returns:** `\Especialistas\Domain\HorarioEspecialista[]` An array of HorarioEspecialista objects.

---

#### `public function getHorarioById()`
Retrieves a specialist schedule by its ID.

**Parameters:**
- `int` **id** The ID of the schedule entry.

**Returns:** `\Especialistas\Domain\HorarioEspecialista|null` The HorarioEspecialista object if found, null otherwise.

---

#### `public function getHorariosByEspecialista()`
Retrieves all schedule entries for a specific specialist.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.

**Returns:** `\Especialistas\Domain\HorarioEspecialista[]` An array of HorarioEspecialista objects.

---

#### `public function getHorariosByDia()`
Retrieves all schedule entries for a specific day of the week.

**Parameters:**
- `int` **dia_semana** The day of the week (1-7).

**Returns:** `\Especialistas\Domain\HorarioEspecialista[]` An array of HorarioEspecialista objects.

---

#### `public function getHorariosByEspecialistaYDia()`
Retrieves all schedule entries for a specific specialist and day of the week.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.
- `int` **dia_semana** The day of the week (1-7).

**Returns:** `\Especialistas\Domain\HorarioEspecialista[]` An array of HorarioEspecialista objects.

---

#### `public function addHorario()`
Adds a new schedule entry to the database.

**Parameters:**
- `\Especialistas\Domain\HorarioEspecialista` **horario** The HorarioEspecialista object to add.

**Returns:** `void` 

---

#### `public function updateHorario()`
Updates an existing schedule entry in the database.

**Parameters:**
- `\Especialistas\Domain\HorarioEspecialista` **horario** The HorarioEspecialista object with updated data.

**Returns:** `void` 

---

#### `public function deleteHorario()`
Deletes a schedule entry by its ID.

**Parameters:**
- `int` **id** The ID of the schedule entry to delete.

**Returns:** `void` 

---

#### `public function deleteHorariosByEspecialista()`
Deletes all schedule entries for a specific specialist.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.

**Returns:** `void` 

---

#### `public function getHorariosGroupedByDay()`
Retrieves all schedule entries for a specific specialist, grouped by day of the week.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.

**Returns:** `array` An associative array where keys are days of the week and values are arrays of HorarioEspecialista objects.

---

#### `public function isEspecialistaAvailableAt()`
Checks if a specialist is available at a specific time on a specific day.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.
- `int` **dia_semana** The day of the week.
- `string` **hora** The time to check (e.g., "HH:MM").

**Returns:** `bool` True if the specialist is available, false otherwise.

---

#### `public function getAvailableDaysForEspecialista()`
Retrieves all days of the week a specialist has availability.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.

**Returns:** `int[]` An array of integers representing the available days of the week (1-7).

---

#### `public function existsHorarioConflict()`
Checks for scheduling conflicts for a new or updated schedule entry.

**Parameters:**
- `int` **id_especialista** The ID of the specialist.
- `int` **dia_semana** The day of the week.
- `string` **hora_inicio** The start time of the potential new slot.
- `string` **hora_fin** The end time of the potential new slot.

**Returns:** `bool` True if a conflict exists, false otherwise.

---


## Class: `\Especialistas\Presentation\EspecialistaApiController`
> Application

### Methods

#### `public function __construct()`
EspecialistaApiController constructor.

**Parameters:**
- `\Especialistas\Infrastructure\EspecialistaRepository` **repository** The specialist repository instance.

---

#### `public function getDisponibles()`
Retrieves a list of available specialists for a given service and date.

**Returns:** `void` 

---


## Class: `\Reservas\Application\ReservaCompletaDTO`
> Application

### Methods

#### `public function fromDatabase()`
Creates a ReservaCompletaDTO instance from an associative array, typically a database row.

**Parameters:**
- `array` **data** Associative array containing all booking-related data.

**Returns:** `self` A new ReservaCompletaDTO instance.

---

#### `public function toArray()`
Converts the DTO to an associative array.

**Returns:** `array` The DTO data as an associative array.

---


## Class: `\Reservas\Application\ReservaService`
> Application

### Methods

#### `public function __construct()`
No description.

**Parameters:**
- `\Reservas\Infrastructure\ReservaRepository` **reservaRepository** 

---

#### `public function createReserva()`
Creates a new booking with validation and business rules.

**Parameters:**
- `array` **data** Booking data
- `bool` **isAdmin** Whether the user is an administrator (bypasses time restrictions)

**Returns:** `int` Created booking ID

---

#### `public function updateReserva()`
Updates an existing booking with validation.

**Parameters:**
- `int` **reservaId** Booking ID
- `array` **data** Updated booking data
- `bool` **isAdmin** Whether the user is an administrator (bypasses time restrictions)

**Returns:** `bool` True if updated successfully

---

#### `public function deleteReserva()`
Deletes a booking.

**Parameters:**
- `int` **reservaId** Booking ID

**Returns:** `bool` True if deleted successfully

---

#### `public function updateReservaStatus()`
Updates booking status with validation.

**Parameters:**
- `int` **reservaId** Booking ID
- `string` **newStatus** New status

**Returns:** `bool` True if updated successfully

---

#### `public function getReservasByClient()`
Gets bookings for a specific client.

**Parameters:**
- `int` **clientId** Client ID
- `int` **limit** Max results
- `int` **offset** Pagination offset

**Returns:** `\Reservas\Domain\Reserva[]` Array of bookings

---

#### `public function getReservaById()`
Gets a booking by ID with complete data.

**Parameters:**
- `int` **id** Booking ID

**Returns:** `\Reservas\Application\ReservaCompletaDTO|null` Complete booking DTO or null

---

#### `public function getAllReservasByFilter()`
Gets user bookings with optional filters.

**Parameters:**
- `int` **userId** User ID
- `int` **limit** Max results
- `int` **offset** Pagination offset
- `string|null` **fechaDesde** Start date filter
- `string|null` **fechaHasta** End date filter
- `string|null` **estado** Status filter

**Returns:** `\Reservas\Application\ReservaCompletaDTO[]` Array of complete booking DTOs

---

#### `public function countReservasByFilter()`
Counts user bookings with optional filters.

**Parameters:**
- `int` **userId** User ID
- `string|null` **fechaDesde** Start date filter
- `string|null` **fechaHasta** End date filter
- `string|null` **estado** Status filter

**Returns:** `int` Total count

---

#### `public function getLatestReserva()`
Gets the latest booking for a user.

**Parameters:**
- `int` **userId** User ID

**Returns:** `\Reservas\Application\ReservaCompletaDTO|null` Latest booking or null

---

#### `public function getAllReservasWithFilters()`
Gets all bookings with optional filters (admin).

**Parameters:**
- `array` **filtros** Filters array
- `int` **limit** Max results
- `int` **offset** Pagination offset

**Returns:** `\Reservas\Application\ReservaCompletaDTO[]` Array of complete booking DTOs

---

#### `public function countAllReservasWithFilters()`
Counts all bookings with optional filters (admin).

**Parameters:**
- `array` **filtros** Filters array

**Returns:** `int` Total count

---

#### `private function validateReservaData()`
Validates booking data format and content.

**Parameters:**
- `array` **data** Data to validate
- `bool` **isAdmin** Whether the user is an administrator (bypasses date restrictions)

**Returns:** `void` 

---

#### `private function prepareBookingData()`
Prepares and normalizes booking data.

**Parameters:**
- `array` **data** Raw booking data

**Returns:** `array` Normalized booking data

---

#### `private function validateNoConflicts()`
Validates no time conflicts exist for new booking.

**Parameters:**
- `string` **date** Booking date
- `string` **startTime** Start time
- `string` **endTime** End time
- `int` **specialistId** Specialist ID
- `int` **clientId** Client ID

**Returns:** `void` 

---

#### `private function validateNoConflictsForUpdate()`
Validates no time conflicts exist for booking update.

**Parameters:**
- `string` **date** Booking date
- `string` **startTime** Start time
- `string` **endTime** End time
- `int` **specialistId** Specialist ID
- `int` **clientId** Client ID
- `int` **excludeReservaId** Booking ID to exclude from check

**Returns:** `void` 

---

#### `private function validateWeeklyLimit()`
Validates client weekly booking limit.

**Parameters:**
- `int` **clientId** Client ID
- `int` **serviceId** Service ID
- `string` **date** Booking date

**Returns:** `void` 

---

#### `private function validateTotalHoursLimit()`
Validates that the client does not exceed the weekly hours limit.

**Parameters:**
- `int` **clientId** Client ID
- `string` **date** Booking date
- `int` **newDuration** New booking duration in minutes

**Returns:** `void` 

---


## Class: `\Reservas\Domain\Reserva`
> Application

### Methods

#### `public function __construct()`
Reserva constructor.

**Parameters:**
- `int` **id_cliente** The ID of the client who made the booking.
- `int` **id_especialista** The ID of the specialist for the booking.
- `int` **id_servicio** The ID of the service booked.
- `string` **fecha_reserva** The date of the booking in 'YYYY-MM-DD' format.
- `string` **hora_inicio** The start time of the booking in 'HH:MM:SS' format.
- `string` **hora_fin** The end time of the booking in 'HH:MM:SS' format.
- `string` **estado** The current status of the booking (e.g., 'Pendiente', 'Confirmada').
- `string|null` **observaciones** Optional observations for the booking.
- `string|null` **fecha_creacion** The creation date and time of the booking in 'YYYY-MM-DD HH:MM:SS' format. Defaults to current time if null.
- `int|null` **id_reserva** The unique ID of the booking. Optional, used for existing bookings.

---

#### `public function getIdReserva()`
Get the booking ID.

**Returns:** `int` The unique ID of the booking.

---

#### `public function getIdCliente()`
Get the client ID.

**Returns:** `int` The ID of the client who made the booking.

---

#### `public function getIdEspecialista()`
Get the specialist ID.

**Returns:** `int` The ID of the specialist for the booking.

---

#### `public function getIdServicio()`
Get the service ID.

**Returns:** `int` The ID of the service booked.

---

#### `public function getFechaReserva()`
Get the booking date.

**Returns:** `\DateTime` A DateTime object representing the date of the booking.

---

#### `public function getHoraInicio()`
Get the start time of the booking.

**Returns:** `string` The start time in 'HH:MM:SS' format.

---

#### `public function getHoraFin()`
Get the end time of the booking.

**Returns:** `string` The end time in 'HH:MM:SS' format.

---

#### `public function getEstado()`
Get the status of the booking.

**Returns:** `string` The current status of the booking.

---

#### `public function getObservaciones()`
Get the observations for the booking.

**Returns:** `string|null` Optional observations for the booking.

---

#### `public function getFechaCreacion()`
Get the creation date and time of the booking.

**Returns:** `\DateTime` A DateTime object representing the creation date and time.

---

#### `public function fromDatabase()`
Creates a Reserva instance from database row data.

**Parameters:**
- `array` **data** Associative array of data from the database.

**Returns:** `self` A new Reserva instance.

---


## Class: `\Reservas\Infrastructure\ReservaRepository`
> Application

### Methods

#### `public function __construct()`
Initializes the repository with the database connection.

**Parameters:**
- `\PDO` **db** The database connection.

---

#### `private function applyFilters()`
Applies dynamic filters to the SQL query.

**Parameters:**
- `array` **filtros** List of filters to apply (client, specialist, service, etc.).
- `string` **sql** The SQL query string to be modified.
- `array` **params** The parameters array to be populated.

---

#### `public function getReservaCompletaById()`
Retrieves a complete booking with details by its ID.

**Parameters:**
- `int` **id** The booking ID.

**Returns:** `\Reservas\Application\ReservaCompletaDTO|null` The booking data or null if not found.

---

#### `public function addReserva()`
Adds a new booking to the database.

**Parameters:**
- `\Reservas\Domain\Reserva` **reserva** The booking entity.

**Returns:** `int|null` The ID of the created booking or null on failure.

---

#### `public function updateReserva()`
Updates an existing booking.

**Parameters:**
- `\Reservas\Domain\Reserva` **reserva** The booking entity with updated data.

**Returns:** `bool` True if successful, false otherwise.

---

#### `public function deleteReserva()`
Deletes a booking by its ID.

**Parameters:**
- `int` **id** The booking ID.

**Returns:** `bool` True if successful, false otherwise.

---

#### `public function findByClient()`
Finds bookings associated with a specific client.

**Parameters:**
- `int` **id_cliente** The client's user ID.
- `int` **limit** Max number of results.
- `int` **offset** Offset for pagination.

**Returns:** `\Reservas\Application\ReservaCompletaDTO[]` List of bookings.

---

#### `public function findAllFiltered()`
Retrieves all bookings matching the specified filters.

**Parameters:**
- `array` **filtros** Filters to apply (client, specialist, service, state, date range).
- `int` **limit** Max number of results.
- `int` **offset** Offset for pagination.

**Returns:** `\Reservas\Application\ReservaCompletaDTO[]` List of matching bookings.

---

#### `public function findConflicts()`
Checks for scheduling conflicts for a specialist.

**Parameters:**
- `string` **fecha** Date of the booking.
- `string` **hora_inicio** Start time.
- `string` **hora_fin** End time.
- `int` **id_especialista** Specialist ID.
- `int|null` **exclude_id_reserva** Booking ID to exclude (for updates).

**Returns:** `bool` True if a conflict exists, false otherwise.

---

#### `public function findClientConflicts()`
Checks for scheduling conflicts for a client.

**Parameters:**
- `string` **fecha** Date of the booking.
- `string` **hora_inicio** Start time.
- `string` **hora_fin** End time.
- `int` **id_cliente** Client ID.
- `int|null` **exclude_id_reserva** Booking ID to exclude.

**Returns:** `bool` True if a conflict exists, false otherwise.

---

#### `public function findByUserId()`
Finds bookings for a specific user ID (client).

**Parameters:**
- `int` **userId** The user ID.
- `int` **limit** Max results.
- `int` **offset** Pagination offset.

**Returns:** `\Reservas\Application\ReservaCompletaDTO[]` List of bookings.

---

#### `public function countByUserId()`
Counts the total number of bookings for a specific user.

**Parameters:**
- `int` **userId** The user ID.

**Returns:** `int` Total count.

---

#### `public function findById()`
Finds a booking by its ID and returns a DTO.

**Parameters:**
- `int` **reservaId** The booking ID.

**Returns:** `\Reservas\Application\ReservaCompletaDTO|null` The booking DTO or null if not found.

---

#### `public function updateStatus()`
Updates the status of a booking.

**Parameters:**
- `int` **reservaId** The booking ID.
- `string` **newStatus** The new status.

**Returns:** `bool` True if successful, false otherwise.

---

#### `public function findByUserIdWithFilters()`
Finds bookings for a user with optional filters

**Parameters:**
- `int` **userId** User/Client ID
- `int` **limit** Maximum number of results
- `int` **offset** Pagination offset
- `string|null` **fechaDesde** Start date (Y-m-d format)
- `string|null` **fechaHasta** End date (Y-m-d format)
- `string|null` **estado** Booking status

**Returns:** `array` Array of ReservaCompletaDTO

---

#### `public function countByUserIdWithFilters()`
Counts bookings for a user with optional filters

**Parameters:**
- `int` **userId** User/Client ID
- `string|null` **fechaDesde** Start date (Y-m-d format)
- `string|null` **fechaHasta** End date (Y-m-d format)
- `string|null` **estado** Booking status

**Returns:** `int` Total number of bookings matching criteria

---

#### `public function findLatestByUserId()`
Gets the latest booking for a user

**Parameters:**
- `int` **userId** User/Client ID

**Returns:** `\Reservas\Application\ReservaCompletaDTO|null` The latest booking or null if none found

---

#### `public function countAllFiltered()`
Counts all bookings with optional filters

**Parameters:**
- `array` **filtros** Filters (cliente, especialista, estado, fecha_desde, fecha_hasta)

**Returns:** `int` Total count of bookings matching filters

---

#### `public function findByEspecialistaIdWithFilters()`
Finds bookings for a specific specialist with optional filters

**Parameters:**
- `int` **especialistaId** Specialist ID
- `int` **limit** Maximum results
- `int` **offset** Pagination offset
- `string|null` **fechaDesde** Start date (Y-m-d format)
- `string|null` **fechaHasta** End date (Y-m-d format)
- `string|null` **estado** Booking status
- `string|null` **clienteSearch** Client name search

**Returns:** `array` Array of ReservaCompletaDTO

---

#### `public function countByEspecialistaIdWithFilters()`
Counts bookings for a specific specialist with optional filters

**Parameters:**
- `int` **especialistaId** Specialist ID
- `string|null` **fechaDesde** Start date (Y-m-d format)
- `string|null` **fechaHasta** End date (Y-m-d format)
- `string|null` **estado** Booking status
- `string|null` **clienteSearch** Client name search

**Returns:** `int` Total count

---


## Class: `\Reservas\Presentation\BookingAdminApiController`
> Admin API controller for booking management

### Methods

#### `public function __construct()`
BookingAdminApiController constructor.

**Parameters:**
- `\Reservas\Application\ReservaService` **reservaService** The booking service instance.

---

#### `public function getAllBookings()`
Gets all bookings with optional filters

**Returns:** `void` 

---

#### `public function getBookingById()`
Gets a single booking by ID

**Parameters:**
- `int` **id** Booking ID

**Returns:** `void` 

---

#### `public function createBooking()`
Creates a new booking

**Returns:** `void` 

---

#### `public function updateBooking()`
Updates an existing booking

**Parameters:**
- `int` **id** Booking ID

**Returns:** `void` 

---

#### `public function deleteBooking()`
Deletes a booking

**Parameters:**
- `int` **id** Booking ID

**Returns:** `void` 

---


## Class: `\Reservas\Presentation\BookingApiController`
> Application

### Methods

#### `public function __construct()`
BookingApiController constructor.

**Parameters:**
- `\Reservas\Application\ReservaService` **reservaService** The booking service instance.

---

#### `public function createReserva()`
Creates a new booking for the authenticated user.

**Returns:** `void` 

---

#### `public function getReservas()`
Retrieves all bookings for the authenticated user.

**Returns:** `void` 

---


## Class: `\Reservas\Presentation\BookingController`
> Application

### Methods

#### `public function __construct()`
BookingController constructor.

**Parameters:**
- `\Latte\Engine` **latte** The Latte templating engine instance.

---

#### `public function index()`
Displays the new booking page for users.

**Returns:** `string` The rendered HTML content of the new booking page.

---


## Class: `\Reservas\Presentation\MyBookingsController`
> Shows the history of bookings for the logged-in client.

### Methods

#### `public function __construct()`
MyBookingsController constructor.

**Parameters:**
- `\Latte\Engine` **latte** The Latte templating engine instance.
- `\Reservas\Application\ReservaService` **reservaService** The booking service instance.

---

#### `public function index()`
Muestra el listado paginado de reservas del usuario con filtros opcionales

**Returns:** `string` HTML renderizado de la página de reservas

---

#### `public function cancel()`
Cancela una reserva verificando permisos del usuario

**Parameters:**
- `int` **bookingId** ID de la reserva a cancelar

**Returns:** `void` 

---

#### `public function modify()`
Modifica una reserva cancelándola y redirigiendo al formulario de nueva reserva

**Parameters:**
- `int` **bookingId** ID de la reserva a modificar

**Returns:** `void` 

---


## Class: `\Reservas\Presentation\PaymentController`
> Application

### Methods

#### `public function __construct()`
No description.

---

#### `public function createCheckoutSession()`
Creates a Stripe Checkout Session.

---

#### `public function handleSuccess()`
Handles the success callback from Stripe

---


## Class: `\Reservas\Presentation\PdfExportController`
> Utility to generate PDF documents for salon appointments or reports.

### Methods

#### `public function __construct()`
PdfExportController constructor.

**Parameters:**
- `\Latte\Engine` **latte** The Latte templating engine instance.
- `\Reservas\Application\ReservaService` **reservaService** The booking service instance.
- `\Usuarios\Application\UserService|null` **userService** The user service instance (optional).

---

#### `public function exportReservas()`
Exporta las reservas del usuario a PDF aplicando filtros opcionales

**Returns:** `void` Envía el PDF directamente al navegador

---

#### `public function exportAdminReservas()`
Exporta todas las reservas (visión admin) a PDF aplicando filtros

**Returns:** `void` Envía el PDF directamente al navegador

---

#### `public function exportAdminUsers()`
Exporta usuarios a PDF aplicando filtros y ordenación

**Returns:** `void` Envía el PDF directamente al navegador

---


## Class: `\Servicios\Application\ServicioService`
> Service class that coordinates service-related operations.

### Methods

#### `public function __construct()`
ServicioService constructor.

**Parameters:**
- `\Servicios\Infrastructure\ServicioRepository` **repository** The repository for service data operations.

---

#### `public function createService()`
Creates a new service after validation

**Parameters:**
- `array` **data** Service data

**Returns:** `\Servicios\Domain\Servicio` Created service

---

#### `public function updateService()`
Updates an existing service after validation

**Parameters:**
- `int` **id** Service ID
- `array` **data** Updated service data

**Returns:** `\Servicios\Domain\Servicio` Updated service

---

#### `public function deactivateService()`
Deactivates a service (soft delete)

**Parameters:**
- `int` **id** Service ID

---

#### `public function activateService()`
Activates a service

**Parameters:**
- `int` **id** Service ID

---

#### `public function getServiceById()`
Gets a service by ID

**Parameters:**
- `int` **id** Service ID

**Returns:** `\Servicios\Domain\Servicio|null` Service or null if not found

---

#### `public function getAllServices()`
Gets all services, optionally filtered by active status

**Parameters:**
- `bool|null` **activo** Filter by active status (null = all)

**Returns:** `\Servicios\Domain\Servicio[]` Array of Servicio objects

---

#### `private function validateServiceData()`
Validates service data

**Parameters:**
- `array` **data** Service data to validate

---


## Class: `\Servicios\Domain\Servicio`
> Represents a single service offered by the hair salon.

### Methods

#### `public function __construct()`
Servicio constructor.

**Parameters:**
- `string` **nombre_servicio** The name of the service.
- `int` **duracion_minutos** Estimated duration in minutes.
- `string` **descripcion** Detailed description of the service.
- `float` **precio** Price in decimal format.
- `int|null` **id_servicio** The unique database ID (optional).
- `bool` **activo** Whether the service is currently active.

---

#### `public function getIdServicio()`
Gets the service ID.

**Returns:** `int` The unique service ID.

---

#### `public function getNombreServicio()`
Gets the service name.

**Returns:** `string` The name of the service.

---

#### `public function getDuracionMinutos()`
Gets the service duration.

**Returns:** `int` Duration in minutes.

---

#### `public function getDescripcion()`
Gets the service description.

**Returns:** `string` The detailed description.

---

#### `public function getPrecio()`
Gets the service price.

**Returns:** `float` The price.

---

#### `public function isActivo()`
Checks if the service is active.

**Returns:** `bool` True if active, false otherwise.

---

#### `public function fromDatabase()`
Hydrates a Servicio object from a database associative array.

**Parameters:**
- `array` **data** The raw record from the database.

**Returns:** `self` A new instance of Servicio.

---


## Class: `\Servicios\Infrastructure\ServicioRepository`
> Handles all database operations for the services table.

### Methods

#### `public function __construct()`
ServicioRepository constructor.

**Parameters:**
- `\PDO` **db** The PDO database connection.

---

#### `public function getAllServicios()`
Gets all services, optionally filtered by active status

**Parameters:**
- `bool|null` **activo** Filter by active status (null = all)

**Returns:** `\Servicios\Domain\Servicio[]` Array of Servicio objects

---

#### `public function getServicioById()`
Retrieves a service by its ID.

**Parameters:**
- `int` **id** The ID of the service.

**Returns:** `\Servicios\Domain\Servicio|null` The Servicio object or null if not found.

---

#### `public function save()`
Saves a new service in the database.

**Parameters:**
- `\Servicios\Domain\Servicio` **servicio** The service object to save.

**Returns:** `int|null` The ID of the newly created service or null on failure.

---

#### `public function update()`
Updates an existing service record.

**Parameters:**
- `\Servicios\Domain\Servicio` **servicio** The service object with updated data.

**Returns:** `bool` True on success, false on failure.

---

#### `public function deactivate()`
Deactivates a service (soft delete)

**Parameters:**
- `int` **id** Service ID

**Returns:** `bool` Success status

---

#### `public function activate()`
Activates a service

**Parameters:**
- `int` **id** Service ID

**Returns:** `bool` Success status

---

#### `public function getTotalCount()`
Gets the total count of services in the system.

**Returns:** `int` The total number of services.

---


## Class: `\Servicios\Presentation\ServiceApiController`
> Application

### Methods

#### `public function __construct()`
ServiceApiController constructor.

**Parameters:**
- `\Servicios\Application\ServicioService` **service** The service application service instance.

---

#### `public function getAll()`
Retrieves all services.

**Returns:** `void` 

---

#### `public function getServiceById()`
Retrieves a single service by its ID.

**Parameters:**
- `int` **id** The ID of the service to retrieve.

**Returns:** `void` 

---

#### `public function createService()`
Creates a new service.

**Returns:** `void` 

---

#### `public function updateService()`
Updates an existing service.

**Parameters:**
- `int` **id** The ID of the service to update.

**Returns:** `void` 

---

#### `public function deactivateService()`
Deactivates a service (sets its 'activo' status to false).

**Parameters:**
- `int` **id** The ID of the service to deactivate.

**Returns:** `void` 

---

#### `public function activateService()`
Activates a service (sets its 'activo' status to true).

**Parameters:**
- `int` **id** The ID of the service to activate.

**Returns:** `void` 

---


## Class: `\Shared\Domain\Exceptions\InvalidUserException`
> Exception thrown when a user is not found or is invalid.


## Class: `\Shared\Domain\Exceptions\InvalidEmailException`
> Exception thrown when an email is already in use or is invalid.


## Class: `\Shared\Domain\Exceptions\InvalidPasswordException`
> Exception thrown when a password does not meet the security requirements.


## Class: `\Shared\Domain\Exceptions\InvalidUserDataException`
> Exception thrown when user profile data is invalid.


## Class: `\Shared\Domain\Exceptions\InvalidValidation`
> General purpose validation exception.


## Class: `\Shared\Domain\Exceptions\ServiceNotFoundException`
> Exception thrown when a service is not found.


## Class: `\Shared\Domain\Exceptions\ServiceOperationException`
> Exception thrown when a service operation fails.


## Class: `\Shared\Domain\Exceptions\ServiceValidationException`
> Exception thrown when service data validation fails.


## Class: `\Shared\Domain\Exceptions\BookingNotFoundException`
> Exception thrown when a booking is not found.


## Class: `\Shared\Domain\Exceptions\BookingOperationException`
> Exception thrown when a booking operation fails.


## Class: `\Shared\Domain\Exceptions\BookingValidationException`
> Exception thrown when booking data validation fails.


## Class: `\Shared\Domain\Exceptions\BookingConflictException`
> Exception thrown when a booking conflict is detected.


## Class: `\Shared\Domain\Exceptions\BookingLimitException`
> Exception thrown when a booking limit is exceeded.


## Class: `\Shared\Domain\Exceptions\MissingDependencyException`
> Exception thrown when a required dependency is missing.


## Class: `\Shared\Infrastructure\Database\Database`
> Application

### Methods

#### `public function __construct()`
Database constructor.

---

#### `public function getConnection()`
Retrieves the PDO database connection instance.

**Returns:** `\PDO` The PDO database connection.

---


## Class: `\Shared\Infrastructure\Email\EmailService`
> Communicates with the Brevo API to deliver transactional emails.

### Methods

#### `public function __construct()`
Email service constructor

---

#### `public function sendEmail()`
Sends a generic email using Brevo API

**Parameters:**
- `string` **to** Recipient email
- `string` **subject** Email subject
- `string` **htmlContent** HTML content of the email

**Returns:** `array` API response

---

#### `public function sendPasswordRecoveryEmail()`
Sends a password recovery email

**Parameters:**
- `string` **to** Recipient email
- `string` **resetLink** Recovery link

**Returns:** `array` API response

---

#### `private function sendRequest()`
Sends the request to Brevo API using curl

**Parameters:**
- `array` **data** Data to send

**Returns:** `array` API response

---


## Class: `\Shared\Infrastructure\Middleware\AuthMiddleware`
> Application

### Methods

#### `public function requireAdmin()`
Ensures that the current user is authenticated and has an 'Admin' role.

**Returns:** `void` 

---

#### `public function requireAuth()`
Ensures that the current user is authenticated.

**Returns:** `void` 

---

#### `public function apiRequireAuth()`
Ensures that the current user is authenticated for API access.

**Returns:** `void` 

---

#### `public function apiRequireAdmin()`
Ensures that the current user is authenticated and has an 'Admin' role for API access.

**Returns:** `void` 

---

#### `public function requireClient()`
Ensures that the current user is authenticated and has a 'Client' role.

**Returns:** `void` 

---

#### `public function requireSpecialist()`
Requires user to be authenticated and have Especialista role

**Returns:** `void` 

---


## Class: `\Shared\Infrastructure\Pagination\Paginator`
> Application

### Methods

#### `public function getPagination()`
Generates pagination details for a given set of parameters.

**Parameters:**
- `int` **currentPage** The current page number.
- `int` **totalPages** The total number of pages.
- `string` **baseUrl** The base URL for pagination links (e.g., "/users").

**Returns:** `array` An associative array containing pagination data.

---

#### `public function getTotalPages()`
Calculates the total number of pages required for a given number of items.

**Parameters:**
- `int` **totalItems** The total number of items.
- `int` **itemsPerPage** The number of items to display per page.

**Returns:** `int` The total number of pages.

---

#### `public function getOffset()`
Calculates the offset for a given page number.

**Parameters:**
- `int` **page** The current page number.
- `int` **itemsPerPage** The number of items to display per page.

**Returns:** `int` The offset to use in database queries.

---

#### `private function buildUrl()`
Builds a pagination URL for a specific page.

**Parameters:**
- `string` **baseUrl** The base URL without page parameters.
- `int` **page** The target page number.

**Returns:** `string` The constructed URL with the page parameter.

---

#### `public function validatePage()`
Validates a given page number, ensuring it is within valid bounds.

**Parameters:**
- `mixed` **page** The page number to validate.
- `int` **totalPages** The total number of available pages.

**Returns:** `int` The validated and corrected page number.

---


## Class: `\Shared\Presentation\AdminController`
> Controls the display of the main admin dashboard and settings.

### Methods

#### `public function __construct()`
AdminController constructor.

**Parameters:**
- `\Latte\Engine` **latte** The Latte templating engine instance.
- `\Usuarios\Application\UserService|null` **userService** The user management service.
- `\Servicios\Application\ServicioService|null` **servicioService** The salon services service.
- `\Reservas\Application\ReservaService|null` **reservaService** The booking management service.
- `\Especialistas\Infrastructure\EspecialistaServicioRepository|null` **especialistaServicioRepository** 
- `\Especialistas\Infrastructure\EspecialistaRepository|null` **especialistaRepository** 

---

#### `public function index()`
Renders the main admin dashboard page.

**Returns:** `string` The rendered HTML content.

---

#### `public function usersManagement()`
Renders the user management page with filtering and pagination.

**Returns:** `string` The rendered HTML content.

---

#### `public function servicesManagement()`
Renders the services management page.

**Returns:** `string` The rendered HTML content.

---

#### `public function bookingsManagement()`
Renders the bookings management page with filtering and pagination.

**Returns:** `string` The rendered HTML content.

---

#### `private function getUsersFilters()`
Builds the filters array from the request.

**Returns:** `array` 

---

#### `private function enrichUsersWithServices()`
Enriches user data with services if they are specialists.

**Parameters:**
- `array` **usersArray** 

**Returns:** `array` 

---

#### `private function getBookingsFilters()`
Builds the filters array for bookings from the request.

**Returns:** `array` 

---


## Class: `\Shared\Presentation\HomeController`
> Application

### Methods

#### `public function __construct()`
HomeController constructor.

**Parameters:**
- `\Latte\Engine` **latte** The Latte templating engine instance.
- `\Shared\Infrastructure\Email\EmailService` **emailService** The email service instance.

---

#### `public function index()`
Displays the homepage.

**Returns:** `string` The rendered homepage content.

---

#### `public function contact()`
Handles contact form submissions.

**Returns:** `void` 

---


## Class: `\Shared\Presentation\SpecialistController`
> Application

### Methods

#### `public function __construct()`
SpecialistController constructor.

**Parameters:**
- `\Latte\Engine` **latte** The Latte templating engine instance.
- `\Especialistas\Infrastructure\EspecialistaRepository` **especialistaRepository** The repository for specialist data.
- `\Reservas\Infrastructure\ReservaRepository` **reservaRepository** The repository for booking data.

---

#### `public function index()`
Shows specialist dashboard with KPIs and quick actions

**Returns:** `string` 

---

#### `public function bookings()`
Shows specialist bookings with filters and pagination

**Returns:** `string` 

---

#### `public function profile()`
Shows specialist profile with services

**Returns:** `string` 

---

#### `private function getSpecialistPhoto()`
Gets specialist photo URL

**Parameters:**
- `int` **userId** 

**Returns:** `string|null` 

---


## Class: `\Shared\Presentation\StatsApiController`
> Application

### Methods

#### `public function __construct()`
StatsApiController constructor.

**Parameters:**
- `\Especialistas\Infrastructure\EspecialistaRepository` **especialistaRepository** The repository for specialist data.
- `\Reservas\Application\ReservaService` **reservaService** The service for booking operations.
- `\Servicios\Application\ServicioService` **servicioService** The service for salon services management.

---

#### `public function getEspecialistas()`
Returns all specialists with basic user data for admin panel.

**Returns:** `void` 

---

#### `public function getSpecialistOccupancy()`
Returns specialist occupancy statistics for dashboard charts.

**Returns:** `void` 

---

#### `public function getPopularServices()`
Returns popular services statistics for dashboard charts.

**Returns:** `void` 

---

#### `public function getTodayKpis()`
Returns today's KPIs (Key Performance Indicators) for dashboard.

**Returns:** `void` 

---


## Class: `\Usuarios\Application\AuthService`
> Servicio de autenticación y gestión de sesiones

### Methods

#### `public function __construct()`
AuthService constructor.

**Parameters:**
- `\Usuarios\Infrastructure\UserRepository` **userRepository** Repository for user data operations.
- `\Usuarios\Application\UserService` **userService** Service for high-level user management.
- `\Usuarios\Infrastructure\PasswordResetRepository` **passwordResetRepository** Repository for password reset tokens.

---

#### `public function register()`
Registra un nuevo usuario validando datos y verificando email único

**Parameters:**
- `array` **userData** Datos del usuario (nombre, apellidos, email, password, telefono, rol)

**Returns:** `\Usuarios\Domain\Usuario` Usuario creado

---

#### `private function validateUserData()`
Valida el formato y contenido de los datos del usuario

**Parameters:**
- `array` **userData** Datos a validar

**Returns:** `void` 

---

#### `private function validatePassword()`
Valida que la contraseña cumpla con los requisitos de seguridad

**Parameters:**
- `string` **password** Contraseña a validar

**Returns:** `void` 

---

#### `public function login()`
Autentica un usuario verificando email y contraseña

**Parameters:**
- `string` **email** Email del usuario
- `string` **password** Contraseña en texto plano

**Returns:** `\Usuarios\Domain\Usuario|null` Usuario si las credenciales son correctas, null si no

---

#### `public function startSession()`
Inicia una sesión PHP para el usuario autenticado

**Parameters:**
- `\Usuarios\Domain\Usuario` **user** Usuario autenticado

**Returns:** `void` 

---

#### `public function logout()`
Cierra la sesión del usuario actual

**Returns:** `void` 

---

#### `public function getCurrentUser()`
Obtiene el usuario actualmente autenticado

**Returns:** `\Usuarios\Domain\Usuario|null` Usuario actual o null si no hay sesión activa

---

#### `public function isAuthenticated()`
Verifica si hay un usuario autenticado

**Returns:** `bool` True si existe una sesión activa con user_id

---

#### `public function changePassword()`
Cambia la contraseña de un usuario verificando la contraseña actual

**Parameters:**
- `int` **userId** ID del usuario
- `string` **oldPassword** Contraseña actual en texto plano
- `string` **newPassword** Nueva contraseña en texto plano

**Returns:** `bool` True si se cambió correctamente, false si la contraseña actual es incorrecta

---

#### `public function hasRole()`
Verifica si el usuario actual tiene un rol específico

**Parameters:**
- `\Usuarios\Domain\UserRole` **role** Rol a verificar

**Returns:** `bool` True si el usuario tiene el rol especificado

---

#### `public function generatePasswordResetToken()`
Genera un token de recuperación de contraseña para un usuario

**Parameters:**
- `string` **email** Email del usuario

**Returns:** `string` Token generado

---

#### `public function validateResetToken()`
Valida un token de recuperación de contraseña

**Parameters:**
- `string` **token** Token de recuperación

**Returns:** `\Usuarios\Domain\Usuario|null` Usuario si el token es válido, null si no

---

#### `public function resetPassword()`
Resetea la contraseña de un usuario usando un token válido

**Parameters:**
- `string` **token** Token de recuperación
- `string` **newPassword** Nueva contraseña en texto plano

**Returns:** `bool` True si se reseteo correctamente, false si el token es inválido

---


## Class: `\Usuarios\Application\UserService`
> Provides methods for creating, updating, and retrieving users.

### Methods

#### `public function __construct()`
UserService constructor.

**Parameters:**
- `\Usuarios\Infrastructure\UserRepository` **userRepository** The repository for user data operations.

---

#### `public function getAllUsers()`
Obtiene todos los usuarios con paginación

**Parameters:**
- `int` **limit** Número máximo de resultados
- `int` **offset** Desplazamiento para paginación
- `string` **sort** Campo por el que ordenar
- `string` **order** Dirección del ordenamiento (asc/desc)

**Returns:** `\Usuarios\Domain\Usuario[]` Array de usuarios

---

#### `public function getTotalUsers()`
Cuenta el total de usuarios en el sistema

**Returns:** `int` Número total de usuarios

---

#### `public function getUserById()`
Busca un usuario por su ID

**Parameters:**
- `int` **id** ID del usuario

**Returns:** `\Usuarios\Domain\Usuario|null` Usuario encontrado o null

---

#### `public function getUserByRole()`
Obtiene todos los usuarios con un rol específico

**Parameters:**
- `\Usuarios\Domain\UserRole` **role** Rol a filtrar (ADMIN, ESPECIALISTA, CLIENTE)

**Returns:** `\Usuarios\Domain\Usuario[]` Array de usuarios con ese rol

---

#### `public function getUsersByRole()`
Obtiene usuarios por rol con paginación

**Parameters:**
- `string` **rol** Nombre del rol (Admin, Especialista, Cliente)
- `int` **limit** Número máximo de resultados
- `int` **offset** Desplazamiento para paginación
- `string` **sort** Campo por el que ordenar
- `string` **order** Dirección del ordenamiento (asc/desc)

**Returns:** `\Usuarios\Domain\Usuario[]` Array de usuarios con ese rol

---

#### `public function getTotalUsersByRole()`
Cuenta el total de usuarios con un rol específico

**Parameters:**
- `string` **rol** Nombre del rol (Admin, Especialista, Cliente)

**Returns:** `int` Número de usuarios con ese rol

---

#### `public function searchUsers()`
Busca usuarios por nombre, apellidos o email con paginación

**Parameters:**
- `string` **search** Término de búsqueda
- `int` **limit** Número máximo de resultados
- `int` **offset** Desplazamiento para paginación
- `string` **sort** Campo por el que ordenar
- `string` **order** Dirección del ordenamiento (asc/desc)

**Returns:** `\Usuarios\Domain\Usuario[]` Array de usuarios que coinciden con la búsqueda

---

#### `public function getTotalSearchResults()`
Cuenta el total de resultados de una búsqueda

**Parameters:**
- `string` **search** Término de búsqueda

**Returns:** `int` Número de usuarios que coinciden

---

#### `public function getAllUsersWithFilters()`
Obtiene usuarios aplicando múltiples filtros (visión admin)

**Parameters:**
- `array` **filters** Filtros asociados arrays asociativo
- `int` **limit** Límite
- `int` **offset** Desplazamiento

**Returns:** `\Usuarios\Domain\Usuario[]` Array de usuarios

---

#### `public function countAllUsersWithFilters()`
Cuenta el total de usuarios aplicando múltiples filtros

**Parameters:**
- `array` **filters** Filtros asociados arrays asociativo

**Returns:** `int` Total de usuarios

---

#### `public function setUser()`
Crea un nuevo usuario validando datos y verificando email único

**Parameters:**
- `\Usuarios\Domain\Usuario` **user** Usuario a crear

**Returns:** `void` 

---

#### `public function updateUser()`
Actualiza un usuario existente validando datos y email único

**Parameters:**
- `\Usuarios\Domain\Usuario` **user** Usuario con datos actualizados

**Returns:** `void` 

---

#### `private function validateUser()`
Valida los datos de un usuario

**Parameters:**
- `\Usuarios\Domain\Usuario` **user** Usuario a validar

**Returns:** `void` 

---

#### `public function deleteUser()`
Elimina un usuario del sistema

**Parameters:**
- `int` **id** ID del usuario a eliminar

**Returns:** `void` 

---

#### `public function deactivateUser()`
Desactiva un usuario (baja lógica)

**Parameters:**
- `int` **id** ID del usuario a desactivar

**Returns:** `void` 

---

#### `public function activateUser()`
Activa un usuario

**Parameters:**
- `int` **id** ID del usuario a activar

**Returns:** `void` 

---


## Class: `\Usuarios\Domain\Usuario`
> The main user class with all basic profile information.

### Methods

#### `public function __construct()`
Usuario constructor.

**Parameters:**
- `string` **rol** The user's role (e.g., 'Admin', 'Cliente').
- `string` **nombre** The user's first name.
- `string` **apellidos** The user's last name.
- `string` **email** The user's email address.
- `string` **password_hash** The hashed password.
- `string|null` **telefono** The user's phone number (optional).
- `array` **options** Optional settings (fecha_registro, activo, id_usuario).

---

#### `public function getId()`
Gets the user's ID.

**Returns:** `int` The unique ID of the user.

---

#### `public function setId()`
Sets the user's ID.

**Parameters:**
- `int` **id** The unique ID of the user.

**Returns:** `void` 

---

#### `public function getNombre()`
Gets the user's first name.

**Returns:** `string` The first name of the user.

---

#### `public function getApellidos()`
Gets the user's last name.

**Returns:** `string` The last names of the user.

---

#### `public function getEmail()`
Gets the user's email address.

**Returns:** `string` The email address.

---

#### `public function getPassword()`
Gets the user's password hash.

**Returns:** `string` The hashed password.

---

#### `public function getTelefono()`
Gets the user's phone number.

**Returns:** `string|null` The phone number, or null if not set.

---

#### `public function getActivo()`
Checks if the user account is active.

**Returns:** `bool` True if active, false otherwise.

---

#### `public function getRol()`
Gets the user's role.

**Returns:** `\Usuarios\Domain\UserRole` The user's role enum.

---

#### `public function getFechaRegistro()`
Gets the user's registration date.

**Returns:** `\DateTime` The registration timestamp.

---

#### `public function setPassword()`
Sets the user's password hash.

**Parameters:**
- `string` **password** The new hashed password.

**Returns:** `void` 

---

#### `public function setNombre()`
Sets the user's first name.

**Parameters:**
- `string` **nombre** The new first name.

**Returns:** `void` 

---

#### `public function setApellidos()`
Sets the user's last name.

**Parameters:**
- `string` **apellidos** The new last names.

**Returns:** `void` 

---

#### `public function setEmail()`
Sets the user's email address.

**Parameters:**
- `string` **email** The new email address.

**Returns:** `void` 

---

#### `public function setTelefono()`
Sets the user's phone number.

**Parameters:**
- `string|null` **telefono** The new phone number.

**Returns:** `void` 

---

#### `public function fromDatabase()`
Hydrates a Usuario object from a database associative array.

**Parameters:**
- `array` **data** The raw record from the database.

**Returns:** `self` A new instance of Usuario.

---


## Class: `\Usuarios\Infrastructure\PasswordResetRepository`
> Manages all database actions for password reset tokens.

### Methods

#### `public function __construct()`
No description.

**Parameters:**
- `\PDO` **db** The PDO database connection.

---

#### `public function savePasswordResetToken()`
No description.

**Parameters:**
- `int` **userId** User ID
- `string` **token** Reset token
- `string` **expiration** Expiration date (format: Y-m-d H:i:s)

**Returns:** `void` 

---

#### `public function getUserByResetToken()`
No description.

**Parameters:**
- `string` **token** Reset token

**Returns:** `\Usuarios\Domain\Usuario|null` 

---

#### `public function clearResetToken()`
No description.

**Parameters:**
- `int` **userId** User ID

**Returns:** `void` 

---


## Class: `\Usuarios\Infrastructure\UserRepository`
> Performs all CRUD operations for the users table.

### Methods

#### `public function __construct()`
UserRepository constructor.

**Parameters:**
- `\PDO` **db** The PDO database connection.

---

#### `public function getConnection()`
Obtains the PDO connection

**Returns:** `\PDO` PDO connection

---

#### `private function buildOrderBy()`
Builds the ORDER BY clause for queries.

**Parameters:**
- `string` **sort** The column to sort by ('nombre', 'email', 'rol', 'fecha').
- `string` **order** The sorting order ('asc' or 'desc').

**Returns:** `string` The ORDER BY clause.

---

#### `public function getAllUsers()`
Retrieves all users from the database with pagination and sorting.

**Parameters:**
- `int` **limit** The maximum number of users to retrieve.
- `int` **offset** The number of users to skip for pagination.
- `string` **sort** The column to sort by (e.g., 'nombre', 'email', 'rol', 'fecha').
- `string` **order** The sorting order ('asc' or 'desc').

**Returns:** `\Usuarios\Domain\Usuario[]` An array of Usuario objects.

---

#### `public function getTotalUsers()`
Counts the total number of users in the database.

**Returns:** `int` The total number of users.

---

#### `public function searchUsers()`
Searches for users based on a search term with pagination and sorting.

**Parameters:**
- `string` **search** The search term.
- `int` **limit** The maximum number of users to retrieve.
- `int` **offset** The number of users to skip for pagination.
- `string` **sort** The column to sort by.
- `string` **order** The sorting order ('asc' or 'desc').

**Returns:** `\Usuarios\Domain\Usuario[]` An array of Usuario objects matching the search criteria.

---

#### `public function getTotalSearchResults()`
Counts the total number of users matching a search term.

**Parameters:**
- `string` **search** The search term.

**Returns:** `int` The total number of search results.

---

#### `public function getUserById()`
Retrieves a user by their ID.

**Parameters:**
- `int` **id** The ID of the user to retrieve.

**Returns:** `\Usuarios\Domain\Usuario|null` The Usuario object if found, null otherwise.

---

#### `public function getUserByRole()`
Retrieves users by their role.

**Parameters:**
- `\Usuarios\Domain\UserRole` **role** The role to filter by.

**Returns:** `\Usuarios\Domain\Usuario[]` An array of Usuario objects matching the specified role.

---

#### `public function getUsersByRole()`
Retrieves users by role with pagination and sorting.

**Parameters:**
- `string` **rol** The role to filter by.
- `int` **limit** The maximum number of users to retrieve.
- `int` **offset** The number of users to skip for pagination.
- `string` **sort** The column to sort by.
- `string` **order** The sorting order ('asc' or 'desc').

**Returns:** `\Usuarios\Domain\Usuario[]` An array of Usuario objects matching the specified role.

---

#### `public function getTotalUsersByRole()`
Counts the total number of users with a specific role.

**Parameters:**
- `string` **rol** The role to count.

**Returns:** `int` The total number of users with the specified role.

---

#### `public function getUserByEmail()`
Retrieves a user by their email address.

**Parameters:**
- `string` **email** The email address of the user to retrieve.

**Returns:** `\Usuarios\Domain\Usuario|null` The Usuario object if found, null otherwise.

---

#### `public function addUser()`
Adds a new user to the database.

**Parameters:**
- `\Usuarios\Domain\Usuario` **user** The Usuario object to add.

**Returns:** `int` The ID of the newly created user.

---

#### `public function updateUser()`
Updates an existing user in the database.

**Parameters:**
- `\Usuarios\Domain\Usuario` **user** The Usuario object with updated data.

**Returns:** `void` 

---

#### `public function setUserStatus()`
Sets the active status of a user.

**Parameters:**
- `int` **id** The ID of the user.
- `bool` **active** The new active status (true for active, false for inactive).

**Returns:** `void` 

---

#### `public function getResetTokenExpiration()`
Obtiene la fecha de expiración del token de recuperación

**Parameters:**
- `int` **userId** ID del usuario

**Returns:** `string|null` Fecha de expiración o null

---

#### `public function deleteUser()`
Deletes a user from the database.

**Parameters:**
- `int` **id** The ID of the user to delete.

**Returns:** `void` 

---

#### `public function findAllFiltered()`
Obtiene usuarios aplicando múltiples filtros

**Parameters:**
- `array` **filters** Filtros (rol, search, sort, order)
- `int` **limit** Límite de resultados
- `int` **offset** Desplazamiento

**Returns:** `\Usuarios\Domain\Usuario[]` Array de usuarios

---

#### `public function countAllFiltered()`
Cuenta usuarios aplicando múltiples filtros

**Parameters:**
- `array` **filters** Filtros (rol, search, estado)

**Returns:** `int` Total de usuarios

---


## Class: `\Usuarios\Presentation\AuthController`
> Manages all authentication-related HTTP requests and sessions.

### Methods

#### `public function __construct()`
AuthController constructor.

**Parameters:**
- `\Latte\Engine` **latte** The Latte templating engine instance.
- `\Usuarios\Application\AuthService` **authService** Service for authentication logic.
- `\Shared\Infrastructure\Email\EmailService` **emailService** Service for sending emails.
- `\Usuarios\Application\UserService|null` **userService** Service for user management (optional).

---

#### `public function showLogin()`
Displays the login page.

**Returns:** `string` The rendered login page HTML.

---

#### `public function login()`
Handles the login request.

**Returns:** `void` 

---

#### `public function logout()`
Handles the logout request.

**Returns:** `void` 

---

#### `public function showRegister()`
Displays the registration page.

**Returns:** `string` The rendered registration page HTML.

---

#### `public function register()`
Handles the registration request.

**Returns:** `void` 

---

#### `public function showForgotPasswordForm()`
Shows the password recovery form.

**Returns:** `string` Rendered HTML for the forgot password page.

---

#### `public function sendResetLink()`
Processes the password reset link request.

**Returns:** `void` 

---

#### `public function showResetPasswordForm()`
Shows the password reset form.

**Returns:** `string` Rendered HTML for the reset password page.

---

#### `public function resetPassword()`
Processes the password reset request.

**Returns:** `void` 

---

#### `public function showReactivate()`
Shows the account reactivation page.

**Returns:** `string` Rendered HTML for the reactivation page.

---

#### `public function reactivate()`
Processes the account reactivation request.

**Returns:** `void` 

---


## Class: `\Usuarios\Presentation\ProfileController`
> Handles profile-related actions like viewing and updating account details.

### Methods

#### `public function __construct()`
No description.

---

#### `public function index()`
No description.

---

#### `public function update()`
No description.

---

#### `public function delete()`
No description.

---


## Class: `\Usuarios\Presentation\Transformers\UserTransformer`
> Transforms user domain models into presentation-friendly data structures.

### Methods

#### `public function toArray()`
Transforms a single user domain model into a flat array for general presentation.

**Parameters:**
- `\Usuarios\Domain\Usuario` **user** The user domain model to transform.

**Returns:** `array` The transformed array containing user data.

---

#### `public function toArrayCollection()`
Transforms a collection of user domain models into an array of arrays.

**Parameters:**
- `\Usuarios\Domain\Usuario[]` **users** The list of users to transform.

**Returns:** `array[]` A collection of user data arrays.

---

#### `public function toJsonApi()`
Transforms a single user domain model into a structure suitable for JSON APIs.

**Parameters:**
- `\Usuarios\Domain\Usuario` **user** The user domain model to transform.

**Returns:** `array` The user data as an associative array.

---

#### `public function toJsonApiCollection()`
Transforms a collection of user domain models into a JSON API-ready structure.

**Parameters:**
- `\Usuarios\Domain\Usuario[]` **users** The list of users to transform.

**Returns:** `array[]` A collection of user associative arrays.

---

#### `private function getRoleBadgeColor()`
Determines the Bootstrap badge color based on the user's role.

**Parameters:**
- `string` **role** The user role name.

**Returns:** `string` The associated CSS color class.

---


## Class: `\Usuarios\Presentation\UserApiController`
> Provides JSON responses for user management operations.

### Methods

#### `public function __construct()`
No description.

---

#### `public function getAllUsers()`
Retrieves all users with pagination and optional search.

**Returns:** `void` 

---

#### `public function getUserById()`
Retrieves a single user by ID with specialist data if applicable.

**Parameters:**
- `int` **id** User ID

**Returns:** `void` 

---

#### `public function getUsersTable()`
Renders the users table HTML for admin panel.

**Returns:** `string` HTML content

---

#### `public function searchUsersTable()`
Renders the search results table HTML for admin panel.

**Returns:** `string` HTML content

---

#### `public function deleteUser()`
Deactivates a user (soft delete).

**Parameters:**
- `int` **id** User ID

**Returns:** `void` 

---

#### `public function createUser()`
Creates a new user with validation.

**Returns:** `void` 

---

#### `public function updateUser()`
Updates an existing user with validation.

**Parameters:**
- `int` **id** User ID

**Returns:** `void` 

---

#### `public function getCurrentUser()`
Returns the currently authenticated user data.

**Returns:** `void` 

---

#### `private function validateUserData()`
Validates user data using Respect Validation.

**Parameters:**
- `array` **data** User data to validate
- `bool` **requirePassword** Whether password is required

**Returns:** `void` 

---

#### `private function enrichUsersWithServices()`
Enriches user array with specialist services.

**Parameters:**
- `array` **usersArray** Users array to enrich (passed by reference)

**Returns:** `void` 

---

#### `private function handleEspecialistaCreation()`
Handles specialist creation with services and avatar.

**Parameters:**
- `int` **userId** User ID
- `array` **data** Request data

**Returns:** `void` 

---

#### `private function handleEspecialistaUpdate()`
Handles specialist update with services and avatar.

**Parameters:**
- `int` **userId** User ID
- `array` **data** Request data

**Returns:** `void` 

---

#### `private function getRequestData()`
Retrieves request data from JSON or POST.

**Returns:** `array` Request data

---

#### `private function handleAvatarUpload()`
Handles avatar file upload with validation.

**Parameters:**
- `array|null` **file** Uploaded file data

**Returns:** `string|null` Avatar URL or null if upload failed

---


## Class: `\Usuarios\Presentation\UserController`
> Handles HTTP requests for listing, creating, and editing users.

### Methods

#### `public function __construct()`
No description.

---

#### `public function index()`
No description.

---


