#!/usr/bin/env bash
# WallyGrow — setup local WordPress (Linux / macOS / Git Bash)
# Uso: bash scripts/setup-wp.sh

set -euo pipefail

CONTAINER="${WP_CONTAINER:-wallygrow-wordpress-1}"
SITE_URL="${WP_SITE_URL:-http://localhost:8082}"

echo "==> Esperando contenedor WordPress..."
for i in $(seq 1 40); do
  if docker exec "$CONTAINER" test -f /var/www/html/wp-config.php 2>/dev/null; then
    break
  fi
  sleep 3
done

echo "==> Instalando WP-CLI..."
docker exec "$CONTAINER" bash -c 'command -v wp >/dev/null 2>&1 || (curl -sS -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x /usr/local/bin/wp)'

echo "==> Instalación WordPress (si aplica)..."
if ! docker exec "$CONTAINER" wp core is-installed --allow-root 2>/dev/null; then
  docker exec "$CONTAINER" wp core install \
    --url="$SITE_URL" \
    --title="Wally Grow" \
    --admin_user="${WP_ADMIN_USER:-admin}" \
    --admin_password="${WP_ADMIN_PASS:-admin}" \
    --admin_email="${WP_ADMIN_EMAIL:-admin@example.com}" \
    --skip-email \
    --allow-root
fi

echo "==> Permalink structure..."
docker exec "$CONTAINER" wp rewrite structure '/%postname%/' --allow-root
docker exec "$CONTAINER" wp rewrite flush --allow-root

echo "==> Temas Blocksy + Wally Grow Child..."
docker exec "$CONTAINER" wp theme install blocksy --activate --allow-root
docker exec "$CONTAINER" wp theme activate wally-grow-child --allow-root

echo "==> WooCommerce..."
docker exec "$CONTAINER" wp plugin install woocommerce --activate --allow-root
docker exec "$CONTAINER" wp option update woocommerce_store_address "Buenos Aires" --allow-root
docker exec "$CONTAINER" wp option update woocommerce_default_country "AR" --allow-root
docker exec "$CONTAINER" wp option update woocommerce_currency "ARS" --allow-root
docker exec "$CONTAINER" wp option update woocommerce_coming_soon "no" --allow-root
docker exec "$CONTAINER" wp option update woocommerce_store_pages_only "no" --allow-root
docker exec "$CONTAINER" wp option update woocommerce_weight_unit "kg" --allow-root
docker exec "$CONTAINER" wp option update woocommerce_dimension_unit "cm" --allow-root
docker exec "$CONTAINER" wp wc tool run install_pages --user=1 --allow-root 2>/dev/null || true

docker exec "$CONTAINER" wp eval 'update_option("woocommerce_bacs_settings", array("enabled"=>"yes","title"=>"Transferencia bancaria","description"=>"Completar CBU/alias en WooCommerce > Ajustes > Pagos.","instructions"=>"")); update_option("woocommerce_cod_settings", array("enabled"=>"no"));' --allow-root

echo "==> Categorías base (vacías, slugs del tema)..."
for pair in "Iluminación:iluminacion" "Nutrientes:nutrientes" "Carpas:carpas" "Accesorios:accesorios"; do
  name="${pair%%:*}"
  slug="${pair##*:}"
  if [[ -z "$(docker exec "$CONTAINER" wp term list product_cat --slug="$slug" --field=term_id --allow-root 2>/dev/null | tr -d '\r')" ]]; then
    docker exec "$CONTAINER" wp term create product_cat "$name" --slug="$slug" --allow-root >/dev/null
  fi
done

echo "==> Páginas legales en borrador (contenido del cliente)..."
stub='<!-- Completar contenido con el cliente antes de publicar. -->'
while IFS='|' read -r title slug; do
  if [[ -z "$(docker exec "$CONTAINER" wp post list --post_type=page --name="$slug" --field=ID --allow-root 2>/dev/null | tr -d '\r')" ]]; then
    docker exec "$CONTAINER" wp post create \
      --post_type=page \
      --post_title="$title" \
      --post_name="$slug" \
      --post_status=draft \
      --post_content="$stub" \
      --porcelain \
      --allow-root >/dev/null
  fi
done <<'PAGES'
Preguntas frecuentes|preguntas-frecuentes
Política de envíos|politica-de-envios
Términos y condiciones|terminos-y-condiciones
Política de privacidad|politica-de-privacidad
Soporte técnico|soporte-tecnico
PAGES

echo "==> Página de inicio estática..."
HOME_ID="$(docker exec "$CONTAINER" wp post list --post_type=page --name=inicio --field=ID --allow-root 2>/dev/null | head -n1 | tr -d '\r')"
if [[ -z "$HOME_ID" ]]; then
  HOME_ID="$(docker exec "$CONTAINER" wp post create \
    --post_type=page \
    --post_title="Inicio" \
    --post_name=inicio \
    --post_status=publish \
    --porcelain \
    --allow-root | tr -d '\r')"
fi
docker exec "$CONTAINER" wp option update show_on_front page --allow-root
docker exec "$CONTAINER" wp option update page_on_front "$HOME_ID" --allow-root

SHOP_ID="$(docker exec "$CONTAINER" wp option get woocommerce_shop_page_id --allow-root 2>/dev/null | tr -d '\r')"
CONTACT_ID="$(docker exec "$CONTAINER" wp post list --post_type=page --name=contacto --field=ID --allow-root 2>/dev/null | head -n1 | tr -d '\r')"
if [[ -z "$CONTACT_ID" ]]; then
  CONTACT_ID="$(docker exec "$CONTAINER" wp post create \
    --post_type=page \
    --post_title="Contacto" \
    --post_name=contacto \
    --post_status=publish \
    --porcelain \
    --allow-root | tr -d '\r')"
fi

echo "==> Menú principal..."
MENU_ID="$(docker exec "$CONTAINER" wp menu list --format=ids --allow-root 2>/dev/null | awk '{print $1}' | tr -d '\r')"
if [[ -z "$MENU_ID" ]]; then
  MENU_ID="$(docker exec "$CONTAINER" wp menu create "Menu Wally Grow" --porcelain --allow-root | tr -d '\r')"
fi

docker exec "$CONTAINER" wp menu item add-post "$MENU_ID" "$HOME_ID" --title="Inicio" --allow-root 2>/dev/null || true
if [[ -n "$SHOP_ID" ]]; then
  docker exec "$CONTAINER" wp menu item add-post "$MENU_ID" "$SHOP_ID" --title="Tienda" --allow-root 2>/dev/null || true
fi
docker exec "$CONTAINER" wp menu item add-custom "$MENU_ID" "Categorías" "${SITE_URL}/#categorias" --allow-root 2>/dev/null || true
docker exec "$CONTAINER" wp menu item add-post "$MENU_ID" "$CONTACT_ID" --title="Contacto" --allow-root 2>/dev/null || true

docker exec "$CONTAINER" wp menu location assign "$MENU_ID" menu_1 --allow-root 2>/dev/null || true
docker exec "$CONTAINER" wp menu location assign "$MENU_ID" menu_mobile --allow-root 2>/dev/null || true

docker exec "$CONTAINER" wp cache flush --allow-root

echo ""
echo "Listo. Sitio: $SITE_URL"
echo "Admin:  $SITE_URL/wp-admin (admin / admin)"
echo "phpMyAdmin: http://localhost:8083"
echo "Tienda: AR / ARS. Legales en borrador. WhatsApp/productos/pagos: ver docs/desarrollo/checklist-cliente.md"
