# WallyGrow — setup local WordPress (Windows / PowerShell)
# Uso: .\scripts\setup-local.ps1

$ErrorActionPreference = "Continue"
$Container = if ($env:WP_CONTAINER) { $env:WP_CONTAINER } else { "wallygrow-wordpress-1" }
$SiteUrl   = if ($env:WP_SITE_URL) { $env:WP_SITE_URL } else { "http://localhost:8082" }

function Invoke-DockerExec {
    param([Parameter(Mandatory)][string[]]$Args)
    $prev = $ErrorActionPreference
    $ErrorActionPreference = "SilentlyContinue"
    & docker exec $Container @Args 2>&1 | Where-Object { $_ -is [string] -or $_.ToString() }
    $script:LastDockerExit = $LASTEXITCODE
    $ErrorActionPreference = $prev
    return $script:LastDockerExit
}

function Get-WpOut {
    param([Parameter(ValueFromRemainingArguments = $true)][string[]]$WpArgs)
    $prev = $ErrorActionPreference
    $ErrorActionPreference = "SilentlyContinue"
    $raw = & docker exec $Container wp @WpArgs --allow-root 2>&1
    $script:LastDockerExit = $LASTEXITCODE
    $ErrorActionPreference = $prev
    $lines = @($raw | ForEach-Object { "$_" } | Where-Object {
        $_ -and
        $_ -notmatch 'PHP Warning' -and
        $_ -notmatch 'PHP Notice' -and
        $_ -notmatch 'Deprecated:'
    })
    return ($lines -join "`n").Trim()
}

Write-Host "==> Esperando contenedor WordPress..." -ForegroundColor Cyan
$attempts = 0
while ($attempts -lt 40) {
    Invoke-DockerExec @("test", "-f", "/var/www/html/wp-config.php") | Out-Null
    if ($script:LastDockerExit -eq 0) { break }
    Start-Sleep -Seconds 3
    $attempts++
}
if ($attempts -ge 40) {
    throw "WordPress no respondió a tiempo. ¿Corriste 'docker compose up -d'?"
}

Write-Host "==> Instalando WP-CLI..." -ForegroundColor Cyan
Invoke-DockerExec @(
    "bash", "-c",
    "command -v wp >/dev/null 2>&1 || (curl -sS -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x /usr/local/bin/wp)"
) | Out-Null

Write-Host "==> Instalación WordPress (si aplica)..." -ForegroundColor Cyan
Get-WpOut core is-installed | Out-Null
if ($script:LastDockerExit -ne 0) {
    Get-WpOut core install `
        --url="$SiteUrl" `
        --title="Wally Grow" `
        --admin_user="admin" `
        --admin_password="admin" `
        --admin_email="admin@example.com" `
        --skip-email | Out-Host
    if ($script:LastDockerExit -ne 0) { throw "Falló wp core install" }
}

Write-Host "==> Permalink structure..." -ForegroundColor Cyan
Get-WpOut rewrite structure "/%postname%/" | Out-Null
Get-WpOut rewrite flush | Out-Null

Write-Host "==> Temas Blocksy + Wally Grow Child..." -ForegroundColor Cyan
Get-WpOut theme install blocksy --activate | Out-Host
Get-WpOut theme activate wally-grow-child | Out-Host
if ($script:LastDockerExit -ne 0) { throw "No se pudo activar wally-grow-child" }

Write-Host "==> WooCommerce..." -ForegroundColor Cyan
Get-WpOut plugin install woocommerce --activate | Out-Host
Get-WpOut option update woocommerce_store_address "Buenos Aires" | Out-Null
Get-WpOut option update woocommerce_default_country "AR" | Out-Null
Get-WpOut option update woocommerce_currency "ARS" | Out-Null
Get-WpOut option update woocommerce_coming_soon "no" | Out-Null
Get-WpOut option update woocommerce_store_pages_only "no" | Out-Null
Get-WpOut option update woocommerce_weight_unit "kg" | Out-Null
Get-WpOut option update woocommerce_dimension_unit "cm" | Out-Null
Get-WpOut wc tool run install_pages --user=1 | Out-Null

# Scaffolding técnico: métodos listos para que el cliente complete datos (sin inventar cuentas ni tarifas).
Invoke-DockerExec @(
    "wp", "eval",
    "update_option('woocommerce_bacs_settings', array('enabled'=>'yes','title'=>'Transferencia bancaria','description'=>'Completar CBU/alias en WooCommerce > Ajustes > Pagos.','instructions'=>'')); update_option('woocommerce_cod_settings', array('enabled'=>'no'));",
    "--allow-root"
) | Out-Null

Write-Host "==> Categorías base (vacías, slugs del tema)..." -ForegroundColor Cyan
@(
    @{ name = "Iluminación"; slug = "iluminacion" },
    @{ name = "Nutrientes"; slug = "nutrientes" },
    @{ name = "Carpas"; slug = "carpas" },
    @{ name = "Accesorios"; slug = "accesorios" }
) | ForEach-Object {
    $exists = Get-WpOut term list product_cat --slug=$($_.slug) --field=term_id
    if (-not $exists) {
        Get-WpOut term create product_cat $_.name --slug=$($_.slug) | Out-Null
    }
}

Write-Host "==> Páginas legales en borrador (contenido del cliente)..." -ForegroundColor Cyan
$stub = "<!-- Completar contenido con el cliente antes de publicar. -->"
@(
    @{ title = "Preguntas frecuentes"; slug = "preguntas-frecuentes" },
    @{ title = "Política de envíos"; slug = "politica-de-envios" },
    @{ title = "Términos y condiciones"; slug = "terminos-y-condiciones" },
    @{ title = "Política de privacidad"; slug = "politica-de-privacidad" },
    @{ title = "Soporte técnico"; slug = "soporte-tecnico" }
) | ForEach-Object {
    $id = Get-WpOut post list --post_type=page --name=$($_.slug) --field=ID
    if (-not $id) {
        Get-WpOut post create `
            --post_type=page `
            --post_title="$($_.title)" `
            --post_name="$($_.slug)" `
            --post_status=draft `
            --post_content="$stub" `
            --porcelain | Out-Null
    }
}

Write-Host "==> Página de inicio estática..." -ForegroundColor Cyan
$homeId = Get-WpOut post list --post_type=page --name=inicio --field=ID
if (-not $homeId) {
    $homeId = Get-WpOut post create `
        --post_type=page `
        --post_title="Inicio" `
        --post_name=inicio `
        --post_status=publish `
        --porcelain
}
Get-WpOut option update show_on_front page | Out-Null
Get-WpOut option update page_on_front $homeId | Out-Null

$shopId = Get-WpOut option get woocommerce_shop_page_id
$contactId = Get-WpOut post list --post_type=page --name=contacto --field=ID
if (-not $contactId) {
    $contactId = Get-WpOut post create `
        --post_type=page `
        --post_title="Contacto" `
        --post_name=contacto `
        --post_status=publish `
        --porcelain
}

Write-Host "==> Menú principal..." -ForegroundColor Cyan
$menuId = (Get-WpOut menu list --format=ids) -split "\s+" | Select-Object -First 1
if (-not $menuId) {
    $menuId = Get-WpOut menu create "Menu Wally Grow" --porcelain
}

Get-WpOut menu item add-post $menuId $homeId --title="Inicio" | Out-Null
if ($shopId) {
    Get-WpOut menu item add-post $menuId $shopId --title="Tienda" | Out-Null
}
Get-WpOut menu item add-custom $menuId "Categorías" "$SiteUrl/#categorias" | Out-Null
Get-WpOut menu item add-post $menuId $contactId --title="Contacto" | Out-Null

Get-WpOut menu location assign $menuId menu_1 | Out-Null
Get-WpOut menu location assign $menuId menu_mobile | Out-Null

Get-WpOut cache flush | Out-Null

Write-Host ""
Write-Host "Listo. Sitio: $SiteUrl" -ForegroundColor Green
Write-Host "Admin:  $SiteUrl/wp-admin (admin / admin)" -ForegroundColor Green
Write-Host "phpMyAdmin: http://localhost:8083" -ForegroundColor Green
Write-Host "Tienda: AR / ARS. Legales en borrador. WhatsApp/productos/pagos: ver docs/desarrollo/checklist-cliente.md" -ForegroundColor Yellow
