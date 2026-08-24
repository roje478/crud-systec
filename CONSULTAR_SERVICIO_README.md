# Implementación de la Funcionalidad "Consultar Servicios"

## 📋 Descripción

Esta funcionalidad permite a los usuarios consultar servicios utilizando filtros avanzados. La implementación incluye soporte para diferentes perfiles de usuario (Administrador, Técnico, Técnico Administrador, Asesor) con permisos específicos.

## ✅ Componentes Implementados

### 1. Vista
- **Archivo**: `app/views/servicios/consultar.php`
- **Características**:
  - Formulario de filtros avanzados
  - Búsqueda por ID de servicio, cliente, fechas, técnico, estado, tipo de servicio, equipo y problema
  - Resultados dinámicos con AJAX
  - Exportación a CSV
  - DataTables para visualización y gestión de datos
  - Diseño responsive
  - Mensajes específicos por perfil de usuario

### 2. Controlador
- **Archivo**: `app/controllers/ServicioController.php`
- **Métodos Agregados/Modificados**:
  - `consultar()`: Renderiza la vista de consulta con filtros
  - `cargarResultados()`: Carga resultados via AJAX
  - `exportarConsulta()`: Exporta resultados a CSV
  - `tieneFiltrosActivos()`: Verifica si hay filtros aplicados

### 3. Modelo
- **Archivo**: `app/models/Servicio.php`
- **Método Agregado**:
  - `consultarServicios($filtros, $esTecnico, $esTecnicoAdministrador)`: Ejecuta la consulta con filtros aplicados

### 4. Router
- **Archivo**: `index.php`
- **Rutas Agregadas**:
  - `servicios/consultar` (GET): Vista principal de consulta
  - `servicios/cargar-resultados` (POST): Carga resultados via AJAX
  - `servicios/exportar-consulta` (GET): Exporta resultados a CSV

### 5. Sistema de Permisos
- **Archivo**: `config/setup_consultar_servicio.sql`
- **Configuración**:
  - Código de opción: `0205`
  - Descripción: "Consultar Servicios"
  - URL: `servicios/consultar`
  - Icono: `fas fa-search`
  - Submenu: `02` (Servicios)

## 🚀 Instalación

### Paso 1: Ejecutar el Script SQL

Ejecuta el siguiente script SQL en tu base de datos para agregar la opción al sistema de permisos:

```bash
mysql -u [usuario] -p [base_de_datos] < config/setup_consultar_servicio.sql
```

O desde phpMyAdmin, abre el archivo `config/setup_consultar_servicio.sql` y ejecútalo.

### Paso 2: Verificar Permisos

El script SQL automáticamente asigna la opción a los siguientes perfiles:
- ✅ Administrador (perfil 1)
- ✅ Técnico (perfil 2)
- ✅ Asesor (perfil 3)
- ✅ Técnico Administrador (perfil 4)

### Paso 3: Verificar en el Menú

Una vez ejecutado el script, la opción "Consultar Servicios" debe aparecer en el menú lateral bajo la sección "Servicios" para los usuarios con los perfiles mencionados.

### Paso 4: Probar la Funcionalidad

1. Inicia sesión con un usuario que tenga permisos
2. Navega a: `Servicios > Consultar Servicios`
3. Utiliza los filtros para buscar servicios
4. Verifica que los resultados se muestren correctamente
5. Prueba la exportación a CSV

## 🎯 Características por Perfil

### Administrador
- ✅ Acceso completo a todos los servicios
- ✅ Filtros por todos los técnicos
- ✅ Exportación de resultados
- ✅ Visualización completa

### Técnico
- ⚠️ Solo ve servicios asignados a su usuario
- 🚫 No puede filtrar por otros técnicos
- ✅ Puede exportar sus servicios
- ℹ️ Mensaje informativo sobre restricciones

### Técnico Administrador
- ⚠️ Solo ve servicios asignados a su usuario
- 🚫 No puede filtrar por otros técnicos
- ✅ Puede exportar sus servicios
- ℹ️ Mensaje informativo sobre restricciones

### Asesor
- ✅ Acceso completo a todos los servicios
- ✅ Filtros por todos los técnicos
- ✅ Exportación de resultados
- ✅ Visualización completa

## 📊 Filtros Disponibles

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| ID Servicio | Número | Buscar por ID específico del servicio |
| ID Cliente | Texto | Buscar por identificación del cliente |
| Nombre Cliente | Texto | Buscar por nombre del cliente |
| Fecha Desde | Fecha | Fecha inicial del rango |
| Fecha Hasta | Fecha | Fecha final del rango |
| Técnico | Select | Filtrar por técnico asignado |
| Estado | Select | Filtrar por estado del servicio |
| Tipo Servicio | Select | Filtrar por tipo de servicio |
| Equipo | Texto | Buscar en descripción del equipo |
| Problema | Texto | Buscar en descripción del problema |

## 🔒 Seguridad

- ✅ Validación de permisos en el controlador
- ✅ Restricción de datos por perfil de usuario
- ✅ Validación de entrada de datos
- ✅ Prevención de SQL Injection mediante prepared statements
- ✅ Autenticación requerida para acceder

## 📦 Dependencias

### Frontend
- Bootstrap 5.3.0
- Font Awesome 6.4.0
- jQuery 3.7.1
- DataTables 1.13.7
  - DataTables Buttons
  - DataTables Responsive
  - DataTables Spanish locale

### Backend
- PHP 7.4+
- MySQL/MariaDB
- PDO Extension

## 🐛 Solución de Problemas

### La opción no aparece en el menú

1. Verifica que el script SQL se ejecutó correctamente:
```sql
SELECT * FROM opciones WHERE codigo = '0205';
```

2. Verifica que tu perfil tiene la opción asignada:
```sql
SELECT * FROM perfil_opciones 
WHERE codigo_perfil = [TU_CODIGO_PERFIL] AND codigo_opcion = '0205';
```

3. Limpia la caché del navegador y vuelve a iniciar sesión.

### Los filtros no funcionan

1. Verifica que el método `consultarServicios` existe en el modelo:
```bash
grep -n "consultarServicios" app/models/Servicio.php
```

2. Revisa los logs de PHP para errores:
```bash
tail -f /Applications/MAMP/logs/php_error.log
```

### No se exportan los resultados

1. Verifica permisos de escritura en el directorio temporal
2. Revisa la configuración de headers en `exportarConsulta()`
3. Verifica que no haya output antes de los headers

## 📝 Notas Técnicas

### Consulta AJAX
La vista utiliza AJAX para cargar resultados sin recargar la página completa. Esto mejora la experiencia de usuario y el rendimiento.

### DataTables
Se utiliza DataTables para:
- Ordenamiento de columnas
- Búsqueda en tiempo real
- Paginación
- Exportación a múltiples formatos
- Diseño responsive

### Performance
La consulta está optimizada con:
- LEFT JOINs eficientes
- Índices en las columnas clave
- Prepared statements
- Límite de resultados razonable

## 🔄 Actualizaciones Futuras

Posibles mejoras a considerar:
- [ ] Guardar filtros favoritos
- [ ] Gráficas y estadísticas de servicios
- [ ] Exportación a PDF con formato personalizado
- [ ] Búsqueda por rangos de costo
- [ ] Filtros avanzados por múltiples criterios a la vez
- [ ] Historial de consultas recientes

## 👥 Soporte

Para problemas o preguntas sobre esta funcionalidad, contacta al equipo de desarrollo.

## 📄 Licencia

Este código es parte del Sistema de Gestión de Servicios MVC.

