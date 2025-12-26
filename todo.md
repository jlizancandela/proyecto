# TODO - Refactorización y Testing

## JavaScript - Pendientes de Refactorizar

### 🔴 Alta Prioridad

#### `registerForm.js` (124 líneas)

- [ ] Revisar y simplificar validaciones
- [ ] Verificar que use `form.elements`
- [ ] Añadir file header comment
- [ ] Verificar JSDoc en todas las funciones

#### `bookings.js` (109 líneas)

- [ ] Auditar para verificar que no duplique SSR
- [ ] Extraer fetch calls a `api.js` si existen
- [ ] Aplicar patrón `form.elements`
- [ ] Añadir JSDoc
- [ ] Crear tests unitarios

### 🟡 Media Prioridad

#### `specialist/bookings-filters.js` (39 líneas)

- [ ] Auditar para verificar SSR
- [ ] Refactorizar si tiene fetch calls
- [ ] Añadir JSDoc
- [ ] File header comment

#### `booking-filters.js` (20 líneas)

- [ ] Auditar para verificar SSR
- [ ] Añadir JSDoc
- [ ] File header comment

## PHP - Archivos Grandes que Revisar

### 🔴 Crítico - Archivos >500 líneas

#### `ReservaRepository.php` (955 líneas) ⚠️

- [ ] Revisar si se puede dividir en clases más pequeñas
- [ ] Extraer queries complejas a métodos privados
- [ ] Verificar PHPDoc en todos los métodos
- [ ] Considerar extraer lógica de reportes a clase separada

#### `UserRepository.php` (600 líneas)

- [ ] Revisar si se puede dividir
- [ ] Extraer queries complejas
- [ ] Verificar PHPDoc completo
- [ ] Considerar separar lógica de especialistas

#### `EspecialistaRepository.php` (518 líneas)

- [ ] Revisar complejidad
- [ ] Extraer queries complejas
- [ ] Verificar PHPDoc

#### `UserApiController.php` (496 líneas)

- [ ] Considerar dividir en controladores más específicos
- [ ] Verificar que validaciones usen Respect Validation
- [ ] PHPDoc completo

### 🟡 Revisar - Archivos >300 líneas

#### `router.php` (462 líneas)

- [ ] Considerar agrupar rutas por módulo
- [ ] Documentar cada grupo de rutas

#### `ReservaService.php` (395 líneas)

- [ ] Revisar lógica de negocio
- [ ] Extraer validaciones complejas
- [ ] PHPDoc completo

#### `AuthService.php` (363 líneas)

- [ ] Revisar lógica de autenticación
- [ ] PHPDoc completo

#### `AuthController.php` (339 líneas)

- [ ] Revisar manejo de sesiones
- [ ] PHPDoc completo

## Tests - Pendientes

### JavaScript Unit Tests

#### Falta Testear

- [ ] `bookings.js` - Tests para lógica pública
- [ ] `booking-filters.js` - Tests si tiene lógica
- [ ] `specialist/bookings-filters.js` - Tests si tiene lógica

#### Preact Components (Opcional)

- [ ] Componentes de bookings (si se requiere)
- [ ] Context y hooks (si se requiere)

### PHP Unit Tests (Pest)

- [ ] Revisar coverage actual
- [ ] Identificar servicios sin tests
- [ ] Identificar repositorios sin tests

## Documentación

### File Headers

- [ ] Verificar que todos los JS tengan header comment (2-3 líneas)
- [ ] Verificar que todos los PHP tengan PHPDoc de clase

### JSDoc / PHPDoc

- [ ] Auditar que todas las funciones JS tengan JSDoc
- [ ] Auditar que todos los métodos PHP tengan PHPDoc
- [ ] Verificar tipos de retorno documentados

## Código Limpio

### Comentarios Innecesarios

- [ ] Buscar y eliminar comentarios redundantes en JS
- [ ] Buscar y eliminar comentarios redundantes en PHP
- [ ] Solo mantener JSDoc/PHPDoc

### Constantes y Variables

- [ ] Verificar nombres descriptivos en inglés
- [ ] Verificar que no haya magic numbers
- [ ] Verificar que constantes estén en UPPER_CASE

## Priorización Sugerida

1. **Semana 1**: Refactorizar JS pendientes (bookings.js, filters)
2. **Semana 2**: Revisar y dividir ReservaRepository (955 líneas)
3. **Semana 3**: Revisar y dividir UserRepository (600 líneas)
4. **Semana 4**: Tests PHP con Pest para servicios críticos
5. **Semana 5**: Documentación completa (JSDoc/PHPDoc)

## Notas

- **KISS**: No sobre-ingenierizar, mantener simple
- **SSR First**: JS solo para AJAX necesario
- **Tests**: 100% coverage para APIs y servicios críticos
- **Documentación**: Breve pero completa
