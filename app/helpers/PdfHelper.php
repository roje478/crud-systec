<?php
/**
 * Helper para generar PDFs de órdenes de servicio
 */

class PdfHelper {

    /**
     * Generar PDF de orden de servicio
     */
    public static function generarOrdenServicio($servicio, $empresa = null) {
        // Si no se proporciona información de empresa, obtenerla
        if (!$empresa) {
            require_once __DIR__ . '/EmpresaHelper.php';
            $empresa = EmpresaHelper::getEmpresaInfo();
        }

        // Obtener cláusulas
        require_once __DIR__ . '/../models/Clausula.php';
        $clausulaModel = new Clausula();
        $clausulas = $clausulaModel->getAll();

        // Generar contenido HTML del PDF
        $html = self::generarHTMLOrdenServicio($servicio, $empresa, $clausulas);

        // Generar PDF usando TCPDF o alternativa
        return self::generarPDF($html, 'OrdenServicio' . $servicio['IdServicio']);
    }

    /**
     * Verificar si el servicio está terminado
     */
    private static function servicioEstaTerminado($servicio) {
        // Verificar por ID (3 = Terminado) o por descripción
        return $servicio['IdEstadoEnTaller'] == 3 ||
               strtolower($servicio['estado_descripcion'] ?? '') == 'terminado';
    }

    /**
     * Generar HTML para la orden de servicio
     */
    private static function generarHTMLOrdenServicio($servicio, $empresa, $clausulas) {
        $logoUrl = '';
        if (!empty($empresa['logo']) && file_exists($empresa['logo'])) {
            $logoUrl = 'data:image/png;base64,' . base64_encode(file_get_contents($empresa['logo']));
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Orden de Servicio #' . $servicio['IdServicio'] . '</title>
            <style>
                @page {
                    margin: 1cm;
                    size: A4;
                }
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    font-size: 10px;
                    line-height: 1.2;
                    color: #000;
                }
                .container {
                    max-width: 100%;
                    margin: 0 auto;
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 20px;
                    border-bottom: 1px solid #000;
                    padding-bottom: 10px;
                }
                .header-columna {
                    flex: 1;
                    padding: 0 10px;
                }
                .header-columna-centro {
                    text-align: center;
                }
                .header-columna-derecha {
                    text-align: right;
                }
                .logo {
                    width: 80px;
                    height: 80px;
                    margin-bottom: 5px;
                }
                .empresa-nombre {
                    font-size: 14px;
                    font-weight: bold;
                    margin-bottom: 3px;
                }
                .empresa-datos {
                    font-size: 8px;
                    color: #333;
                    line-height: 1.1;
                }
                .orden-numero {
                    font-size: 16px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .orden-info {
                    font-size: 8px;
                    color: #333;
                }
                .tabla-cliente {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                .tabla-cliente th {
                    background-color: #f0f0f0;
                    border: 1px solid #000;
                    padding: 4px;
                    font-size: 9px;
                    font-weight: bold;
                    text-align: left;
                }
                .tabla-cliente td {
                    border: 1px solid #000;
                    padding: 4px;
                    font-size: 9px;
                }
                .tabla-servicio {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                .tabla-servicio th {
                    background-color: #f0f0f0;
                    border: 1px solid #000;
                    padding: 4px;
                    font-size: 9px;
                    font-weight: bold;
                    text-align: left;
                }
                .tabla-servicio td {
                    border: 1px solid #000;
                    padding: 4px;
                    font-size: 9px;
                    vertical-align: top;
                }
                .firmas {
                    margin-top: 64px;
                    display: flex;
                    justify-content: space-between;
                }
                .firma {
                    text-align: center;
                    flex: 1;
                    margin: 0 10px;
                }
                .firma-linea {
                    border-top: 1px solid #000;
                    width: 120px;
                    margin: 0 auto 3px auto;
                }
                .firma-texto {
                    font-size: 8px;
                    color: #333;
                    font-weight: bold;
                }
                .clausulas {
                    margin-top: 15px;
                }
                .clausulas-titulo {
                    font-size: 10px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .clausula {
                    font-size: 8px;
                    margin-bottom: 3px;
                    line-height: 1.1;
                }
                .clausula-numero {
                    font-weight: bold;
                }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <!-- Header con logo y datos de empresa -->
                <div class="header">
                    <div class="header-columna">
                        ' . ($logoUrl ? '<img src="' . $logoUrl . '" class="logo" alt="Logo">' : '') . '
                    </div>
                    <div class="header-columna header-columna-centro">
                        <div class="empresa-nombre">' . htmlspecialchars($empresa['nombreempresa']) . '</div>
                        <div class="empresa-datos">
                            Servicio Técnico Especializado<br>
                            ' . htmlspecialchars($empresa['telefono']) . '<br>
                            ' . htmlspecialchars($empresa['correo']) . '<br>
                            ' . htmlspecialchars($empresa['direccion']) . '
                        </div>
                    </div>
                    <div class="header-columna header-columna-derecha">
                        <div class="orden-numero">No. Orden: ' . $servicio['IdServicio'] . '</div>
                        <div class="orden-info">
                            Fecha: ' . date('Y-m-d', strtotime($servicio['FechaIngreso'])) . '<br>
                            Hora: ' . date('H:i:s', strtotime($servicio['FechaIngreso'])) . '<br>
                            Salida: ' . date('Y-m-d', strtotime($servicio['FechaIngreso']) + (2 * 24 * 60 * 60)) . '
                        </div>
                    </div>
                </div>

                <!-- Información del cliente en tabla -->
                <table class="tabla-cliente">
                    <tr>
                        <th>Nombre del Cliente:</th>
                        <td>' . htmlspecialchars($servicio['cliente_nombre']) . '</td>
                        <th>Documento CC:</th>
                        <td>' . htmlspecialchars($servicio['NoIdentificacionCliente']) . '</td>
                    </tr>
                    <tr>
                        <th>Telefono:</th>
                        <td>' . htmlspecialchars($servicio['cliente_telefono'] ?? 'No especificado') . '</td>
                        <th>Direccion:</th>
                        <td>' . htmlspecialchars($servicio['cliente_direccion'] ?? 'No especificada') . '</td>
                    </tr>
                </table>

                <!-- Detalles del servicio en tabla -->
                <table class="tabla-servicio">
                    <tr>
                        <th>ITEM</th>
                        <th>DESCRIPCION</th>
                    </tr>
                    <tr>
                        <td>' . htmlspecialchars($servicio['Equipo']) . '</td>
                        <td>
                            ' . (!empty($servicio['CondicionesEntrega']) ? '<b>Condiciones producto:</b> ' . htmlspecialchars($servicio['CondicionesEntrega']) . '<br>' : '') . '
                            <b>Problema:</b> ' . htmlspecialchars($servicio['Problema']) . '<br>
                            ' . (!empty($servicio['Solucion']) && self::servicioEstaTerminado($servicio) ? '<b>Solucion:</b> ' . htmlspecialchars($servicio['Solucion']) . '<br>' : '') . '
                            ' . (!empty($servicio['Costo']) ? '<b>Total:</b> ' . number_format($servicio['Costo'], 0, ',', '.') : '') . '
                        </td>
                    </tr>
                </table>

                <!-- Firmas -->
                <div class="firmas">
                    <div class="firma">
                        <div class="firma-linea"></div>
                        <div class="firma-texto">Firma Empleado</div>
                    </div>
                    <div class="firma">
                        <div class="firma-linea"></div>
                        <div class="firma-texto">Firma Cliente</div>
                    </div>
                </div>

                <!-- Cláusulas -->
                <div class="clausulas">
                    <div class="clausulas-titulo">Clausulas:</div>
                    ';

        $numeroClausula = 1;
        foreach ($clausulas as $clausula) {
            $html .= '
                    <div class="clausula">
                        <span class="clausula-numero">' . $numeroClausula . '.</span> ' . htmlspecialchars($clausula['descripcion']) . '
                    </div>
                ';
            $numeroClausula++;
        }

        $html .= '
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Generar PDF desde HTML
     */
    private static function generarPDF($html, $filename) {
        // Intentar usar TCPDF si está disponible
        if (class_exists('TCPDF')) {
            return self::generarPDFConTCPDF($html, $filename);
        } else {
            // Fallback: generar HTML para impresión
            return self::generarHTMLParaImpresion($html, $filename);
        }
    }

    /**
     * Generar PDF usando TCPDF
     */
    private static function generarPDFConTCPDF($html, $filename) {
        // Crear nueva instancia de TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Configurar información del documento
        $pdf->SetCreator('SYSTEC SOLUCIONES');
        $pdf->SetAuthor('SYSTEC SOLUCIONES');
        $pdf->SetTitle($filename);

        // Configurar márgenes
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // Configurar saltos de página automáticos
        $pdf->SetAutoPageBreak(TRUE, 25);

        // Agregar página
        $pdf->AddPage();

        // Escribir HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Generar PDF
        $pdfContent = $pdf->Output('', 'S');

        // Configurar headers para descarga
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        echo $pdfContent;
        exit;
    }

    /**
     * Generar HTML para impresión (fallback)
     */
    private static function generarHTMLParaImpresion($html, $filename) {
        // Configurar headers para HTML
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: inline; filename="' . $filename . '.html"');

        // Agregar script para imprimir automáticamente
        $html = str_replace('</head>', '
            <script>
                window.onload = function() {
                    window.print();
                }
            </script>
        </head>', $html);

        echo $html;
        exit;
    }
}