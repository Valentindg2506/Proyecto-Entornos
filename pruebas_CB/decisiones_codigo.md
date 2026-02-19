# Lista de decisiones del código (punto 3)

Fecha: 2026-02-19

## Flujo correcto del login

Referencia funcional replicada desde `front/controladores/login_procesa.php`.

1. **Entrada del formulario**
	- Si faltan `usuario` o `contrasena` en POST, el flujo termina con redirección a `../index.php`.

2. **Búsqueda de usuario**
	- Si el usuario no existe en la fuente de datos, redirección a `../index.php?error=1`.

3. **Verificación de contraseña**
	- Si `password_verify` falla, redirección a `../index.php?error=1`.

4. **Decisión por rol/nombre de usuario**
	- Si el usuario autenticado es `Admin1`, redirección a `../../admin/index.php`.
	- Si no es `Admin1`, redirección a `../exito.php`.

5. **Datos de sesión en login válido**
	- Se inicializan `usuario` e `id_usuario`.

## Flujo correcto del validador (registro)

Referencia funcional replicada desde `front/controladores/registro_procesa.php`.

1. **Usuario existente**
	- Si el usuario ya existe, se registra error `usuario`.

2. **Formato de email**
	- Si `filter_var(..., FILTER_VALIDATE_EMAIL)` falla, se registra error `email`.

3. **Regla de contraseña**
	- Debe cumplir regex: `^(?=.*[A-Z])(?=.*[\W_]).{8,16}$`.
	- Si no cumple, se registra error `pass`.

4. **Regla de usuario**
	- Debe cumplir regex: `^(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9_]{5,20}$`.
	- Si no cumple, se registra/actualiza error `usuario`.

5. **Decisión final**
	- Si hay errores, el registro no continúa.
	- Si no hay errores, el registro se considera válido.

## Cobertura de decisiones realizada

- Login: todas las ramas ejecutadas al menos una vez.
  - Acceso directo sin POST.
  - Usuario no existente.
  - Contraseña incorrecta.
  - Login admin (`Admin1`).
  - Login usuario normal.

- Validador: todas las validaciones ejecutadas y verificadas.
  - Email válido e inválido.
  - Contraseña válida e inválida.
  - Usuario válido e inválido.
  - Rama de usuario existente.
  - Rama sin errores.
