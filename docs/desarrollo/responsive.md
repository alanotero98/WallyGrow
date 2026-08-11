# Diseño y responsividad — Wally Grow

Notas de la pasada técnica sobre layout (sin inventar contenido del cliente).

## Problemas encontrados

| Área | Problema | Corrección |
|------|----------|------------|
| Contenedores | `width: calc(100% - Npx)` frágil con scrollbars | Padding horizontal + `width: 100%` |
| Overflow | Riesgo de scroll horizontal | `overflow-x: clip` en `body` + `max-width: 100%` en media |
| Hero / expertise | CTAs no full-width en móvil; imagen expertise muy alta | Stack + `min-height: 44px`; altura ~280px |
| Tipografía móvil | H1 fijo rígido | `clamp` fluido |
| Categorías | Copy con `opacity: 0.72` | Color muted sólido |
| Footer tablet | Brand comprimido en 2 cols | Brand a ancho completo desde 1024px |
| Shop | Buscador/orden no full-width; títulos largos | 100% width + `overflow-wrap` |
| Touch | CTAs principales | `min-height: 44px` en móvil |

## Breakpoints

- **1024** — grids 2 cols, footer brand full
- **781** — single column home sections, CTAs stack
- **480** — productos 1 col, CTA global full width
- Shop: **999** / **689** (existentes, reforzados)
