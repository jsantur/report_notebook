<?php
$f = 'd:/report_notebook/resources/views/reportes/nuevo.blade.php';
$c = file_get_contents($f);
$c = preg_replace('/placeholder="BUSCAR C.*MARA\.\.\.">/', 'placeholder="BUSCAR CÁMARA...">', $c);
file_put_contents($f, $c);
echo "Reemplazo hecho.\n";
