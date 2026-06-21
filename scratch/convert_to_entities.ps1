
$path = "d:\report_notebook\resources\views\reportes\nuevo.blade.php"
$content = Get-Content -Path $path -Raw -Encoding UTF8

$htmlEntities = @{
    'á' = '&aacute;'
    'é' = '&eacute;'
    'í' = '&iacute;'
    'ó' = '&oacute;'
    'ú' = '&uacute;'
    'Á' = '&Aacute;'
    'É' = '&Eacute;'
    'Í' = '&Iacute;'
    'Ó' = '&Oacute;'
    'Ú' = '&Uacute;'
    'ñ' = '&ntilde;'
    'Ñ' = '&Ntilde;'
}

# Solo aplicamos entidades HTML a las partes que NO están dentro de <script> o atributos x-
# Pero como es un archivo Blade mezclado, es más fácil usar un enfoque de "escapado de JS" para JS y "entidades" para el resto.
# Sin embargo, las entidades HTML funcionan bien dentro de la mayoría de los strings de JS en el navegador si se inyectan en el DOM.

foreach ($key in $htmlEntities.Keys) {
    $content = $content.Replace($key, $htmlEntities[$key])
}

Set-Content -Path $path -Value $content -Encoding UTF8
