-- Carrusel de inicio: 3 segundos en ordenador y 4 en móvil.
-- Para una web que ya está instalada. Importar con phpMyAdmin o por SSH:
--   mysql -u USUARIO -p BASE < database/actualizaciones/2026-11-12-carrusel.sql
-- (También se puede cambiar desde el panel: Ajustes → Inicio.)
INSERT INTO settings (clave, valor) VALUES ('home.carrusel.segundos', '3'), ('home.carrusel.segundos_movil', '4')
  ON DUPLICATE KEY UPDATE valor = VALUES(valor);
