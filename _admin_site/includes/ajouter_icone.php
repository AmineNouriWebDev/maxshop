<!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- Row -->
<?php 
if (isset($_POST['action']) && $_POST['action'] == 'ajout' )
{
	$titre  	 = formReception($_POST['titre']);
	
		$requete = 'INSERT INTO `icones` (`titre`) VALUES ("'. $titre .'")'; 

		$connexion=ouvrirCnx() or die("erreur cnx");
		$result  = mysqli_query($connexion, $requete);	
		$ids     = mysqli_insert_id($connexion);
			
		if (isset($_FILES['photo']) && $_FILES['photo']['type'] != '') {
			if ($_FILES['photo']['type']=="image/jpeg" || $_FILES['photo']['type']=="image/png" || $_FILES['photo']['type']=="image/gif" || $_FILES['photo']['type']=="image/webp" ) {
				$base_name = $ids."-icones-" . pathinfo($_FILES['photo']['name'], PATHINFO_FILENAME);
				$base_name = str_replace([' ', 'é', 'è', 'à', 'ù', 'ç'], ['-', 'e', 'e', 'a', 'u', 'c'], $base_name);
				$dest_base = "../media/icones/" . $base_name;
				
				$webp_filename = convertAndSaveWebP($_FILES['photo']['tmp_name'], $dest_base);
				if ($webp_filename) {
					$photo = $webp_filename;
				} else {
					$destination = $base_name . "." . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
					copy($_FILES['photo']['tmp_name'], "../media/icones/" . $destination);
					$photo = $destination;
				}
				$requete = 'UPDATE `icones` set `photo`="'. $photo .'" WHERE `id`="'.$ids.'"';
				$result = executeRequete($requete);	
			}
		}
	?>
	<script language="javascript">
	<!--
		window.location = 'index.php?r=icones';
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
                                <h4 class="card-title">Ajouter icone</h4>
                                <form method="POST" enctype="multipart/form-data" novalidate="novalidate">
                                    <div class="form-group">
                                        <h5>Titre <span class="text-danger">*</span></h5>
                                        <div class="controls">
                                            <input type="text" name="titre" value="" class="form-control" required data-validation-required-message="Ce champ est obligatoire"> </div>
                                    </div>
                                    <div class="row">
                                     <div class="col-md-6">
                                      <div class="form-group">
                                        <h5>Image</h5>
                                        <div class="controls">
                                            <input type="file" name="photo" class="form-control"> 
                                        </div>
                                    </div>
                                     </div>
                                    </div>                               
                                    <div class="text-xs-right">
                                        <button type="submit" class="btn btn-info">Enregistrer</button>
                                        <button type="reset" class="btn btn-inverse" onclick="location.href='index.php?r=icones'">Annuler</button>
                                        <input name="action" type="hidden" id="action" value="ajout">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>