# 🚀 Instrucciones de Instalación - Consultar Servicios

## ✅ Estado de la Implementación

**TODO EL CÓDIGO HA SIDO IMPLEMENTADO CORRECTAMENTE** ✅

Solo falta ejecutar el script de configuración de base de datos para activar los permisos.

---

## 📋 Paso a Paso para Activar la Funcionalidad

### Método 1: Usando el Script PHP (RECOMENDADO)

1. **Abre tu terminal o línea de comandos**

2. **Navega al directorio del proyecto:**
   ```bash
   cd /Applications/MAMP/htdocs/systecsoluciones_mvc
   ```

3. **Ejecuta el script de configuración:**
   ```bash
   php config/setup_consultar_servicio.php
   ```

4. **Verifica que aparezca el mensaje de éxito:**
   ```
   ✅ CONFIGURACIÓN COMPLETADA EXITOSAMENTE
   ```

### Método 2: Usando el Script Bash

```bash
cd /Applications/MAMP/htdocs/systecsoluciones_mvc
./instalar_consultar_servicio.sh
```

### Método 3: Ejecutar SQL Manualmente

Si los métodos anteriores no funcionan, ejecuta el SQL directamente:

**Opción A - Desde Terminal:**
```bash
mysql -u root -p systecsoluciones < config/setup_consultar_servicio.sql
```

**Opción B - Desde phpMyAdmin:**
1. Abre phpMyAdmin
2. Selecciona la base de datos `systecsoluciones` (o tu base de datos)
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido de `config/setup_consultar_servicio.sql`
5. Haz clic en "Continuar"

---

## 🔍 Verificación de la Instalación

### 1. Verificar en Base de Datos

Ejecuta esta consulta en phpMyAdmin o terminal:

```sql
SELECT * FROM opciones WHERE codigo = '0205';
```

**Debe retornar:**
```
codigo: 0205
descripcion: Consultar Servicios
url: servicios/consultar
icono: fas fa-search
submenu: 0
activo: 1
```

### 2. Verificar Permisos Asignados

```sql
SELECT 
    p.codigo_perfil,
    p.descripcion as perfil,
    o.descripcion as opcion
FROM perfil p
INNER JOIN perfil_opciones po ON p.codigo_perfil = po.codigo_perfil
INNER JOIN opciones o ON po.codigo_opcion = o.codigo
WHERE o.codigo = '0205'
ORDER BY p.codigo_perfil;
```

**Debe mostrar 4 filas** (Administrador, Técnico, Asesor, Técnico Admin)

### 3. Verificar en la Aplicación

1. **Inicia sesión** en el sistema
2. **Busca en el menú lateral** la opción: `Servicios > Consultar Servicios`
3. **Haz clic** en la opción
4. **Verifica** que cargue el formulario de filtros
5. **Prueba** aplicar un filtro simple (por ejemplo, por estado)
6. **Verifica** que se muestren resultados

---

## 🎯 Acceso Directo

Una vez configurado, puedes acceder directamente con esta URL:

```
http://localhost/systecsoluciones_mvc/index.php?route=servicios/consultar
```

O desde MAMP:
```
http://localhost:8888/systecsoluciones_mvc/index.php?route=servicios/consultar
```

---

## 📁 Archivos Implementados

### ✨ Archivos Nuevos
- ✅ `app/views/servicios/consultar.php` - Vista principal
- ✅ `config/setup_consultar_servicio.sql` - Script SQL
- ✅ `config/setup_consultar_servicio.php` - Script PHP de configuración
- ✅ `instalar_consultar_servicio.sh` - Script bash de instalación
- ✅ `CONSULTAR_SERVICIO_README.md` - Documentación completa
- ✅ `RESUMEN_IMPLEMENTACION.md` - Resumen de implementación
- ✅ `INSTRUCCIONES_INSTALACION.md` - Este archivo

### 🔧 Archivos Modificados
- ✅ `app/models/Servicio.php` - Agregado método `consultarServicios()`
- ✅ `app/controllers/ServicioController.php` - Ya tenía los métodos necesarios
- ✅ `index.php` - Agregadas 3 rutas nuevas

---

## 🛠️ Solución de Problemas

### Problema: Error de conexión a la base de datos

**Causa:** El script no puede conectarse a la base de datos.

**Solución:**
1. Verifica que MAMP esté ejecutándose
2. Verifica las credenciales en `config/Database.php`
3. Prueba la conexión manualmente:
   ```bash
   mysql -u root -p
   ```
4. Usa el Método 3 (SQL manual) en su lugar

### Problema: La opción no aparece en el menú

**Causa:** Los permisos no se configuraron o la caché del navegador está activa.

**Solución:**
1. Verifica en base de datos que la opción exista (ver sección de verificación)
2. Cierra sesión y vuelve a iniciar
3. Limpia caché del navegador (Ctrl+Shift+Delete)
4. Prueba en modo incógnito

### Problema: Error 404 al acceder

**Causa:** Las rutas no están correctamente configuradas.

**Solución:**
1. Verifica que `index.php` tiene las 3 rutas agregadas:
   - `servicios/consultar`
   - `servicios/cargar-resultados`
   - `servicios/exportar-consulta`
2. Reinicia Apache/servidor web
3. Verifica el archivo de log de errores

---

## 📝 Características Principales

### Filtros Disponibles
- ✅ ID del Servicio
- ✅ ID del Cliente
- ✅ Nombre del Cliente
- ✅ Fecha Desde / Fecha Hasta
- ✅ Técnico Asignado
- ✅ Estado del Servicio
- ✅ Tipo de Servicio
- ✅ Equipo
- ✅ Problema/Descripción

### Funcionalidades
- ✅ Búsqueda con múltiples filtros
- ✅ Resultados dinámicos (AJAX)
- ✅ Exportación a CSV
- ✅ Paginación y ordenamiento (DataTables)
- ✅ Diseño responsive
- ✅ Validación de formularios
- ✅ Restricciones por perfil de usuario

### Perfiles con Acceso
- ✅ Administrador - Acceso completo
- ✅ Técnico - Solo sus servicios asignados
- ✅ Técnico Administrador - Solo sus servicios asignados
- ✅ Asesor - Acceso completo

---

## 🎓 Uso de la Funcionalidad

### Ejemplo 1: Buscar servicios de un cliente específico

1. Ingresa el nombre del cliente en "Nombre del Cliente"
2. Haz clic en "Consultar"
3. Revisa los resultados en la tabla

### Ejemplo 2: Buscar servicios en un rango de fechas

1. Selecciona "Fecha Desde": 01/10/2025
2. Selecciona "Fecha Hasta": 21/10/2025
3. Haz clic en "Consultar"
4. Revisa los servicios en ese período

### Ejemplo 3: Exportar resultados

1. Aplica los filtros deseados
2. Haz clic en "Consultar" para obtener resultados
3. Haz clic en "Exportar Resultados"
4. Se descargará un archivo CSV con los datos

---

## 📞 Contacto y Soporte

Si tienes problemas durante la instalación:

1. **Revisa la documentación completa:** `CONSULTAR_SERVICIO_README.md`
2. **Revisa el resumen de implementación:** `RESUMEN_IMPLEMENTACION.md`
3. **Verifica los logs de PHP:** `/Applications/MAMP/logs/php_error.log`
4. **Verifica los logs de MySQL:** `/Applications/MAMP/logs/mysql_error.log`

---

## ✅ Checklist de Instalación

Marca cada paso conforme lo completes:

- [ ] He navegado al directorio del proyecto
- [ ] He ejecutado el script de configuración (Método 1, 2 o 3)
- [ ] He verificado que la opción existe en la tabla `opciones`
- [ ] He verificado que los permisos están asignados
- [ ] He iniciado sesión en el sistema
- [ ] La opción aparece en el menú lateral
- [ ] He accedido a "Consultar Servicios"
- [ ] He probado un filtro simple
- [ ] Los resultados se muestran correctamente
- [ ] He probado la exportación a CSV
- [ ] La funcionalidad está completamente operativa ✅

---

## 🎉 ¡Listo!

Una vez completados todos los pasos, la funcionalidad de **Consultar Servicios** estará completamente operativa y lista para usar.

**¡Disfruta de tu nueva funcionalidad! 🚀**

