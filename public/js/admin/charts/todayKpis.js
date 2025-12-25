/**
 * Today's KPIs Module
 * Fetches and displays today's key performance indicators
 */

const todayBookingsElement = document.getElementById("todayBookingsCount");
const todayRevenueElement = document.getElementById("todayRevenueAmount");
const kpisErrorElement = document.getElementById("kpisError");

/**
 * Fetches today's KPI data from API
 * @return {Promise<Object>}
 */
const fetchTodayKpis = async () => {
  const response = await fetch("/admin/api/stats/today-kpis");
  if (!response.ok) {
    throw new Error("Failed to fetch KPIs");
  }
  return await response.json();
};

/**
 * Updates KPI display with data
 * @param {Object} data - KPI data from API
 */
const updateKpis = (data) => {
  if (!data.success) {
    throw new Error(data.error || "Unknown error");
  }

  // Update bookings count
  todayBookingsElement.innerHTML = `
    <span class="text-primary">${data.totalBookings}</span>
    <small class="text-muted ms-2">reserva${data.totalBookings === 1 ? "" : "s"}</small>
  `;

  // Update revenue amount
  todayRevenueElement.innerHTML = `
    <span class="text-success">${data.estimatedRevenue}€</span>
  `;
};

/**
 * Shows error state
 */
const showKpisError = () => {
  todayBookingsElement.innerHTML = '<span class="text-danger">--</span>';
  todayRevenueElement.innerHTML = '<span class="text-danger">--</span>';
  kpisErrorElement.classList.remove("d-none");
};

/**
 * Loads and displays KPIs
 */
const loadTodayKpis = async () => {
  try {
    const data = await fetchTodayKpis();
    updateKpis(data);
  } catch (error) {
    console.error("Error loading today's KPIs:", error);
    showKpisError();
  }
};

// Initialize KPIs on page load
if (todayBookingsElement && todayRevenueElement) {
  loadTodayKpis();
}
