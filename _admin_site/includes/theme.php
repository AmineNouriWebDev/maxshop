<?php
// Initialiser la table si nécessaire
themeCreateTableIfNeeded();

$msg = '';
$msg_type = 'success';

// ── Actions POST ───────────────────────────────────────────────────
$action = $_POST['theme_action'] ?? $_GET['theme_action'] ?? '';

if ($action === 'activate' && isset($_GET['tid'])) {
    themeActivate($_GET['tid']);
    $msg = '✅ Thème activé avec succès !';
}
if ($action === 'delete' && isset($_GET['tid'])) {
    themeDelete($_GET['tid']);
    $msg = '🗑️ Thème supprimé.';
}
if ($action === 'duplicate' && isset($_GET['tid'])) {
    themeDuplicate($_GET['tid']);
    $msg = '📋 Thème dupliqué.';
}
if ($action === 'apply_preset' && isset($_POST['preset_key'])) {
    $presets = themePresets();
    $key = $_POST['preset_key'];
    if (isset($presets[$key])) {
        $new_id = themeSave($presets[$key]);
        themeActivate($new_id);
        $msg = '🎨 Thème prédéfini appliqué !';
    }
}
if ($action === 'save') {
    $tid = !empty($_POST['tid']) ? intval($_POST['tid']) : null;
    $new_id = themeSave($_POST, $tid);
    if (!empty($_POST['activer'])) {
        themeActivate($new_id);
        $msg = '✅ Thème enregistré et activé !';
    } else {
        $msg = '✅ Thème enregistré.';
    }
}

// Thème en cours d'édition
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$editing = $edit_id ? themeGetById($edit_id) : null;
$themes  = themeGetAll();
$presets = themePresets();

$FONTS = ['Inter','Poppins','Nunito','Montserrat','Outfit','Roboto','Lato','Open Sans','Raleway','Quicksand'];
?>

<style>
.tm-wrap { padding: 1.5rem; max-width: 1100px; }
.tm-header { display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
.tm-header h1 { font-size:1.5rem; font-weight:700; margin:0; color:#1E1646; }
.tm-alert { padding:.75rem 1rem; border-radius:.5rem; margin-bottom:1rem; background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; font-weight:500; }
.tm-card { background:#fff; border:1px solid #e5e7eb; border-radius:.875rem; padding:1.25rem; margin-bottom:1rem; box-shadow:0 1px 4px rgba(0,0,0,.06); }
.tm-card h2 { font-size:1rem; font-weight:700; margin:0 0 1rem; color:#1E1646; display:flex; align-items:center; gap:.5rem; }
/* Presets grid */
.tm-presets { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:.75rem; }
.tm-preset-btn { border:2px solid #e5e7eb; border-radius:.75rem; padding:.75rem; cursor:pointer; text-align:center; background:#fff; transition:all 200ms; }
.tm-preset-btn:hover { border-color:#5A31F4; box-shadow:0 4px 16px rgba(90,49,244,.15); transform:translateY(-2px); }
.tm-preset-swatch { display:flex; gap:4px; justify-content:center; margin-bottom:.5rem; }
.tm-preset-swatch span { width:22px; height:22px; border-radius:50%; border:2px solid rgba(0,0,0,.08); }
.tm-preset-name { font-size:.8rem; font-weight:600; color:#374151; }
/* Themes list */
.tm-theme-list { display:flex; flex-direction:column; gap:.5rem; }
.tm-theme-row { display:flex; align-items:center; gap:.75rem; padding:.625rem 1rem; border:1px solid #e5e7eb; border-radius:.625rem; background:#fafafa; }
.tm-theme-row.active { border-color:#5A31F4; background:#f5f3ff; }
.tm-theme-swatch { width:16px; height:16px; border-radius:50%; border:2px solid rgba(0,0,0,.1); flex-shrink:0; }
.tm-theme-name { flex:1; font-weight:600; font-size:.875rem; color:#1E1646; }
.tm-theme-badge { font-size:.7rem; font-weight:700; background:#5A31F4; color:#fff; padding:2px 8px; border-radius:99px; }
.tm-theme-actions a { font-size:.8rem; padding:4px 10px; border-radius:.375rem; text-decoration:none; font-weight:500; margin-left:4px; }
.tm-btn-edit { background:#EEF0F8; color:#1E1646; }
.tm-btn-edit:hover { background:#5A31F4; color:#fff; }
.tm-btn-activate { background:#d1fae5; color:#065f46; }
.tm-btn-activate:hover { background:#059669; color:#fff; }
.tm-btn-dup { background:#FEF3C7; color:#92400E; }
.tm-btn-dup:hover { background:#F59E0B; color:#fff; }
.tm-btn-del { background:#FEE2E2; color:#991B1B; }
.tm-btn-del:hover { background:#DC2626; color:#fff; }
/* Form */
.tm-form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
@media(max-width:640px){ .tm-form-grid{grid-template-columns:1fr;} }
.tm-form-section { margin-top:1.25rem; padding-top:1rem; border-top:1px solid #e5e7eb; }
.tm-form-section h3 { font-size:.875rem; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .75rem; }
.tm-field { display:flex; flex-direction:column; gap:.375rem; }
.tm-field label { font-size:.8125rem; font-weight:600; color:#374151; }
.tm-field input[type=color] { width:100%; height:38px; border:1px solid #e5e7eb; border-radius:.5rem; cursor:pointer; padding:2px; }
.tm-field input[type=text],.tm-field select,.tm-field input[type=number] {
    width:100%; padding:.5rem .75rem; border:1px solid #e5e7eb; border-radius:.5rem;
    font-size:.875rem; color:#111; outline:none; transition:border-color 200ms;
}
.tm-field input:focus,.tm-field select:focus { border-color:#5A31F4; box-shadow:0 0 0 3px rgba(90,49,244,.1); }
.tm-color-row { display:flex; align-items:center; gap:.5rem; }
.tm-color-row input[type=color] { width:42px; height:38px; flex-shrink:0; }
.tm-color-row input[type=text] { flex:1; }
.tm-actions { display:flex; gap:.75rem; flex-wrap:wrap; margin-top:1.25rem; }
.tm-btn-save { background:#5A31F4; color:#fff; border:none; padding:.625rem 1.25rem; border-radius:.625rem; font-weight:700; cursor:pointer; font-size:.875rem; }
.tm-btn-save:hover { background:#4A24E8; }
.tm-btn-save-active { background:#059669; color:#fff; border:none; padding:.625rem 1.25rem; border-radius:.625rem; font-weight:700; cursor:pointer; font-size:.875rem; }
.tm-btn-save-active:hover { background:#047857; }
.tm-btn-cancel { background:#e5e7eb; color:#374151; border:none; padding:.625rem 1.25rem; border-radius:.625rem; font-weight:600; cursor:pointer; font-size:.875rem; text-decoration:none; display:inline-flex; align-items:center; }
/* Preview bar */
.tm-preview { height:48px; border-radius:.75rem; border:1px solid #e5e7eb; overflow:hidden; display:flex; align-items:center; padding:0 1rem; gap:.75rem; margin-bottom:1rem; }
.tm-preview-logo { width:28px; height:28px; border-radius:6px; }
.tm-preview-bar { flex:1; height:8px; border-radius:4px; opacity:.3; }
.tm-preview-btn { height:28px; padding:0 12px; border-radius:6px; font-size:.75rem; font-weight:700; color:#fff; display:flex; align-items:center; }
</style>

<div class="tm-wrap">
    <div class="tm-header">
        <div>
            <h1>🎨 Thème Manager</h1>
            <p style="margin:0;font-size:.85rem;color:#6B7280;">Personnalisez l'apparence du site sans toucher au code</p>
        </div>
        <?php if (!$editing): ?>
        <a href="index.php?r=theme&edit=new" style="margin-left:auto;background:#5A31F4;color:#fff;padding:.5rem 1rem;border-radius:.625rem;font-weight:700;text-decoration:none;font-size:.875rem;">
            + Nouveau thème
        </a>
        <?php endif; ?>
    </div>

    <?php if ($msg): ?>
    <div class="tm-alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if ($editing || $edit_id === 0 || isset($_GET['edit'])): ?>
    <!-- ── FORMULAIRE ÉDITEUR ── -->
    <?php
    $t = $editing ?: [
        'id'=>'','nom'=>'Nouveau thème','primary'=>'#5A31F4','primary_hover'=>'','primary_active'=>'',
        'secondary'=>'#0EA5E9','accent'=>'#F43F5E','bg_base'=>'#F8F7FF','bg_alt'=>'#EEF0F8',
        'surface'=>'#FFFFFF','border'=>'#E0DEFF','text_primary'=>'#120B2E','text_secondary'=>'#6B6589',
        'font_family'=>'Inter','radius'=>'0.75',
        'dark_primary'=>'#7B5EF8','dark_secondary'=>'#38BDF8','dark_accent'=>'#FB7185',
        'dark_bg_base'=>'#0D0B1A','dark_bg_alt'=>'#13111F','dark_surface'=>'#1C1930',
        'dark_border'=>'#2E2752','dark_text_primary'=>'#EDE9FF','dark_text_secondary'=>'#9B96BB',
    ];
    ?>
    <div class="tm-card">
        <h2>✏️ <?= $t['id'] ? 'Modifier' : 'Créer' ?> un thème</h2>

        <!-- Preview live -->
        <div class="tm-preview" id="livePreview" style="background:<?= htmlspecialchars($t['bg_base']) ?>">
            <div class="tm-preview-logo" style="background:<?= htmlspecialchars($t['primary']) ?>"></div>
            <div class="tm-preview-bar" style="background:<?= htmlspecialchars($t['primary']) ?>"></div>
            <div class="tm-preview-btn" style="background:<?= htmlspecialchars($t['primary']) ?>"><?= htmlspecialchars($t['nom']) ?></div>
            <div class="tm-preview-btn" style="background:<?= htmlspecialchars($t['accent']) ?>">Promo</div>
        </div>

        <form method="POST" action="index.php?r=theme">
            <input type="hidden" name="theme_action" value="save">
            <input type="hidden" name="tid" value="<?= $t['id'] ?>">

            <div class="tm-field" style="margin-bottom:1rem;">
                <label>Nom du thème</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($t['nom']) ?>" required>
            </div>

            <!-- Light mode -->
            <div class="tm-form-section">
                <h3>☀️ Mode clair</h3>
                <div class="tm-form-grid">
                    <?php
                    $light_fields = [
                        'primary'       => 'Couleur principale',
                        'secondary'     => 'Couleur secondaire',
                        'accent'        => 'Couleur accent (badges, promos)',
                        'bg_base'       => 'Fond de page',
                        'bg_alt'        => 'Fond alternatif',
                        'surface'       => 'Surface (cartes)',
                        'border'        => 'Bordures',
                        'text_primary'  => 'Texte principal',
                        'text_secondary'=> 'Texte secondaire',
                    ];
                    foreach ($light_fields as $fname => $flabel):
                        $val = htmlspecialchars($t[$fname] ?? '#000000');
                    ?>
                    <div class="tm-field">
                        <label><?= $flabel ?></label>
                        <div class="tm-color-row">
                            <input type="color" value="<?= $val ?>" oninput="syncColor(this,'txt_<?= $fname ?>')" onchange="updatePreview()">
                            <input type="text" id="txt_<?= $fname ?>" name="<?= $fname ?>" value="<?= $val ?>" oninput="syncColorFromText(this)" placeholder="#000000">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Typo & forme -->
            <div class="tm-form-section">
                <h3>🔤 Typographie & Formes</h3>
                <div class="tm-form-grid">
                    <div class="tm-field">
                        <label>Police principale (Google Fonts)</label>
                        <select name="font_family">
                            <?php foreach ($FONTS as $f): ?>
                            <option value="<?= $f ?>" <?= ($t['font_family'] === $f) ? 'selected' : '' ?>><?= $f ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="tm-field">
                        <label>Rayon des coins (rem) — 0=carré, 1.5=très arrondi</label>
                        <input type="number" name="radius" step="0.125" min="0" max="2" value="<?= htmlspecialchars($t['radius'] ?? '0.75') ?>">
                    </div>
                </div>
            </div>

            <!-- Dark mode -->
            <div class="tm-form-section">
                <h3>🌙 Mode sombre</h3>
                <div class="tm-form-grid">
                    <?php
                    $dark_fields = [
                        'dark_primary'        => 'Principale (dark)',
                        'dark_secondary'      => 'Secondaire (dark)',
                        'dark_accent'         => 'Accent (dark)',
                        'dark_bg_base'        => 'Fond (dark)',
                        'dark_bg_alt'         => 'Fond alt (dark)',
                        'dark_surface'        => 'Surface (dark)',
                        'dark_border'         => 'Bordures (dark)',
                        'dark_text_primary'   => 'Texte principal (dark)',
                        'dark_text_secondary' => 'Texte secondaire (dark)',
                    ];
                    foreach ($dark_fields as $fname => $flabel):
                        $val = htmlspecialchars($t[$fname] ?? '#000000');
                    ?>
                    <div class="tm-field">
                        <label><?= $flabel ?></label>
                        <div class="tm-color-row">
                            <input type="color" value="<?= $val ?>" oninput="syncColor(this,'txt_<?= $fname ?>')">
                            <input type="text" id="txt_<?= $fname ?>" name="<?= $fname ?>" value="<?= $val ?>" oninput="syncColorFromText(this)" placeholder="#000000">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tm-actions">
                <button type="submit" class="tm-btn-save">💾 Enregistrer</button>
                <button type="submit" name="activer" value="1" class="tm-btn-save-active">✅ Enregistrer &amp; Activer</button>
                <a href="index.php?r=theme" class="tm-btn-cancel">Annuler</a>
            </div>
        </form>
    </div>

    <script>
    function syncColor(picker, txtId) {
        document.getElementById(txtId).value = picker.value;
    }
    function syncColorFromText(txt) {
        const prev = txt.previousElementSibling;
        if (/^#[0-9A-Fa-f]{6}$/.test(txt.value)) prev.value = txt.value;
    }
    function updatePreview() {
        const p  = document.getElementById('txt_primary')?.value  || '#5A31F4';
        const a  = document.getElementById('txt_accent')?.value   || '#F43F5E';
        const bg = document.getElementById('txt_bg_base')?.value  || '#F8F7FF';
        const pr = document.querySelector('.tm-preview');
        if (!pr) return;
        pr.style.background = bg;
        const btns = pr.querySelectorAll('.tm-preview-btn');
        const logo = pr.querySelector('.tm-preview-logo');
        const bar  = pr.querySelector('.tm-preview-bar');
        if (logo) logo.style.background = p;
        if (bar)  bar.style.background  = p;
        if (btns[0]) btns[0].style.background = p;
        if (btns[1]) btns[1].style.background = a;
    }
    document.querySelectorAll('input[type=color]').forEach(el => el.addEventListener('input', updatePreview));
    </script>

    <?php else: ?>
    <!-- ── LISTE DES THÈMES ── -->
    <div class="tm-card">
        <h2>🎭 Thèmes prédéfinis</h2>
        <div class="tm-presets">
            <?php foreach ($presets as $key => $p): ?>
            <form method="POST" action="index.php?r=theme">
                <input type="hidden" name="theme_action" value="apply_preset">
                <input type="hidden" name="preset_key" value="<?= $key ?>">
                <button type="submit" class="tm-preset-btn" title="Appliquer <?= $p['nom'] ?>">
                    <div class="tm-preset-swatch">
                        <span style="background:<?= $p['primary'] ?>"></span>
                        <span style="background:<?= $p['secondary'] ?>"></span>
                        <span style="background:<?= $p['accent'] ?>"></span>
                    </div>
                    <div class="tm-preset-name"><?= $p['nom'] ?></div>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="tm-card">
        <h2>📋 Mes thèmes</h2>
        <div class="tm-theme-list">
            <?php foreach ($themes as $t): ?>
            <div class="tm-theme-row <?= $t['actif'] ? 'active' : '' ?>">
                <div class="tm-theme-swatch" style="background:<?= htmlspecialchars($t['primary']) ?>"></div>
                <div class="tm-theme-swatch" style="background:<?= htmlspecialchars($t['secondary']) ?>"></div>
                <div class="tm-theme-swatch" style="background:<?= htmlspecialchars($t['accent']) ?>"></div>
                <div class="tm-theme-name"><?= htmlspecialchars($t['nom']) ?></div>
                <?php if ($t['actif']): ?>
                <span class="tm-theme-badge">ACTIF</span>
                <?php endif; ?>
                <div class="tm-theme-actions">
                    <a href="index.php?r=theme&edit=<?= $t['id'] ?>" class="tm-btn-edit">✏️ Modifier</a>
                    <?php if (!$t['actif']): ?>
                    <a href="index.php?r=theme&theme_action=activate&tid=<?= $t['id'] ?>" class="tm-btn-activate">✅ Activer</a>
                    <?php endif; ?>
                    <a href="index.php?r=theme&theme_action=duplicate&tid=<?= $t['id'] ?>" class="tm-btn-dup">📋 Dupliquer</a>
                    <?php if (!$t['actif']): ?>
                    <a href="index.php?r=theme&theme_action=delete&tid=<?= $t['id'] ?>" class="tm-btn-del" onclick="return confirm('Supprimer ce thème ?')">🗑️</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
