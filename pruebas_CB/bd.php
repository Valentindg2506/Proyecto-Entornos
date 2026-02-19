<?php

function buscarUsuarioPorNombre(array $usuarios, string $usuario): ?array
{
    foreach ($usuarios as $fila) {
        if (($fila['usuario'] ?? null) === $usuario) {
            return $fila;
        }
    }

    return null;
}

function verificarCredenciales(string $contrasenaPlano, string $hashGuardado): bool
{
    return password_verify($contrasenaPlano, $hashGuardado);
}

function resolverFlujoLogin(array $post, callable $buscarUsuario): array
{
    if (!isset($post['usuario']) || !isset($post['contrasena'])) {
        return [
            'ok' => false,
            'redirect' => '../index.php',
            'motivo' => 'acceso_directo'
        ];
    }

    $usuario = $post['usuario'];
    $passIngresada = $post['contrasena'];
    $fila = $buscarUsuario($usuario);

    if ($fila === null) {
        return [
            'ok' => false,
            'redirect' => '../index.php?error=1',
            'motivo' => 'usuario_no_existe'
        ];
    }

    $hashGuardado = $fila['contrasena'] ?? '';

    if (!verificarCredenciales($passIngresada, $hashGuardado)) {
        return [
            'ok' => false,
            'redirect' => '../index.php?error=1',
            'motivo' => 'contrasena_incorrecta'
        ];
    }

    if (($fila['usuario'] ?? '') === 'Admin1') {
        return [
            'ok' => true,
            'redirect' => '../../admin/index.php',
            'motivo' => 'login_admin',
            'session' => [
                'usuario' => $fila['usuario'],
                'id_usuario' => $fila['id'] ?? null
            ]
        ];
    }

    return [
        'ok' => true,
        'redirect' => '../exito.php',
        'motivo' => 'login_usuario',
        'session' => [
            'usuario' => $fila['usuario'] ?? null,
            'id_usuario' => $fila['id'] ?? null
        ]
    ];
}
