# Input de contraseña

## Descripción

Se requiere modificar el input de contraseña tanto de login como de registro para que permita a los usuarios ver y ocultar su contraseña.

## Funcionalidades

- Debe tener un icono que permita al usuario ver y ocultar su contraseña.

## Requisitos

- Debe ser responsive.
- Debe ser accesible.
- Debe ser seguro.

## Diseño debe ser similar al siguiente

```html
<div class="input-group mb-3">
  <button class="btn btn-outline-secondary" type="button" id="button-addon1">Button</button>
  <input
    type="text"
    class="form-control"
    placeholder=""
    aria-label="Example text with button addon"
    aria-describedby="button-addon1"
  />
</div>
```

## Logica

Debe de usar javascript para que al hacer click en el icono se oculte la contraseña y al hacer click en el icono se muestre la contraseña.

## Requisitos de implementación

- La implementación del js debe hacerse de manera exterior al html.
- A ser posible en el js donde se hagan las validaciones. Si este fichero no existe, debe crearse.
- No se debe hacer mas modificaciones al html que las que se requieran para que funcione.
- No se debe usar css, todo debe hacerse con bootstrap.
- Debe de seguirse el concepto kiss.
- En caso de tener dudas preguntar antes de actuar.
