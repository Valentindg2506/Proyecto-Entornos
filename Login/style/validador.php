<?php
	function validarContrasena($password, $minLongitud = 8) {
		// 1. Validar longitud mínima
		if (strlen($password) < $minLongitud) {
		    return false;
		}
	
?>
