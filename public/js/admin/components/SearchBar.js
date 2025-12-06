(function () {
  const h = window.preact.h;

  window.SearchBar = function ({ value, onChange }) {
    return h(
      "div",
      { className: "mb-3" },
      h("input", {
        type: "text",
        className: "form-control",
        placeholder: "Buscar por nombre, email o teléfono...",
        value: value,
        onInput: (e) => onChange(e.target.value),
      })
    );
  };
})();
