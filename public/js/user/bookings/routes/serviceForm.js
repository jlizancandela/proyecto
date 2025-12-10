import { h } from "https://esm.sh/preact@10.19.3";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import htm from "https://esm.sh/htm";
import { Service } from "../components/service.js";
import { $services, $selectedService, $estado } from "../context/bookingsContext.js";

const html = htm.bind(h);

export const ServiceForm = () => {
  const services = useStore($services);
  const selectedService = useStore($selectedService);

  return html`
    <div class="container-fluid p-3 d-flex flex-column align-items-center">
      <div class="row g-3">
        ${services.map(
          (service) =>
            html`
              <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <${Service} service=${service} />
              </div>
            `
        )}
      </div>
      <button
        class="btn btn-primary btn-lg col-3 rounded-pill my-5"
        onclick=${() => $estado.set("DateForm")}
        disabled=${!selectedService}
      >
        Siguiente
      </button>
    </div>
  `;
};
