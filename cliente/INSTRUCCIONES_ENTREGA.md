# 📦 Instrucciones de Entrega

## Contenido del Prototipo

La carpeta `cliente/` contiene un prototipo estático completamente funcional e independiente de SR-Peluquería.

## ✅ Verificación antes de entregar

1. **Abrir el archivo**: Haz doble clic en `index.html` para abrirlo en tu navegador
2. **Verificar que todo carga**: Comprueba que:
   - El logo aparece en la navbar
   - La imagen de fondo del hero se muestra
   - Las 4 tarjetas de servicios tienen sus imágenes
   - La imagen de "Quiénes Somos" se visualiza
   - Los estilos de Bootstrap están aplicados (colores rosa, botones, etc.)
3. **Probar responsive**: Redimensiona la ventana del navegador para verificar que se adapta a diferentes tamaños
4. **Probar sin internet**: Desconecta el WiFi y recarga la página - debe funcionar perfectamente

## 📤 Cómo entregar

### Opción 1: Comprimir la carpeta

```bash
cd /home/jorge/Documentos/Fp/proyecto
zip -r cliente.zip cliente/
```

Esto creará un archivo `cliente.zip` que puedes enviar.

### Opción 2: Subir a repositorio

Si usas Git, simplemente haz commit y push de la carpeta `cliente/`:

```bash
git add cliente/
git commit -m "Añadir prototipo estático Bootstrap 5"
git push
```

### Opción 3: Copiar carpeta completa

Simplemente copia toda la carpeta `cliente/` y entrégala tal cual.

## 🎯 Componentes incluidos (según enunciado)

- ✅ **Navbar** con logo y enlaces
- ✅ **Cards** (4 tarjetas en servicios)
- ✅ **Forms** (formulario de contacto)
- ✅ **Buttons** (varios botones primary y outline)
- ✅ **Footer** completo con información
- ✅ **Responsive** con sistema de rejilla Bootstrap
- ✅ **Landing page** explicativa

## 📊 Criterios de evaluación

| Criterio                   | %   | Estado             |
| -------------------------- | --- | ------------------ |
| Diseño responsive          | 30% | ✅ Implementado    |
| Componentes y estructura   | 50% | ✅ Todos incluidos |
| Accesibilidad y usabilidad | 10% | ✅ Cumple          |
| Landing page explicativa   | 10% | ✅ Completa        |

## ⚠️ Importante

- **NO modifiques** la estructura de carpetas `assets/`
- **NO elimines** ningún archivo de `assets/`
- El archivo `index.html` debe estar en la raíz de la carpeta `cliente/`
- Todos los archivos deben entregarse juntos

## 💡 Notas adicionales

- El prototipo es 100% estático (HTML + CSS)
- No requiere servidor web
- No requiere conexión a internet
- Funciona en cualquier navegador moderno
- Cumple todos los requisitos del enunciado
