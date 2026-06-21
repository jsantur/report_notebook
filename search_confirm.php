<?php
$lines = file('d:/report_notebook/resources/views/reportes/nuevo.blade.php');
foreach ($lines as $i => $line) {
    if (strpos($line, 'confirm') !== false || strpos($line, 'removePatrullando') !== false || strpos($line, '@click.stop') !== false) {
        echo 'L'.($i+1).': '.trim($line).PHP_EOL;
    }
}
?>
