/**
 * Specialist Occupancy Chart Module
 * Fetches and displays pie chart showing booking distribution across specialists
 */

const chartCanvas = document.getElementById("specialistOccupancyChart");
const loadingElement = document.getElementById("chartLoading");
const errorElement = document.getElementById("chartError");

let occupancyChart = null;

/**
 * Fetches specialist occupancy data from API
 * @return {Promise<Object>}
 */
const fetchOccupancyData = async () => {
  const response = await fetch("/admin/api/stats/specialist-occupancy");
  if (!response.ok) {
    throw new Error("Failed to fetch occupancy data");
  }
  return await response.json();
};

/**
 * Initializes the pie chart with data
 * @param {Object} data - Chart data from API
 */
const initializeChart = (data) => {
  if (!data.success) {
    throw new Error(data.error || "Unknown error");
  }

  const ctx = chartCanvas.getContext("2d");

  occupancyChart = new Chart(ctx, {
    type: "pie",
    data: {
      labels: data.labels,
      datasets: [
        {
          label: "Reservas Activas",
          data: data.data,
          backgroundColor: data.colors,
          borderColor: data.colors.map((color) => color.replace("0.8", "1")),
          borderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            padding: 15,
            font: {
              size: 12,
            },
          },
        },
        tooltip: {
          callbacks: {
            label: (context) => {
              const label = context.label || "";
              const value = context.parsed || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
              return `${label}: ${value} reservas (${percentage}%)`;
            },
          },
        },
      },
    },
  });
};

/**
 * Shows loading state
 */
const showLoading = () => {
  loadingElement.classList.remove("d-none");
  chartCanvas.style.display = "none";
  errorElement.classList.add("d-none");
};

/**
 * Shows chart
 */
const showChart = () => {
  loadingElement.classList.add("d-none");
  chartCanvas.style.display = "block";
  errorElement.classList.add("d-none");
};

/**
 * Shows error state
 */
const showError = () => {
  loadingElement.classList.add("d-none");
  chartCanvas.style.display = "none";
  errorElement.classList.remove("d-none");
};

/**
 * Loads and renders the chart
 */
const loadChart = async () => {
  try {
    showLoading();
    const data = await fetchOccupancyData();
    initializeChart(data);
    showChart();
  } catch (error) {
    console.error("Error loading specialist occupancy chart:", error);
    showError();
  }
};

// Initialize chart on page load
if (chartCanvas) {
  loadChart();
}
