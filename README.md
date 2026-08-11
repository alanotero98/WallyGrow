# Wally Grow Child Theme

Child theme de Blocksy para Gutenberg y WooCommerce. La Home implementada toma
`/docs/DESIGN.md` y `/docs/screen.png` como referencia visual.

## Quick start (Docker)

```powershell
cp .env.example .env
docker compose up -d
.\scripts\setup-local.ps1
```

- Sitio: http://localhost:8082
- phpMyAdmin: http://localhost:8083
- Admin local: `admin` / `admin`

El directorio del repo se monta como tema `wally-grow-child` (cambios en caliente).

**Git:** trabajo diario en `develop` (o `feat/*`); `main` es producción. Ver [docs/desarrollo/git-flujo.md](docs/desarrollo/git-flujo.md).

Ver también [AGENTS.md](AGENTS.md) y [docs/BUENAS-PRACTICAS-IA.md](docs/BUENAS-PRACTICAS-IA.md).

## Home

- `front-page.php`: muestra el contenido Gutenberg de la portada. Si la página
  seleccionada está vacía, renderiza automáticamente la Home incluida.
- `inc/home-content.php`: fuente del patrón **Home completa — Wally Grow**.
- `assets/css/child.css`: layout, componentes y responsive.
- `assets/css/editor.css`: paridad visual en el editor.
- `assets/images/`: imágenes locales obtenidas de la referencia de Stitch.
- `theme.json`: colores, tipografías, ancho de contenido y botones del editor.

El catálogo destacado usa el shortcode nativo de WooCommerce y muestra los
cuatro productos publicados más recientes.

## Configuración manual en WordPress

1. Activar **Wally Grow Child** en Apariencia > Temas.
2. Crear o elegir una página llamada **Inicio** y seleccionarla en
   Ajustes > Lectura > Tu portada muestra > Una página estática.
3. La portada incluida reemplaza automáticamente el contenido anterior que no
   pertenezca a la nueva Home. Para editarla desde Gutenberg, borrar ese contenido
   anterior e insertar el patrón Wally Grow > **Home completa — Wally Grow**.
4. Publicar al menos cuatro productos con imagen, título y precio. El bloque
   destacado muestra automáticamente los cuatro más recientes.
5. En Apariencia > Personalizar > Header, configurar logo, menú principal,
   búsqueda y carrito de Blocksy. Menú sugerido: Inicio, Tienda, Categorías
   (ancla `#categorias`) y Contacto.
6. Revisar que WooCommerce tenga asignada su página Tienda. Los CTA usan esa URL.
7. Reemplazar en los bloques del footer el email, ubicación, enlaces legales y
   textos de contacto definitivos.

## Alcance

No se modificaron WordPress Core, WooCommerce Core ni Blocksy. Las plantillas
Shop, Single Product y Cart continúan fuera del alcance de esta etapa.

## Identidad visual global

La identidad se divide en tres capas para evitar valores duplicados y conservar
compatibilidad entre Blocksy y Gutenberg.

### Blocksy

El child theme redefine las variables públicas de Blocksy en
`assets/css/child.css`, sin modificar el tema padre:

- Paleta 1–8: verde Wally, verde hover, carbón, grises, superficie y blanco.
- Texto, títulos, enlaces normal/hover/active y selección de texto.
- Botones normal/hover, tipografía, radio y color del texto.
- Formularios, foco, controles de selección, tablas y bordes.
- Contenedor global de 1280 px y ritmo de contenido de 24 px.
- H1–H6 y tipografía base mediante las variables tipográficas que consume
  Blocksy.

Esto reemplaza el azul predeterminado de Blocksy incluso en componentes de
WooCommerce, header, buscador, carrito, formularios y plugins integrados.
No es necesario duplicar estos valores en el Personalizador. Si se cambia la
paleta desde Blocksy, la capa de identidad del child theme continúa teniendo
prioridad deliberadamente.

### `theme.json`

Es la fuente principal para Gutenberg y el editor:

- Paleta cerrada, sin colores ni gradientes predeterminados de WordPress.
- Verde primario `#1FA34A`, hover `#08752F`, acento `#166534`.
- Carbón `#111827`, secundarios neutros, blanco, superficies y bordes.
- Manrope para H1–H5; Hanken Grotesk para cuerpo, etiquetas, botones y H6.
- Escala H1–H6, texto de 12/14/16/18 px y display fluido 32–48 px.
- Escala de espaciado basada en 8 px, de 8 a 120 px.
- Botones de 4 px de radio, imágenes/tarjetas de 8 px y ancho wide de 1280 px.
- Estilos base de enlaces, citas, separadores y tablas.

Todos los bloques nuevos heredan estos presets automáticamente. Los colores
personalizados de Gutenberg están desactivados para mantener consistencia.

### CSS del child theme

`assets/css/child.css` cubre solamente lo que `theme.json` no resuelve de forma
fiable entre frontend, Blocksy, WooCommerce y estados interactivos:

- Variables de compatibilidad de Blocksy.
- Hover y foco visible de botones, enlaces y formularios.
- Radios de cards, badges y componentes WooCommerce.
- Layout responsive y composición propia de la Home.
- Fondos alternados neutros `#FFFFFF` / `#F9FAFB`.

Se eliminaron las superficies `#F1F3FF`, bordes `#DCE2F7` y los azules
predeterminados `#2872FA` / `#1559ED` de la identidad efectiva del sitio.

## Footer global

El footer diseñado originalmente para la Home es ahora un componente global y
único:

- `inc/footer.php` contiene el contenido, enlaces y markup del footer.
- Se integra mediante el filtro nativo
  `blocksy:builder:footer:custom-output`.
- Blocksy conserva el elemento semántico `<footer>`, schema y ciclo de render.
- La salida de filas del Footer Builder queda sustituida, por lo que no se
  generan dos footers.
- `assets/css/child.css` contiene su presentación responsive bajo la clase
  `.wg-global-footer`.

El componente se carga en Home, Tienda, archivos de categoría, producto,
Carrito, Checkout, Mi Cuenta, páginas estáticas y páginas de sistema que usen el
footer normal de Blocksy.

Para cambiar textos o enlaces se edita solamente
`wally_grow_get_footer_content()` en `inc/footer.php`. También puede modificarse
el array sin duplicar markup mediante el filtro
`wally_grow:footer:content`.

No se debe volver a insertar el footer como bloques dentro de una página. El
Footer Builder puede permanecer habilitado: el filtro del child theme reemplaza
su contenido de manera deliberada y global.

## Shop y buscador de productos

La página Shop conserva el loop, ordenamiento, contador, paginación, productos y
markup nativos de WooCommerce.

### Archivos

- `inc/woocommerce.php`: encabezado del archivo, columnas, assets y sidebar
  condicional.
- `inc/product-search.php`: HTML reutilizable, shortcode y endpoint REST.
- `assets/css/shop.css`: layout del archivo, cards nativas, toolbar, paginación,
  sidebar y buscador.
- `assets/js/product-search.js`: autocomplete accesible en JavaScript nativo.

### Hooks y filtros

- `woocommerce_before_shop_loop` prioridad 5: título, descripción real del
  archivo y buscador antes de la toolbar de Blocksy.
- `loop_shop_columns`: tres columnas de escritorio.
- `blocksy:general:sidebar-position`: ancho completo mientras no haya facetas o
  widgets; sidebar izquierdo cuando ambos existan.
- `blocksy:hero:enabled`: evita duplicar el título del archivo.
- `rest_api_init`: registra `wally-grow/v1/product-search`.
- `wally_grow:product-search:enqueue`: permite precargar el buscador cuando se
  incorpore al header global.

El componente también está disponible mediante `[wally_product_search]`. Para
reutilizarlo en PHP se usa `wally_grow_get_product_search_markup()`.

### Configuración manual

1. En WooCommerce > Ajustes > Sitio visible, publicar la tienda cuando el
   catálogo esté listo. Actualmente WooCommerce muestra “Próximamente”.
2. La página WooCommerce asignada es `/shop/` (ID 17). La antigua página
   estática `/tienda/` redirige permanentemente a `/shop/`.
3. Publicar productos reales. No se generaron productos ni datos de ejemplo.
4. Cuando existan categorías o atributos, añadir bloques/widgets nativos
   (categorías, filtro por precio, stock o atributos) en Apariencia > Widgets >
   WooCommerce Sidebar. El sidebar se activará automáticamente; si está vacío,
   el catálogo seguirá a ancho completo.
