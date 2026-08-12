# Wally Grow — handoff para Cursor

## Objetivo

Implementar una mejora visual y de usabilidad incremental en la página de inicio de Wally Grow, construida con WordPress, WooCommerce, Blocksy y un child theme.

La implementación debe conservar la identidad actual, el contenido existente y la arquitectura funcional del sitio. La prioridad es mejorar jerarquía, legibilidad, ritmo vertical, claridad de las acciones y exposición de productos sin convertir la home en un rediseño total.

## Material que debe recibir Cursor

Entregar juntos:

1. El repositorio o workspace del sitio.
2. La captura de la home actual.
3. La maqueta visual propuesta.
4. Este archivo.

Nombres recomendados dentro del repositorio:

```text
docs/references/wally-grow-home-actual.png
docs/references/wally-grow-home-propuesta.png
docs/Wally-Grow-Cursor-Handoff.md
```

La maqueta propuesta es la referencia de composición, jerarquía y densidad. No debe copiarse como una imagen ni utilizarse como fuente de nombres, precios o inventario. Los datos comerciales deben provenir de WooCommerce.

---

# Prompt principal para Cursor

Copiar desde aquí y ejecutar con este documento y las dos imágenes adjuntas:

```text
Actuá como desarrollador senior especializado en WordPress, Blocksy child themes, WooCommerce, frontend responsive, accesibilidad y rendimiento web.

Tenés acceso al repositorio de Wally Grow y a estos materiales:
- captura de la home actual;
- maqueta visual propuesta;
- documento “Wally Grow — handoff para Cursor”.

OBJETIVO
Implementá la mejora incremental de la home definida en el handoff. Conservá la identidad actual, el contenido real, el comportamiento de WordPress/WooCommerce y la estructura general de las secciones. Usá la maqueta como referencia visual, no como una captura que deba reproducirse de forma rígida.

ORDEN DE AUTORIDAD
1. Datos y comportamiento reales del repositorio y WooCommerce.
2. Requisitos funcionales y criterios de aceptación del handoff.
3. Composición y jerarquía de la maqueta propuesta.
4. Estado visual de la captura actual.

Si dos fuentes entran en conflicto, aplicá ese orden. No inventes productos, precios, promociones, beneficios, testimonios ni contenido comercial.

FLUJO OBLIGATORIO
1. Inspeccioná el repositorio antes de modificarlo.
2. Confirmá cuál es el child theme activo, cómo se construye la home y qué archivos/blocks/hooks generan cada sección.
3. Revisá git status y preservá cualquier cambio preexistente que no pertenezca a esta tarea.
4. Localizá los estilos globales, variables, breakpoints, tipografías y componentes existentes antes de crear otros.
5. Identificá cómo se generan actualmente el header, la búsqueda, el carrito, la cuenta y la sección de recomendados.
6. Presentá un plan breve con los archivos que vas a tocar y continuá con la implementación, salvo que exista un bloqueo que cambie materialmente el alcance.
7. Implementá, validá y corregí la versión desktop y responsive.
8. Al terminar, entregá resumen, archivos modificados, decisiones, validaciones ejecutadas y cualquier pendiente verificable.

RESTRICCIONES
- Modificá únicamente el child theme, contenido editable de la home o extensiones propias ya previstas por el proyecto.
- No edites WordPress core, WooCommerce, Blocksy padre ni archivos de plugins de terceros.
- No reemplaces el sistema de templates si puede resolverse con bloques, hooks, clases o estilos del child theme.
- No agregues frameworks CSS, librerías JavaScript, constructores visuales ni fuentes externas nuevas.
- No dupliques funciones ya provistas por Blocksy o WooCommerce.
- No hardcodees productos, precios, stock, URLs, moneda ni año de copyright.
- No agregues nuevas secciones, banners promocionales, carruseles, testimonios, newsletter, popups o beneficios que no existan.
- No uses púrpura/índigo, paleta crema-terracota, dark mode forzado, glassmorphism, pills excesivos, sombras múltiples, emojis ni gradientes decorativos.
- Evitá selectores globales como h1, a, button o .container sin un scope propio de la home.
- Evitá !important salvo que documentes por qué la especificidad de Blocksy no permite una alternativa más segura.
- JavaScript solo si una interacción no puede resolverse con las capacidades actuales del theme o con CSS.

IMPLEMENTACIÓN ESPERADA
- Mantener el orden actual: header, hero, categorías, control y herramientas, asesoramiento, recomendados, CTA final y footer.
- Aumentar legibilidad de navegación, textos secundarios y footer.
- Hacer más visibles y consistentes los CTA.
- Normalizar anchos, gaps y espaciados verticales.
- Mantener la primera categoría como destacada y más ancha en desktop.
- Hacer clickeable toda la superficie de cada tarjeta de categoría con foco de teclado visible.
- Mostrar hasta cuatro productos reales en “Recomendados por Wally Grow” inmediatamente debajo de su encabezado.
- Si no existe una fuente de productos recomendados o el resultado está vacío, ocultar el bloque completo sin dejar altura reservada.
- Usar datos, enlaces, imágenes, moneda y disponibilidad provistos por WooCommerce.
- Mantener el comportamiento nativo de búsqueda, carrito y cuenta de Blocksy/WooCommerce.
- Aplicar todos los criterios responsive, de accesibilidad, rendimiento y aceptación definidos en el handoff.

ENTREGA
- Código final implementado, no solo una propuesta.
- Diff limitado al alcance.
- Sin errores PHP, errores de consola ni overflow horizontal atribuibles a los cambios.
- Captura final desktop y mobile si el entorno permite levantar el sitio.
- Resultados de las validaciones realmente ejecutadas; no afirmes que corriste una prueba que no ejecutaste.
```

---

# Alcance funcional y visual

## 1. Header

Conservar:

- Logo Wally Grow.
- Inicio, Tienda, Categorías y Contacto.
- Búsqueda.
- Carrito.
- Mi cuenta.

Cambios:

- Altura visual objetivo: `72–80px` en desktop.
- Navegación: `14px`, peso `600`, separación de `22–26px`.
- Logo con ancho legible, aproximadamente `130–150px`, respetando su proporción real.
- Búsqueda, carrito y cuenta con áreas interactivas mínimas de `44 × 44px`.
- Mostrar el contador del carrito mediante la funcionalidad nativa cuando corresponda.
- Mantener estados current, hover y `:focus-visible` claros.
- No cambiar el comportamiento sticky salvo que ya exista.

## 2. Hero

Composición desktop:

- Dos columnas equilibradas.
- Texto a la izquierda e imagen de producto a la derecha.
- `gap: 56–72px`.
- Alineación vertical centrada.
- Padding vertical objetivo: `80–96px`.
- No usar altura fija.

Contenido:

```text
TODO PARA CULTIVAR MEJOR

Hacé crecer tu cultivo con los productos correctos

Encontrá iluminación, fertilizantes, carpas y accesorios seleccionados para cada etapa de tu cultivo.

Ver productos

Catálogo especializado · Atención personalizada
```

Tratamiento:

- H1 de una sola instancia en la página.
- Ancho máximo del H1: `11–12ch`.
- Tamaño desktop: `clamp(44px, 5vw, 64px)`.
- Interlineado: `1.02–1.08`.
- Tracking: aproximadamente `-0.035em`.
- Párrafo: `16px`, interlineado `1.6`, máximo `52ch`.
- CTA de al menos `44px` de alto.
- La imagen debe utilizar el asset real disponible, no una captura de la maqueta.

## 3. Categorías

Contenido:

```text
Explorá por categoría
Encontrá lo que necesitás para cada etapa de tu cultivo.
```

Tarjetas visibles:

1. Iluminación — destacada.
2. Nutrientes.
3. Cultivo indoor.

Desktop:

```css
grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1fr);
gap: 16px;
```

Requisitos:

- Altura uniforme de `270–310px`.
- Padding interno de `28–32px`.
- Texto alineado en la zona inferior.
- Título visible a `22–26px` en la destacada y `19–22px` en las restantes.
- Toda la tarjeta debe funcionar como enlace.
- El enlace debe conservar nombre accesible y destino real.
- Hover discreto: variación de color, borde o subrayado. Movimiento máximo de `2px`.
- Foco visible sin depender solo del color.
- Mantener un radio discreto de `6–8px`.
- No agregar sombras decorativas.

## 4. Control y herramientas

Mantener el bloque dividido con texto a la izquierda e imagen a la derecha.

Contenido principal:

```text
CONTROL Y HERRAMIENTAS

Todo para cuidar cada detalle de tu cultivo

Instrumentos de medición, ventilación y herramientas para controlar mejor el ambiente y trabajar con mayor precisión.

Medición de pH y temperatura
Control de humedad y ventilación
Herramientas para mantenimiento

Ver accesorios
```

Requisitos:

- Columnas `1fr 1fr` en desktop.
- Texto centrado verticalmente dentro de su columna.
- Imagen con `width: 100%`, `height: 100%` y `object-fit: cover`.
- Separación interna mínima de `40–48px`.
- Checks reales en HTML/SVG o iconos existentes; no caracteres emoji.

## 5. Asesoramiento personalizado

Mantener imagen a la izquierda y texto a la derecha.

Contenido:

```text
ASESORAMIENTO PERSONALIZADO

Tu cultivo no necesita cualquier producto. Necesita una buena recomendación.

Explorá iluminación, nutrientes, ventilación y accesorios según la etapa de tu cultivo y el espacio disponible.

Recomendaciones según tu cultivo
Alternativas para distintos presupuestos
Atención antes y después de la compra
```

Requisitos:

- Usar la fotografía real existente.
- No agregar un CTA si el bloque actual no lo tiene.
- Mantener el mismo sistema de checks del bloque anterior.
- Evitar que el titular supere `15–17ch` en desktop.

## 6. Recomendados por Wally Grow

Contenido:

```text
Recomendados por Wally Grow
Productos seleccionados para mejorar cada etapa de tu cultivo.
Ver todos
```

Comportamiento:

- Mostrar hasta cuatro productos reales.
- Usar primero la fuente o consulta ya implementada en el sitio.
- No copiar nombres, imágenes o precios de la maqueta.
- No renderizar markup de producto duplicado si WooCommerce ya dispone de un template reutilizable.
- Conservar enlaces, formato monetario, estado de stock e imágenes de WooCommerce.
- No reservar `min-height` para una grilla vacía.
- Si no hay resultados, ocultar título, enlace y contenedor completo.

Fallback permitido solo si el proyecto no tiene una fuente de recomendados:

- Consultar productos publicados y marcados como destacados.
- Limitar a cuatro.
- Permitir modificar los argumentos con un filtro del child theme.
- Si tampoco existen productos destacados, ocultar la sección.

Contrato orientativo, para adaptar a la arquitectura encontrada:

```php
$args = apply_filters(
    'wally_grow_recommended_products_args',
    array(
        'status'   => 'publish',
        'limit'    => 4,
        'featured' => true,
        'orderby'  => 'date',
        'order'    => 'DESC',
        'return'   => 'objects',
    )
);

$products = wc_get_products($args);
```

Este fragmento define el criterio de fallback, no obliga a reemplazar un bloque o loop existente.

Grilla:

- Cuatro columnas en desktop.
- Dos columnas en tablet.
- Una o dos columnas en mobile según el ancho real del card.
- `gap: 20–24px`.
- Imágenes con proporción consistente y `object-fit: contain`.
- Nombres capaces de ocupar dos líneas sin romper la altura del card.
- Precios obtenidos con las funciones de WooCommerce.
- Área interactiva principal claramente identificable.
- Botón o enlace visible con etiqueta “Ver producto”, salvo que el loop nativo requiera otra acción por tipo de producto.

## 7. CTA final

Contenido:

```text
¿Listo para mejorar tu cultivo?
Encontrá productos seleccionados para cada etapa de tu cultivo.
Ir a la tienda
```

Tratamiento:

- Fondo verde de marca.
- Texto blanco centrado.
- Botón blanco con texto verde.
- Botón de al menos `44px` de alto.
- Párrafo con máximo `55ch`.
- Sin textura, ilustraciones, degradados ni patrones nuevos.

## 8. Footer

Conservar el contenido existente y mejorar:

- Tamaño mínimo recomendado: `14px`.
- Interlineado: `1.55–1.65`.
- Padding vertical: `56–64px`.
- Enlaces con hover y foco visibles.
- Copyright con año dinámico.
- Sin aumentar artificialmente su altura.

---

# Sistema visual

## Colores

Antes de definir variables nuevas, reutilizar los tokens existentes del child theme o Blocksy. Si no existen, usar estos valores como fallback:

```css
.home .wg-home {
  --wg-green-700: #08752f;
  --wg-green-800: #066127;
  --wg-green-950: #021a12;
  --wg-ink: #111827;
  --wg-text: #374151;
  --wg-muted: #667085;
  --wg-surface: #ffffff;
  --wg-surface-alt: #f7f9fa;
  --wg-border: #e5e7eb;
  --wg-focus: #159447;
  --wg-radius: 8px;
  --wg-container: 1180px;
}
```

No crear dos verdes casi iguales para el mismo rol. Verificar contraste de texto normal conforme a WCAG 2.2 AA.

## Tipografía

- Mantener la familia tipográfica ya cargada.
- No incorporar otra fuente desde Google Fonts o CDN.
- Títulos con peso `700–800`.
- Cuerpo con peso `400–500`.
- Tamaño base visual: `16px`.
- Texto auxiliar: no menor a `14px` cuando contiene información necesaria.
- Eyebrows: `12px`, peso `700`, uppercase y tracking aproximado de `.08em`.

## Botones y enlaces

```css
.home .wg-button {
  min-height: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0 22px;
  border-radius: 6px;
  font-size: 15px;
  font-weight: 700;
  line-height: 1;
  transition: background-color 180ms ease, color 180ms ease,
              border-color 180ms ease, transform 180ms ease;
}

.home .wg-button:focus-visible,
.home .wg-card-link:focus-visible {
  outline: 3px solid var(--wg-focus);
  outline-offset: 3px;
}

@media (prefers-reduced-motion: reduce) {
  .home .wg-button,
  .home .wg-card-link {
    transition: none;
  }
}
```

Adaptar nombres de clase al código real. No introducir un segundo sistema de botones si Blocksy ya ofrece uno equivalente.

## Superficies

- Radio uniforme de `6–8px`.
- Bordes de `1px` cuando se necesite separación.
- Sin sombras apiladas.
- No usar blur ni glassmorphism.

---

# Layout y responsive

| Propiedad | Desktop ≥ 1200 px | Tablet 768–1199 px | Mobile ≤ 767 px |
|---|---:|---:|---:|
| Contenedor | `max-width: 1180px` | gutter `24px` | gutter `20px` |
| Padding de sección | `80–96px` | `64–72px` | `48–56px` |
| Hero | 2 columnas | 2 columnas ajustadas o stack según contenido | 1 columna |
| Categorías | `2fr 1fr 1fr` | 2 columnas | 1 columna |
| Bloques divididos | `1fr 1fr` | `1fr 1fr` o stack | 1 columna |
| Productos | 4 columnas | 2 columnas | 1–2 columnas |
| H1 | `44–64px` | `40–52px` | `36–42px` |
| Gap principal | `56–72px` | `32–48px` | `28–32px` |

Reglas responsive:

- No depender solo de un breakpoint: verificar que el contenido no colisione con títulos largos.
- En mobile, mantener primero el texto y después la imagen del hero.
- En los bloques editoriales, conservar una secuencia lógica de lectura aunque el orden visual alterne en desktop.
- No usar `order` de CSS de forma que contradiga el orden del DOM para teclado o lectores de pantalla.
- Ningún elemento debe generar scroll horizontal a partir de `320px`.
- No truncar nombres de productos sin un mecanismo para acceder al nombre completo.

---

# Integración WordPress, Blocksy y WooCommerce

## Criterios de implementación

1. Detectar si la home se construye con Gutenberg, patrón, template PHP, Elementor u otro mecanismo ya instalado.
2. Mantener ese mecanismo salvo que exista una razón técnica documentada para cambiarlo.
3. Reutilizar las variables y controles de Blocksy cuando sea posible.
4. Cargar CSS/JS solo donde se usa, preferentemente en la home.
5. Versionar assets propios con `filemtime()` si esa es la convención del child theme.
6. Evitar overrides de templates de WooCommerce si hooks, bloques o CSS resuelven el requisito.
7. Si un override resulta necesario, mantenerlo mínimo y registrar qué template y versión se sobrescribe.
8. Escapar salida según contexto: `esc_html()`, `esc_attr()`, `esc_url()` y funciones equivalentes.
9. Internacionalizar cualquier string incorporado en PHP con el text domain real del child theme.
10. Usar nonces y validación de permisos si se agrega cualquier entrada administrativa; esta tarea no debería requerirla.

## Scope CSS

Preferencia:

```html
<main class="wg-home">
  ...
</main>
```

Todos los estilos nuevos deben quedar bajo `.home .wg-home` o una raíz equivalente ya existente. No afectar páginas de producto, tienda, carrito, checkout, cuenta o entradas.

## Assets

- Reutilizar las imágenes reales existentes.
- No exportar texto dentro de imágenes.
- Incluir `width` y `height` o `aspect-ratio` para reducir cambios de layout.
- Hero: carga prioritaria solo si efectivamente es el elemento LCP.
- Imágenes bajo el primer viewport: `loading="lazy"` cuando corresponda.
- Mantener `srcset` y `sizes` de WordPress.
- Alt text según propósito: descriptivo cuando aporta contenido; vacío cuando es puramente decorativo.

---

# Accesibilidad

- Un solo H1.
- Jerarquía secuencial de encabezados.
- Navegación y controles utilizables solo con teclado.
- `:focus-visible` perceptible en botones, enlaces y tarjetas.
- Áreas táctiles de al menos `44 × 44px` para controles principales.
- Contraste mínimo WCAG 2.2 AA para texto normal y componentes interactivos.
- No comunicar estado únicamente mediante color.
- Etiqueta accesible “Buscar productos” para el disparador de búsqueda.
- Contador del carrito con texto accesible comprensible.
- No duplicar enlaces con nombres ambiguos como “Ver más” sin contexto.
- Respetar `prefers-reduced-motion`.

---

# Rendimiento

- No agregar dependencias externas para resolver layout o hover.
- Evitar JavaScript para comportamiento puramente visual.
- No cargar CSS de la home en todo el sitio si la arquitectura permite aislarlo.
- No reemplazar imágenes optimizadas de WordPress por archivos base64.
- Mantener dimensiones de imagen declaradas para reducir CLS.
- Revisar que el hero no introduzca una regresión visible de LCP.
- Comparar métricas antes y después cuando el entorno permita ejecutar Lighthouse o la herramienta ya configurada.
- Informar las métricas medidas sin inventar resultados.

---

# Criterios de aceptación

## Visuales

- [ ] La home conserva la identidad verde, blanca y azul oscuro.
- [ ] El hero mantiene texto a la izquierda e imagen a la derecha en desktop.
- [ ] El CTA del hero tiene una presencia claramente mayor que en la versión actual.
- [ ] La primera categoría continúa destacada sin romper el grid.
- [ ] Los bloques de herramientas y asesoramiento mantienen su alternancia visual.
- [ ] Los espacios verticales son consistentes y no hay grandes zonas vacías accidentales.
- [ ] El CTA final utiliza botón blanco sobre fondo verde.
- [ ] No se agregaron secciones o estilos fuera del alcance.

## Funcionales

- [ ] Todos los enlaces conservan destinos reales.
- [ ] Búsqueda, carrito y cuenta continúan funcionando.
- [ ] El contador de carrito se actualiza mediante el mecanismo existente.
- [ ] Las categorías completas son clickeables y operables con teclado.
- [ ] La sección de recomendados usa productos reales de WooCommerce.
- [ ] La sección completa se oculta cuando no hay resultados.
- [ ] Los productos enlazan a su ficha correcta.
- [ ] Precios y moneda provienen de WooCommerce.

## Responsive

- [ ] Sin overflow horizontal desde `320px`.
- [ ] Validado al menos en `1440`, `1024`, `768`, `390` y `360px`.
- [ ] Títulos largos no se superponen ni quedan cortados.
- [ ] Las imágenes mantienen proporción y no deforman el contenido.
- [ ] La secuencia de lectura mobile es lógica.

## Calidad

- [ ] No se modificó WordPress core, Blocksy padre ni plugins de terceros.
- [ ] No hay errores PHP atribuibles a los cambios.
- [ ] No hay errores de consola atribuibles a los cambios.
- [ ] `git diff --check` no reporta errores.
- [ ] Los archivos PHP modificados pasan `php -l`.
- [ ] Se ejecutaron los linters/tests existentes y se informaron sus resultados.
- [ ] Los cambios están acotados a la home y no alteran tienda, producto, carrito, checkout o cuenta.

---

# Matriz mínima de pruebas

| Área | Caso | Resultado esperado |
|---|---|---|
| Header | Navegación con teclado | Orden lógico y foco visible |
| Búsqueda | Activar buscador | Abre el mecanismo existente de Blocksy/WooCommerce |
| Carrito | Carrito vacío y con productos | Contador y enlace coherentes |
| Categorías | Click en cualquier zona del card | Navega a la categoría correcta |
| Recomendados | Cuatro productos destacados | Cards con datos reales y enlaces correctos |
| Recomendados | Sin productos destacados | Sección completamente oculta, sin espacio vacío |
| Productos | Título de dos o más líneas | Card conserva alineación y legibilidad |
| Productos | Producto sin stock | Estado mostrado según WooCommerce |
| Responsive | 320–390 px | Sin scroll horizontal ni solapamientos |
| Motion | `prefers-reduced-motion` | Sin transiciones innecesarias |
| Regresión | Tienda, producto, carrito y checkout | Sin cambios visuales no solicitados |

---

# Validación técnica sugerida

Cursor debe detectar y usar los comandos reales del proyecto. Como mínimo:

```bash
git status --short
git diff --check
```

Para cada archivo PHP modificado:

```bash
php -l ruta/al/archivo.php
```

Además:

- Ejecutar linters, build y tests únicamente con los scripts presentes en el repositorio.
- No asumir que existen `npm`, Composer, PHPCS o Playwright sin verificarlo.
- Si el sitio puede levantarse localmente, capturar una vista desktop y una mobile.
- Verificar visualmente home, tienda, producto, carrito, checkout y cuenta para detectar fuga de estilos.

---

# Entrega esperada de Cursor

Al finalizar debe informar:

1. Resumen breve del resultado.
2. Archivos modificados y propósito de cada uno.
3. Cómo se resolvió la sección de recomendados.
4. Qué se reutilizó de Blocksy y WooCommerce.
5. Breakpoints y estados responsive comprobados.
6. Validaciones ejecutadas y resultado real.
7. Pendientes o bloqueos que no pudo verificar.
8. Capturas desktop y mobile si el entorno lo permite.

---

# Brief para regenerar o iterar la maqueta visual

La maqueta actual se generó utilizando la captura original como referencia de identidad, estructura y orden de contenido. Este es el brief visual consolidado:

```text
Use case: ui-mockup
Asset type: high-fidelity full-page desktop ecommerce homepage mockup for developer handoff
Input images: Image 1 is the current Wally Grow homepage and must be used as the layout, brand, content-order, and visual identity reference.

Primary request: Create a polished incremental redesign of the same Wally Grow WordPress + WooCommerce homepage. Preserve the existing identity, logo, restrained aesthetic, section order, Spanish Rioplatense voice, product/grow-shop subject matter, and alternating text-image composition. Improve only hierarchy, readability, spacing, ecommerce clarity, and CTA prominence. This is not a total redesign.

Canvas/composition: Full-page desktop browser view, approximately 1440 px wide and tall enough to show the entire homepage. Use a centered 1180 px content grid, balanced margins, consistent 80–96 px section spacing, and no oversized empty areas.

Visual system: Clean white and very light cool-gray backgrounds; current deep brand green approximately #08752F; near-black green category cards; dark navy text; crisp geometric sans-serif typography with strong headlines and readable body copy. Small 4–8 px corner radii. No decorative shadows.

Required visible layout, in this exact order:

1. Header: Wally Grow logo at left; clearly readable navigation “INICIO”, “TIENDA”, “CATEGORÍAS”, “CONTACTO”; on right visible search, “CARRITO” with a small count, and “MI CUENTA”. Compact 76 px header, clear spacing.

2. Hero: left eyebrow “TODO PARA CULTIVAR MEJOR”; exact headline “Hacé crecer tu cultivo con los productos correctos”; supporting copy “Encontrá iluminación, fertilizantes, carpas y accesorios seleccionados para cada etapa de tu cultivo.”; prominent green button “Ver productos”; small trust line “Catálogo especializado · Atención personalizada”. Right side retains a clean product composition similar to Image 1 with grow tent, LED light and nutrient bottles, slightly larger than the original.

3. Light-gray category area: heading “Explorá por categoría”; subheading “Encontrá lo que necesitás para cada etapa de tu cultivo.”; three equal-height deep-green cards, with the first “Iluminación” visually featured and wider, followed by “Nutrientes” and “Cultivo indoor”. Keep all card text near the lower area, but larger and clearer. Each card looks fully clickable.

4. Existing “CONTROL Y HERRAMIENTAS” split block: text left with exact headline “Todo para cuidar cada detalle de tu cultivo”, three green check bullets, button “Ver accesorios”; existing accessory-store product photo on the right.

5. Existing advice split block: macro green leaf image on the left; text on the right with eyebrow “ASESORAMIENTO PERSONALIZADO” and exact headline “Tu cultivo no necesita cualquier producto. Necesita una buena recomendación.” plus three check bullets. Preserve its restrained style.

6. “Recomendados por Wally Grow” section: eliminate the empty void seen in Image 1. Immediately below the heading, show a clean row of four WooCommerce-style product cards with consistent product imagery, product name placeholder, category, price placeholder in Argentine pesos, and compact “Ver producto” action. These are visual placeholders only, not promotions. Add the link “Ver todos”.

7. Full-width deep-green CTA band: exact heading “¿Listo para mejorar tu cultivo?”, concise supporting text, and a high-contrast white button with green text “Ir a la tienda”.

8. Existing minimal footer with Wally Grow logo, short brand description, category links, divider, and copyright; improve text size and spacing.

Interaction appearance: Buttons at least 44 px high, obvious primary/secondary hierarchy, visible link affordance, no excessive badges.

Constraints: Preserve only the sections visible in Image 1. Do not add announcement bars, testimonials, benefits strips, newsletters, popups, discounts, reviews, new sections, or extra marketing claims. Keep Wally Grow branding. No purple or indigo. No cream-and-terracotta palette. No serif editorial style. No forced dark mode. No excessive pills. No multiple shadows. No emojis. No glassmorphism. No gradients except the existing extremely subtle dark-green card depth. No watermark. Render required text cleanly and exactly.
```

Los productos y precios representados en cualquier maqueta generada con este brief son ilustrativos. La implementación debe obtener esa información desde WooCommerce.
