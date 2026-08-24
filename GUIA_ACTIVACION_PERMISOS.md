# 🔑 Guía de Activación - Consultar Servicios usando el Módulo de Permisos

## ✅ Estado de la Implementación

**TODO EL CÓDIGO ESTÁ IMPLEMENTADO** ✅

Solo falta agregar la opción en la base de datos y asignarla desde el **Módulo de Permisos**.

---

## 📋 Proceso de Activación en 2 Pasos

### ✨ Paso 1: Agregar la Opción en la Base de Datos

Necesitas agregar la opción "Consultar Servicios" a la tabla `opciones`.

**Opción A - Usando phpMyAdmin:**

1. Abre **phpMyAdmin**
2. Selecciona tu base de datos
3. Ve a la pestaña **"SQL"**
4. Ejecuta este código SQL:

```sql
INSERT INTO opciones (codigo, descripcion, url, icono, submenu, activo)
VALUES ('0205', 'Consultar Servicios', 'servicios/consultar', 'fas fa-search', 0, 1);
```

5. Haz clic en **"Continuar"**
6. ✅ Deberías ver: "1 fila insertada"

**Opción B - Desde Terminal:**

```bash
cd /Applications/MAMP/htdocs/systecsoluciones_mvc
php config/setup_consultar_servicio.php
```

---

### 🔐 Paso 2: Asignar Permisos desde el Módulo de Permisos

Ahora que la opción existe, debes asignarla a los perfiles que necesitan acceso:

#### 2.1 Accede al Módulo de Permisos

1. **Inicia sesión** en el sistema como Administrador
2. Ve al menú lateral → **Permisos**
3. Verás la lista de perfiles del sistema

#### 2.2 Asignar a Perfil Administrador

1. En la fila del perfil **"Administrador"**, haz clic en el botón con icono de llave 🔑 (**Asignar Permisos**)
2. Busca la sección **"Servicios"**
3. Encontrarás un nuevo checkbox: **"Consultar Servicios"** ✅
4. **Marca el checkbox**
5. Haz clic en **"Guardar Permisos"** (botón verde arriba)
6. ✅ Deberías ver: "Permisos actualizados correctamente"

#### 2.3 Asignar a Perfil Técnico

1. Vuelve a **Permisos** (clic en "Volver a Permisos")
2. En la fila del perfil **"Técnico"**, haz clic en el botón 🔑
3. Busca la sección **"Servicios"**
4. **Marca** el checkbox "Consultar Servicios"
5. Haz clic en **"Guardar Permisos"**

#### 2.4 Asignar a Perfil Asesor

1. Repite el proceso para el perfil **"Asesor"**
2. Marca "Consultar Servicios" en la sección de Servicios
3. Guarda los permisos

#### 2.5 Asignar a Perfil Técnico Administrador

1. Repite el proceso para el perfil **"Técnico Administrador"**
2. Marca "Consultar Servicios"
3. Guarda los permisos

---

## ✅ Verificación

### 1. Verificar que la opción se agregó

En phpMyAdmin, ejecuta:

```sql
SELECT * FROM opciones WHERE codigo = '0205';
```

**Resultado esperado:**
| codigo | descripcion | url | icono | submenu | activo |
|--------|-------------|-----|-------|---------|--------|
| 0205 | Consultar Servicios | servicios/consultar | fas fa-search | 0 | 1 |

### 2. Verificar en el Sistema

1. **Cierra sesión** y vuelve a **iniciar sesión**
2. Busca en el **menú lateral**: `Servicios` → debería aparecer **"Consultar Servicios"**
3. **Haz clic** en la opción
4. Deberías ver el **formulario de filtros** ✅

---

## 🎯 Captura Visual del Proceso

### Paso 1: SQL en phpMyAdmin
```
┌─────────────────────────────────────────┐
│ phpMyAdmin > SQL                        │
├─────────────────────────────────────────┤
│                                         │
│ INSERT INTO opciones (...);             │
│                                         │
│         [Continuar]                     │
└─────────────────────────────────────────┘
         ↓
     ✅ Opción creada
```

### Paso 2: Módulo de Permisos
```
┌─────────────────────────────────────────┐
│ Permisos > Lista de Perfiles            │
├─────────────────────────────────────────┤
│                                         │
│ • Administrador        [🔑 Asignar]     │
│ • Técnico              [🔑 Asignar]     │
│ • Asesor               [🔑 Asignar]     │
│ • Técnico Admin        [🔑 Asignar]     │
│                                         │
└─────────────────────────────────────────┘
         ↓ (Clic en 🔑)
┌─────────────────────────────────────────┐
│ Asignar Permisos: Administrador         │
├─────────────────────────────────────────┤
│                                         │
│ 📁 Servicios                            │
│   □ Crear Servicio                      │
│   □ Lista de Servicios                  │
│   ☑ Consultar Servicios ← ¡NUEVO!      │
│   □ Buscar Servicios                    │
│                                         │
│         [Guardar Permisos]              │
└─────────────────────────────────────────┘
         ↓
     ✅ Permisos asignados
```

### Resultado: Menú actualizado
```
┌─────────────────────────────────────────┐
│ ☰ MENÚ                                  │
├─────────────────────────────────────────┤
│                                         │
│ 🛠️ Servicios                            │
│   • Crear Servicio                      │
│   • Lista de Servicios                  │
│   • Consultar Servicios ← ¡APARECE!     │
│   • Buscar Servicios                    │
│                                         │
└─────────────────────────────────────────┘
```

---

## 🔍 Detalles de la Opción

| Campo | Valor |
|-------|-------|
| **Código** | `0205` |
| **Descripción** | Consultar Servicios |
| **URL** | servicios/consultar |
| **Icono** | fas fa-search |
| **Submenu** | 0 (pertenece al menú "02 - Servicios") |
| **Activo** | 1 |

---

## 🎯 Perfiles Recomendados

| Perfil | Acceso | Restricciones |
|--------|--------|---------------|
| **Administrador** | ✅ Completo | Ninguna - Ve todos los servicios |
| **Técnico** | ✅ Limitado | Solo ve sus servicios asignados |
| **Asesor** | ✅ Completo | Ninguna - Ve todos los servicios |
| **Técnico Admin** | ✅ Limitado | Solo ve sus servicios asignados |

---

## 📊 Características de "Consultar Servicios"

### Filtros Disponibles:
- 🔍 ID del Servicio
- 👤 ID y Nombre del Cliente  
- 📅 Rango de Fechas (Desde/Hasta)
- 👨‍💻 Técnico Asignado
- 📊 Estado del Servicio
- 🛠️ Tipo de Servicio
- 💻 Equipo
- 📝 Descripción del Problema

### Funcionalidades:
- ✅ Búsqueda con múltiples filtros combinables
- ✅ Resultados en tabla con DataTables
- ✅ Exportación a CSV
- ✅ Paginación y ordenamiento
- ✅ Búsqueda en tiempo real
- ✅ Diseño responsive

---

## 🆘 Solución de Problemas

### Problema: "Consultar Servicios" no aparece en el Módulo de Permisos

**Causa:** La opción no se agregó correctamente en la tabla `opciones`.

**Solución:**
1. Verifica en phpMyAdmin:
   ```sql
   SELECT * FROM opciones WHERE codigo = '0205';
   ```
2. Si no aparece, ejecuta el INSERT del Paso 1 nuevamente
3. Recarga la página del Módulo de Permisos

### Problema: La opción no aparece en el menú después de asignarla

**Causa:** La caché del navegador o sesión no está actualizada.

**Solución:**
1. **Cierra sesión** completamente
2. **Limpia caché del navegador** (Ctrl+Shift+Delete)
3. **Vuelve a iniciar sesión**
4. La opción debería aparecer ahora

### Problema: Error al guardar permisos

**Causa:** Puede haber un problema con la sesión o base de datos.

**Solución:**
1. Revisa los logs de PHP: `/Applications/MAMP/logs/php_error.log`
2. Verifica la conexión a la base de datos
3. Intenta con otro perfil primero

---

## 💡 Ventajas de Usar el Módulo de Permisos

✅ **Control visual** - Ves todas las opciones con checkboxes  
✅ **Sin SQL manual** - Solo se requiere 1 INSERT inicial  
✅ **Auditable** - Puedes ver qué perfiles tienen qué permisos  
✅ **Reversible** - Fácil de quitar permisos si es necesario  
✅ **Interfaz gráfica** - No necesitas conocimientos técnicos  

---

## 📚 Documentación Adicional

- **Guía completa:** [CONSULTAR_SERVICIO_README.md](CONSULTAR_SERVICIO_README.md)
- **Inicio rápido:** [INICIO_RAPIDO.md](INICIO_RAPIDO.md)
- **Resumen:** [LEEME_PRIMERO.md](LEEME_PRIMERO.md)

---

## ✅ Checklist de Activación

- [ ] Paso 1: Ejecuté el INSERT en phpMyAdmin
- [ ] Verifiqué que la opción aparece en tabla `opciones`
- [ ] Paso 2: Accedí al Módulo de Permisos
- [ ] Asigné la opción al perfil Administrador
- [ ] Asigné la opción al perfil Técnico
- [ ] Asigné la opción al perfil Asesor
- [ ] Asigné la opción al perfil Técnico Administrador
- [ ] Cerré sesión y volví a iniciar
- [ ] La opción aparece en el menú lateral
- [ ] Puedo acceder a "Consultar Servicios"
- [ ] El formulario de filtros se muestra correctamente
- [ ] ✅ ¡Todo funciona!

---

## 🎉 ¡Listo!

Una vez completados estos 2 pasos, la funcionalidad de **Consultar Servicios** estará **100% operativa**.

**Acceso directo:**
```
http://localhost/systecsoluciones_mvc/index.php?route=servicios/consultar
```

---

**Implementado el:** 21 de Octubre de 2025  
**Método:** Módulo de Permisos del Sistema  
**Estado:** ✅ Listo para Producción

