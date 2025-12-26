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

  todayBookingsElement.textContent = "";

  const bookingsCount = document.createElement("span");
  bookingsCount.className = "text-primary";
  bookingsCount.textContent = data.totalBookings;

  const bookingsLabel = document.createElement("small");
  bookingsLabel.className = "text-muted ms-2";
  bookingsLabel.textContent = `reserva${data.totalBookings === 1 ? "" : "s"}`;

  todayBookingsElement.appendChild(bookingsCount);
  todayBookingsElement.appendChild(bookingsLabel);

  todayRevenueElement.textContent = "";

  const revenueAmount = document.createElement("span");
  revenueAmount.className = "text-success";
  revenueAmount.textContent = `${data.estimatedRevenue}€`;

  todayRevenueElement.appendChild(revenueAmount);
};

/**
 * Shows error state
 */
const showKpisError = () => {
  todayBookingsElement.textContent = "";
  todayRevenueElement.textContent = "";

  const bookingsError = document.createElement("span");
  bookingsError.className = "text-danger";
  bookingsError.textContent = "--";

  const revenueError = document.createElement("span");
  revenueError.className = "text-danger";
  revenueError.textContent = "--";

  todayBookingsElement.appendChild(bookingsError);
  todayRevenueElement.appendChild(revenueError);
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
