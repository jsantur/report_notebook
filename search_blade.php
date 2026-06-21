<?php
$lines = file('d:/report_notebook/resources/views/reportes/nuevo.blade.php');
foreach ($lines as $i => $line) {
    $n = $i + 1;
    if (strpos($line, 'removePatrullando') !== false || 
        strpos($line, 'showPatrullandoModal') !== false ||
        strpos($line, 'REPORTE DE UNIDADES') !== false ||
        (strpos($line, 'hora') !== false && strpos($line, 'input') !== false) ||
        (strpos($line, 'currentHora') !== false) ||
        (strpos($line, 'reportHora') !== false)) {
        echo "L$n: " . trim($line) . "\n";
    }
}
?>
