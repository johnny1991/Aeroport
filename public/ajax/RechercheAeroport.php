<?php
//echo "<select name='modele'>";
/*
foreach($listeModele as $ligne1){
	$modele=$ligne1;
	echo "<option value='".$modele->getID_MODELE()."'>".$modele->getNOM()."</option>";
}*/
$pays=$_POST['pays'];
echo '<option value="1">'.$pays.'</option>';
//echo "</select>";

?>