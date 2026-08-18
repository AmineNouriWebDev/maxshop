<?php if($valid_token): ?>
<div class="cx-card" style="max-width: 500px; grid-template-columns: 1fr; margin: 0 auto;">
    <div class="cx-form-panel" style="text-align: center; padding: 3rem 2.5rem;">
        <h1 style="margin-bottom: 0.5rem;">Nouveau mot de passe</h1>
        <p class="cx-subtitle text-secondary">Saisissez votre nouveau mot de passe ci-dessous.</p>

        <?php if($erreur != ""): ?>
            <div class="cx-error" style="text-align: left;">
                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $erreur; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" style="text-align: left;">
            <input type="hidden" name="action" value="reset">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email_q); ?>">
            
            <label class="cx-label" for="cx-pass">Nouveau mot de passe</label>
            <div class="pw-wrap">
                <input class="cx-input" type="password" name="password" id="cx-pass" placeholder="••••••••" required style="padding-right: 2.5rem; margin-bottom: 1rem;">
                <button type="button" class="pw-toggle" onclick="togglePassword('cx-pass', this)" aria-label="Afficher le mot de passe">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
            </div>

            <label class="cx-label" for="cx-confirm">Confirmez le mot de passe</label>
            <div class="pw-wrap">
                <input class="cx-input" type="password" name="confirm_password" id="cx-confirm" placeholder="••••••••" required style="padding-right: 2.5rem; margin-bottom: 1.25rem;">
                <button type="button" class="pw-toggle" onclick="togglePassword('cx-confirm', this)" aria-label="Afficher le mot de passe">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
            </div>

            <?php if (!empty($cloudflare_site_key)): ?>
                <div class="cf-turnstile mb-3" data-sitekey="<?php echo $cloudflare_site_key; ?>"></div>
            <?php endif; ?>

            <button type="submit" class="cx-btn shadow-lg" style="margin-top: 0.5rem;">
                Valider la modification
            </button>
        </form>

    </div>
</div>
<?php else: ?>
<div class="cx-card" style="grid-template-columns: 1fr;">
    <div class="cx-form-panel text-left" style="text-align: center; align-items: center;">
      <h1 style="text-align: center;">Lien invalide ou expiré</h1>
      <p class="cx-subtitle text-secondary" style="text-align: center;"><?php echo $erreur; ?></p>
      <div style="margin-top: 1rem;">
          <a href="<?php echo lienforget(); ?>" class="cx-btn shadow-lg" style="color: white; text-decoration: none;">
              Demander un nouveau lien
          </a>
      </div>
    </div>
</div>
<?php endif; ?>