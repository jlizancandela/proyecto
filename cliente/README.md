# Prototipo Estático - SR-Peluquería

## 📋 Descripción

Este es un prototipo estático de la landing page de SR-Peluquería, desarrollado con **Bootstrap 5** según los requisitos del proyecto intermodular.

## 🎯 Componentes de Bootstrap Incluidos

El prototipo incluye los siguientes componentes de Bootstrap requeridos:

### ✅ Elementos Obligatorios

- **Navbar** (Menú superior con logotipo y enlaces)
- **Cards** (4 tarjetas en la sección de servicios)
- **Forms** (Formulario de contacto con inputs y textarea)
- **Buttons** (Botones primary y outline en navbar y hero)
- **Footer** (Con información de contacto y redes sociales)

### 📱 Características Responsive

- Sistema de rejilla de Bootstrap (`row`, `col-12`, `col-md-6`, etc.)
- Clases responsive (`d-lg-none`, `d-none d-lg-inline-block`)
- Navbar colapsable para dispositivos móviles
- Imágenes responsive con `img-fluid`
- Contenedores y espaciado adaptativo

## 🎨 Personalización CSS

El archivo `style.css` incluye **solo 63 líneas** de CSS personalizado mínimo para casos que no se pueden resolver con Bootstrap:

1. **Variables de color** - Personalización del tema rosa (#E83E8C) y rosa claro (#FFF0F3)
2. **Estilos de botones** - Asegurar que los botones usen los colores personalizados
3. **Estilos de formularios** - Aplicar colores rosa al hacer foco en inputs
4. **Estilos de enlaces** - Aplicar color rosa a los enlaces
5. **Centrado del menú de navegación** - No se puede hacer solo con clases de Bootstrap
6. **Imagen de fondo del hero** - Requiere CSS para `background-image`

**Nota importante**: Se evita CSS adicional siguiendo estrictamente las pautas del enunciado. Solo se incluyen estilos necesarios para aplicar los colores personalizados y casos que Bootstrap no puede manejar.

### Paleta de Colores

- **Primary**: #E83E8C (Rosa)
- **Light**: #FFF0F3 (Rosa claro)

## 📄 Estructura de la Landing Page

La página incluye las siguientes secciones:

1. **Header/Navbar**

   - Logo de la peluquería
   - Menú de navegación (Inicio, Servicios, Contacto)
   - Botones de Iniciar Sesión y Registrarse

2. **Hero Section**

   - Imagen de fondo con overlay oscuro
   - Título principal y subtítulo
   - Call-to-action (Reservar una Cita)

3. **Quiénes Somos** (con Carousel)

   - Carousel de galería con 4 imágenes
   - Transición automática cada 3 segundos
   - Descripción de la empresa

4. **Servicios** (con Cards)

   - Cortes de pelo
   - Coloración
   - Tratamientos
   - Peinados

5. **Contacto** (con Form)

   - Formulario con campos: Nombre, Email, Mensaje
   - Botón de envío

6. **Footer**
   - Logo de la empresa
   - Información de la empresa
   - Datos de contacto
   - Iconos de redes sociales (Instagram, Facebook, TikTok)
   - Copyright y enlaces legales

## 🚀 Cómo Visualizar

1. Abre el archivo `index.html` directamente en cualquier navegador web
2. **No requiere servidor local**
3. **No requiere conexión a internet** - Todos los recursos están incluidos localmente

## 📁 Estructura del Proyecto

```
cliente/
├── assets/
│   ├── css/
│   │   ├── bootstrap.min.css      # Bootstrap 5.3.8 local
│   │   └── style.css              # Estilos personalizados
│   ├── images/
│   │   ├── coloracion.avif
│   │   ├── corte.avif
│   │   ├── hero-salon.avif
│   │   ├── logo.png
│   │   ├── peinados.avif
│   │   ├── tratamientos.avif
│   │   └── whoami.avif
│   └── js/
│       └── bootstrap.bundle.min.js # Bootstrap JS local
├── enunciado.md                     # Requisitos del proyecto
├── index.html                       # Página principal
├── INSTRUCCIONES_ENTREGA.md         # Guía de entrega
└── README.md                        # Este archivo
```

## 📝 Notas Importantes

- **Completamente independiente**: Todos los recursos (CSS, JS, imágenes) están incluidos en la carpeta `assets/`
- Bootstrap 5.3.8 está descargado localmente (no usa CDN)
- El CSS personalizado está en `assets/css/style.css` (buenas prácticas)
- El archivo es completamente estático (sin PHP ni JavaScript personalizado)
- Cumple con todos los requisitos del enunciado del proyecto
- **Puede funcionar sin conexión a internet**

## ✨ Criterios de Evaluación Cubiertos

- ✅ **Diseño responsive** (30%): Sistema de rejilla y adaptación a diferentes dispositivos
- ✅ **Componentes y estructura** (50%): Todos los componentes obligatorios presentes
- ✅ **Accesibilidad y usabilidad** (10%): Estructura clara, contraste de colores adecuado
- ✅ **Landing page explicativa** (10%): Presenta el proyecto de forma clara y atractiva
