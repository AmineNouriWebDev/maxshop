<?php

	session_start();


	include("include.php");


        $Request = 'SELECT * FROM `site_menu` WHERE `id` = "20" AND `etat` = "1" ';
		
    	$Result  = executeRequete($Request) ;
		
    	$Datum = mysqli_fetch_array($Result);
            
        if(isset($_GET['marque']) && isset($_GET['categorie'])) { $title_page = str_replace("%%CATEGORIE%%",titreCategories($_GET['categorie']),$title_marque);  $title_page = str_replace("%%MARQUE%%",$_GET['marque'],$title_page); }elseif($Datum['titre_page'] != '') $title_page=afficheChamp($Datum['titre_page']); 
            
        if(isset($_GET['marque']) && isset($_GET['categorie'])) { $keywords_page = str_replace("%%CATEGORIE%%",titreCategories($_GET['categorie']),$keywords_marque); $keywords_page = str_replace("%%MARQUE%%",$_GET['marque'],$keywords_page);  }elseif($Datum['keywords'] != '') $keywords_page=afficheChamp($Datum['keywords']);  
            
        if(isset($_GET['marque']) && isset($_GET['categorie'])) { $description_page = str_replace("%%CATEGORIE%%",titreCategories($_GET['categorie']),$description_marque); $description_page = str_replace("%%MARQUE%%",$_GET['marque'],$description_page);  }elseif($Datum['description'] != '') $description_page=afficheChamp($Datum['description']); 
		
        $contenu = afficheChamp($Datum['contenu']);
		
    	$titre   = afficheChamp($Datum['titre']);
		
    	$id = $Datum['id'];
    	
        $img=afficheChamp($Datum['image']);
        
        $img_entete = photoPageSite($id);
    	
    	
	$variable2='<li class="breadcrumb-item text-secondary" aria-current="page">'.$titre.'</li>';
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <?php include('includes/script-header.php'); ?>
  <?php include('includes/script_panier.php'); ?>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--shop-bg-base); color: var(--shop-text-primary); }
    /* jQuery UI slider: brand purple */
    #price_range.ui-slider { height:5px !important; background:var(--shop-border,#E0DEFF) !important; border:none !important; border-radius:3px !important; width: 85% !important; margin: 1.5rem auto 1rem auto !important; }
    #price_range .ui-slider-range { background:var(--shop-primary,#5A31F4) !important; }
    #price_range .ui-slider-handle { width:16px !important;height:16px !important;top:-6px !important;border-radius:50% !important;background:var(--shop-primary,#5A31F4) !important;border:2px solid #fff !important;box-shadow:0 2px 6px rgba(90,49,244,.4) !important;cursor:pointer !important; }
    #price_range .ui-slider-handle:focus { outline:none !important; box-shadow:0 0 0 3px rgba(90,49,244,.25) !important; }
    
    /* Prevent sidebar horizontal overflow */
    .shop_sidebar_area { overflow-x: hidden; }
    /* Prevent product area from shrinking when few products are loaded */
    .amado_product_area { flex: 1; min-width: 0; }
    
    /* Sidebar Collapsible Styles */
    #shopMainWrapper { position: relative; display: flex; transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); overflow: hidden; align-items: flex-start; }
    #shopSidebar { width: 320px; flex-shrink: 0; margin-left: -330px; transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); opacity: 0; position: sticky; top: 1rem; max-height: calc(100vh - 2rem); overflow-y: auto; }
    .sidebar-is-open #shopSidebar { margin-left: 0; opacity: 1; }
    #shopProductArea { flex-grow: 1; transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); min-width: 0; width: 100%; }

    /* Filter Toggle Button */
    .filter-toggle-btn {
        position: fixed;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        background: var(--shop-primary, #5a31f4);
        color: white;
        border: none;
        border-radius: 0 8px 8px 0;
        padding: 12px 16px;
        font-size: 1.2rem;
        cursor: pointer;
        z-index: 1050;
        box-shadow: 4px 0 15px rgba(90,49,244,0.4);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-toggle-btn:hover { background: #4a21e4; padding-left: 20px; }
    .filter-toggle-btn i.fa-filter { animation: pulse-icon 2s infinite; }
    @keyframes pulse-icon {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    .sidebar-is-open .filter-toggle-btn {
        left: 320px;
        border-radius: 8px 0 0 8px;
        box-shadow: -4px 0 15px rgba(0,0,0,0.1);
    }
    
    /* Product Grid layout */
    .product-grid-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: center;
    }
    .product-grid-container > * {
        width: calc((100% - 1rem) / 2); /* Mobile: 2 per row */
    }
    @media (min-width: 640px) {
        .product-grid-container > * { width: calc((100% - 2rem) / 3); }
    }
    @media (min-width: 1024px) {
        /* Default closed sidebar = 5 per row */
        .product-grid-container > * { width: calc((100% - 4rem) / 5); }
        /* Open sidebar = 4 per row */
        .sidebar-is-open .product-grid-container > * { width: calc((100% - 3rem) / 4); }
    }
    .product-list-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    /* Pagination dynamique */
    .pagination .page-item.active .page-link {
        background-color: var(--shop-primary, #5a31f4) !important;
        border-color: var(--shop-primary, #5a31f4) !important;
        color: white !important;
    }
    .pagination .page-link {
        color: var(--shop-text-primary, #111827);
        transition: all 0.2s ease;
    }
    .pagination .page-link:hover {
        background-color: var(--shop-primary, #5a31f4) !important;
        border-color: var(--shop-primary, #5a31f4) !important;
        color: white !important;
    }
    /* Sidebar Scrollbar */
    #shopSidebar::-webkit-scrollbar { width: 6px; }
    #shopSidebar::-webkit-scrollbar-track { background: var(--shop-bg-base, #f8fafc); border-radius: 4px; }
    #shopSidebar::-webkit-scrollbar-thumb { background: var(--shop-primary, #5a31f4); border-radius: 4px; }
    #shopSidebar::-webkit-scrollbar-thumb:hover { background: var(--shop-primary, #5a31f4); opacity: 0.8; }
  </style>
</head>
<body>
  <?php include('includes/feedback.php'); ?>
  <?php include('includes/header-tw.php'); ?>
	
    <button id="sidebarToggleBtn" class="filter-toggle-btn" title="Filtrer les produits">
        <i class="fa fa-filter"></i>
    </button>

	<?php include('includes/recherche.php');?>


  <?php include('includes/footer-tw.php'); ?>
  <?php include('includes/script-footer.php'); ?>
	 
	 
	 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.js"></script>
	 
	 
	<?php
    $categ_cond = (isset($_GET['categorie']) && $_GET['categorie'] != '') ? ' (pv.categorie="'.idCategBlog($_GET['categorie']).'" || pv.idparent_categ="'.idCategBlog($_GET['categorie']).'") ' : ' 1 ';
    $p_categ_cond = (isset($_GET['categorie']) && $_GET['categorie'] != '') ? ' (p.categorie="'.idCategBlog($_GET['categorie']).'" || p.idparent_categ="'.idCategBlog($_GET['categorie']).'") ' : ' 1 ';

    $type_cond = (isset($afficher_abonnements) && $afficher_abonnements == '0') ? " AND pv.type != 'A' " : " ";
    $p_type_cond = (isset($afficher_abonnements) && $afficher_abonnements == '0') ? " AND p.type != 'A' " : " ";

    $reqprice = "SELECT MIN(val) as min, MAX(val) as max FROM (
        SELECT prix_vente as val FROM produits pv WHERE $categ_cond $type_cond AND prix_vente > 0
        UNION ALL
        SELECT prix_promo as val FROM produits pv WHERE $categ_cond $type_cond AND prix_promo > 0
        UNION ALL
        SELECT v.prix_vente as val FROM produit_variations v JOIN produits p ON v.idproduit = p.id WHERE $p_categ_cond $p_type_cond AND v.prix_vente > 0
        UNION ALL
        SELECT v.prix_promo as val FROM produit_variations v JOIN produits p ON v.idproduit = p.id WHERE $p_categ_cond $p_type_cond AND v.prix_promo > 0
    ) as all_prices";
    
    $resprice = executeRequete($reqprice);
    $dataprice = mysqli_fetch_array($resprice);
    // Safety defaults
    if (!$dataprice['min']) $dataprice['min'] = 0;
    if (!$dataprice['max']) $dataprice['max'] = 1000;
    ?>	
	
 	<script type="text/javascript">
 	
    $(document).ready(function(){
        
        var currentPage = 1;

		filter_data(1);

		function filter_data(page)
		{
            if(typeof page === 'undefined') page = 1;
            currentPage = page;

			var action = 'fetch_data';
            var minimum_price = $('#hidden_minimum_price').val();
            var maximum_price = $('#hidden_maximum_price').val();
			var search    = "<?php if (isset($_POST['recherche']) && $_POST['recherche'] != ''){ echo addslashes($_POST['recherche']); } elseif (isset($_GET['recherche']) && $_GET['recherche'] != '') { echo addslashes($_GET['recherche']); } elseif (isset($_GET['search']) && $_GET['search'] != '') { echo addslashes($_GET['search']); } else { echo ''; } ?>";
			var brand = get_filter('brand') ;
            var promo         = "<?php if(isset($_GET['promo'])) echo 'promo';else echo ''; ?>";
			var marque = "<?php if((isset($_GET['marque']) && $_GET['marque'] != '')){ echo $_GET['marque']; }else{ echo  ''; }  ?>";
			var typeEl = document.getElementById('typeProd');
			var linkEl = document.getElementById('linkProd');
			var type = typeEl ? typeEl.value : '';
			var link = linkEl ? linkEl.value : '';
			var category = get_filter('category');
			var caracteristique = get_filter('caracteristique');
			var categoryByTitre = '<?php if ((isset($_GET['categorie']) && $_GET['categorie'] != '')){ echo $_GET['categorie']; }elseif ((isset($_POST['categorie']) && $_POST['categorie'] != '')){ echo linkCategBlog($_POST['categorie']); }else{ echo ''; } ?>';
			var sort = $('#sort_order').length ? $('#sort_order').val() : 'price_asc';
			
            $('.filter_data').html('<div class="row"> <div class="col-12"><div id="loading"></div></div></div>');

			$.ajax({
				url:"<?php echo CHEMIN; ?>includes/fetch_data.php",
				method:"POST",
				data:{
                    action:action,
                    brand:brand, 
                    category:category,
                    caracteristique:caracteristique, 
                    type:type,
                    link:link,
                    search:search, 
                    minimum_price:minimum_price, 
                    maximum_price:maximum_price,
                    categoryByTitre:categoryByTitre,
                    marque:marque,
                    promo:promo,
                    sort:sort,
                    page:currentPage
                },
				
				success:function(data){
					$('.filter_data').html(data);
                    if(window.compareSyncButtons) window.compareSyncButtons();
                    if(window.updateFlashCountdowns) window.updateFlashCountdowns();
				}
			});
		}

        /* Exposed globally so pagination buttons in AJAX response can call this */
        window.filter_data_page = function(page){ filter_data(page); };

		function get_filter(class_name)
		{
			var filter = [];
			$('.'+class_name+':checked').each(function(){
				filter.push($(this).val());
			});
			/* Support for mobile select dropdowns */
			$('select.'+class_name).each(function(){
				if($(this).val() && $(this).val() !== '') {
					filter.push($(this).val());
				}
			});
			return filter;
		}

		$(document).on('change', '.common_selector', function(){
			filter_data(1);
		});

		$(document).on('change', '.sort_selector', function(){
            filter_data(1);
        });

        /* Pagination delegate (matching categorie.php logic) */
        document.addEventListener('click', function(e){
            var t = e.target.closest('.pag-btn');
            if(t){
                var page = parseInt(t.getAttribute('data-page'));
                if(typeof window.filter_data_page === 'function') window.filter_data_page(page);
            }
        });

        $('#price_range').slider({
            range:true,
            min:<?php echo $dataprice['min'] ?? 0; ?>,
            max:<?php echo $dataprice['max'] ?? 1000; ?>,
            values:[<?php echo $dataprice['min'] ?? 0; ?>, <?php echo $dataprice['max'] ?? 1000; ?>],
            step:0.001,
            format:'DT',
            stop:function(event, ui)
            {
                $('#price_show').html(ui.values[0] + ' DT - ' + ui.values[1] +' DT');
                $('#hidden_minimum_price').val(ui.values[0]);
                $('#hidden_maximum_price').val(ui.values[1]);
                filter_data();
            }
        });
		$('.slect2').select2();

    });
    // JS pour basculer la sidebar
    $(document).ready(function() {
        $('#sidebarToggleBtn').on('click', function() {
            var wrapper = $('#shopMainWrapper');
            wrapper.toggleClass('sidebar-is-open');
            // Change icon
            var icon = $(this).find('i');
            if (wrapper.hasClass('sidebar-is-open')) {
                icon.removeClass('fa-filter').addClass('fa-times');
                icon.css('animation', 'none');
            } else {
                icon.removeClass('fa-times').addClass('fa-filter');
                icon.css('animation', 'pulse-icon 2s infinite');
            }
        });
    });
    
    </script>
	
</body>

</html>