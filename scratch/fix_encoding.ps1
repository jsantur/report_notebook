
$path = "d:\report_notebook\resources\views\reportes\nuevo.blade.php"
$content = Get-Content -Path $path -Raw -Encoding UTF8

$replacements = @{
    'Ã¡' = 'á'
    'Ã©' = 'é'
    'Ã­' = 'í'
    'Ã³' = 'ó'
    'Ãº' = 'ú'
    'Ã±' = 'ñ'
    'Ã‘' = 'Ñ'
    'Ã‰' = 'É'
    'Ã“' = 'Ó'
    'Ãš' = 'Ú'
    'Ã ' = 'Á'
    'Ã' = 'í' # Special case for single Ã often being í in some manglings
    'Ã¢â‚¬Å“' = '"'
    'Ã¢â‚¬Â ' = "'"
    'Ã¢â‚¬â„¢' = "'"
    'Ã¢â‚¬â€œ' = "-"
}

foreach ($key in $replacements.Keys) {
    $content = $content.Replace($key, $replacements[$key])
}

# Fix specific mangled regex again
$content = $content -replace '\[\^a-zA-ZáéíóúÁÉÍÓÚñÑ ]', '[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]'

Set-Content -Path $path -Value $content -Encoding UTF8
