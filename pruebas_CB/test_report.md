# Informe de pruebas

Fecha: 2026-02-19

## Alcance

- Ejecución de pruebas de `validador.php`.
- Ejecución de pruebas de `bd.php` y flujo completo de login.
- Verificación de ramas de decisión del código.

## Comando ejecutado

```bash
cd '/var/www/html/GitHub/Proyecto-Entornos copy/pruebas_CB' && php test_validador.php && php test_bd.php
```

## Resultado de ejecución

### test_validador.php

- Checks ejecutados: 14
- Fallos: 0
- Estado: OK

Cobertura verificada:
- Todas las funciones del validador ejecutadas al menos una vez.
- Todas las validaciones probadas (email, contraseña, usuario, usuario existente).
- Rama de datos válidos y rama de datos inválidos ejecutadas.

### test_bd.php

- Checks ejecutados: 9
- Fallos: 0
- Estado: OK

Cobertura verificada:
- Todas las funciones del módulo de login ejecutadas al menos una vez.
- Todas las ramas del flujo de login ejecutadas:
	- acceso directo sin POST,
	- usuario no existente,
	- contraseña incorrecta,
	- login admin,
	- login usuario normal.

## Resultado global

- Total checks: 23
- Total fallos: 0
- Conclusión: todos los tests funcionan correctamente.

## Incidencias / resultados no esperados

- No se detectaron resultados no esperados durante la ejecución.
