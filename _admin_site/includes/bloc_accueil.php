<!-- ============================================================== -->
                <!-- Start Page Content -->
             <!-- ============================================================== -->
                <!-- Row -->
    <?php	
    if (isset($_GET['action']) && $_GET['action'] == 'supp' ) {
		supprimerBloc($_GET['id']);
        ?>
        <script language="javascript">
            window.location = 'index.php?r=bloc_accueil';
        </script>
        <?php 
        exit;
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'toggle' ) {
        $id_toggle = intval($_GET['id']);
        $r_toggle = executeRequete("SELECT etat FROM bloc_accueil WHERE id='$id_toggle'");
        if ($row_toggle = mysqli_fetch_assoc($r_toggle)) {
            $new_etat = $row_toggle['etat'] == '1' ? '0' : '1';
            executeRequete("UPDATE bloc_accueil SET etat='$new_etat' WHERE id='$id_toggle'");
        }
        ?>
        <script language="javascript">
            window.location = 'index.php?r=bloc_accueil';
        </script>
        <?php 
        exit;
    }
    ?>
                <div class="row">
				    <div class="col-12">
                        <div class="admin-card">
                            <div class="admin-card-body">
                                
                                <div class="row">
                                    <div class="col-4 mb-2">
                                        <h4 class="card-title">Bloc accueil</h4>
                                    </div>
                                    <div class="col-8 text-right mb-2">
                                        <a href="index.php?r=nbloc_accueil" class="admin-btn admin-btn-primary">Ajouter bloc accueil</a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>Intitulé</th>
                                                <th>Type bloc</th>
                                                <th>Créée par</th>
                                                <th class="text-nowrap">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
								          $requete = 'SELECT * FROM `bloc_accueil` ORDER BY `ordre` ASC ';
                                          $resultat = executeRequete($requete);
	                                      $num = mysqli_num_rows($resultat);
		                                  if ($num > 0 ) { 
			                               while ($data = mysqli_fetch_array($resultat))  {
								         ?>
                                              <tr data-id="<?php echo afficheChamp($data['id']); ?>" style="cursor: grab;">
                                                 <td><i class="fa fa-arrows-v text-muted m-r-10 drag-handle" style="cursor: grab;" aria-hidden="true"></i> <?php echo afficheChamp($data['titre']); ?></td>
                                                 <td><?php echo titreListeSection($data['type_section']); ?></td>
                                                 <td><?php echo auteur_name($data['auteur']); ?></td>
                                                 <td class="text-nowrap">
                                                     <?php if($data['type_section'] == '99'): ?>
                                                         <!-- Carousel principal : engrenage + toggle -->
                                                         <a href="index.php?r=carousel_config" data-toggle="tooltip" data-original-title="Configurer" style="margin-right: 8px;">
                                                             <i class="fa fa-cog text-inverse"></i>
                                                         </a>
                                                         <?php if($data['etat'] == '1'): ?>
                                                             <a href="index.php?r=bloc_accueil&id=<?php echo afficheChamp($data['id']); ?>&action=toggle" style="color:#2ec4b6;font-weight:600;font-size:.8125rem;border:1px solid #2ec4b6;padding:2px 6px;border-radius:4px;text-decoration:none;">Actif</a>
                                                         <?php else: ?>
                                                             <a href="index.php?r=bloc_accueil&id=<?php echo afficheChamp($data['id']); ?>&action=toggle" style="color:#ef5350;font-weight:600;font-size:.8125rem;border:1px solid #ef5350;padding:2px 6px;border-radius:4px;text-decoration:none;">Inactif</a>
                                                         <?php endif; ?>

                                                     <?php elseif($data['type_section'] == '9'): ?>
                                                         <!-- Trust/Réassurance : engrenage + toggle -->
                                                         <a href="index.php?r=addSectionContent&id=<?php echo afficheChamp($data['id']); ?>" data-toggle="tooltip" data-original-title="Configurer les éléments" style="margin-right: 8px;">
                                                             <i class="fa fa-cog text-inverse"></i>
                                                         </a>
                                                         <?php if($data['etat'] == '1'): ?>
                                                             <a href="index.php?r=bloc_accueil&id=<?php echo afficheChamp($data['id']); ?>&action=toggle" style="color:#2ec4b6;font-weight:600;font-size:.8125rem;border:1px solid #2ec4b6;padding:2px 6px;border-radius:4px;text-decoration:none;">Actif</a>
                                                         <?php else: ?>
                                                             <a href="index.php?r=bloc_accueil&id=<?php echo afficheChamp($data['id']); ?>&action=toggle" style="color:#ef5350;font-weight:600;font-size:.8125rem;border:1px solid #ef5350;padding:2px 6px;border-radius:4px;text-decoration:none;">Inactif</a>
                                                         <?php endif; ?>

                                                     <?php elseif($data['type_section'] == '8'): ?>
                                                         <!-- Ticker : crayon + toggle -->
                                                         <a href="index.php?r=addSectionContent&id=<?php echo afficheChamp($data['id']); ?>" data-toggle="tooltip" data-original-title="Modifier" style="margin-right: 8px;"> <i class="fa fa-pencil text-inverse"></i> </a>
                                                         <?php if($data['etat'] == '1'): ?>
                                                             <a href="index.php?r=bloc_accueil&id=<?php echo afficheChamp($data['id']); ?>&action=toggle" style="color:#2ec4b6;font-weight:600;font-size:.8125rem;border:1px solid #2ec4b6;padding:2px 6px;border-radius:4px;text-decoration:none;">Actif</a>
                                                         <?php else: ?>
                                                             <a href="index.php?r=bloc_accueil&id=<?php echo afficheChamp($data['id']); ?>&action=toggle" style="color:#ef5350;font-weight:600;font-size:.8125rem;border:1px solid #ef5350;padding:2px 6px;border-radius:4px;text-decoration:none;">Inactif</a>
                                                         <?php endif; ?>

                                                     <?php elseif($data['type_section'] == '7'): ?>
                                                         <!-- Topbar : crayon + toggle -->
                                                         <a href="index.php?r=mbloc_accueil&id=<?php echo afficheChamp($data['id']); ?>" data-toggle="tooltip" data-original-title="Modifier" style="margin-right: 8px;"> <i class="fa fa-pencil text-inverse"></i> </a>
                                                         <?php if($data['etat'] == '1'): ?>
                                                             <a href="index.php?r=bloc_accueil&id=<?php echo afficheChamp($data['id']); ?>&action=toggle" style="color:#2ec4b6;font-weight:600;font-size:.8125rem;border:1px solid #2ec4b6;padding:2px 6px;border-radius:4px;text-decoration:none;">Actif</a>
                                                         <?php else: ?>
                                                             <a href="index.php?r=bloc_accueil&id=<?php echo afficheChamp($data['id']); ?>&action=toggle" style="color:#ef5350;font-weight:600;font-size:.8125rem;border:1px solid #ef5350;padding:2px 6px;border-radius:4px;text-decoration:none;">Inactif</a>
                                                         <?php endif; ?>

                                                     <?php else: ?>
                                                         <!-- Blocs standards : crayon + liste + supprimer -->
                                                         <a href="index.php?r=mbloc_accueil&id=<?php echo afficheChamp($data['id']); ?>" data-toggle="tooltip" data-original-title="Modifier" style="margin-right: 8px;"> <i class="fa fa-pencil text-inverse"></i> </a>
                                                         <?php if(typeSectionBloc($data['id']) == '4'): ?>
                                                             <a href="index.php?r=addproduits&id=<?php echo afficheChamp($data['id']); ?>" data-toggle="tooltip" data-original-title="Ajouter produits" style="margin-right: 8px;"> <i class="fa fa-list text-inverse"></i> </a>
                                                         <?php else: ?>
                                                             <a href="index.php?r=addSectionContent&id=<?php echo afficheChamp($data['id']); ?>" data-toggle="tooltip" data-original-title="Ajouter section content" style="margin-right: 8px;"> <i class="fa fa-list text-inverse"></i> </a>
                                                         <?php endif; ?>
                                                         <a href="javascript:void(0);" data-toggle="tooltip" data-original-title="Supprimer" onclick="confirmGlobalDelete('index.php?r=bloc_accueil&id=<?php echo afficheChamp($data['id']); ?>&action=supp')"> <i class="fa fa-close text-danger"></i></a>
                                                     <?php endif; ?>
                                                 </td>
                                             </tr>
                                         <?php } ?>
                                        <?php } ?>
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.querySelector('.admin-table tbody');
    if (el) {
        var sortable = Sortable.create(el, {
            animation: 150,
            handle: '.drag-handle',
            onEnd: function (evt) {
                var rows = el.querySelectorAll('tr[data-id]');
                var ids = [];
                rows.forEach(function(row) {
                    ids.push(row.getAttribute('data-id'));
                });
                
                $.ajax({
                    url: 'ajax_order_bloc_accueil.php',
                    method: 'POST',
                    data: { ids: ids },
                    success: function(response) {
                        try {
                            var res = JSON.parse(response);
                            if(res.status === 'success') {
                                if(typeof showToast === 'function') {
                                    showToast('Ordre mis à jour avec succès', 'success');
                                }
                            } else {
                                if(typeof showToast === 'function') {
                                    showToast('Erreur: ' + res.message, 'error');
                                }
                            }
                        } catch(e) {}
                    },
                    error: function() {
                        if(typeof showToast === 'function') {
                            showToast('Erreur serveur lors de la mise à jour', 'error');
                        }
                    }
                });
            }
        });
    }
});
</script>
