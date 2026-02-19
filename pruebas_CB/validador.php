<?php

function validarEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validarContrasena(string $contrasena): bool
{
    return preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,16}$/', $contrasena) === 1;
}

function validarUsuario(string $usuario): bool
{
    return preg_match('/^(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9_]{5,20}$/', $usuario) === 1;
}

function validarRegistro(array $datos, callable $usuarioExiste): array
{
    $errores = [];

    $usuario = $datos['usuario'] ?? '';
    $email = $datos['email'] ?? '';
    $contrasena = $datos['contrasena'] ?? '';

    if ($usuarioExiste($usuario)) {
        $errores['usuario'] = 'Este usuario ya está ocupado. Elige otro.';
    }

    if (!validarEmail($email)) {
        $errores['email'] = 'El correo no tiene un formato válido.';
    }

    if (!validarContrasena($contrasena)) {
        $errores['pass'] = 'La contraseña debe tener 8-16 carácteres, 1 Mayúscula y 1 Símbolo';
    }

    if (!validarUsuario($usuario)) {
        $errores['usuario'] = 'El usuario requiere 5-20 caracteres, al menos 1 mayúscula y 1 número.';
    }

    return $errores;
}
