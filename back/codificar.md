####### CODIFICAR ##############	
	<?php
		
	  // Original
	  
		$contrasena = "contraseñasegura1234";
		echo $contrasena;
		echo "<br>";
	  
	  // Codificar
	  
		$codificado = base64_encode($contrasena);
		echo $codificado;
		echo "<br>";
	  
	?>

######## DECODIFICAR ###############
	<?php
		
	  // Original
	  
		$contrasena = "contraseñasegura1234";
		echo $contrasena;
		echo "<br>";
	  
	  // Codificar
	  
		$codificado = base64_encode($contrasena);
		echo $codificado;
		echo "<br>";
	  
	  // Descodificamos
	  
		$descodificado = base64_decode($codificado);
		echo $descodificado;
	 	echo "<br>";
	  
	?>

####### FUNCIÓN CODIFICAR ###########

	<?php
		function codificar($cadena){
		for($i = 0;$i<9;$i++){
			$cadena = base64_encode($cadena);
			}
		return $cadena;
		}
	  
		$contrasena = "contraseñasegura1234";
		echo $contrasena;
		echo "<br>";
	  
		echo codificar($contrasena);
		echo "<br>";
	  
	?>

####### FUNCIÓN DESCODIFICAR ########

	<?php
		function codificar($cadena){
		for($i = 0;$i<9;$i++){
			$cadena = base64_encode($cadena);
		}
		return $cadena;
		}
		function descodificar($cadena){
		for($i = 0;$i<9;$i++){
			$cadena = base64_decode($cadena);
		}
		return $cadena;
		}

		$contrasena = "contraseñasegura1234";
		echo $contrasena;
		echo "<br>";

		$codificado = codificar($contrasena);
		echo $codificado;
		echo "<br>";

		$descodificado = descodificar($codificado);
		echo $descodificado;
		echo "<br>";


	  
	?>

######### HASHEO #######################

	<?php

		$cadena = "Hola";
		echo $cadena;
		echo "<br>";

		// Hash mediante md5

		$picadillo1 = md5($cadena);
		echo $picadillo1;
		echo "<br>";

		// Hash mediante sha1 

		$picadillo2 = sha1($cadena);
		echo $picadillo2;
		echo "<br>";
	  
	?>

##### CONDICIÓN DEL PICADILLO ##########

	<?php

		// Primera ronda //////////////////////////////////

		$cadena = "Hola";
		echo $cadena;
		echo "<br>";

		// Hash mediante md5

		$picadillo1 = md5($cadena);
		echo $picadillo1;
		echo "<br>";

		// Segunda ronda //////////////////////////////////

		$cadena2 = "Hola";
		echo $cadena2;
		echo "<br>";

		// Hash mediante md5

		$picadillo2 = md5($cadena2);
		echo $picadillo2;
		echo "<br>";
	  
	?>

###### COMPROBACIÓN DEL PICADILLO ########

	<?php
		// Tengo este picadillo (imagina que viene de la base de datos)
		$picadillo = "f688ae26e9cfa3ba6235477831d5122e";
		// Cojo lo que envia el usuario
		$contrasena = $_POST['contrasena'];
		// Pico la contraseña
		$picadillo2 = md5($contrasena);
		// Y comparo
		if($picadillo == $picadillo2){
		echo "Las contraseñas coinciden";
		}else{
		echo "Las contraseñas no coinciden";
		}
	?>
	<form method="POST" action="?">
		<input type="text" name="contrasena" placeholder="Prueba una contraseña">
		<input type="submit">
	</form>
