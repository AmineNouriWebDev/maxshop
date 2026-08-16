<?php
/**
 * theme.php — Générateur de thème CSS dynamique
 * Servi avec Content-Type: text/css
 * Chargé dans includes/script-header.php
 * Remplace (surcharge) dist/css/design-tokens.css
 */

// Cache: 1 heure (les couleurs changent peu souvent)
$cache_seconds = 3600;
header('Content-Type: text/css; charset=utf-8');
header('Cache-Control: public, max-age=' . $cache_seconds);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cache_seconds) . ' GMT');

// Bootstrap minimal — connexion directe à la BDD
$base_dir = realpath(__DIR__ . '/../../') . '/';
require_once $base_dir . 'env.php';   // définit $conn_env

// Connexion MySQLi directe (évite les chemins relatifs de connec.php)
$connexion = mysqli_connect(
    $conn_env['serveur'],
    $conn_env['user_bdd'],
    $conn_env['user_pass'],
    $conn_env['name_bdd']
);
if (!$connexion) {
    // En cas d'erreur BDD, on sort le CSS par défaut et on stoppe
    echo ":root {}";
    exit;
}
mysqli_set_charset($connexion, 'utf8mb4');

// executeRequete() dont dépendent nos fonctions de thème
$chemin_admin    = '_admin_site/';
$chemin_functions = 'fonctions';
require_once $base_dir . $chemin_admin . 'includes/' . $chemin_functions . '/fction_db.php';

// Valeurs par défaut (identiques à design-tokens.css)
$defaults = [
    'primary'           => '#5A31F4',
    'primary_hover'     => '#4A24E8',
    'primary_active'    => '#3A18CC',
    'secondary'         => '#0EA5E9',
    'accent'            => '#F43F5E',
    'bg_base'           => '#F8F7FF',
    'bg_alt'            => '#EEF0F8',
    'surface'           => '#FFFFFF',
    'border'            => '#E0DEFF',
    'text_primary'      => '#120B2E',
    'text_secondary'    => '#6B6589',
    'font_family'       => 'Inter',
    'radius'            => '0.75',
    // Dark mode
    'dark_primary'      => '#7B5EF8',
    'dark_secondary'    => '#38BDF8',
    'dark_accent'       => '#FB7185',
    'dark_bg_base'      => '#0D0B1A',
    'dark_bg_alt'       => '#13111F',
    'dark_surface'      => '#1C1930',
    'dark_border'       => '#2E2752',
    'dark_text_primary' => '#EDE9FF',
    'dark_text_secondary'=> '#9B96BB',
];

// Lire le thème actif en BDD
$theme = $defaults;
try {
    $res = executeRequete("SELECT * FROM `site_theme` WHERE `actif` = 1 ORDER BY `id` DESC LIMIT 1");
    if ($res && ($row = mysqli_fetch_assoc($res))) {
        foreach ($defaults as $key => $val) {
            if (!empty($row[$key])) {
                $theme[$key] = $row[$key];
            }
        }
    }
} catch (Exception $e) {
    // Silently use defaults if table doesn't exist yet
}

// Fonction pour assombrir une couleur hex de X%
function darkenHex($hex, $percent = 10) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $r = max(0, hexdec(substr($hex,0,2)) - round(255*$percent/100));
    $g = max(0, hexdec(substr($hex,2,2)) - round(255*$percent/100));
    $b = max(0, hexdec(substr($hex,4,2)) - round(255*$percent/100));
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

$primary_hover  = $theme['primary_hover'] !== $defaults['primary_hover'] ? $theme['primary_hover'] : darkenHex($theme['primary'], 8);
$primary_active = $theme['primary_active'] !== $defaults['primary_active'] ? $theme['primary_active'] : darkenHex($theme['primary'], 16);

$radius    = floatval($theme['radius']);
$radius_sm = round($radius * 0.667, 3);
$radius_lg = round($radius * 1.333, 3);
$radius_xl = round($radius * 2, 3);

echo ":root {
    /* ── Couleurs primaires (thème client) ── */
    --shop-primary: {$theme['primary']};
    --shop-primary-glow: {$theme['secondary']};
    --shop-primary-hover: {$primary_hover};
    --shop-primary-active: {$primary_active};

    /* ── Couleurs secondaires & accent ── */
    --shop-secondary: {$theme['secondary']};
    --shop-accent: {$theme['accent']};

    /* ── Backgrounds ── */
    --shop-bg-base: {$theme['bg_base']};
    --shop-bg-alt: {$theme['bg_alt']};

    /* ── Surfaces ── */
    --shop-surface: {$theme['surface']};
    --shop-surface-raised: {$theme['bg_alt']};

    /* ── Borders ── */
    --shop-border: {$theme['border']};

    /* ── Textes ── */
    --shop-text-primary: {$theme['text_primary']};
    --shop-text-secondary: {$theme['text_secondary']};
    --shop-text-disabled: color-mix(in srgb, {$theme['text_secondary']} 60%, {$theme['bg_base']});

    /* ── États ── */
    --shop-success: #10B981;
    --shop-warning: #F59E0B;
    --shop-error: #EF4444;
    --shop-info: {$theme['secondary']};

    /* ── Shadows ── */
    --shop-shadow-card: 0 2px 12px color-mix(in srgb, {$theme['primary']} 8%, transparent), 0 1px 3px rgba(0,0,0,0.05);
    --shop-shadow-glow: 0 0 24px color-mix(in srgb, {$theme['primary']} 35%, transparent);
    --shop-shadow-soft: 0 4px 24px rgba(0,0,0,0.08);
    --shop-shadow-card-hover: 0 8px 32px color-mix(in srgb, {$theme['primary']} 14%, transparent);

    /* ── Transitions ── */
    --shop-transition-fast: 150ms ease;
    --shop-transition-base: 250ms ease;
    --shop-transition-slow: 400ms ease;

    /* ── Border radius ── */
    --shop-radius-sm: {$radius_sm}rem;
    --shop-radius: {$radius}rem;
    --shop-radius-lg: {$radius_lg}rem;
    --shop-radius-xl: {$radius_xl}rem;

    /* ── Font ── */
    --shop-font-sans: '{$theme['font_family']}', system-ui, sans-serif;
}

/* ── Dark mode overrides ── */
html.dark,
[data-theme=\"dark\"] {
    --shop-primary: {$theme['dark_primary']};
    --shop-secondary: {$theme['dark_secondary']};
    --shop-accent: {$theme['dark_accent']};

    --shop-bg-base: {$theme['dark_bg_base']};
    --shop-bg-alt: {$theme['dark_bg_alt']};

    --shop-surface: {$theme['dark_surface']};
    --shop-surface-raised: color-mix(in srgb, {$theme['dark_surface']} 80%, {$theme['dark_primary']});

    --shop-border: {$theme['dark_border']};

    --shop-text-primary: {$theme['dark_text_primary']};
    --shop-text-secondary: {$theme['dark_text_secondary']};
    --shop-text-disabled: color-mix(in srgb, {$theme['dark_text_secondary']} 50%, {$theme['dark_bg_base']});

    --shop-success: #34D399;
    --shop-warning: #FCD34D;
    --shop-error: #F87171;
    --shop-info: {$theme['dark_secondary']};

    --shop-shadow-card: 0 2px 16px rgba(0,0,0,0.4), 0 1px 4px rgba(0,0,0,0.3);
    --shop-shadow-glow: 0 0 28px color-mix(in srgb, {$theme['dark_primary']} 40%, transparent);
    --shop-shadow-soft: 0 4px 24px rgba(0,0,0,0.25);
    --shop-shadow-card-hover: 0 8px 32px rgba(0,0,0,0.5);
}
";
?>
