-- Migration: Add activo field to SERVICIO table for soft delete functionality

USE sistema_reservas;

-- Add activo column to SERVICIO table
ALTER TABLE SERVICIO
ADD COLUMN activo BOOLEAN DEFAULT TRUE NOT NULL AFTER descripcion;

-- Set all existing services to active
UPDATE SERVICIO SET activo = TRUE WHERE activo IS NULL;

-- Add index for better query performance
CREATE INDEX idx_activo ON SERVICIO(activo);
