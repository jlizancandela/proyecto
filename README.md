# SR-Peluquería

Proyecto Intermodular para el Ciclo Superior de Desarrollo de Aplicaciones Web (DAW).
Aplicación web para la gestión de reservas en una peluquería.

## 🛠️ Instalación

1.  **Clonar el repositorio**

    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd proyecto
    ```

2.  **Configurar entorno**
    Copia el archivo de ejemplo para crear tu configuración local:

    ```bash
    cp .env.example .env
    ```

    > **Nota:** Para ver las credenciales de la base de datos y otros servicios, ejecuta `ddev describe` una vez iniciado el entorno. Por defecto en DDEV suelen ser `db` / `db` / `db`.

3.  **Iniciar el entorno** (requiere DDEV instalado)

    ```bash
    ddev start
    ```

4.  **Instalar dependencias**

    ```bash
    ddev composer install
    ```

5.  **Base de Datos**
    Importar la estructura inicial:

    ```bash
    ddev mysql < init.sql
    ```

    (Opcional) Cargar usuarios de prueba:

    ```bash
    ddev mysql < database/test_users.sql
    ```

6.  **Acceder**
    Entra en: https://proyecto.ddev.site

