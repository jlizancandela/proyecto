/**
 * @file Database configuration helper
 * @description Loads database configuration from environment variables
 */

const dbConfig = {
  host: process.env.DB_HOST || "127.0.0.1",
  port: parseInt(process.env.DB_PORT || "32773"),
  user: process.env.DB_USER || "db",
  password: process.env.DB_PASSWORD || "db",
  database: process.env.DB_NAME || "sistema_reservas",
};

module.exports = { dbConfig };
