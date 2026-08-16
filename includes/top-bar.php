<?php 
$req_topbar = executeRequete("SELECT b.titre, b.icone FROM bloc_accueil b JOIN liste_sections s ON b.type_section = s.id WHERE s.titre = 'Texte Topbar' AND b.etat = '1' ORDER BY b.ordre LIMIT 1");
if($req_topbar && $row_topbar = mysqli_fetch_assoc($req_topbar)):
    $txt_topbar = $row_topbar['titre'];
    $icone_topbar = $row_topbar['icone'];
?>
<!--------------- Top-bar ----------------------->
<div class="top-bar">
	<div class="container" style="max-width: 1400px;">
			<div class="d-flex align-items-center justify-content-between">
			
					<span class="text-left full-sc">
                        <?php 
                          if (!empty($icone_topbar)) {
                              echo '<i class="' . htmlspecialchars($icone_topbar) . '" style="margin-right: 8px;"></i>';
                          }
                          echo $txt_topbar; 
                        ?>
                    </span>
					<div class="text-right mx-xs-auto mx-sm-auto mr-md-0">
					    <div class="fav-icon"><a href="tel:<?php echo $gsm; ?>" class="phone mobile-call-btn"><i class="fa fa-phone-square"></i><span class="d-none d-md-inline"><?php echo $gsm; ?></span></a></div>
					</div>
			</div>
	</div>
</div>
<!--------------- Fin Top-bar ------------------->
<?php endif; ?>