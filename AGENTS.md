# AGENTS.md — Wally Grow Child Theme

Guía para asistentes de IA que trabajan en este repositorio.

## Proyecto

- **Producto:** Wally Grow (ecommerce grow shop)
- **Stack:** WordPress child theme de Blocksy + WooCommerce + Gutenberg
- **Text domain:** `wally-grow-child`
- **Prefijo PHP / handles:** `wally_grow_*` / `wally-grow-*`
- **Tema en disco:** raíz del repo montada como `wally-grow-child`

## Git (obligatorio)

- Trabajar en **`develop`** o en `feat/*` / `fix/*` derivadas de `develop`.
- **No** push ni commits de trabajo diario a **`main`** (producción).
- PR de features → `develop`. Release → PR `develop` → `main`.
- Detalle: [docs/desarrollo/git-flujo.md](docs/desarrollo/git-flujo.md).
- Reglas Cursor: `.cursor/rules/` (todas `alwaysApply`).

## Flujo de tarea

1. Reformular el objetivo.
2. Revisar archivos reales antes de asumir APIs o rutas.
3. Spec breve si la tarea es grande o ambigua; pedir OK antes de editar.
4. Cambios mínimos; no tocar lo no pedido.
5. Validar en local (`http://localhost:8082` con Docker) cuando aplique.

## Seguridad (obligatorio)

- Escapar salida: `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`.
- Sanitizar entrada en REST/AJAX/formularios.
- No secretos en el repo (`.env` está ignorado).
- Endpoints públicos: rate limit + no filtrar stock numérico ni datos internos.
- JS: preferir `textContent` / DOM APIs; evitar `innerHTML` con datos remotos.

## Arquitectura

| Archivo | Rol |
|---------|-----|
| `functions.php` | Bootstrap, fonts, enqueue |
| `inc/contact.php` | WhatsApp + URLs de categorías |
| `inc/setup.php` + `inc/home-content.php` | Patrones / Home |
| `inc/woocommerce.php` + `inc/product-search.php` | Shop + autocomplete REST |
| `inc/footer.php` | Footer global Blocksy |
| `theme.json` | Design tokens Gutenberg |
| `assets/css/*` | Presentación (fonts self-hosted) |

- No modificar WordPress core, Blocksy ni WooCommerce core.
- Extender con hooks/filtros del child theme.
- Soporte WooCommerce: solo en `inc/woocommerce.php`.

## Config local relevante

Definir en `wp-config.php` (o equivalente) cuando el cliente entregue datos reales:

```php
define('WALLY_GROW_WHATSAPP_NUMBER', '54911XXXXXXXX'); // dígitos internacionales
// opcional: define('WALLY_GROW_WHATSAPP_URL', 'https://wa.me/54911...');
```

Sin número/URL, los CTAs de WhatsApp no se renderizan.

## Docker local

```powershell
cp .env.example .env
docker compose up -d
.\scripts\setup-local.ps1
```

- Sitio: `http://localhost:8082`
- phpMyAdmin: `http://localhost:8083`
- Admin setup: `admin` / `admin` (solo local)

## Documentación

- [docs/BUENAS-PRACTICAS-IA.md](docs/BUENAS-PRACTICAS-IA.md)
- [docs/desarrollo/git-flujo.md](docs/desarrollo/git-flujo.md)

## Commits

Conventional Commits, mensaje en imperativo y enfocados en el **por qué**. No push ni commit de secretos. No push a `main` en el día a día.
