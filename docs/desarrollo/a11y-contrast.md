# Contraste y color — Wally Grow

Ajustes de tokens para WCAG 2.2 AA (texto normal ≥ 4.5:1; controles UI ≥ 3:1).

## Cambios

| Token | Antes | Después | Motivo |
|-------|--------|---------|--------|
| Primary / botones / links | `#1FA34A` | `#1D8847` | Blanco↔verde pasaba ~3.28 (fallaba AA) |
| Selection | `#1FA34A` | `#08752F` | Texto seleccionado blanco con contraste AA |
| Borde de formularios / outline | `#E5E7EB` | `#8B939E` | Contraste no-texto de controles (~3.1:1) |
| Borde decorativo | — | `#E5E7EB` (`border-subtle`) | Cards/header sin “caja” demasiado marcada |
| Placeholder | (opacidad baja) | `#5C5F60` sólido | Texto auxiliar legible |

Hover de marca se mantiene en `#08752F`. Acento de eyebrows/badges `#166534` sobre `#DCFCE7` se conserva.

## Qué no cambia el cliente

Copy, fotos y datos de negocio. Solo tokens visuales del tema.
