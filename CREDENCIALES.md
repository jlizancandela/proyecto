# Credenciales de Usuarios - Sistema de Reservas

## Usuarios Existentes (del init.sql)

### Administrador Existente

- **Email:** admin@sistema.com
- **Password:** admin123
- **Rol:** Admin
- **Nombre:** Carlos González Pérez

### Clientes Existentes

- **Email:** maria.lopez@email.com
- **Password:** cliente123
- **Rol:** Cliente

- **Email:** juan.rodriguez@email.com
- **Password:** cliente123
- **Rol:** Cliente

### Especialistas Existentes

- **Email:** ana.fernandez@email.com
- **Password:** especialista123
- **Rol:** Especialista

- **Email:** pedro.martin@email.com
- **Password:** especialista123
- **Rol:** Especialista

- **Email:** laura.sanchez@email.com
- **Password:** especialista123
- **Rol:** Especialista

## Nuevos Usuarios para Desarrollo (DDEV)

### Administrador de Desarrollo

- **Email:** admin@dev.com
- **Password:** password
- **Rol:** Admin
- **Nombre:** Admin Desarrollo

### Usuario Normal de Desarrollo

- **Email:** usuario@dev.com
- **Password:** password
- **Rol:** Cliente
- **Nombre:** Usuario Normal

## Comandos DDEV para ejecutar

Para aplicar los usuarios de desarrollo:

```bash
ddev mysql < database/ddev_users.sql
```

Para reiniciar la base de datos completamente:

```bash
ddev mysql < init.sql
ddev mysql < database/ddev_users.sql
```
