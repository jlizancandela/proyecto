import {
  init_booking_filters,
  init_bookings,
  init_toast,
  notification
} from "./chunk-4FPDOJS5.js";
import {
  __commonJS,
  __esm,
  bootstrap_esm_exports,
  init_bootstrap_esm
} from "./chunk-JPOHOG3X.js";

// node_modules/bootstrap/dist/css/bootstrap.min.css
var init_bootstrap_min = __esm({
  "node_modules/bootstrap/dist/css/bootstrap.min.css"() {
  }
});

// src/js/user/clientApp.js
var require_clientApp = __commonJS({
  "src/js/user/clientApp.js"() {
    init_bookings();
    init_booking_filters();
    init_toast();
    init_bootstrap_esm();
    init_bootstrap_min();
    window.toast = notification;
    window.bootstrap = bootstrap_esm_exports;
  }
});
export default require_clientApp();
//# sourceMappingURL=client.js.map
