ALTER TABLE `persona` 
ADD COLUMN `documentoIdentidadPersona` varchar(255) NULL DEFAULT NULL AFTER `fechaUMPersona`;

ALTER TABLE `persona` 
ADD UNIQUE INDEX `docuementoIdentidad`(`documentoIdentidadPersona`);