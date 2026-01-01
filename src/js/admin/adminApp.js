/**
 * @file Entry point for Admin and Specialist panels.
 */

import Chart from "chart.js/auto";

// Admin modules
import "../admin/bookings/bookingsManager.js";
import "../admin/servicios/servicesManager.js";
import "../admin/usuarios/usersManager.js";

// Charts
import "../admin/charts/popularServices.js";
import "../admin/charts/specialistOccupancy.js";
import "../admin/charts/todayKpis.js";

// Specialist modules
import "../specialist/bookings-filters.js";

// Shared
import "../shared/booking-filters.js";
import "../shared/bookings.js";
import { notification } from "../shared/components/toast.js";

// Expose Chart to window because some inline scripts might use it (legacy support)
window.Chart = Chart;

// Expose toast to window if needed by legacy scripts
window.toast = notification;

// Import and expose Bootstrap
import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;
