# Implementar validaciones

# Descripción

Se requiere implementar validaciones en la capa de servicio, para las entidades de usuario y reservas usando la librería Respect Validation.

# Requisitos

- Usar la librería Respect Validation.
- Se usaran las validaciones de la capa de servicio en los controladores implementados.
- Seran validaciones basicas a no ser que hayan ya implementadas, por lo que se seguira la misma logica de validacion.
- No se modificara ninguna funcionalidad existente.
- La logica nueva sera implementada en la capa de servicio.
- Se deve de usar el concepto kiss.
- El codigo debe ser limpio y facil de mantener.
- El codigo debe entenderse por si solo.
- No se usaran comentarios, y en caso de haberlos seran eliminados.

# Requisitos de git y github

- Se hara una nueva rama para esta implementacion.
- Se hara un commit final con el mensaje en ingles y siguendo las convenciones de git.
- El mensaje del commit no debe de tener un cuerpo extenso y no debe ser enrevesado.
- Por ultimo sera un push a github y se hara un pull request.

# Otras consideraciones

- Si hubiera alguna complicacion o duda preguntar antes de proceder.

## Preguntas

- ¿Debo crear un servicio de Reservas? Veo que no existe un ReservaService en la capa de aplicación, solo el
  BookingApiController
  que usa directamente el ReservaRepository. ¿Quieres que cree un ReservaService con validaciones, o prefieres agregar las validaciones directamente en el controlador?
- ¿Qué validaciones específicas necesitas para las Reservas? Por ejemplo:
  Validar que la fecha de reserva sea futura
  Validar formato de hora
  Validar que el estado sea válido
  ¿Alguna otra?
- Para Usuario, ¿debo refactorizar las validaciones existentes en
  AuthService
  ? Actualmente hay validaciones manuales de email, contraseña, etc. ¿Quieres que las reemplace con Respect Validation manteniendo las mismas reglas?

- ¿Debo crear clases de validación separadas o métodos dentro de los servicios? Por ejemplo, UserValidator y ReservaValidator como clases independientes, o métodos validate() dentro de
  UserService
  y ReservaService.

Primero ver como estan implementadas las reservas , si se hacen directamente con el repositorio. En caso de que si, evaluar si se debe crear un servicio de reservas o un metodo de controlador o repositorio.

En las reservas , las vasicas para que no falle la inserccion, luego que no se pueda reservar una hora que ya esta reservada, que no se puedan hacer mas reservas de lo mismo en la misma semana.

refactorizar las ya existentes.

Hacer las validaciones con un nuevo metodo, nada muy complejo como he dicho siguiendo el concepto kiss.
