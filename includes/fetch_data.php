<?php 


session_start();

include("../include.php");
include("../includes/script_panier.php");

/* ============================================================
   Helper : génère la TOPBAR (compteur + grid/list + filtres)
   ============================================================ */
function renderTopbar($total_row, $sort_val = 'price_asc', $stock_only = false) {
    $opts = [
        'price_asc'  => 'Prix croissant',
        'price_desc' => 'Prix décroissant',
        'recent'     => 'Plus récents',
        'name_asc'   => 'Nom A→Z',
        'name_desc'  => 'Nom Z→A',
    ];
    $select_opts = '';
    foreach($opts as $val => $label) {
        $sel = ($val === $sort_val) ? ' selected' : '';
        $select_opts .= '<option value="'.$val.'"'.$sel.'>'.$label.'</option>';
    }
    return '
    <div class="row mb-3">
      <div class="col-12">
        <div class="product-topbar d-flex flex-wrap align-items-center justify-content-center"
             style="background:var(--shop-bg-base,#f4f7f6);border:1px solid var(--shop-border,#e2e8f0);border-radius:0.6rem;padding:0.75rem 0.9rem;gap:2rem;">
          <div class="d-flex align-items-center flex-wrap" style="gap:20px;">
            <span class="text-muted fw-bold" style="font-size:0.9rem; margin:0; display:flex; align-items:center;">
              <i class="fa fa-shopping-basket me-2"></i> '.$total_row.' produits
            </span>
            <div class="d-flex align-items-center" style="gap:15px;">
              <a href="javascript:void(0)" id="grid" title="Vue grille"
                 style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:6px;border:1.5px solid var(--shop-primary,#5A31F4);color:var(--shop-primary,#5A31F4);font-size:1.1rem;transition:all .15s; text-decoration:none; padding:0; line-height:1;">
                <i class="fa fa-th-large" style="margin:auto;"></i>
              </a>
              <a href="javascript:void(0)" id="list" title="Vue liste"
                 style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:6px;border:1.5px solid var(--shop-border,#e2e8f0);color:#9ca3af;font-size:1.1rem;transition:all .15s; text-decoration:none; padding:0; line-height:1;">
                <i class="fa fa-bars" style="margin:auto;"></i>
              </a>
            </div>
          </div>
          <div class="d-flex align-items-center flex-wrap" style="gap:0.6rem;">
            <label class="d-flex align-items-center mb-0" style="font-size:0.82rem;color:var(--shop-text-secondary,#6B7280);gap:5px;cursor:pointer;">
              <input type="checkbox" id="stock_only_filter" class="sort_selector" value="1" '.($stock_only ? 'checked' : '').'
                     style="accent-color:var(--shop-primary,#5A31F4);width:14px;height:14px;"> En stock
            </label>
            <select id="sort_order" class="sort_selector"
                    style="height:30px;padding:0 0.5rem;font-size:0.82rem;border:1.5px solid var(--shop-border,#e2e8f0);border-radius:5px;color:var(--shop-text-primary,#111827);background:var(--shop-bg-base,#fff);">
              '.$select_opts.'
            </select>
          </div>
        </div>
      </div>
    </div>';
}

/* ============================================================
   Helper : CARD GRILLE (style offipro.net / hp-card)
   ============================================================ */
function renderGridCard($id_p, $link_p, $qty, $vprice_js, $vname_js, $sp_disc, $sp_from, $var_badge) {
    $titre     = titreProduits($id_p);
    $photo     = photoProduitsSite($id_p);
    $lien      = lienProduits($link_p);
    $in_stock  = etatStockProduits($id_p) == '1';
    
    $prix_vente = prixVenteProduits($id_p);
    $prix_promo = prixPromoProduits($id_p);
    $has_promo = ($prix_promo && $prix_promo != '0.000');

    // Flash sale info
    $flash_q = executeRequete("SELECT is_flash, promo_end_date FROM produits WHERE id='".intval($id_p)."'");
    $flash_data = mysqli_fetch_assoc($flash_q);
    $is_flash = ($flash_data && $flash_data['is_flash'] == 1);
    $promo_end_ts = ($flash_data && !empty($flash_data['promo_end_date'])) ? strtotime($flash_data['promo_end_date']) : 0;
    $show_countdown = $is_flash && $has_promo && $promo_end_ts > time();

    $discount = 0;
    if ($has_promo && floatval($prix_vente) > 0) {
        $discount = round(((floatval($prix_vente) - floatval($prix_promo)) / floatval($prix_vente)) * 100);
    }

    $badge_html = '';
    if ($discount > 0) {
        $badge_html = '<div class="hp-badge-abs left"><span class="hp-badge hp-badge-promo">-'.$discount.'%</span></div>';
    }
    if ($is_flash && $has_promo) {
        $badge_html .= '<div class="hp-badge-abs right"><span class="hp-badge" style="background:linear-gradient(135deg,#f97316,#ef4444);color:white;animation:glow-pulse 2s ease-in-out infinite;">🔥 Flash</span></div>';
    }

    // Flash countdown overlay on image
    $countdown_html = '';
    if ($show_countdown) {
        $countdown_html = '
        <div style="position:absolute;bottom:8px;left:8px;right:8px;background:rgba(0,0,0,0.75);color:white;border-radius:6px;padding:5px 8px;text-align:center;font-weight:700;font-size:0.72rem;z-index:10;display:flex;align-items:center;justify-content:center;gap:6px;backdrop-filter:blur(4px);box-shadow:0 2px 10px rgba(0,0,0,0.3);outline:1px solid rgba(255,152,0,0.5);">
            <span style="color:#ffb74d;">⏱</span>
            <span class="flash-countdown" data-end="'.$promo_end_ts.'" style="letter-spacing:1px;">...</span>
        </div>';
    }

    $marque_html = '';
    $id_marque = marquesProduits($id_p);
    if ($id_marque != '0' && ApercuMarque($id_marque) != '') {
        $marque_html = '<div class="hp-card-brand">
            <img src="'.photoMarqueSite($id_marque).'" alt="" style="max-height:18px; max-width:70px; object-fit:contain; vertical-align:middle;">
        </div>';
    }

    $prix_html = '<div class="hp-price-row">';
    if (hasVariationPrices($id_p)) {
        $prix_html .= '<span style="font-size:0.7rem; color:var(--shop-text-secondary,#6b7280); font-weight:400; display:block; margin-bottom:-2px;">À partir de</span>';
    }
    if ($has_promo) {
        $prix_html .= '<span class="hp-price-main">'.$prix_promo.' DT</span>
                       <span class="hp-price-old">'.$prix_vente.' DT</span>';
        if ($discount > 0) {
            $prix_html .= '<span class="hp-price-saving">-'.$discount.'%</span>';
        }
    } else {
        $prix_html .= '<span class="hp-price-main">'.$prix_vente.' DT</span>';
    }
    $prix_html .= '</div>';

    $btn_cart_disabled = !$in_stock ? 'disabled' : '';
    $btn_cart_text = $in_stock ? 'Ajouter' : 'Rupture';
    $btn_cart_title = $in_stock ? 'Ajouter au panier' : 'Rupture de stock';

    $titre_js = htmlspecialchars(json_encode($titre), ENT_QUOTES);
    $photo_js = htmlspecialchars(json_encode($photo), ENT_QUOTES);

    return '
    <div class="list-element grid-group-item d-block">
        <article class="hp-card" style="height: 100%; display: flex; flex-direction: column;">
          '.$badge_html.'
          
          <div class="hp-card-img-wrap" style="position:relative;">
            <a href="'.$lien.'" tabindex="-1">
              <img src="'.$photo.'" alt="'.htmlspecialchars($titre).'" loading="lazy">
            </a>
            '.$countdown_html.'
            <div class="hp-card-overlay">
              <button class="hp-card-overlay-btn compare-ol"
                data-compare-id="'.$id_p.'"
                onclick=\'compareToggle('.$id_p.', '.$titre_js.', '.$photo_js.')\'
                title="Comparer">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 0-2-2V9m0 0h18"/></svg>
                <span class="cmp-btn-txt">Comparer</span>
              </button>
            </div>
          </div>

          <div class="hp-card-body">
            '.$marque_html.'

            <div class="hp-card-name">
              <a href="'.$lien.'">'.$titre.'</a>
            </div>

            <div class="hp-card-footer">
              '.$prix_html.'

              <div class="hp-card-btn-row">
                <button
                  class="hp-btn-cart"
                  onclick="addToCart('.afficheChamp($id_p).','.$qty.','.$vprice_js.','.$vname_js.')"
                  '.$btn_cart_disabled.'
                  title="'.$btn_cart_title.'"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                  '.$btn_cart_text.'
                </button>
                <button class="hp-btn-compare-mobile compare-ol" 
                  data-compare-id="'.$id_p.'"
                  onclick=\'compareToggle('.$id_p.', '.$titre_js.', '.$photo_js.')\'
                  title="Comparer">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 0-2-2V9m0 0h18"/></svg>
                </button>
                <a href="'.$lien.'" class="hp-btn-detail" title="Voir le produit">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  <span class="hp-btn-text">Détails</span>
                </a>
              </div>
            </div>
          </div>
        </article>
    </div>';
}

/* ============================================================
   Helper : CARD LISTE (style offipro.net)
   ============================================================ */
function renderListCard($id_p, $link_p, $qty, $vprice_js, $vname_js, $sp_disc, $sp_from, $var_badge) {
    $titre     = titreProduits($id_p);
    $photo     = photoProduitsSite($id_p);
    $lien      = lienProduits($link_p);
    $in_stock  = etatStockProduits($id_p) == '1';
    $has_promo = prixPromoProduits($id_p) != '0.000';
    $prix_show = $has_promo ? prixPromoProduits($id_p) : prixVenteProduits($id_p);
    $prix_barre = $has_promo ? '<span style="text-decoration:line-through;color:#9ca3af;font-size:0.8rem;">'.prixVenteProduits($id_p).' DT</span>' : '';
    $desc = tronquer(strip_tags(caracteristiqueProduits($id_p)), 150);

    $badge_promo = $sp_disc > 0 ? '<span style="position:absolute;top:0.4rem;left:0.4rem;background:var(--shop-accent,#ef4444);color:#fff;font-size:0.6rem;font-weight:800;padding:2px 6px;border-radius:99px;z-index:5;">-'.$sp_disc.'%</span>' : '';

    $stock_badge = $in_stock
        ? '<span style="font-size:0.75rem;font-weight:600;color:#16a34a;"><i class="fa fa-circle" style="font-size:0.5rem;vertical-align:middle;"></i> EN STOCK</span>'
        : '<span style="font-size:0.75rem;font-weight:600;color:#9ca3af;"><i class="fa fa-circle" style="font-size:0.5rem;vertical-align:middle;"></i> EN RUPTURE</span>';

    $btn_voir = '<a href="'.$lien.'" title="Voir"
        style="width:38px;height:38px;background:var(--shop-primary,#F59E0B);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;flex-shrink:0;">
        <i class="fa fa-eye"></i>
    </a>';
    if($in_stock) {
        $btn_cart = '<button onclick="addToCart('.afficheChamp($id_p).','.$qty.','.$vprice_js.','.$vname_js.')" title="Ajouter"
            style="width:38px;height:38px;background:var(--shop-bg-base,#f3f4f6);border:1px solid var(--shop-border,#e2e8f0);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--shop-text-secondary,#6B7280);cursor:pointer;flex-shrink:0;">
            <i class="fa fa-shopping-cart"></i>
        </button>';
    } else {
        $btn_cart = '<button disabled title="En rupture"
            style="width:38px;height:38px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#d1d5db;cursor:not-allowed;flex-shrink:0;opacity:0.6;">
            <i class="fa fa-shopping-cart"></i>
        </button>';
    }

    return '
    <div class="list-element list-group-item d-none" style="padding:0;margin-bottom:0.6rem;">
      <div style="display:flex;align-items:stretch;background:#fff;border:1px solid var(--shop-border,#e2e8f0);border-radius:0.75rem;overflow:hidden;transition:box-shadow .2s;" onmouseover="this.style.boxShadow=\'0 4px 15px rgba(0,0,0,0.08)\';" onmouseout="this.style.boxShadow=\'none\';">
        <!-- Image gauche -->
        <a href="'.$lien.'" style="position:relative;flex-shrink:0;width:160px;background:var(--shop-bg-base,#f8fafc);display:flex;align-items:center;justify-content:center;padding:0.75rem;">
          <img src="'.$photo.'" alt="'.htmlspecialchars($titre).'" style="max-height:120px;max-width:140px;object-fit:contain;">
          '.$badge_promo.'
        </a>
        <!-- Contenu -->
        <div style="flex:1;padding:0.75rem 1rem;display:flex;flex-direction:column;justify-content:space-between;min-width:0;">
          <div>
            <a href="'.$lien.'" style="text-decoration:none;">
              <p style="font-size:0.9rem;font-weight:700;color:var(--shop-primary,#5A31F4);margin:0 0 4px;line-height:1.3;">'.$titre.'</p>
            </a>
            <p style="font-size:0.78rem;color:var(--shop-text-secondary,#6B7280);margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">'.$desc.'</p>
            '.$var_badge.'
          </div>
          <div style="margin-top:8px;">
            '.$stock_badge.'
          </div>
        </div>
        <!-- Prix + boutons droite -->
        <div style="flex-shrink:0;padding:0.75rem;display:flex;flex-direction:column;align-items:flex-end;justify-content:space-between;border-left:1px solid var(--shop-border,#f1f5f9);min-width:130px;">
          <div style="text-align:right;">
            <div style="font-size:1rem;font-weight:700;color:var(--shop-text-primary,#111827);">'.$sp_from.$prix_show.' DT</div>
            '.$prix_barre.'
          </div>
          <div style="display:flex;gap:8px;margin-top:8px;">
            '.$btn_voir.'
            '.$btn_cart.'
          </div>
        </div>
      </div>
    </div>';
}

/* ============================================================
   Helper : PAGINATION HTML
   ============================================================ */
function renderPagination($total_pages, $current_page) {
    if ($total_pages <= 1) return "";
    $html = '<nav aria-label="Page navigation" class="mt-4"><ul class="pagination justify-content-center">';
    
    // Précédent
    $prev_disabled = ($current_page <= 1) ? "disabled" : "";
    $prev_page = max(1, $current_page - 1);
    $html .= '<li class="page-item '.$prev_disabled.'"><a class="page-link" href="javascript:void(0)" onclick="window.filter_data_page('.$prev_page.')">Précédent</a></li>';

    // Numéros
    for($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $current_page) ? "active" : "";
        if ($total_pages > 7) {
            if ($i == 1 || $i == $total_pages || ($i >= $current_page - 2 && $i <= $current_page + 2)) {
                $html .= '<li class="page-item '.$active.'"><a class="page-link" href="javascript:void(0)" onclick="window.filter_data_page('.$i.')">'.$i.'</a></li>';
            } elseif ($i == $current_page - 3 || $i == $current_page + 3) {
                $html .= '<li class="page-item disabled"><a class="page-link" href="javascript:void(0)">...</a></li>';
            }
        } else {
            $html .= '<li class="page-item '.$active.'"><a class="page-link" href="javascript:void(0)" onclick="window.filter_data_page('.$i.')">'.$i.'</a></li>';
        }
    }

    // Suivant
    $next_disabled = ($current_page >= $total_pages) ? "disabled" : "";
    $next_page = min($total_pages, $current_page + 1);
    $html .= '<li class="page-item '.$next_disabled.'"><a class="page-link" href="javascript:void(0)" onclick="window.filter_data_page('.$next_page.')">Suivant</a></li>';

    $html .= '</ul></nav>';
    return $html;
}


if(isset($_POST["action"]) )
{

	
	$type_filter = $_POST["type"] ?? '';
	$qty = "1";

	$limit = 12; // Nombre d'éléments par page
	$page = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
	$start = ($page - 1) * $limit;

	$output 	= '';
	

    if($type_filter == '' || $type_filter == 'produit' || $type_filter == 'E' || $type_filter == 'A')
	{
		
		$query = "	SELECT DISTINCT pr.id, pr.link FROM produits pr, categories_blog ctg WHERE pr.etat = '1' AND pr.categorie IN (SELECT id FROM categories_blog WHERE etat='1')";
        if (isset($afficher_abonnements) && $afficher_abonnements == '0') {
            $query .= " AND pr.type != 'A'";
        }
		if(isset($_POST["link"]) && $_POST["link"]!= '' )
		{
			$link_filter =  $_POST["link"];
			$query .= "
			 AND (pr.categorie IN (SELECT id FROM categories_blog WHERE idparent IN (SELECT id FROM categories_blog WHERE idparent = '".$link_filter."') OR idparent = '".$link_filter."' OR id = '".$link_filter."'))
			";
		}
		if(isset($_POST["brand"]) && !empty($_POST["brand"]))
		{
			$brand_filter = implode("','", $_POST["brand"]);
			$query .= "
			 AND pr.marque IN('".$brand_filter."')
			";
		}
		elseif(isset($_POST["marque"]) && $_POST["marque"]!= '' )
		{
			$marque_link = sanitize($_POST["marque"]);
			$marque_id = idraisonMarque($marque_link);
			if($marque_id) {
				$query .= " AND pr.marque = '".$marque_id."' ";
			}
		}
		if(isset($_POST["category"]))
		{
			$storage_filter = implode("','", $_POST["category"]);
			$query .= "
			 AND pr.categorie IN('".$storage_filter."')
			";
		}
		if(isset($_POST["search"]) && $_POST["search"]!= '' ){
        
            $search= url_rewrite($_POST["search"]);
                    		
            //$query .= " AND ( pr.titre LIKE '%$search%' OR pr.caracteristique LIKE '%$search%' OR pr.link LIKE '%$search%' OR ctg.titre LIKE '%$search%' OR ctg.link LIKE '%$search%') ";
            $query .= " AND ( pr.titre LIKE '%$search%' OR pr.link LIKE '%$search%' ) ";
                                
        }
		if(isset($_POST["caracteristique"]) && $_POST["caracteristique"]!= '' ){
        
            $caracteristique= url_rewrite($_POST["caracteristique"]);
                    		
            //$query .= " AND ( pr.titre LIKE '%$search%' OR pr.caracteristique LIKE '%$search%' OR pr.link LIKE '%$search%' OR ctg.titre LIKE '%$search%' OR ctg.link LIKE '%$search%') ";
            $query .= " AND ( pr.titre LIKE '%$caracteristique%' OR pr.link LIKE '%$caracteristique%' OR pr.caracteristique LIKE '%$caracteristique%' ) ";
                                
        }
            
        if(isset($_POST["categoryByTitre"]) && $_POST["categoryByTitre"]!= '' ){
            
                $ctg= $_POST["categoryByTitre"];
                        		
                //$query .= " AND ( ctg.titre LIKE '%$ctg%'  OR ctg.link LIKE '%$ctg%' OR  pr.idparent_categ = ctg.id OR pr.categorie = ctg.id AND ( pr.categorie IN ( SELECT id FROM categories_blog WHERE idparent = '".idBySearchCategBlog($ctg)."' ) ) ) ";
                $query .= " AND ( pr.categorie IN ( SELECT id FROM categories_blog WHERE idparent IN (SELECT id FROM categories_blog WHERE idparent = '".idBySearchCategBlog($ctg)."') OR idparent = '".idBySearchCategBlog($ctg)."' OR id = '".idBySearchCategBlog($ctg)."' ) ) ";
                                     
        }
		
        if(isset($_POST["minimum_price"], $_POST["maximum_price"]) && !empty($_POST["minimum_price"]) && !empty($_POST["maximum_price"]))
    	{
    		$query .= "
    		 AND pr.prix_vente BETWEEN '".$_POST["minimum_price"]."' AND '".$_POST["maximum_price"]."'
    		";
    	}

        // Filtre En Stock uniquement
        $stock_filter = isset($_POST['stock_only']) && $_POST['stock_only'] == '1' ? " AND pr.etat_stock = '1' " : '';
        $query .= $stock_filter;

        // Tri dynamique
        $sort_val = isset($_POST['sort']) ? $_POST['sort'] : 'price_asc';
        $order_sql = 'pr.prix_vente ASC';
        if($sort_val == 'price_desc') $order_sql = 'pr.prix_vente DESC';
        elseif($sort_val == 'recent')  $order_sql = 'pr.id DESC';
        elseif($sort_val == 'name_asc') $order_sql = 'pr.titre ASC';
        elseif($sort_val == 'name_desc') $order_sql = 'pr.titre DESC';

		$query .=" GROUP BY pr.id ORDER BY ".$order_sql;
        $query_stock_filter = $stock_filter;
		
		// 1. Récupération du total pour la pagination
		$result_total = executeRequete($query);
		$total_row	= mysqli_num_rows($result_total);
		$total_pages = ceil($total_row / $limit);

		// 2. Ajout de la limite pour la page courante
		$query .= " LIMIT $start, $limit ";
		$result = executeRequete($query);
		
		if($total_row > 0)
		{
			
			$is_stock = isset($_POST['stock_only']) && $_POST['stock_only'] == '1';
			$output .= renderTopbar($total_row, $sort_val, $is_stock);
			$output .= '<div class="animated fadeInUp list" data-delay="1s"><div class="product-grid-container">';
			while($row = mysqli_fetch_array($result))
			{
				if($row['id'])    $id_p    = $row['id']; 
				if($row['link'])  $link_p  = $row['link']; 
				
                $sp_pv   = prixVenteProduits($id_p);
                $sp_pp   = prixPromoProduits($id_p);
                $sp_disc = 0;
                $pv_num = floatval(preg_replace('/[^0-9.]/', '', $sp_pv));
                $pp_num = floatval(preg_replace('/[^0-9.]/', '', $sp_pp));
                if($pp_num > 0 && $pv_num > 0 && $pp_num < $pv_num) {
                    $sp_disc = round((($pv_num - $pp_num) / $pv_num) * 100);
                }
                $sp_has_var = hasVariationPrices($id_p);
                $sp_from = $sp_has_var ? '<span style="font-size:0.7rem;color:#9ca3af;font-weight:400;">À partir de </span>' : '';
                
                $vname_js = "''";
                $vprice_js = "null";
                $var_badge = "";
                if($sp_has_var) {
                    $low_var = getLowestPriceVariation($id_p);
                    if($low_var) {
                        $vname_js = "'" . addslashes($low_var['label']) . "'";
                        $vprice_js = floatval($low_var['prix_vente']);
                        $var_badge = '<div style="font-size:0.72rem;color:#64748b;font-weight:500;"><i class="fa fa-info-circle"></i> ' . afficheChamp($low_var['label']) . '</div>';
                    }
                }
				
				$output .= renderListCard($id_p, $link_p, $qty, $vprice_js, $vname_js, $sp_disc, $sp_from, $var_badge);
				$output .= renderGridCard($id_p, $link_p, $qty, $vprice_js, $vname_js, $sp_disc, $sp_from, $var_badge);
			}


			
            $output.= '</div></div>';
            
            // Pagination html
            $output .= renderPagination($total_pages, $page);

		}
		else
		{
				$output = '
				
					<div class="row">
						<div class="col-12">
							<div class="product-topbar d-xl-flex align-items-end justify-content-between">
								<!-- Total Products -->
								<div class="total-products">
									<p> Il y a 0 produits.</p>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-12"><h5 class="text-center"> Aucun produit trouvée </h5></div>
				    </div>';
		}		
	}
	elseif($type_filter == 'E')
	{
	
		$query = "	SELECT DISTINCT pr.id, pr.link FROM produits pr, categories_blog ctg WHERE pr.etat = '1'  AND pr.type = 'E' AND pr.categorie IN (SELECT id FROM categories_blog WHERE etat='1')";
        if (isset($afficher_abonnements) && $afficher_abonnements == '0') {
            $query .= " AND pr.type != 'A'";
        }
		if(isset($_POST["link"]) && $_POST["link"]!= '' )
		{
			$link_filter =  $_POST["link"];
			$query .= "
			 AND (pr.categorie IN (SELECT id FROM categories_blog WHERE idparent IN (SELECT id FROM categories_blog WHERE idparent = '".$link_filter."') OR idparent = '".$link_filter."' OR id = '".$link_filter."'))
			";
		}
		if(isset($_POST["brand"]) && !empty($_POST["brand"]))
		{
			$brand_filter = implode("','", $_POST["brand"]);
			$query .= "
			 AND pr.marque IN('".$brand_filter."')
			";
		}
		elseif(isset($_POST["marque"]) && $_POST["marque"]!= '' )
		{
			$marque_link = sanitize($_POST["marque"]);
			$marque_id = idraisonMarque($marque_link);
			if($marque_id) {
				$query .= " AND pr.marque = '".$marque_id."' ";
			}
		}
		if(isset($_POST["category"]))
		{
			$storage_filter = implode("','", $_POST["category"]);
			$query .= "
			 AND pr.categorie IN('".$storage_filter."')
			";
		}
		if(isset($_POST["search"]) && $_POST["search"]!= '' ){
        
            $search= url_rewrite($_POST["search"]);
                    		
            //$query .= " AND ( pr.titre LIKE '%$search%' OR pr.caracteristique LIKE '%$search%' OR pr.link LIKE '%$search%' OR ctg.titre LIKE '%$search%' OR ctg.link LIKE '%$search%') ";
            $query .= " AND ( pr.titre LIKE '%$search%' OR pr.link LIKE '%$search%' ) ";
                                
        }
            
        if(isset($_POST["categoryByTitre"]) && $_POST["categoryByTitre"]!= '' ){
            
                $ctg= $_POST["categoryByTitre"];
                        		
                //$query .= " AND ( ctg.titre LIKE '%$ctg%'  OR ctg.link LIKE '%$ctg%' OR  pr.idparent_categ = ctg.id OR pr.categorie = ctg.id AND ( pr.categorie IN ( SELECT id FROM categories_blog WHERE idparent = '".idBySearchCategBlog($ctg)."' ) ) ) ";
                $query .= " AND ( pr.categorie IN ( SELECT id FROM categories_blog WHERE idparent IN (SELECT id FROM categories_blog WHERE idparent = '".idBySearchCategBlog($ctg)."') OR idparent = '".idBySearchCategBlog($ctg)."' OR id = '".idBySearchCategBlog($ctg)."' ) ) ";
                                     
        }
		
		// Filtre En Stock + Tri dynamique (type E)
        $stock_filter = isset($_POST['stock_only']) && $_POST['stock_only'] == '1' ? " AND pr.etat_stock = '1' " : '';
        $query .= $stock_filter;
        $sort_val = isset($_POST['sort']) ? $_POST['sort'] : 'price_asc';
        $order_sql = 'pr.prix_vente ASC';
        if($sort_val == 'price_desc') $order_sql = 'pr.prix_vente DESC';
        elseif($sort_val == 'recent')  $order_sql = 'pr.id DESC';
        elseif($sort_val == 'name_asc') $order_sql = 'pr.titre ASC';
        elseif($sort_val == 'name_desc') $order_sql = 'pr.titre DESC';
		$query .=" GROUP BY pr.id ORDER BY ".$order_sql;

		// 1. Récupération du total pour la pagination
		$result_total = executeRequete($query);
		$total_row	= mysqli_num_rows($result_total);
		$total_pages = ceil($total_row / $limit);

		// 2. Ajout de la limite pour la page courante
		$query .= " LIMIT $start, $limit ";
		$result = executeRequete($query);
		
		if($total_row > 0)
		{
			
			$is_stock = isset($_POST['stock_only']) && $_POST['stock_only'] == '1';
			$output .= renderTopbar($total_row, $sort_val, $is_stock);
			$output .= '<div class="animated fadeInUp list" data-delay="1s"><div class="product-grid-container">';
			while($row = mysqli_fetch_array($result))
			{
				if($row['id'])    $id_p    = $row['id']; 
				if($row['link'])  $link_p  = $row['link']; 
				
                $sp_pv   = prixVenteProduits($id_p);
                $sp_pp   = prixPromoProduits($id_p);
                $sp_disc = 0;
                $pv_num = floatval(preg_replace('/[^0-9.]/', '', $sp_pv));
                $pp_num = floatval(preg_replace('/[^0-9.]/', '', $sp_pp));
                if($pp_num > 0 && $pv_num > 0 && $pp_num < $pv_num) {
                    $sp_disc = round((($pv_num - $pp_num) / $pv_num) * 100);
                }
                $sp_has_var = hasVariationPrices($id_p);
                $sp_from = $sp_has_var ? '<span style="font-size:0.7rem;color:#9ca3af;font-weight:400;">À partir de </span>' : '';
                $vname_js = "''";
                $vprice_js = "null";
                $var_badge = "";
				
				$output .= renderListCard($id_p, $link_p, $qty, $vprice_js, $vname_js, $sp_disc, $sp_from, $var_badge);
				$output .= renderGridCard($id_p, $link_p, $qty, $vprice_js, $vname_js, $sp_disc, $sp_from, $var_badge);
			}
			
                $output.= '</div></div>';
                
            // Pagination html
            $output .= renderPagination($total_pages, $page);

		}
		else
		{
				$output = '
				
					<div class="row">
						<div class="col-12">
							<div class="product-topbar d-xl-flex align-items-end justify-content-between">
								<!-- Total Products -->
								<div class="total-products">
									<p> Il y a 0 produits.</p>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-12"><h5 class="text-center"> Aucun équipement trouvé </h5></div>
				</div>';
		}
	
	
	}elseif($type_filter == 'A'){
		
		$query = " SELECT DISTINCT pr.id, pr.link FROM produits pr,categories_blog ctg WHERE pr.etat = '1'  AND pr.type = 'A' AND pr.categorie IN (SELECT id FROM categories_blog WHERE etat='1')";
        if (isset($afficher_abonnements) && $afficher_abonnements == '0') {
            $query .= " AND 1=0 ";
        }
		if(isset($_POST["link"]) && $_POST["link"]!= '' )
		{
			$link_filter =  $_POST["link"];
			$query .= "
			 AND (pr.categorie IN (SELECT id FROM categories_blog WHERE idparent IN (SELECT id FROM categories_blog WHERE idparent = '".$link_filter."') OR idparent = '".$link_filter."' OR id = '".$link_filter."'))
			";
		}
		if(isset($_POST["brand"]) && !empty($_POST["brand"]))
		{
			$brand_filter = implode("','", $_POST["brand"]);
			$query .= "
			 AND pr.marque IN('".$brand_filter."')
			";
		}
		elseif(isset($_POST["marque"]) && $_POST["marque"]!= '' )
		{
			$marque_link = sanitize($_POST["marque"]);
			$marque_id = idraisonMarque($marque_link);
			if($marque_id) {
				$query .= " AND pr.marque = '".$marque_id."' ";
			}
		}
		if(isset($_POST["category"]))
		{
			$storage_filter = implode("','", $_POST["category"]);
			$query .= "
			 AND pr.categorie IN('".$storage_filter."')
			";
		}
		if(isset($_POST["search"]) && $_POST["search"]!= '' ){
        
            $search= url_rewrite($_POST["search"]);
                    		
            //$query .= " AND ( pr.titre LIKE '%$search%' OR pr.caracteristique LIKE '%$search%' OR pr.link LIKE '%$search%' OR ctg.titre LIKE '%$search%' OR ctg.link LIKE '%$search%') ";
            $query .= " AND ( pr.titre LIKE '%$search%' OR pr.link LIKE '%$search%' ) ";
                                
        }
            
        if(isset($_POST["categoryByTitre"]) && $_POST["categoryByTitre"]!= '' ){
            
                $ctg= $_POST["categoryByTitre"];
                        		
                //$query .= " AND ( ctg.titre LIKE '%$ctg%'  OR ctg.link LIKE '%$ctg%' OR  pr.idparent_categ = ctg.id OR pr.categorie = ctg.id AND ( pr.categorie IN ( SELECT id FROM categories_blog WHERE idparent = '".idBySearchCategBlog($ctg)."' ) ) ) ";
                $query .= " AND ( pr.categorie IN ( SELECT id FROM categories_blog WHERE idparent IN (SELECT id FROM categories_blog WHERE idparent = '".idBySearchCategBlog($ctg)."') OR idparent = '".idBySearchCategBlog($ctg)."' OR id = '".idBySearchCategBlog($ctg)."' ) ) ";
                                     
        }
		
		// Filtre En Stock + Tri dynamique (type A)
        $stock_filter = isset($_POST['stock_only']) && $_POST['stock_only'] == '1' ? " AND pr.etat_stock = '1' " : '';
        $query .= $stock_filter;
        $sort_val = isset($_POST['sort']) ? $_POST['sort'] : 'price_asc';
        $order_sql = 'pr.prix_vente ASC';
        if($sort_val == 'price_desc') $order_sql = 'pr.prix_vente DESC';
        elseif($sort_val == 'recent')  $order_sql = 'pr.id DESC';
        elseif($sort_val == 'name_asc') $order_sql = 'pr.titre ASC';
        elseif($sort_val == 'name_desc') $order_sql = 'pr.titre DESC';
		$query .=" GROUP BY pr.id ORDER BY ".$order_sql;

		// 1. Récupération du total pour la pagination
		$result_total = executeRequete($query);
		$total_row	= mysqli_num_rows($result_total);
		$total_pages = ceil($total_row / $limit);

		// 2. Ajout de la limite pour la page courante
		$query .= " LIMIT $start, $limit ";
		$result = executeRequete($query);
		
		if($total_row > 0)
		{
			$is_stock = isset($_POST['stock_only']) && $_POST['stock_only'] == '1';
			$output .= renderTopbar($total_row, $sort_val, $is_stock);
			$output .= '<div class="animated fadeInUp list" data-delay="1s"><div class="product-grid-container">'; 
			while($row = mysqli_fetch_array($result))
			{
				if($row['id'])    $id_p    = $row['id']; 
				if($row['link'])  $link_p  = $row['link']; 
				
                $sp_pv   = prixVenteProduits($id_p);
                $sp_pp   = prixPromoProduits($id_p);
                $sp_disc = 0;
                $pv_num = floatval(preg_replace('/[^0-9.]/', '', $sp_pv));
                $pp_num = floatval(preg_replace('/[^0-9.]/', '', $sp_pp));
                if($pp_num > 0 && $pv_num > 0 && $pp_num < $pv_num) {
                    $sp_disc = round((($pv_num - $pp_num) / $pv_num) * 100);
                }
                $sp_has_var = hasVariationPrices($id_p);
                $sp_from = $sp_has_var ? '<span style="font-size:0.7rem;color:#9ca3af;font-weight:400;">À partir de </span>' : '';
                $vname_js = "''";
                $vprice_js = "null";
                $var_badge = "";
				
				$output .= renderListCard($id_p, $link_p, $qty, $vprice_js, $vname_js, $sp_disc, $sp_from, $var_badge);
				$output .= renderGridCard($id_p, $link_p, $qty, $vprice_js, $vname_js, $sp_disc, $sp_from, $var_badge);
			}
			
			$output.= '</div></div>';
			
            // Pagination html
            $output .= renderPagination($total_pages, $page);


		}
		else
		{
				$output = '
				
					<div class="row">
						<div class="col-12">
							<div class="product-topbar d-xl-flex align-items-end justify-content-between">
								<!-- Total Products -->
								<div class="total-products">
									<p> Il y a 0 produits.</p>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-12"><h5 class="text-center"> Aucune abonnement trouvée </h5></div>
				    </div>';
		}		
	}
	
	
		echo $output;
}
?>
	<div class="alert alert-success alert-dismissible mt-2" role="alert" id="myAlert" style="position: fixed; top: 0; right: 10px;z-index: 999999;display:none;">
		<img src="dist/img/cart.png" class="rounded mr-2" alt="...">
        <strong class="mr-auto"> Panier</strong>
		<hr>
		<p class="mb-0"> Succès ! votre produit à été ajouté au panier. <a href="panier/" class="alert-link" style="font-size: 0.9rem;float: right;text-decoration: underline;">Voir votre panier</a></p>
		
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	
	<script type="text/javascript">
	
	$(document).ready(function(){
	    // Le code "Afficher plus" obsolète a été retiré.
	});

    $(document).on('click', '#list', function(event){
        event.preventDefault();
        $(this).css({'border-color':'var(--shop-primary,#5A31F4)','color':'var(--shop-primary,#5A31F4)'});
        $('#grid').css({'border-color':'var(--shop-border,#E0DEFF)','color':'#9ca3af'});
        $(this).addClass('active'); $('#grid').removeClass('active');
        $('.list-element.grid-group-item').removeClass('d-block').addClass('d-none');
        $('.list-element.list-group-item').removeClass('d-none').addClass('d-block');
    });
    $(document).on('click', '#grid', function(event){
        event.preventDefault();
        $(this).css({'border-color':'var(--shop-primary,#5A31F4)','color':'var(--shop-primary,#5A31F4)'});
        $('#list').css({'border-color':'var(--shop-border,#E0DEFF)','color':'#9ca3af'});
        $(this).addClass('active'); $('#list').removeClass('active');
        $('.list-element.list-group-item').removeClass('d-block').addClass('d-none');
        $('.list-element.grid-group-item').removeClass('d-none').addClass('d-block');
        // Reset the container class to grid
        $('.animated.fadeInUp.list > div.product-list-container, .animated.fadeInUp.list > div.row').removeClass('product-list-container row').addClass('product-grid-container');
    });
    $(document).on('click', '#list', function(event){
        event.preventDefault();
        $(this).css({'border-color':'var(--shop-primary,#5A31F4)','color':'var(--shop-primary,#5A31F4)'});
        $('#grid').css({'border-color':'var(--shop-border,#E0DEFF)','color':'#9ca3af'});
        $(this).addClass('active'); $('#grid').removeClass('active');
        $('.list-element.grid-group-item').removeClass('d-block').addClass('d-none');
        $('.list-element.list-group-item').removeClass('d-none').addClass('d-block');
        // Change container class to list
        $('.animated.fadeInUp.list > div.row, .animated.fadeInUp.list > div.product-grid-container').removeClass('product-grid-container row').addClass('product-list-container');
    });

	</script>

    
    <script type="text/javascript">
		$(document).ready(function(){
		  $('[data-toggle="tooltip"]').tooltip();
		  function afficheGrid(x) {
              if (x.matches) { // If media query matches
                $('#grid').addClass('active');
                $('#list').removeClass('active');
                $('.list-element.list-group-item').removeClass('d-block').addClass('d-none');
                $('.list-element.grid-group-item').removeClass('d-none').addClass('d-block');
              } else {
                // By default keep grid active on desktop as requested by user
                $('#grid').addClass('active');
                $('#list').removeClass('active');
                $('.list-element.list-group-item').removeClass('d-block').addClass('d-none');
                $('.list-element.grid-group-item').removeClass('d-none').addClass('d-block');
              }
            }
            
            var gr = window.matchMedia("(max-width: 992px)")
            afficheGrid(gr) // Call listener function at run time
            gr.addListener(afficheGrid) // Attach listener function on state changes
		});
	</script>

				

