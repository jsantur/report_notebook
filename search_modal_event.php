<?php
$lines = file('d:/report_notebook/resources/views/reportes/nuevo.blade.php');
foreach ($lines as $i => $line) {
    if (strpos($line, 'watch(\'showModal\'') !== false || strpos($line, 'abrir-modal-ocurrencia') !== false || strpos($line, 'editar-ocurrencia') !== false) {
        echo 'L'.($i+1).': '.trim($line).PHP_EOL;
    }
}
?>
