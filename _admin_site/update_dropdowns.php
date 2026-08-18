<?php
$files = [
    "ajouter_categorie_blog.php",
    "modifier_produits.php",
    "edit_produits.php",
    "add_produits.php",
    "add_produits_similaire.php",
    "ajouter_produits.php",
    "modifier_categorie_blog.php"
];

foreach ($files as $file) {
    $path = "/home/maxsolving/Projets/MaxSolving/maxshop/_admin_site/includes/" . $file;
    if (!file_exists($path)) continue;
    
    $content = file_get_contents($path);
    
    // We want to find the inner loop and inject the 3rd level loop right after the <option> tag.
    // The option tag ends with </option>
    // Then there is a closing brace `}` for the inner loop.
    
    // Because each file has slightly different conditions (e.g. AND `type`="E", or selected logic),
    // we need to extract the exact query string used for $req1, but replace $data['id'] with $data1['id']
    
    $lines = explode("\n", $content);
    $new_lines = [];
    
    $in_loop = false;
    $req1_str = "";
    
    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        
        if (strpos($line, '$req1 =') !== false && strpos($line, 'categories_blog') !== false) {
            $req1_str = trim($line); // e.g. $req1 = 'SELECT * FROM `categories_blog` WHERE `idparent` = "'.$data['id'].'" ORDER BY `ordre` ASC';
        }
        
        $new_lines[] = $line;
        
        if (strpos($line, 'echo afficheChamp1($data1[\'titre\'])') !== false && strpos($line, '</option>') !== false) {
            // We just outputted the level 2 option. Let's add level 3!
            // But we need to keep the "selected" logic if present.
            // Let's capture the selected logic from the current line by regex:
            // e.g. <?php if( categorieProduits($_GET['id']) == $data1['id']) echo "selected"; ? >
            
            $selected_logic = "";
            if (preg_match('/<\?php(.*?)\?>/', $line, $matches)) {
                $php_code = $matches[1];
                if (strpos($php_code, 'echo') !== false && strpos($php_code, 'selected') !== false) {
                    // It's a selected logic! Replace $data1 with $data2
                    $selected_logic = "<?php " . str_replace('$data1', '$data2', trim($php_code)) . " ?>";
                }
            }
            
            // Build req2 from req1
            $req2_str = str_replace('$req1', '$req2', $req1_str);
            $req2_str = str_replace('$data[\'id\']', '$data1[\'id\']', $req2_str);
            
            $indent = "                                                  ";
            $new_lines[] = $indent . "<?php";
            $new_lines[] = $indent . $req2_str;
            $new_lines[] = $indent . "\$res2 = executeRequete(\$req2);";
            $new_lines[] = $indent . "while (\$data2 = mysqli_fetch_array(\$res2)) { ?>";
            $new_lines[] = $indent . "<option value=\"<?php echo \$data2['id']; ?>\" $selected_logic >----&gt; <?php echo afficheChamp1(\$data2['titre']); ?></option>";
            $new_lines[] = $indent . "<?php } ?>";
        }
    }
    
    file_put_contents($path, implode("\n", $new_lines));
    echo "Updated $file\n";
}
?>
