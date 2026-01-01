/**
 * @file Entry point for Client panel pages (My Bookings, Profile, etc).
 */

import "../shared/bookings.js";
import "../shared/booking-filters.js";
import { notification } from "../shared/components/toast.js";

// Expose toast
window.toast = notification;

// Import and expose Bootstrap
import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;
import "bootstrap/dist/css/bootstrap.min.css";
