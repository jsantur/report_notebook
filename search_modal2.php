<?php
$lines = file('d:/report_notebook/resources/views/reportes/nuevo.blade.php');
foreach ($lines as $i => $line) {
    if (strpos($line, 'showModal = true') !== false || strpos($line, 'Ocurrencias del Relevo') !== false || strpos($line, '@click') !== false && strpos($line, 'showModal') !== false) {
        echo 'L'.($i+1).': '.trim($line).PHP_EOL;
    }
}
?>
