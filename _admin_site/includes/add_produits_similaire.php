<!-- ============================================================== -->
                <!-- Start Page Content -->
<!-- ============================================================== -->
 <?php	if (isset($_GET['action']) && $_GET['action'] == 'supp' ) {
		$idps   = $_GET['idps'];
		$idp   = $_GET['idp'];
		$start   = $_GET['start'];
	    executeRequete("DELETE FROM `produits_similaire` WHERE `id` = '". $idps ."'");
		  ?>
	<script language="javascript">
	<!--
		window.location = 'index.php?r=addproduitssimilaire&id=<?php echo $idp; ?>&start=<?php echo $start; ?>';
	-->
	</script>
	<?php } ?>
<?php 
if (isset($_POST['action']) && $_POST['action'] == 'ajt' ){
	$id                = formReception($_POST['id']);
	$idcateg           = formReception($_POST['idcateg']);
	$id_prod_similaire = isset($_POST['id_prod_similaire']) ? formReception($_POST['id_prod_similaire']) : 0;
	
		$requete = 'INSERT INTO `produits_similaire`(`id_produit`, `id_categ`, `id_prod_similaire`) VALUES ("'. $id .'","'. $idcateg .'", "'. $id_prod_similaire .'")';
		$result = executeRequete($requete);	
	?>
	<script language="javascript">
	<!--
		window.location = 'index.php?r=addproduitssimilaire&id=<?php echo $id; ?>&start=<?php echo $_GET['start']; ?>';
	-->
	</script>
	<?php
	//echo $strSQL
}
?>
                <div class="row">
				<div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Produits similaire : " <?php echo titreProduits($_GET['id']); ?> "</h4>
                                <div class="table-responsive">
                                    <table class="table color-table info-table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Type / Titre</th>
                                                <th class="text-nowrap">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
								          $requete = 'SELECT * FROM `produits_similaire` WHERE `id_produit` ="'.$_GET['id'].'"';
                                          $resultat = executeRequete($requete);
	                                      $num = mysqli_num_rows($resultat);
		                                  if ($num > 0 ) { 
			                               while ($data = mysqli_fetch_array($resultat))  {
                                               if ($data['id_prod_similaire'] > 0) {
                                                   $titre_aff = "Produit exact : <strong>" . titreProduits($data['id_prod_similaire']) . "</strong>";
                                               } else {
                                                   $titre_aff = "Catégorie : " . titreCategBlog($data['id_categ']);
                                               }
								             ?>
                                            <tr>
                                                <td><?php echo $titre_aff; ?></td>
                                                <td class="text-nowrap">
                                                    <a href="index.php?r=addproduitssimilaire&idps=<?php echo $data['id']; ?>&idp=<?php echo $_GET['id']; ?>&action=supp&start=<?php echo $_GET['start']; ?>" data-toggle="tooltip" data-original-title="Supprimer"> <i class="fa fa-close text-danger"></i></a>
                                                </td>
                                            </tr>
                                         <?php } ?>
                                        <?php } else { ?>
                                        <tr>
                                          <td colspan="3">Aucun produit trouvé</td>
                                        </tr>
                                        <?php } ?>   
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Ajouter produits similaire</h4>
                                <form method="POST" enctype="multipart/form-data" novalidate="novalidate">
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <h5 style="color:var(--shop-primary); font-weight:600;"><i class="fa fa-search"></i> Produit Spécifique (Recherche Live)</h5>
                                                <div class="controls position-relative">
                                                    <input type="text" id="live_search_produit" class="form-control" autocomplete="off" placeholder="Tapez le nom ou la référence du produit... (Laissez vide pour sélectionner une catégorie)">
                                                    <input type="hidden" name="id_prod_similaire" id="idproduit_hidden" value="0">
                                                    <div id="live_search_results" style="position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #ccc; max-height:250px; overflow-y:auto; z-index:100; display:none; border-radius:4px; box-shadow:0 4px 12px rgba(0,0,0,0.15);"></div>
                                                </div>
                                                <small class="text-muted mt-1 d-block">Si vous sélectionnez un produit, la catégorie ci-dessous sera ignorée.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
									<div class="row mt-4">
										<div class="col-md-6">
											<div class="form-group">
											<h5>Ou par sous-catégorie complète</h5>
											<div class="controls">
												<select name="idcateg" id="select2" class="form-control">
												
													
													<option value="0">-- Selectionner --</option>
												
												<?php
            	                                 $req = 'SELECT * FROM `categories_blog` WHERE `idparent` = "0" AND `type` = "E" ORDER BY `ordre` ASC';
            	                                 $res = executeRequete($req);
            	                                  while ($data = mysqli_fetch_array($res)) { ?>
        	                                    <option value="<?php echo $data['id']; ?>"><?php echo afficheChamp1($data['titre']); ?></option>
                                                 <?php
        	                                      $req1 = 'SELECT * FROM `categories_blog` WHERE `idparent` = "'.$data['id'].'" AND `type` = "E" ORDER BY `ordre` ASC';
        	                                      $res1 = executeRequete($req1);
        	                                       while ($data1 = mysqli_fetch_array($res1)) { ?>
        	                                      <option value="<?php echo $data1['id']; ?>">--> <?php echo afficheChamp1($data1['titre']); ?></option>
                                                  <?php
        	                                      $req2 = 'SELECT * FROM `categories_blog` WHERE `idparent` = "'.$data1['id'].'" AND `type` = "E" ORDER BY `ordre` ASC';
        	                                      $res2 = executeRequete($req2);
        	                                       while ($data2 = mysqli_fetch_array($res2)) { ?>
        	                                      <option value="<?php echo $data2['id']; ?>">----> <?php echo afficheChamp1($data2['titre']); ?></option>
        	                                      <?php 
        	                                       } 
        	                                       } 
        	                                     } 
        	                                     ?>
												</select>
											</div>
											</div>
										</div>
									</div>
                                    
                                    <div class="text-xs-right">
                                       <button type="submit" class="btn btn-info">Enregistrer</button>
                                       <input name="action" type="hidden" id="action" value="ajt">
                                       <button type="reset" class="btn btn-inverse" onclick="location.href='index.php?r=produits&start=<?php echo isset($_GET['start']) ? $_GET['start'] : ''; ?>'">Annuler</button>
                                        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('live_search_produit');
    const hiddenInput = document.getElementById('idproduit_hidden');
    const resultsContainer = document.getElementById('live_search_results');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        hiddenInput.value = "0"; // Reset hidden input if user types

        if(query.length < 2) {
            resultsContainer.style.display = 'none';
            return;
        }

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            // Re-using the same search endpoint used for add_produits
            fetch('ajax_search_products.php?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                if(data.length === 0) {
                    resultsContainer.innerHTML = '<div style="padding:15px; color:#999; text-align:center;">Aucun produit trouvé.</div>';
                } else {
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.style.display = 'flex';
                        div.style.alignItems = 'center';
                        div.style.padding = '10px 15px';
                        div.style.cursor = 'pointer';
                        div.style.borderBottom = '1px solid #f0f0f0';
                        div.style.transition = 'background 0.2s';
                        
                        const img = item.photo ? `<img src="../${item.photo}" style="width:40px; height:40px; object-fit:contain; margin-right:12px; border-radius:4px; background:#fff; border:1px solid #eee;">` : `<div style="width:40px; height:40px; margin-right:12px; background:#f5f5f5; border-radius:4px;"></div>`;
                        
                        div.innerHTML = `
                            ${img}
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#333;">${item.titre}</div>
                                <div style="font-size:12px; color:var(--info); font-weight:bold;">${item.prix}</div>
                            </div>
                            <div style="color:#bbb; font-size:12px;"><i class="fa fa-plus-circle"></i></div>
                        `;
                        
                        div.addEventListener('mouseover', () => div.style.backgroundColor = '#f8f9fa');
                        div.addEventListener('mouseout', () => div.style.backgroundColor = 'transparent');
                        
                        div.addEventListener('click', () => {
                            searchInput.value = item.titre;
                            hiddenInput.value = item.id;
                            resultsContainer.style.display = 'none';
                            
                            // Optional: auto-submit to add right away
                            searchInput.closest('form').submit();
                        });
                        resultsContainer.appendChild(div);
                    });
                }
                resultsContainer.style.display = 'block';
            })
            .catch(err => {
                console.error(err);
                resultsContainer.innerHTML = '<div style="padding:10px; color:red;">Erreur de chargement.</div>';
            });
        }, 300);
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if(!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.style.display = 'none';
        }
    });
});
</script>