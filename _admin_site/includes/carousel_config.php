<?php
/**
 * carousel_config.php — Configuration du Carousel Principal
 * Accessible depuis bloc_accueil via type_section = 99
 */

// Sauvegarde des paramètres
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_carousel'])) {
    $carousel_actif              = isset($_POST['carousel_actif']) ? 1 : 0;
    $carousel_autoplay           = isset($_POST['carousel_autoplay']) ? 1 : 0;
    $carousel_interval           = max(1000, min(10000, intval($_POST['carousel_interval'] ?? 4000)));
    $carousel_hauteur            = mysqli_real_escape_string($connexion, trim($_POST['carousel_hauteur'] ?? 'clamp(320px, 52vw, 640px)'));
    $carousel_afficher_fleches   = isset($_POST['carousel_afficher_fleches']) ? 1 : 0;
    $carousel_afficher_points    = isset($_POST['carousel_afficher_points']) ? 1 : 0;
    $carousel_afficher_compteur  = isset($_POST['carousel_afficher_compteur']) ? 1 : 0;
    $carousel_afficher_progressbar = isset($_POST['carousel_afficher_progressbar']) ? 1 : 0;

    executeRequete("UPDATE site_configuration SET 
        carousel_actif='$carousel_actif',
        carousel_autoplay='$carousel_autoplay',
        carousel_interval='$carousel_interval',
        carousel_hauteur='$carousel_hauteur',
        carousel_afficher_fleches='$carousel_afficher_fleches',
        carousel_afficher_points='$carousel_afficher_points',
        carousel_afficher_compteur='$carousel_afficher_compteur',
        carousel_afficher_progressbar='$carousel_afficher_progressbar'
        WHERE id=1");

    // Also update bloc_accueil etat
    executeRequete("UPDATE bloc_accueil SET etat='$carousel_actif' WHERE type_section='99'");

    sessionStorage_toast('Paramètres du carousel mis à jour avec succès.', 'success');
    header('Location: index.php?r=carousel_config');
    exit;
}

// Récupération des paramètres actuels
$conf = mysqli_fetch_assoc(executeRequete("SELECT * FROM site_configuration WHERE id=1"));
$carousel_actif              = $conf['carousel_actif'] ?? 1;
$carousel_autoplay           = $conf['carousel_autoplay'] ?? 1;
$carousel_interval           = $conf['carousel_interval'] ?? 4000;
$carousel_hauteur            = $conf['carousel_hauteur'] ?? 'clamp(320px, 52vw, 640px)';
$carousel_afficher_fleches   = $conf['carousel_afficher_fleches'] ?? 1;
$carousel_afficher_points    = $conf['carousel_afficher_points'] ?? 1;
$carousel_afficher_compteur  = $conf['carousel_afficher_compteur'] ?? 1;
$carousel_afficher_progressbar = $conf['carousel_afficher_progressbar'] ?? 1;

// Slides actifs
$slides = [];
$r = executeRequete("SELECT * FROM sliders WHERE etat='1' ORDER BY ordre ASC");
while ($s = mysqli_fetch_assoc($r)) $slides[] = $s;
$total_slides = count($slides);

$r_all = executeRequete("SELECT COUNT(*) as n FROM sliders");
$total_all = mysqli_fetch_assoc($r_all)['n'];
?>

<div class="row">
    <div class="col-12">

        <!-- En-tête -->
        <div class="admin-card" style="margin-bottom:1.5rem;">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:18px;height:18px;color:var(--color-primary);">
                        <path d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    Configuration du Carousel Principal
                </div>
                <div style="display:flex;gap:0.5rem;align-items:center;">
                    <a href="index.php?r=sliders" class="admin-btn admin-btn-ghost" style="font-size:0.8rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                        </svg>
                        Gérer les slides (<?php echo $total_all; ?>)
                    </a>
                    <a href="index.php?r=nslider" class="admin-btn admin-btn-primary" style="font-size:0.8rem;">
                        Ajouter un slide
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Formulaire de configuration -->
            <div class="col-lg-7">
                <form method="POST" action="index.php?r=carousel_config">
                    <input type="hidden" name="save_carousel" value="1">

                    <!-- Statut général -->
                    <div class="admin-card" style="margin-bottom:1.5rem;">
                        <div class="admin-card-header" style="padding:1rem 1.25rem;">
                            <div style="font-weight:600;font-size:0.875rem;color:var(--color-text-primary);">
                                ⚡ Statut général
                            </div>
                        </div>
                        <div class="admin-card-body">
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem;background:var(--color-bg-secondary);border-radius:0.5rem;border:1px solid var(--color-border);">
                                <div>
                                    <div style="font-weight:600;font-size:0.9rem;color:var(--color-text-primary);">Carousel actif</div>
                                    <div style="font-size:0.8rem;color:var(--color-text-muted);">Afficher ou masquer le carousel sur la page d'accueil</div>
                                </div>
                                <label class="toggle-switch" style="position:relative;display:inline-block;width:48px;height:26px;flex-shrink:0;">
                                    <input type="checkbox" name="carousel_actif" <?php echo $carousel_actif ? 'checked' : ''; ?> style="opacity:0;width:0;height:0;">
                                    <span style="position:absolute;cursor:pointer;inset:0;background:<?php echo $carousel_actif ? 'var(--color-primary)' : '#ccc'; ?>;border-radius:26px;transition:.3s;">
                                        <span style="position:absolute;content:'';height:20px;width:20px;left:3px;bottom:3px;background:white;border-radius:50%;transition:.3s;transform:<?php echo $carousel_actif ? 'translateX(22px)' : 'translateX(0)'; ?>;"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Lecture automatique -->
                    <div class="admin-card" style="margin-bottom:1.5rem;">
                        <div class="admin-card-header" style="padding:1rem 1.25rem;">
                            <div style="font-weight:600;font-size:0.875rem;color:var(--color-text-primary);">🎬 Lecture automatique</div>
                        </div>
                        <div class="admin-card-body" style="display:flex;flex-direction:column;gap:1rem;">
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem;background:var(--color-bg-secondary);border-radius:0.5rem;border:1px solid var(--color-border);">
                                <div>
                                    <div style="font-weight:600;font-size:0.875rem;">Défilement automatique</div>
                                    <div style="font-size:0.78rem;color:var(--color-text-muted);">Les slides changent automatiquement</div>
                                </div>
                                <label style="position:relative;display:inline-block;width:48px;height:26px;flex-shrink:0;">
                                    <input type="checkbox" name="carousel_autoplay" <?php echo $carousel_autoplay ? 'checked' : ''; ?> style="opacity:0;width:0;height:0;">
                                    <span style="position:absolute;cursor:pointer;inset:0;background:<?php echo $carousel_autoplay ? 'var(--color-primary)' : '#ccc'; ?>;border-radius:26px;transition:.3s;">
                                        <span style="position:absolute;height:20px;width:20px;left:3px;bottom:3px;background:white;border-radius:50%;transition:.3s;transform:<?php echo $carousel_autoplay ? 'translateX(22px)' : 'translateX(0)'; ?>;"></span>
                                    </span>
                                </label>
                            </div>

                            <div class="admin-form-group" style="margin-bottom:0;">
                                <label for="carousel_interval" style="font-size:0.875rem;font-weight:600;">
                                    ⏱ Intervalle de défilement
                                    <span style="font-weight:400;color:var(--color-text-muted);">(ms)</span>
                                </label>
                                <div style="display:flex;gap:0.75rem;align-items:center;margin-top:0.5rem;">
                                    <input type="range" name="carousel_interval" id="carousel_interval" 
                                           min="1000" max="10000" step="500" 
                                           value="<?php echo intval($carousel_interval); ?>"
                                           oninput="document.getElementById('interval_val').textContent=this.value+'ms'"
                                           style="flex:1;accent-color:var(--color-primary);">
                                    <span id="interval_val" style="min-width:60px;font-weight:700;color:var(--color-primary);font-size:0.9rem;"><?php echo $carousel_interval; ?>ms</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;font-size:0.7rem;color:var(--color-text-muted);margin-top:0.25rem;">
                                    <span>1s (rapide)</span><span>5s (normal)</span><span>10s (lent)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dimensions -->
                    <div class="admin-card" style="margin-bottom:1.5rem;">
                        <div class="admin-card-header" style="padding:1rem 1.25rem;">
                            <div style="font-weight:600;font-size:0.875rem;color:var(--color-text-primary);">📐 Hauteur du carousel</div>
                        </div>
                        <div class="admin-card-body" style="display:flex;flex-direction:column;gap:0.75rem;">
                            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.5rem;" id="height-presets">
                                <?php 
                                $presets = [
                                    'clamp(240px, 40vw, 480px)' => ['label'=>'Compact', 'desc'=>'~480px max'],
                                    'clamp(320px, 52vw, 640px)' => ['label'=>'Standard', 'desc'=>'~640px max'],
                                    'clamp(400px, 60vw, 720px)' => ['label'=>'Large',    'desc'=>'~720px max'],
                                    'clamp(480px, 70vw, 880px)' => ['label'=>'Full',      'desc'=>'~880px max'],
                                ];
                                foreach ($presets as $val => $p): 
                                    $active = ($carousel_hauteur === $val);
                                ?>
                                <button type="button" onclick="setHeight('<?php echo $val; ?>')"
                                    style="padding:0.6rem;border-radius:0.4rem;border:2px solid <?php echo $active ? 'var(--color-primary)' : 'var(--color-border)'; ?>;background:<?php echo $active ? 'rgba(90,49,244,0.07)' : 'var(--color-bg-secondary)'; ?>;cursor:pointer;text-align:center;transition:all .2s;"
                                    data-val="<?php echo $val; ?>" class="height-preset-btn">
                                    <div style="font-weight:700;font-size:0.85rem;color:<?php echo $active ? 'var(--color-primary)' : 'var(--color-text-primary)'; ?>;"><?php echo $p['label']; ?></div>
                                    <div style="font-size:0.72rem;color:var(--color-text-muted);"><?php echo $p['desc']; ?></div>
                                </button>
                                <?php endforeach; ?>
                            </div>
                            <div>
                                <label style="font-size:0.78rem;color:var(--color-text-muted);margin-bottom:0.25rem;display:block;">Valeur personnalisée (CSS)</label>
                                <input type="text" name="carousel_hauteur" id="carousel_hauteur_input" class="admin-input" 
                                       value="<?php echo htmlspecialchars($carousel_hauteur); ?>"
                                       placeholder="ex: 500px ou clamp(300px, 50vw, 700px)">
                            </div>
                        </div>
                    </div>

                    <!-- Éléments d'interface -->
                    <div class="admin-card" style="margin-bottom:1.5rem;">
                        <div class="admin-card-header" style="padding:1rem 1.25rem;">
                            <div style="font-weight:600;font-size:0.875rem;color:var(--color-text-primary);">🎛 Éléments d'interface</div>
                        </div>
                        <div class="admin-card-body" style="display:flex;flex-direction:column;gap:0.75rem;">
                            <?php 
                            $toggles = [
                                'carousel_afficher_fleches'      => ['label'=>'Flèches de navigation', 'desc'=>'Boutons Précédent / Suivant', 'val'=>$carousel_afficher_fleches],
                                'carousel_afficher_points'       => ['label'=>'Points de navigation',  'desc'=>'Indicateurs de position (dots)', 'val'=>$carousel_afficher_points],
                                'carousel_afficher_compteur'     => ['label'=>'Compteur de slides',    'desc'=>'Affiche "1/3" en haut à droite', 'val'=>$carousel_afficher_compteur],
                                'carousel_afficher_progressbar'  => ['label'=>'Barre de progression',  'desc'=>'Barre colorée au bas du slide',  'val'=>$carousel_afficher_progressbar],
                            ];
                            foreach ($toggles as $name => $t): ?>
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.6rem 0.75rem;background:var(--color-bg-secondary);border-radius:0.4rem;border:1px solid var(--color-border);">
                                <div>
                                    <div style="font-weight:600;font-size:0.85rem;"><?php echo $t['label']; ?></div>
                                    <div style="font-size:0.75rem;color:var(--color-text-muted);"><?php echo $t['desc']; ?></div>
                                </div>
                                <label style="position:relative;display:inline-block;width:44px;height:24px;flex-shrink:0;">
                                    <input type="checkbox" name="<?php echo $name; ?>" <?php echo $t['val'] ? 'checked' : ''; ?> style="opacity:0;width:0;height:0;">
                                    <span style="position:absolute;cursor:pointer;inset:0;background:<?php echo $t['val'] ? 'var(--color-primary)' : '#ccc'; ?>;border-radius:24px;transition:.3s;">
                                        <span style="position:absolute;height:18px;width:18px;left:3px;bottom:3px;background:white;border-radius:50%;transition:.3s;transform:<?php echo $t['val'] ? 'translateX(20px)' : 'translateX(0)'; ?>;"></span>
                                    </span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="admin-btn admin-btn-primary" style="width:100%;padding:0.75rem;font-size:0.95rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;">
                            <path d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.295 8.235a.75.75 0 10-1.09 1.03l4.25 4.5a.75.75 0 001.09 0l4.25-4.5a.75.75 0 00-1.09-1.03l-2.955 3.129V2.75z"/>
                        </svg>
                        Enregistrer la configuration
                    </button>
                </form>
            </div>

            <!-- Aperçu + liste des slides -->
            <div class="col-lg-5">
                <!-- Statistiques -->
                <div class="admin-card" style="margin-bottom:1.5rem;">
                    <div class="admin-card-header" style="padding:1rem 1.25rem;">
                        <div style="font-weight:600;font-size:0.875rem;color:var(--color-text-primary);">📊 État des slides</div>
                    </div>
                    <div class="admin-card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                            <div style="text-align:center;padding:1rem;background:rgba(16,185,129,0.07);border-radius:0.5rem;border:1px solid rgba(16,185,129,0.2);">
                                <div style="font-size:1.75rem;font-weight:800;color:#10b981;"><?php echo $total_slides; ?></div>
                                <div style="font-size:0.75rem;color:var(--color-text-muted);">Slides actifs</div>
                            </div>
                            <div style="text-align:center;padding:1rem;background:var(--color-bg-secondary);border-radius:0.5rem;border:1px solid var(--color-border);">
                                <div style="font-size:1.75rem;font-weight:800;color:var(--color-text-primary);"><?php echo $total_all; ?></div>
                                <div style="font-size:0.75rem;color:var(--color-text-muted);">Total slides</div>
                            </div>
                        </div>

                        <!-- Liste des slides actifs -->
                        <?php if ($total_slides > 0): ?>
                        <div style="display:flex;flex-direction:column;gap:0.5rem;">
                            <?php foreach ($slides as $i => $sl): 
                                $photo = photoSliderSite($sl['id']);
                            ?>
                            <div style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem;background:var(--color-bg-secondary);border-radius:0.4rem;border:1px solid var(--color-border);">
                                <?php if (!empty($photo)): ?>
                                <img src="../<?php echo htmlspecialchars($photo); ?>" width="50" height="35" style="object-fit:cover;border-radius:0.3rem;flex-shrink:0;" onerror="this.style.display='none'">
                                <?php endif; ?>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:0.8rem;font-weight:600;color:var(--color-text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($sl['titre'] ?: 'Slide '.($i+1)); ?></div>
                                    <?php if (!empty($sl['textBouton'])): ?>
                                    <div style="font-size:0.7rem;color:var(--color-text-muted);"><?php echo htmlspecialchars($sl['textBouton']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <a href="index.php?r=mslider&id=<?php echo $sl['id']; ?>" style="color:var(--color-primary);flex-shrink:0;" title="Modifier">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div style="text-align:center;padding:2rem;color:var(--color-text-muted);">
                            <div style="font-size:2rem;margin-bottom:0.5rem;">🖼</div>
                            <div style="font-size:0.875rem;">Aucun slide actif</div>
                            <a href="index.php?r=nslider" class="admin-btn admin-btn-primary" style="margin-top:1rem;font-size:0.8rem;">Ajouter un slide</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Aperçu visuel de la configuration -->
                <div class="admin-card">
                    <div class="admin-card-header" style="padding:1rem 1.25rem;">
                        <div style="font-weight:600;font-size:0.875rem;">👁 Aperçu de la disposition</div>
                    </div>
                    <div class="admin-card-body">
                        <div id="carousel-preview" style="position:relative;background:#0D0B1A;border-radius:0.5rem;overflow:hidden;height:130px;display:flex;align-items:center;justify-content:center;">
                            <div style="color:rgba(255,255,255,0.3);font-size:0.75rem;text-align:center;">
                                <div style="font-size:2rem;margin-bottom:0.25rem;">🖼</div>
                                Image du slide
                            </div>
                            <!-- Flèches simulées -->
                            <div id="prev-preview" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;">‹</div>
                            <div id="next-preview" style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;">›</div>
                            <!-- Compteur simulé -->
                            <div id="counter-preview" style="position:absolute;top:0.5rem;right:0.75rem;font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.7);">1 / <?php echo max(1, $total_slides); ?></div>
                            <!-- Points simulés -->
                            <div id="dots-preview" style="position:absolute;bottom:0.5rem;left:50%;transform:translateX(-50%);display:flex;gap:4px;">
                                <?php for ($d=0; $d<max(1, min(5, $total_slides)); $d++): ?>
                                <div style="width:<?php echo $d===0?'20px':'7px'; ?>;height:7px;border-radius:99px;background:<?php echo $d===0?'white':'rgba(255,255,255,0.4)'; ?>;"></div>
                                <?php endfor; ?>
                            </div>
                            <!-- Barre de progression simulée -->
                            <div id="progress-preview" style="position:absolute;bottom:0;left:0;height:2px;background:var(--color-primary);width:40%;"></div>
                        </div>
                        <div style="font-size:0.7rem;color:var(--color-text-muted);margin-top:0.5rem;text-align:center;">Aperçu indicatif — l'affichage réel dépend des images des slides</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Sélecteur de hauteur
function setHeight(val) {
    document.getElementById('carousel_hauteur_input').value = val;
    document.querySelectorAll('.height-preset-btn').forEach(function(btn) {
        var active = btn.getAttribute('data-val') === val;
        btn.style.borderColor = active ? 'var(--color-primary)' : 'var(--color-border)';
        btn.style.background  = active ? 'rgba(90,49,244,0.07)' : 'var(--color-bg-secondary)';
        btn.querySelector('div').style.color = active ? 'var(--color-primary)' : 'var(--color-text-primary)';
    });
}

// Toggle visuels interactifs
document.querySelectorAll('input[type=checkbox]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var span = this.nextElementSibling;
        var dot  = span.querySelector('span');
        if (this.checked) {
            span.style.background = 'var(--color-primary)';
            dot.style.transform   = 'translateX(20px)';
        } else {
            span.style.background = '#ccc';
            dot.style.transform   = 'translateX(0)';
        }
        // Mise à jour aperçu
        updatePreview();
    });
});

function updatePreview() {
    var fleches  = document.querySelector('[name=carousel_afficher_fleches]').checked;
    var points   = document.querySelector('[name=carousel_afficher_points]').checked;
    var compteur = document.querySelector('[name=carousel_afficher_compteur]').checked;
    var progress = document.querySelector('[name=carousel_afficher_progressbar]').checked;

    document.getElementById('prev-preview').style.display    = fleches  ? 'flex' : 'none';
    document.getElementById('next-preview').style.display    = fleches  ? 'flex' : 'none';
    document.getElementById('dots-preview').style.display    = points   ? 'flex' : 'none';
    document.getElementById('counter-preview').style.display = compteur ? 'block': 'none';
    document.getElementById('progress-preview').style.display= progress ? 'block': 'none';
}

updatePreview();
</script>
