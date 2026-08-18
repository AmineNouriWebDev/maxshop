<?php
include("include.php");

$succes = "";
$erreur = "";

if (isset($_GET['key']) && !empty($_GET['key'])) {
    $key = sanitize($_GET['key']);
    
    // Check if key exists
    $req = "SELECT * FROM `clients` WHERE `confirm_key` = '" . $key . "'";
    $res = executeRequete($req);
    
    if (mysqli_num_rows($res) > 0) {
        $client = mysqli_fetch_array($res);
        
        if ($client['etat'] == 1) {
            $erreur = "Ce compte est déjà activé. Vous pouvez vous connecter.";
        } else {
            // Activate account
            $update = "UPDATE `clients` SET `etat` = '1' WHERE `id` = '" . $client['id'] . "'";
            executeRequete($update);
            
            // Envoyer la notification n8n de bienvenue et le telegram (puisque le compte est maintenant actif)
            $clientmail = $client['prenom'] . " " . $client['nom'];
            $sujetmail = sujetEmail(4);
            $messagemail = str_replace("%%NOMCLT%%", $clientmail, messageEmail(4));
            
            $n8n_payload = array(
                'event' => 'new_registration',
                'customer_name' => $clientmail,
                'customer_email' => $client['email'],
                'customer_phone' => $client['tel'],
                'customer_whatsapp' => $client['whatsapp'],
                'email_subject' => $sujetmail,
                'email_html' => $messagemail,
                'verification_required' => false
            );
            envoiEmail_n8n($n8n_payload);
            
            $sess_id = md5(microtime());
            $strSQL1 = "UPDATE `clients` SET sess_id='".$sess_id."' WHERE id='".$client['id']."'";
            executeRequete($strSQL1);

            $_SESSION['client_id'] = $client['id']; 
            $_SESSION['client_login'] = $client['email'];
            $_SESSION['client_nom'] = $client['nom'];
            $_SESSION['sess_id'] = $sess_id;
            
            $_SESSION['toast_message'] = [
                'text' => '✅ Félicitations, votre e-mail a été vérifié. Vous êtes maintenant connecté !',
                'color' => 'success'
            ];
            header('Location: ' . lienCompte());
            exit;
        }
    } else {
        $erreur = "Le lien de vérification est invalide ou a expiré.";
    }
} else {
    $erreur = "Aucune clé de vérification n'a été fournie.";
}
// If there is an error, we show the error page
?>
<!DOCTYPE html>
<html lang="fr" class="">
<head>
	<?php include('includes/script-header.php');?>
    <style>
      *, *::before, *::after{box-sizing:border-box;} 
      body{margin:0;font-family:'Inter',system-ui,sans-serif;background:var(--shop-bg-base);color:var(--shop-text-primary);min-height:100vh;display:flex;flex-direction:column;}
      
      .cx-wrap { flex:1; padding: 4rem 1rem; width: 100%; max-width: 800px; margin: 0 auto; display: flex; align-items: center; justify-content: center;}
      .cx-surface { background: var(--shop-surface); padding: 3rem; border-radius: 1.5rem; text-align: center; width: 100%; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
      .cx-border { border: 1px solid var(--shop-border, #E0DEFF); }
      html.dark .cx-border { border-color: var(--shop-border, #323248); }
      html.dark .cx-surface { box-shadow: 0 10px 40px rgba(0,0,0,0.3); }

      .icon-container { width: 80px; height: 80px; margin: 0 auto 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
      .icon-success { background: rgba(16, 185, 129, 0.1); color: #10B981; }
      .icon-error { background: rgba(239, 68, 68, 0.1); color: #EF4444; }

      .cx-btn {
        display: inline-flex; justify-content: center; align-items: center; gap: 0.5rem;
        padding: 0.875rem 2rem;
        background: var(--shop-primary); color: white;
        border: none; border-radius: 0.875rem;
        font-weight: 600; font-size: 1rem; cursor: pointer; text-decoration: none;
        margin-top: 2rem; transition: all 200ms ease;
      }
      .cx-btn:hover { background: var(--shop-primary-hover); transform: translateY(-2px); box-shadow: 0 6px 20px color-mix(in srgb, var(--shop-primary) 35%, transparent); color: white; }
    </style>
</head>
<body>
	<?php include('includes/feedback.php');?>
	<?php include('includes/header-tw.php');?>
    
    <main class="cx-wrap">
        <div class="cx-surface cx-border">
            <?php if (!empty($succes)): ?>
                <div class="icon-container icon-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <h1 class="h3 fw-bold mb-3" style="color:var(--shop-text-primary);">Email Vérifié !</h1>
                <p class="text-secondary fs-5 mb-0"><?php echo $succes; ?></p>
                <a href="<?php echo lienConnexion(); ?>" class="cx-btn">Se connecter</a>
            <?php else: ?>
                <div class="icon-container icon-error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                </div>
                <h1 class="h3 fw-bold mb-3" style="color:var(--shop-text-primary);">Erreur de vérification</h1>
                <p class="text-secondary fs-5 mb-0"><?php echo $erreur; ?></p>
                <a href="<?php echo lienAccueil(); ?>" class="cx-btn">Retour à l'accueil</a>
            <?php endif; ?>
        </div>
    </main>

    <?php include('includes/footer-tw.php');?>
 	<?php include('includes/script-footer.php');?>
</body>
</html>
