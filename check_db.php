<?php include("connec.php"); $res = mysqli_query($connexion, "SELECT * FROM fichestechniques"); while($r = mysqli_fetch_assoc($res)) { print_r($r); } ?>
