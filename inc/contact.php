<?php
/**
 * Contact helpers (WhatsApp and related public URLs).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Digits-only WhatsApp number from WALLY_GROW_WHATSAPP_NUMBER.
 */
function wally_grow_get_whatsapp_number() {
    if (!defined('WALLY_GROW_WHATSAPP_NUMBER')) {
        return '';
    }

    return preg_replace('/\D+/', '', (string) WALLY_GROW_WHATSAPP_NUMBER);
}

/**
 * True when a usable WhatsApp destination is configured.
 */
function wally_grow_has_whatsapp() {
    if (defined('WALLY_GROW_WHATSAPP_URL') && WALLY_GROW_WHATSAPP_URL) {
        return true;
    }

    return wally_grow_get_whatsapp_number() !== '';
}

/**
 * Public WhatsApp deep link. Optional prefilled message.
 *
 * @param string $message Optional chat draft.
 * @return string Empty string when WhatsApp is not configured.
 */
function wally_grow_get_whatsapp_url($message = '') {
    if (defined('WALLY_GROW_WHATSAPP_URL') && WALLY_GROW_WHATSAPP_URL) {
        return esc_url_raw((string) WALLY_GROW_WHATSAPP_URL);
    }

    $number = wally_grow_get_whatsapp_number();
    if ($number === '') {
        return '';
    }

    $url = 'https://wa.me/' . $number;
    if ($message !== '') {
        $url .= '?text=' . rawurlencode($message);
    }

    return $url;
}

/**
 * Sanitized public contact email, or an empty string when unconfigured.
 */
function wally_grow_get_contact_email() {
    if (!defined('WALLY_GROW_CONTACT_EMAIL')) {
        return '';
    }

    return sanitize_email((string) WALLY_GROW_CONTACT_EMAIL);
}

/**
 * First published product_cat URL matching any of the candidate slugs.
 *
 * @param string[] $slugs Candidate term slugs.
 * @param string   $fallback URL when no term exists.
 * @return string
 */
function wally_grow_product_cat_url(array $slugs, $fallback) {
    foreach ($slugs as $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if (!$term || is_wp_error($term)) {
            continue;
        }

        $url = get_term_link($term);
        if (!is_wp_error($url)) {
            return $url;
        }
    }

    return $fallback;
}
