<?php
$lines = file('d:/report_notebook/resources/views/reportes/nuevo.blade.php');
foreach ($lines as $i => $line) {
    if (stripos($line, 'Ocurrencias de relevo') !== false || stripos($line, 'tempHora') !== false || stripos($line, 'incidenciaData.hora') !== false) {
        echo 'L'.($i+1).': '.trim($line).PHP_EOL;
    }
}
?>
