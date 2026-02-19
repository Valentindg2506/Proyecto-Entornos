<?php

require_once __DIR__ . '/validador.php';

$total = 0;
$fallos = 0;

function check($condicion, string $mensaje): void
{
    global $total, $fallos;
    $total++;
    if (!$condicion) {
        $fallos++;
        echo "[FAIL] {$mensaje}\n";
        return;
    }
    echo "[OK]   {$mensaje}\n";
}

check(validarEmail('usuario@dominio.com') === true, 'validarEmail acepta email correcto');
check(validarEmail('correo-invalido') === false, 'validarEmail rechaza email incorrecto');

check(validarContrasena('Abcd123!') === true, 'validarContrasena acepta contraseña válida');
check(validarContrasena('abcd1234') === false, 'validarContrasena rechaza sin mayúscula y símbolo');
check(validarContrasena('ABC!1') === false, 'validarContrasena rechaza longitud insuficiente');

check(validarUsuario('User1') === true, 'validarUsuario acepta usuario válido');
check(validarUsuario('user1') === false, 'validarUsuario rechaza sin mayúscula');
check(validarUsuario('User') === false, 'validarUsuario rechaza sin número');
check(validarUsuario('User-1') === false, 'validarUsuario rechaza caracteres no permitidos');

$datosValidos = [
    'usuario' => 'User9',
    'email' => 'ok@dominio.com',
    'contrasena' => 'ClaveSegura!'
];
$errores = validarRegistro($datosValidos, fn(string $usuario): bool => false);
check(empty($errores), 'validarRegistro no devuelve errores con datos válidos');

$datosIncompletos = [
    'usuario' => 'usr',
    'email' => 'mal-correo',
    'contrasena' => 'abc'
];
$errores = validarRegistro($datosIncompletos, fn(string $usuario): bool => false);
check(isset($errores['email']), 'validarRegistro detecta email inválido');
check(isset($errores['pass']), 'validarRegistro detecta contraseña inválida');
check(isset($errores['usuario']), 'validarRegistro detecta usuario inválido');

$datosUsuarioRepetido = [
    'usuario' => 'User9',
    'email' => 'ok@dominio.com',
    'contrasena' => 'ClaveSegura!'
];
$errores = validarRegistro($datosUsuarioRepetido, fn(string $usuario): bool => $usuario === 'User9');
check(isset($errores['usuario']), 'validarRegistro evalúa rama de usuario existente');

echo "\nResumen: {$total} checks, {$fallos} fallos.\n";
exit($fallos > 0 ? 1 : 0);
