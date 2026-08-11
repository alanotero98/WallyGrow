# Buenas prácticas — Wally Grow (asistentes de IA)

Documento canónico del child theme **Wally Grow** (Blocksy + WooCommerce).

**Alcance:** flujo de trabajo, arquitectura, seguridad, WordPress/Woo, rendimiento, git, versionado y documentación.

**Fuera de alcance:** identidad visual detallada (tokens viven en `theme.json` y CSS del child).

---

## 1. Cómo usar este documento

- Canónico: `docs/BUENAS-PRACTICAS-IA.md`
- Entrada para la IA: `AGENTS.md`
- Reglas Cursor: `.cursor/rules/*.mdc` (`alwaysApply: true`)
- Prefijo: `wally_grow_*` / `WALLY_GROW_*` / handles `wally-grow-*`
- Text domain: `wally-grow-child`

---

## 2. Flujo de trabajo (spec-first)

Discovery → Spec → Approval → Edit → Validate.

- Tareas grandes o ambiguas: no editar hasta confirmación.
- Cambios mínimos; no refactor fuera de alcance.

---

## 3. Clean Code + SOLID

- Una responsabilidad por función/módulo.
- Extender con hooks; no tocar core de WP, Blocksy ni WooCommerce.
- Sin helpers de un solo uso ni abstracciones prematuras.

---

## 4. Seguridad

- Sin secretos en el repo.
- Escapar salida / sanitizar entrada.
- REST públicos: rate limit; no exponer stock numérico.
- Nonce + validación servidor en formularios.

---

## 5. Arquitectura WordPress

| Pieza | Ubicación |
|-------|-----------|
| Bootstrap | `functions.php` |
| Contacto / WhatsApp / URLs cat | `inc/contact.php` |
| Home / patrones | `inc/setup.php`, `inc/home-content.php`, `front-page.php` |
| Woo + search | `inc/woocommerce.php`, `inc/product-search.php` |
| Footer | `inc/footer.php` |
| Tokens | `theme.json` |
| Fonts | `assets/fonts`, `assets/css/fonts.css` |

Docker local monta la raíz del repo como tema `wally-grow-child`.

---

## 6. Git (obligatorio)

| Rama | Rol |
|------|-----|
| `main` | Producción. Sin push diario. |
| `develop` | Integración. Base de trabajo. |
| `feat/*`, `fix/*`, … | Ramas de trabajo → PR a `develop`. |

Release: PR `develop` → `main` cuando el equipo lo pida.

Detalle: [desarrollo/git-flujo.md](desarrollo/git-flujo.md).

---

## 7. Commits

Conventional Commits. Solo cuando el usuario lo pida. Sin secretos.

---

## 8. Versionado del tema

Cada cambio desplegable: bump `Version:` en `style.css`. `WALLY_GROW_CHILD_VERSION` se lee del tema.

---

## 9. UI / i18n

- Copy de marca existente: español rioplatense (Argentina). No cambiar dialecto sin pedido.
- Strings nuevos: funciones i18n con text domain `wally-grow-child`.

---

## 10. Checklist rápido

- [ ] ¿Estoy en `develop` o en una `feat/*` (no en `main`)?
- [ ] ¿Spec / aprobación si hacía falta?
- [ ] ¿Escaping / sanitizing / rate limit en fronteras?
- [ ] ¿Bump de versión si es desplegable?
- [ ] ¿PR hacia `develop`?
