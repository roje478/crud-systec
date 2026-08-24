#!/bin/bash

###############################################################################
# Script de Instalación Rápida - Funcionalidad Consultar Servicios
# 
# Este script configura automáticamente la funcionalidad de consulta de
# servicios en el sistema.
#
# Uso: 
#   ./instalar_consultar_servicio.sh
#
# O si tienes problemas de permisos:
#   bash instalar_consultar_servicio.sh
###############################################################################

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar encabezado
mostrar_encabezado() {
    echo -e "${BLUE}╔═══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║  INSTALADOR - FUNCIONALIDAD CONSULTAR SERVICIOS              ║${NC}"
    echo -e "${BLUE}╚═══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

# Función para mostrar mensaje de éxito
mostrar_exito() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Función para mostrar mensaje de error
mostrar_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Función para mostrar mensaje de advertencia
mostrar_advertencia() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Función para mostrar mensaje de información
mostrar_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Inicio del script
mostrar_encabezado

# Verificar que estamos en el directorio correcto
if [ ! -f "index.php" ]; then
    mostrar_error "Error: No se encontró index.php"
    mostrar_info "Asegúrate de ejecutar este script desde el directorio raíz del proyecto"
    exit 1
fi

mostrar_exito "Directorio del proyecto verificado"

# Verificar que el archivo de configuración existe
if [ ! -f "config/setup_consultar_servicio.php" ]; then
    mostrar_error "Error: No se encontró config/setup_consultar_servicio.php"
    exit 1
fi

mostrar_exito "Archivo de configuración encontrado"

# Ejecutar el script de configuración PHP
echo ""
mostrar_info "Ejecutando script de configuración de base de datos..."
echo ""

php config/setup_consultar_servicio.php

# Verificar el código de salida
if [ $? -eq 0 ]; then
    echo ""
    mostrar_encabezado
    echo -e "${GREEN}╔═══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║  ✅ INSTALACIÓN COMPLETADA EXITOSAMENTE                       ║${NC}"
    echo -e "${GREEN}╚═══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    
    echo -e "${BLUE}📝 Resumen de cambios:${NC}"
    echo "   • Vista agregada: app/views/servicios/consultar.php"
    echo "   • Controlador actualizado: app/controllers/ServicioController.php"
    echo "   • Modelo actualizado: app/models/Servicio.php"
    echo "   • Router actualizado: index.php"
    echo "   • Permisos configurados en base de datos"
    echo ""
    
    echo -e "${BLUE}🎯 Próximos pasos:${NC}"
    echo "   1. Inicia sesión en el sistema"
    echo "   2. Busca en el menú: Servicios > Consultar Servicios"
    echo "   3. Prueba los diferentes filtros de búsqueda"
    echo "   4. Verifica la exportación a CSV"
    echo ""
    
    echo -e "${BLUE}🔗 Acceso directo:${NC}"
    echo "   URL: index.php?route=servicios/consultar"
    echo ""
    
    echo -e "${BLUE}📚 Documentación:${NC}"
    echo "   Lee el archivo: CONSULTAR_SERVICIO_README.md"
    echo ""
    
else
    echo ""
    mostrar_encabezado
    echo -e "${RED}╔═══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${RED}║  ❌ ERROR EN LA INSTALACIÓN                                   ║${NC}"
    echo -e "${RED}╚═══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    
    mostrar_error "La configuración no pudo completarse"
    echo ""
    
    echo -e "${YELLOW}💡 Soluciones posibles:${NC}"
    echo "   1. Verifica la conexión a la base de datos"
    echo "   2. Revisa config/Database.php"
    echo "   3. Ejecuta manualmente: php config/setup_consultar_servicio.php"
    echo "   4. O ejecuta el SQL: mysql -u usuario -p database < config/setup_consultar_servicio.sql"
    echo ""
    
    exit 1
fi

