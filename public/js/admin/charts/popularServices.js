/**
 * Popular Services Bar Chart Module
 * Fetches and displays bar chart showing most requested services
 */

const servicesChartCanvas = document.getElementById("popularServicesChart");
const servicesLoadingElement = document.getElementById("servicesChartLoading");
const servicesErrorElement = document.getElementById("servicesChartError");

let servicesChart = null;

/**
 * Fetches popular services data from API
 * @return {Promise<Object>}
 */
const fetchServicesData = async () => {
  const response = await fetch("/admin/api/stats/popular-services");
  if (!response.ok) {
    throw new Error("Failed to fetch services data");
  }
  return await response.json();
};

/**
 * Initializes the bar chart with data
 * @param {Object} data - Chart data from API
 */
const initializeServicesChart = (data) => {
  if (!data.success) {
    throw new Error(data.error || "Unknown error");
  }

  const ctx = servicesChartCanvas.getContext("2d");

  servicesChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: data.labels,
      datasets: [
        {
          label: "Reservas Activas",
          data: data.data,
          backgroundColor: "rgba(54, 162, 235, 0.6)",
          borderColor: "rgba(54, 162, 235, 1)",
          borderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            precision: 0,
          },
          title: {
            display: true,
            text: "Número de Reservas",
          },
        },
        x: {
          title: {
            display: true,
            text: "Servicios",
          },
        },
      },
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          callbacks: {
            label: (context) => {
              const value = context.parsed.y || 0;
              return `${value} reserva${value === 1 ? "" : "s"}`;
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
const showServicesLoading = () => {
  servicesLoadingElement.classList.remove("d-none");
  servicesChartCanvas.style.display = "none";
  servicesErrorElement.classList.add("d-none");
};

/**
 * Shows chart
 */
const showServicesChart = () => {
  servicesLoadingElement.classList.add("d-none");
  servicesChartCanvas.style.display = "block";
  servicesErrorElement.classList.add("d-none");
};

/**
 * Shows error state
 */
const showServicesError = () => {
  servicesLoadingElement.classList.add("d-none");
  servicesChartCanvas.style.display = "none";
  servicesErrorElement.classList.remove("d-none");
};

/**
 * Loads and renders the chart
 */
const loadServicesChart = async () => {
  try {
    showServicesLoading();
    const data = await fetchServicesData();
    initializeServicesChart(data);
    showServicesChart();
  } catch (error) {
    console.error("Error loading popular services chart:", error);
    showServicesError();
  }
};

// Initialize chart on page load
if (servicesChartCanvas) {
  loadServicesChart();
}
