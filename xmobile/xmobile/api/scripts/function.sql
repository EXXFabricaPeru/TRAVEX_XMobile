CREATE DEFINER=`root`@`localhost` FUNCTION `f_distancia_pedido_cliente`(`latitudPedido` double,`longitudPedido` double,`latitudCliente` double,`longitudCliente` double) RETURNS double
BEGIN
	  DECLARE latitudPedidoR DOUBLE;
		DECLARE longitudPedidoR DOUBLE;
		
		DECLARE latitudClienteR DOUBLE;
		DECLARE longitudClienteR DOUBLE;
		
		declare distancia DOUBLE;
		
		SET latitudPedidoR = (latitudPedido * Pi()) / 180;
		SET longitudPedidoR = (longitudPedido * Pi()) / 180;
		
		SET latitudClienteR = (latitudCliente * Pi()) / 180;
		SET longitudClienteR = (longitudCliente * Pi()) / 180;
		
		set distancia = (ACOS( SIN(latitudPedidoR) * SIN(latitudClienteR) +
														COS(latitudPedidoR) * COS(latitudClienteR) *
														COS(longitudClienteR - longitudPedidoR)
										)*6371); 

	RETURN ROUND(distancia * 1000,2);
END