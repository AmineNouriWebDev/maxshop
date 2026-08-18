<?php include("includes/include.php"); $res = mysqli_query($connexion, "DESCRIBE produits_similaire"); while($row = mysqli_fetch_assoc($res)) { echo $row["Field"]." - ".$row["Type"]."\n"; } ?>
