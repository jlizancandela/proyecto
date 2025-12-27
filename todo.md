# TODO - Refactorización y Testing

## JavaScript - Pendientes de Refactorizar

### 🔴 Alta Prioridad

#### `registerForm.js` (124 líneas)

- [x] Revisar y simplificar validaciones
- [x] Verificar que use `form.elements`
- [x] Añadir file header comment
- [x] Verificar JSDoc en todas las funciones

#### `bookings.js` (109 líneas)

- [x] Auditar para verificar que no duplique SSR
- [x] Extraer fetch calls a `api.js` si existen (No aplica)
- [x] Aplicar patrón `form.elements` (No aplica, usa creación dinámica de form)
- [x] Añadir JSDoc
- [x] Crear tests unitarios

### 🟡 Media Prioridad

#### `specialist/bookings-filters.js` (39 líneas)

- [x] Auditar para verificar SSR
- [x] Refactorizar si tiene fetch calls (No aplica - solo navegación)
- [x] Añadir JSDoc
- [x] File header comment

#### `booking-filters.js` (20 líneas)

- [x] Auditar para verificar SSR
- [x] Añadir JSDoc
- [x] File header comment
- [x] Fix: Export functions to global scope

## PHP - Archivos Grandes que Revisar

### 🔴 Crítico - Archivos >500 líneas

#### `ReservaRepository.php` (Refactored & Tested using DRY) ✅

- [x] Extraer queries complejas a métodos privados (applyFilters)
- [x] Crear tests unitarios (Covered by Pest/Mockery)
- [x] Verificar PHPDoc en todos los métodos restantes
- [x] Considerar extraer lógica de reportes a clase separada (Descartado por simplicidad/KISS)

#### `UserRepository.php` (627 líneas) ✅ Refactored & Tested

- [x] Extraer código duplicado (buildOrderBy method)
- [x] Eliminar método no utilizado (changeUserRole)
- [x] Verificar que todos los métodos se usen
- [x] Reducido de 689 a 627 líneas (-9%)
- [x] Verificar PHPDoc completo ✅ COMPLETADO
- [x] Tests unitarios ✅ 12 tests, 28 assertions

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

- [x] `bookings.js` - Tests para lógica pública
- [x] `booking-filters.js` - Tests completados (5 tests) ✅ ES6 Module
- [x] `specialist/bookings-filters.js` - Tests completados (9 tests) ✅ ES6 Module

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

## Priorización - 1 SEMANA ⏰

### 🔥 Día 1-2: JavaScript Crítico

- [x] `bookings.js` - Auditar SSR y refactorizar
- [x] `registerForm.js` - Verificar form.elements y JSDoc
- [x] Tests para bookings.js si tiene lógica

### 📝 Día 3-4: Documentación Rápida

- [ ] File headers en todos los JS (2-3 líneas)
- [ ] JSDoc en funciones principales
- [ ] PHPDoc en archivos críticos (ReservaRepository, UserRepository)

### 🧹 Día 5: Limpieza de Código

- [ ] Eliminar comentarios redundantes
- [ ] Verificar nombres en inglés
- [ ] Eliminar magic numbers

### 🎯 Día 6-7: Lo Más Crítico

- [x] Revisar ReservaRepository (Refactorizado con éxito)
- [x] Añadir PHPDoc a métodos públicos principales
- [ ] Verificar que todo compile y funcione

## ⚠️ NO HACER (por falta de tiempo)

- ❌ NO dividir archivos grandes PHP (muy arriesgado)
- ❌ NO refactorizar lógica compleja PHP
- ❌ NO tocar Preact components

## Notas

- **KISS**: No sobre-ingenierizar, mantener simple
- **SSR First**: JS solo para AJAX necesario
- **Documentación > Refactoring**: Priorizar documentar sobre cambiar
- **No romper nada**: Mejor documentado que roto
