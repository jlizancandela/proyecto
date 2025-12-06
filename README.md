# SR-Peluquería

Proyecto Intermodular para el Ciclo Superior de Desarrollo de Aplicaciones Web (DAW).
Aplicación web para la gestión de reservas en una peluquería.

## 🛠️ Instalación

1.  **Clonar el repositorio**

    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd proyecto
    ```

2.  **Iniciar el entorno** (requiere DDEV instalado)

    ```bash
    ddev start
    ```

3.  **Instalar dependencias**

    ```bash
    ddev composer install
    ```

4.  **Base de Datos**
    Importar la estructura inicial:

    ```bash
    ddev mysql < init.sql
    ```

    (Opcional) Cargar usuarios de prueba:

    ```bash
    ddev mysql < database/test_users.sql
    ```

5.  **Acceder**
    Entra en: https://proyecto.ddev.site
