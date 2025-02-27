CREATE PROCEDURE `pa_reporte_pedido_cliente`()
BEGIN
	SELECT
		`user`.username,
		usuarioconfiguracion.nombre,
		DocType,
		cabeceradocumentos.CardCode,
		cabeceradocumentos.CardName,
		DATE_FORMAT(cabeceradocumentos.fecharegistro,'%d-%m-%Y') as Fecha,
		DATE_FORMAT(cabeceradocumentos.fecharegistro,'%H:%i:%S') as Hora,
		U_LATITUD,
		U_LONGITUD,
		Latitude,
		Longitude,
		DocTotalPay,
		f_distancia_pedido_cliente ( U_LATITUD, U_LONGITUD, clientes.Latitude, clientes.Longitude ) AS 'Distancia' 
	FROM
		cabeceradocumentos
		INNER JOIN `user` ON `user`.id = cabeceradocumentos.idUser
		INNER JOIN usuarioconfiguracion ON usuarioconfiguracion.idUser = `user`.id
	INNER JOIN clientes ON clientes.CardCode = cabeceradocumentos.CardCode;
END