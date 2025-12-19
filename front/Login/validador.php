<?php
	function validarContrasena($password, $min = 8, $max = 16) {
		$longitud = strlen($password);
		$simbolos = '!@#$%^&*()_+-=[]{}|;:,.<>?';

		// 1. Validar Longitud
		if ($longitud < $min || $longitud > $max) {
		    return false;
		}
		// 2. Validar Símbolo
		if (strpbrk($password, $simbolos) === false) {
		    return false;
		}
		return true;
	}

	foreach ($pruebas as $pass => $caso) {
		$esValida = validarContrasena($pass);
		// Operador ternario para mostrar texto en lugar de true/false
		$resultado = $esValida ? "✅ VÁLIDA" : "❌ INVÁLIDA";
		
		echo "Prueba: '$pass' | Caso: $caso | Resultado: $resultado <br>";
	}
?>
