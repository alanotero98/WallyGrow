<?php
/**
 * Front page template.
 *
 * Uses editable Gutenberg content when the selected front page has content.
 * Falls back to the bundled Home pattern so the design works on activation.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$front_page = get_queried_object();
$content = $front_page instanceof WP_Post ? trim($front_page->post_content) : '';
$uses_wally_home_blocks = $content !== '' && strpos($content, 'wg-') !== false;
?>
<div class="wally-home wg-home">
    <?php
    if ($uses_wally_home_blocks) {
        echo apply_filters('the_content', $content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    } else {
        echo apply_filters('the_content', wally_grow_get_home_blocks()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    ?>
</div>
<?php
get_footer();
