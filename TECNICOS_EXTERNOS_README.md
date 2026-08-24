# Módulo de Técnicos Externos y Órdenes Externas

Registro de los productos que se entregan a talleres o técnicos de terceros:
quién los entrega, con qué motivo, quién los recoge de vuelta y cuánto cobró el técnico.

---

## 1. Instalación

Ejecuta **una** de estas dos opciones:

```bash
# Opción A: desde terminal
php config/setup_tecnicos_externos.php

# Opción B: importando el SQL directamente
mysql -u USUARIO -p BASE_DE_DATOS < config/setup_tecnicos_externos.sql
```

También puedes abrir `config/setup_tecnicos_externos.php` desde el navegador.

Ambos scripts son **idempotentes**: se pueden ejecutar varias veces sin duplicar datos.

### Qué crea la instalación

| Objeto | Tipo | Descripción |
|---|---|---|
| `motivo_orden_externa` | tabla | Catálogo de motivos. Se siembra con Reparación, Garantía y Revisión |
| `tecnico_externo` | tabla | Catálogo de técnicos / talleres externos |
| `orden_tecnico_externo` | tabla | Las órdenes (el formulario) |
| `TE` | opción | Menú principal "Técnicos Externos" |
| `TE01` | opción | Órdenes Externas → `ordenes-externas` |
| `TE02` | opción | Nueva Orden Externa → `ordenes-externas/create` |
| `TE03` | opción | Gestionar Técnicos Externos → `tecnicos-externos` |
| `CF05` | opción | Motivos de Orden Externa → `configuracion/motivos-externos` |

Las opciones se asignan automáticamente a los perfiles **1 (Administrador)** y
**10 (Técnico Administrador)**.

---

## 1.1 Datos de ejemplo (opcional)

Para ver el módulo con contenido real:

```bash
mysql -u USUARIO -p BASE_DE_DATOS < config/datos_ejemplo_tecnicos_externos.sql
```

Crea 4 técnicos externos y 5 órdenes que cubren **los tres estados** y **los tres motivos**:

| Cód. Orden | Técnico | Motivo | Estado | Precio |
|---|---|---|---|---|
| `OE-0001` | Carlos Ramírez (ElectroFix) | Reparación | Entregado | $85.000 |
| `OE-0002` | Marta Gutiérrez (Refrimundo) | Garantía | Recibido | $0 |
| `OE-0003` | Andrés Peña (TecnoDisplay) | Revisión | Recibido | $45.000 |
| `OE-0004` | Carlos Ramírez (ElectroFix) | Reparación | Entregado | $120.000 |
| `OE-0005` | Marta Gutiérrez (Refrimundo) | Reparación | Anulado | $200.000 |

El cuarto técnico (*Servicios JR*) queda **inactivo** a propósito, para que se vea
cómo se comporta la baja lógica: no aparece en el selector de órdenes nuevas.

Las fechas son relativas a `CURDATE()`, así que el listado siempre luce reciente.
El script es idempotente. **Para borrar los datos de ejemplo:**

```sql
DELETE FROM orden_tecnico_externo WHERE CodOrden LIKE 'OE-000%';
DELETE FROM tecnico_externo WHERE documento IN ('900123456','52814907','1098765432','800345112');
```

---

## 2. Permisos: qué perfiles ven el módulo

Se gestiona desde la pantalla que ya existe:

**Permisos → Asignar → {perfil}**

Ahí aparece una tarjeta nueva, **Técnicos Externos**, con sus tres casillas
(Órdenes Externas, Nueva Orden Externa, Gestionar Técnicos Externos), más
**Motivos de Orden Externa** dentro de la tarjeta de Configuración.

> **Los códigos de opción deben empezar por `TE`.**
> `PermisoHelper::generarMenu()` agrupa cada opción bajo el menú padre formado por
> los **dos primeros caracteres** de su código. Una opción con un código que no
> corresponda a un menú padre existente no aparece en el sidebar.

### Protección real de las rutas

Ocultar la opción del menú **no** bloquea la URL. Por eso los controladores del
módulo validan el permiso en su constructor:

| Controlador | Exige |
|---|---|
| `OrdenExternaController` | `TE01` o `TE02` para entrar; `TE01` para listar/ver/editar; `TE02` para crear |
| `TecnicoExternoController` | `TE03` |

Un usuario sin permiso es redirigido a Servicios con un mensaje de error.

---

## 3. Estructura de la orden

| Campo | Obligatorio | Notas |
|---|---|---|
| `CodOrden` | Sí | Único. **Lo asigna el sistema al guardar**, no el formulario: `OE-{consecutivo}`, p. ej. `OE-0001`. No editable |
| `Fecha` | Sí | Fecha de entrega al técnico |
| `IdTecnicoExterno` | Sí | Solo técnicos activos |
| `DetalleProducto` | Sí | Hasta 500 caracteres |
| `IdMotivo` | Sí | Del catálogo `motivo_orden_externa` |
| `QuienEntrega` | Sí | Usuario interno del sistema |
| `QuienRecibe` | No | Al registrarlo, la orden pasa a estado `recibido` |
| `FechaRecibe` | No | |
| `Observaciones` | No | |
| `Precio` | No | `DECIMAL(12,2)`, por defecto 0 |
| `IdServicio` | No | Vínculo opcional con un servicio interno |
| `Estado` | — | `entregado` → `recibido`, o `anulado` |

Los tres datos que el requerimiento marcó como clave — **técnico externo, código de
orden y detalle del producto** — son `NOT NULL` en la base de datos, `required` en el
formulario y se validan también en el servidor.

### Ciclo de vida

```
[entregado] --registrar retorno--> [recibido]
     |                                  |
     +----------- anular ---------------+---> [anulado]
```

`anulado` conserva el registro: no se borra nada. La eliminación definitiva existe
pero está en la "zona de riesgo" del formulario de edición.

---

## 4. Rutas

### Órdenes (`ordenes-externas`)

| Ruta | Método | Acción |
|---|---|---|
| `ordenes-externas` | GET | Listado con filtros y paginación |
| `ordenes-externas/create` | GET | Formulario de creación |
| `ordenes-externas/store` | POST | Guardar |
| `ordenes-externas/view/{id}` | GET | Detalle con trazabilidad |
| `ordenes-externas/edit/{id}` | GET/POST | Editar |
| `ordenes-externas/update/{id}` | POST | Actualizar |
| `ordenes-externas/recibir/{id}` | POST | Registrar retorno (JSON) |
| `ordenes-externas/anular/{id}` | POST | Anular (JSON) |
| `ordenes-externas/delete/{id}` | POST | Eliminar (JSON) |
| `ordenes-externas/imprimir/{id}` | GET | Remisión imprimible |
| `ordenes-externas/exportar` | GET | CSV con los filtros aplicados |

### Técnicos (`tecnicos-externos`)

| Ruta | Método | Acción |
|---|---|---|
| `tecnicos-externos` | GET | Catálogo |
| `tecnicos-externos/create` | GET | Formulario |
| `tecnicos-externos/store` | POST | Guardar |
| `tecnicos-externos/store-ajax` | POST | Alta rápida desde el formulario de órdenes (JSON) |
| `tecnicos-externos/view/{id}` | GET | Ficha + historial de órdenes |
| `tecnicos-externos/edit/{id}` | GET/POST | Editar |
| `tecnicos-externos/toggle-estado/{id}` | POST | Activar / desactivar (JSON) |
| `tecnicos-externos/delete/{id}` | POST | Eliminar (JSON, solo sin órdenes) |

### Motivos (`configuracion`)

| Ruta | Método | Acción |
|---|---|---|
| `configuracion/motivos-externos` | GET | Listado |
| `configuracion/create-motivo-externo` | GET/POST | Crear |
| `configuracion/edit-motivo-externo/{id}` | GET/POST | Editar |
| `configuracion/delete-motivo-externo/{id}` | POST | Eliminar |
| `configuracion/toggle-motivo-externo/{id}` | POST | Activar / desactivar |

---

## 5. Archivos del módulo

```
config/
  setup_tecnicos_externos.sql      Instalación (tablas + opciones + permisos)
  setup_tecnicos_externos.php      Instalador que ejecuta el SQL

app/models/
  MotivoOrdenExterna.php
  TecnicoExterno.php
  OrdenExterna.php

app/controllers/
  TecnicoExternoController.php
  OrdenExternaController.php
  ConfiguracionController.php      (modificado: métodos de motivos)

app/views/tecnicos_externos/
  index.php  create.php  edit.php  view.php  _form.php

app/views/ordenes_externas/
  index.php  create.php  edit.php  view.php  imprimir.php  _form.php

app/views/configuracion/
  motivos_externos.php  create_motivo_externo.php  edit_motivo_externo.php
  index.php                        (modificado: tarjeta de motivos)

index.php                          (modificado: rutas nuevas)
```

---

## 6. Decisiones técnicas

- **Tablas en `utf8mb4`.** El resto de la base está en `latin1`; las tablas nuevas usan
  `utf8mb4_unicode_ci` para que los acentos se guarden correctamente con la conexión
  actual (que ya es `utf8mb4`).
- **Sin claves foráneas hacia `cliente`.** `QuienEntrega`, `QuienRecibe` y
  `RegistradoPor` referencian `cliente.no_identificacion`, pero no como FK: esa tabla
  mezcla clientes y empleados, y un `RESTRICT` bloquearía su mantenimiento. Van
  indexadas.
- **Baja lógica.** Un técnico con órdenes no se elimina, se desactiva. Lo mismo con
  los motivos.
- **Consecutivo global, asignado en el servidor.** `generarCodigo()` toma el mayor
  `OE-####` existente y avanza. El código se genera en el momento de guardar, no al
  abrir el formulario: así dos usuarios con el formulario abierto a la vez no reciben
  el mismo número. El campo del formulario es de solo lectura y en la edición se
  conserva el código ya asignado, aunque alguien manipule el HTML.
