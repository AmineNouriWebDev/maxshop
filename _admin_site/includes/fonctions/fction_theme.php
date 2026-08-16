<?php
/**
 * fction_theme.php — Fonctions du Thème Manager
 * Inclus depuis _admin_site/index.php via le case 'theme'
 */

// ── Créer la table si elle n'existe pas ──────────────────────────────────────
function themeCreateTableIfNeeded() {
    $sql = "CREATE TABLE IF NOT EXISTS `site_theme` (
        `id`                INT AUTO_INCREMENT PRIMARY KEY,
        `nom`               VARCHAR(100) NOT NULL DEFAULT 'Thème par défaut',
        `actif`             TINYINT(1) NOT NULL DEFAULT 0,
        -- Couleurs light
        `primary`           VARCHAR(20) DEFAULT '#5A31F4',
        `primary_hover`     VARCHAR(20) DEFAULT '#4A24E8',
        `primary_active`    VARCHAR(20) DEFAULT '#3A18CC',
        `secondary`         VARCHAR(20) DEFAULT '#0EA5E9',
        `accent`            VARCHAR(20) DEFAULT '#F43F5E',
        `bg_base`           VARCHAR(20) DEFAULT '#F8F7FF',
        `bg_alt`            VARCHAR(20) DEFAULT '#EEF0F8',
        `surface`           VARCHAR(20) DEFAULT '#FFFFFF',
        `border`            VARCHAR(20) DEFAULT '#E0DEFF',
        `text_primary`      VARCHAR(20) DEFAULT '#120B2E',
        `text_secondary`    VARCHAR(20) DEFAULT '#6B6589',
        -- Typographie & forme
        `font_family`       VARCHAR(80)  DEFAULT 'Inter',
        `radius`            DECIMAL(4,2) DEFAULT '0.75',
        -- Couleurs dark mode
        `dark_primary`      VARCHAR(20) DEFAULT '#7B5EF8',
        `dark_secondary`    VARCHAR(20) DEFAULT '#38BDF8',
        `dark_accent`       VARCHAR(20) DEFAULT '#FB7185',
        `dark_bg_base`      VARCHAR(20) DEFAULT '#0D0B1A',
        `dark_bg_alt`       VARCHAR(20) DEFAULT '#13111F',
        `dark_surface`      VARCHAR(20) DEFAULT '#1C1930',
        `dark_border`       VARCHAR(20) DEFAULT '#2E2752',
        `dark_text_primary` VARCHAR(20) DEFAULT '#EDE9FF',
        `dark_text_secondary` VARCHAR(20) DEFAULT '#9B96BB',
        `created_at`        DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at`        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    executeRequete($sql);

    // Insérer un thème par défaut si vide
    $check = executeRequete("SELECT COUNT(*) AS cnt FROM `site_theme`");
    $row   = mysqli_fetch_assoc($check);
    if ((int)$row['cnt'] === 0) {
        executeRequete("INSERT INTO `site_theme` (`nom`, `actif`) VALUES ('Thème par défaut', 1)");
    }
}

// ── Récupérer tous les thèmes ────────────────────────────────────────────────
function themeGetAll() {
    $res = executeRequete("SELECT * FROM `site_theme` ORDER BY `actif` DESC, `id` DESC");
    $themes = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $themes[] = $row;
    }
    return $themes;
}

// ── Récupérer un thème par ID ────────────────────────────────────────────────
function themeGetById($id) {
    $id  = intval($id);
    $res = executeRequete("SELECT * FROM `site_theme` WHERE `id` = $id LIMIT 1");
    return mysqli_fetch_assoc($res);
}

// ── Récupérer le thème actif ─────────────────────────────────────────────────
function themeGetActive() {
    $res = executeRequete("SELECT * FROM `site_theme` WHERE `actif` = 1 LIMIT 1");
    return mysqli_fetch_assoc($res);
}

// ── Sauvegarder (créer ou mettre à jour) ─────────────────────────────────────
function themeSave($data, $id = null) {
    $fields = ['nom','primary','primary_hover','primary_active','secondary','accent',
               'bg_base','bg_alt','surface','border','text_primary','text_secondary',
               'font_family','radius',
               'dark_primary','dark_secondary','dark_accent','dark_bg_base','dark_bg_alt',
               'dark_surface','dark_border','dark_text_primary','dark_text_secondary'];

    $sets = [];
    foreach ($fields as $f) {
        $val = isset($data[$f]) ? mysqli_real_escape_string(getConnexion(), $data[$f]) : '';
        $sets[] = "`$f` = '$val'";
    }
    $sql_set = implode(', ', $sets);

    if ($id) {
        $id = intval($id);
        executeRequete("UPDATE `site_theme` SET $sql_set WHERE `id` = $id");
        return $id;
    } else {
        executeRequete("INSERT INTO `site_theme` SET $sql_set");
        $res = executeRequete("SELECT LAST_INSERT_ID() AS lid");
        $row = mysqli_fetch_assoc($res);
        return $row['lid'];
    }
}

// ── Activer un thème (désactive les autres) ───────────────────────────────────
function themeActivate($id) {
    $id = intval($id);
    executeRequete("UPDATE `site_theme` SET `actif` = 0");
    executeRequete("UPDATE `site_theme` SET `actif` = 1 WHERE `id` = $id");
}

// ── Dupliquer un thème ────────────────────────────────────────────────────────
function themeDuplicate($id) {
    $t = themeGetById($id);
    if (!$t) return null;
    $t['nom']   = $t['nom'] . ' (copie)';
    $t['actif'] = 0;
    return themeSave($t);
}

// ── Supprimer un thème ────────────────────────────────────────────────────────
function themeDelete($id) {
    $id = intval($id);
    // Ne pas supprimer le thème actif
    executeRequete("DELETE FROM `site_theme` WHERE `id` = $id AND `actif` = 0");
}

// ── Thèmes prédéfinis ─────────────────────────────────────────────────────────
function themePresets() {
    return [
        'violet' => [
            'nom' => '🟣 Violet Premium',
            'primary' => '#5A31F4', 'secondary' => '#0EA5E9', 'accent' => '#F43F5E',
            'bg_base' => '#F8F7FF', 'bg_alt' => '#EEF0F8', 'surface' => '#FFFFFF', 'border' => '#E0DEFF',
            'text_primary' => '#120B2E', 'text_secondary' => '#6B6589', 'font_family' => 'Inter', 'radius' => '0.75',
            'dark_primary' => '#7B5EF8', 'dark_secondary' => '#38BDF8', 'dark_accent' => '#FB7185',
            'dark_bg_base' => '#0D0B1A', 'dark_bg_alt' => '#13111F', 'dark_surface' => '#1C1930',
            'dark_border' => '#2E2752', 'dark_text_primary' => '#EDE9FF', 'dark_text_secondary' => '#9B96BB',
        ],
        'bleu' => [
            'nom' => '🔵 Bleu Corporate',
            'primary' => '#2563EB', 'secondary' => '#0EA5E9', 'accent' => '#F59E0B',
            'bg_base' => '#F0F7FF', 'bg_alt' => '#E0EFFE', 'surface' => '#FFFFFF', 'border' => '#BFDBFE',
            'text_primary' => '#0F1729', 'text_secondary' => '#475569', 'font_family' => 'Inter', 'radius' => '0.5',
            'dark_primary' => '#3B82F6', 'dark_secondary' => '#38BDF8', 'dark_accent' => '#FCD34D',
            'dark_bg_base' => '#0A0F1E', 'dark_bg_alt' => '#0F172A', 'dark_surface' => '#1E293B',
            'dark_border' => '#1E3A5F', 'dark_text_primary' => '#E2E8F0', 'dark_text_secondary' => '#94A3B8',
        ],
        'vert' => [
            'nom' => '🟢 Vert Nature',
            'primary' => '#059669', 'secondary' => '#10B981', 'accent' => '#F59E0B',
            'bg_base' => '#F0FDF4', 'bg_alt' => '#DCFCE7', 'surface' => '#FFFFFF', 'border' => '#BBF7D0',
            'text_primary' => '#052E16', 'text_secondary' => '#374151', 'font_family' => 'Poppins', 'radius' => '1',
            'dark_primary' => '#34D399', 'dark_secondary' => '#6EE7B7', 'dark_accent' => '#FCD34D',
            'dark_bg_base' => '#022C22', 'dark_bg_alt' => '#064E3B', 'dark_surface' => '#065F46',
            'dark_border' => '#047857', 'dark_text_primary' => '#ECFDF5', 'dark_text_secondary' => '#A7F3D0',
        ],
        'orange' => [
            'nom' => '🟠 Orange Commerce',
            'primary' => '#EA580C', 'secondary' => '#F59E0B', 'accent' => '#DC2626',
            'bg_base' => '#FFF7ED', 'bg_alt' => '#FFEDD5', 'surface' => '#FFFFFF', 'border' => '#FED7AA',
            'text_primary' => '#1C0A00', 'text_secondary' => '#6B4226', 'font_family' => 'Outfit', 'radius' => '0.625',
            'dark_primary' => '#FB923C', 'dark_secondary' => '#FCD34D', 'dark_accent' => '#F87171',
            'dark_bg_base' => '#1A0900', 'dark_bg_alt' => '#2C1503', 'dark_surface' => '#3D1F05',
            'dark_border' => '#7C2D12', 'dark_text_primary' => '#FFF7ED', 'dark_text_secondary' => '#FED7AA',
        ],
        'rose' => [
            'nom' => '🌸 Rose Élégant',
            'primary' => '#E11D48', 'secondary' => '#EC4899', 'accent' => '#7C3AED',
            'bg_base' => '#FFF1F2', 'bg_alt' => '#FFE4E6', 'surface' => '#FFFFFF', 'border' => '#FECDD3',
            'text_primary' => '#4C0519', 'text_secondary' => '#6B7280', 'font_family' => 'Nunito', 'radius' => '1.25',
            'dark_primary' => '#FB7185', 'dark_secondary' => '#F9A8D4', 'dark_accent' => '#A78BFA',
            'dark_bg_base' => '#1A0010', 'dark_bg_alt' => '#2D001C', 'dark_surface' => '#3B0027',
            'dark_border' => '#881337', 'dark_text_primary' => '#FFF1F2', 'dark_text_secondary' => '#FECDD3',
        ],
        'gris' => [
            'nom' => '⚫ Dark Minimaliste',
            'primary' => '#18181B', 'secondary' => '#3F3F46', 'accent' => '#22C55E',
            'bg_base' => '#FAFAFA', 'bg_alt' => '#F4F4F5', 'surface' => '#FFFFFF', 'border' => '#E4E4E7',
            'text_primary' => '#09090B', 'text_secondary' => '#71717A', 'font_family' => 'Inter', 'radius' => '0.375',
            'dark_primary' => '#A1A1AA', 'dark_secondary' => '#71717A', 'dark_accent' => '#4ADE80',
            'dark_bg_base' => '#09090B', 'dark_bg_alt' => '#18181B', 'dark_surface' => '#27272A',
            'dark_border' => '#3F3F46', 'dark_text_primary' => '#FAFAFA', 'dark_text_secondary' => '#A1A1AA',
        ],
    ];
}

// ── Récupérer la connexion MySQLi ─────────────────────────────────────────────
function getConnexion() {
    global $connexion;
    return $connexion;
}
?>
