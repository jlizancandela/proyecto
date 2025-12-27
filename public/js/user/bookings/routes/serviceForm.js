/**
 * @file Service selection form component.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import htm from "https://esm.sh/htm";
import { Service } from "../components/service.js";
import { $services, $bookingDraft, selectServiceAction } from "../context/bookingsStore.js";

const html = htm.bind(h);

/**
 * Renders the service selection form
 * @returns {import("preact").VNode} Service form component
 */
export const ServiceForm = () => {
  const services = useStore($services);
  const booking = useStore($bookingDraft);

  return html`
    <div class="container-fluid p-3 d-flex flex-column align-items-center">
      <div class="row g-3">
        ${services.map(
          (service) =>
            html`
              <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <${Service}
                  service=${service}
                  onSelect=${selectServiceAction}
                  isSelected=${booking.serviceId === service.id}
                />
              </div>
            `
        )}
      </div>
    </div>
  `;
};
