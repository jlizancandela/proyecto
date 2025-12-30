# 📝 Plan de Ejecución de Tests: SR-Peluquería

Este listado representa la batería de pruebas necesaria para validar el cumplimiento del **Proyecto Intermodular de Reservas**.

## 🔐 Módulo 1: Gestión de Usuarios y Acceso

- [x] **Validación de Alta de Usuario:** Verificar que el sistema valida formatos (email, longitud de campos) antes de guardar en la BD.

- [x] **Funcionalidad "El Ojo":** Comprobar que el botón conmuta la visibilidad de la contraseña en login y registro.

- [x] **Login de Usuario Activo:** Confirmar que un usuario con credenciales correctas inicia sesión y crea la sesión.

- [x] **Baja Lógica de Usuario:** Acceder al perfil, solicitar baja y verificar que el campo `activo` cambia a `0` en la BD.

- [x] **Reactivación de Cuenta:** Intentar login con cuenta inactiva, verificar que el sistema ofrece reactivarla y permite el acceso tras aceptar.

- [x] **Recuperación de Contraseña:** Validar el flujo de "Olvidé mi contraseña" hasta el envío o cambio de la misma.

## 📅 Módulo 2: Sistema de Reservas (Cliente)

- [x] **Disponibilidad en Calendario:** Seleccionar un recurso y verificar que el calendario carga días y horas disponibles mediante JS.

- [x] **Restricción de Frecuencia (JS):** Intentar reservar el mismo tipo de recurso dos veces en la misma semana y confirmar que el sistema lo impide.
- [x] **Control de Carga:** Validar que no se exceda la cantidad de horas permitidas por usuario en un periodo (40h/semana).
- [x] **Conflictos de Horario:** Verificar que no se permitan solapamientos de citas para el mismo usuario.

- [x] **Cancelación de Reserva Propia:** El usuario puede eliminar una de sus reservas desde su listado personal.

- [x] **Visualización Personal:** Comprobar que el usuario ve solo sus reservas y los recursos disponibles.

## 👑 Módulo 3: Panel de Administrador (Gestión Global)

- [x] **Listado de Clientes:** Verificar visualización, búsqueda, ordenación y paginación de los usuarios registrados.

- [x] **Protección de Auto-Baja:** Intentar que el administrador se dé de baja a sí mismo o se cambie el rol; el sistema debe bloquearlo.

- [x] **Gestión de Recursos:** Insertar, editar y aplicar baja lógica a un recurso (Especialista/Servicio).

- [x] **Gestión de Reservas de Terceros:** El administrador crea o edita una reserva para cualquier usuario sin restricciones de tiempo.

## 📂 Módulo 4: Lógica de Eliminación e Historial

- [ ] **Borrado Definitivo (Pendientes):** El administrador elimina físicamente una reserva cuya fecha aún no ha llegado.

- [ ] **Bloqueo de Borrado (Pasadas):** Intentar borrar una reserva antigua y verificar que el sistema la mantiene para el historial.

- [ ] **Generación de PDF:** El administrador descarga con éxito el historial de reservas en formato PDF.

## 📱 Módulo 5: UX y Responsividad

- [ ] **Diseño Responsive:** Verificar que el menú y los formularios se adaptan correctamente a vistas de móvil y tablet.

- [ ] **Optimización de Activos:** Comprobar que las imágenes y fuentes cargan de forma eficiente.

---

### 💡 Sugerencia de implementación:

Empieza por los tests de **Módulo 4 (Lógica de Eliminación)**, ya que son los que demuestran al profesor que has entendido la diferencia entre "borrado físico" y "mantenimiento del historial", que es un punto crítico de la rúbrica.
