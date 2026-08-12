# Checklist del cliente (contenido y operación)

La parte técnica del child theme y del entorno local no inventa datos de negocio.
Completar esto **antes de abrir la tienda** en producción.

## Datos y canales

| Ítem | Dónde | Notas |
|------|--------|------|
| WhatsApp | `wp-config.php` → `WALLY_GROW_WHATSAPP_NUMBER` (dígitos) o `WALLY_GROW_WHATSAPP_URL` | Sin esto no se muestran CTAs de asesoramiento |
| Email de contacto | `WALLY_GROW_CONTACT_EMAIL` | Sin esto el footer no muestra mailto |
| Logo + menú + carrito header | Apariencia → Personalizar → Header (Blocksy) | |
| Google Reviews | Plugin GR Widget + constantes `WALLY_GROW_REVIEWS_*` / URLs Google | Opcional |

## Catálogo

| Ítem | Notas |
|------|------|
| Productos (≥4 recomendados) | Imagen, precio, categoría, stock |
| Categorías | Preferir slugs: `iluminacion`, `nutrientes`, `carpas`, `accesorios` (el setup local ya las crea vacías) |
| Filtros Shop | Widgets en sidebar WooCommerce cuando haya atributos/cats |

## Legales y ayuda (borradores locales)

Publicar y completar en WP Admin:

- Preguntas frecuentes (`preguntas-frecuentes`)
- Política de envíos (`politica-de-envios`)
- Términos y condiciones (`terminos-y-condiciones`)
- Política de privacidad (`politica-de-privacidad`)
- Soporte técnico (`soporte-tecnico`)
- Página Contacto (contenido / formulario)

El footer solo muestra links de páginas **publicadas**.

## Pagos y envíos (operación Woo)

| Ítem | Notas |
|------|------|
| País / moneda | AR / ARS (setup local ya lo deja así) |
| Transferencia (BACS) | Activada como scaffolding; cargar CBU/alias reales |
| Mercado Pago | Instalar/configurar plugin oficial con credenciales del cliente |
| Envíos / retiro en tienda | Zonas, tarifas o local pickup en Woo → Envío |
| Emails Woo | Remitente, plantillas, prueba de pedido |

## No completar en el tema (código)

- Textos legales definitivos
- Precios / stock / fotos de producto
- Credenciales MP / CBU
- Número de WhatsApp productivo
