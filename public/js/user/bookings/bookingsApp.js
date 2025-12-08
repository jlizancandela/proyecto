import { h, render } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";
import { useEffect } from "https://esm.sh/preact/hooks";

const html = htm.bind(h);

const bookingsApp = () => {
  //TODO: fetch especialistas
  //TODO: fetch calendario
  //TODO: fetch servicios

  return html`
    <h1>Bookings</h1>
  `;
};

render(h(bookingsApp), document.getElementById("bookings-app"));
