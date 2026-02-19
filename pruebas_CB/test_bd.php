<?php

require_once __DIR__ . '/bd.php';

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

$hashAdmin = password_hash('AdminPass!1', PASSWORD_DEFAULT);
$hashUser = password_hash('UserPass!1', PASSWORD_DEFAULT);

$usuarios = [
    ['id' => 1, 'usuario' => 'Admin1', 'contrasena' => $hashAdmin],
    ['id' => 2, 'usuario' => 'User9', 'contrasena' => $hashUser],
];

check(buscarUsuarioPorNombre($usuarios, 'Admin1')['id'] === 1, 'buscarUsuarioPorNombre encuentra admin');
check(buscarUsuarioPorNombre($usuarios, 'NoExiste') === null, 'buscarUsuarioPorNombre devuelve null si no existe');

check(verificarCredenciales('AdminPass!1', $hashAdmin) === true, 'verificarCredenciales acepta password correcta');
check(verificarCredenciales('MalPass', $hashAdmin) === false, 'verificarCredenciales rechaza password incorrecta');

$buscar = function (string $usuario) use ($usuarios): ?array {
    return buscarUsuarioPorNombre($usuarios, $usuario);
};

$resultado = resolverFlujoLogin([], $buscar);
check($resultado['motivo'] === 'acceso_directo' && $resultado['redirect'] === '../index.php', 'rama acceso directo sin POST');

$resultado = resolverFlujoLogin(['usuario' => 'NoExiste', 'contrasena' => 'x'], $buscar);
check($resultado['motivo'] === 'usuario_no_existe' && $resultado['redirect'] === '../index.php?error=1', 'rama usuario no existe');

$resultado = resolverFlujoLogin(['usuario' => 'User9', 'contrasena' => 'MalPass'], $buscar);
check($resultado['motivo'] === 'contrasena_incorrecta' && $resultado['redirect'] === '../index.php?error=1', 'rama contraseña incorrecta');

$resultado = resolverFlujoLogin(['usuario' => 'Admin1', 'contrasena' => 'AdminPass!1'], $buscar);
check($resultado['motivo'] === 'login_admin' && $resultado['redirect'] === '../../admin/index.php', 'rama login admin');

$resultado = resolverFlujoLogin(['usuario' => 'User9', 'contrasena' => 'UserPass!1'], $buscar);
check($resultado['motivo'] === 'login_usuario' && $resultado['redirect'] === '../exito.php', 'rama login usuario normal');

echo "\nResumen: {$total} checks, {$fallos} fallos.\n";
exit($fallos > 0 ? 1 : 0);
