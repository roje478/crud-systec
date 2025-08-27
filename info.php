<?php
// Info básico - Verificar que MAMP funciona
echo "MAMP está funcionando correctamente!";
echo "<br>";
echo "Fecha: " . date('Y-m-d H:i:s');
echo "<br>";
echo "PHP Version: " . PHP_VERSION;
echo "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'];
echo "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'];
echo "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'];
echo "<br>";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'];