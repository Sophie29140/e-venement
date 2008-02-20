<?php
/**********************************************************************************
*
*	    This file is part of e-venement.
*
*    e-venement is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License.
*
*    e-venement is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with e-venement; if not, write to the Free Software
*    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
*    Copyright (c) 2006 Baptiste SIMON <baptiste.simon AT e-glop.net>
*
***********************************************************************************/
?>
<?php
	global $bd,$user,$nav,$class,$data,$title,$stage,$subtitle;
	
	$class .= ' finish';
	includeLib("headers");
?>
<h1><?php echo $title ?></h1>
<?php includeLib("tree-view"); ?>
<?php require("actions.php"); ?>
<div class="body">
<?php
	if ( $subtitle ) echo '<h2>'.htmlsecure($subtitle).'</h2>';
	includePage("depot-stages");
?>
<p class="numtransac"><span>Numéro d'opération:</span> <span>#<?php echo htmlsecure($data["numtransac"]) ?></span></p>
<p class="recorded"><?php
	echo 'Enregitrement des places contingeantées et édition des billets en dépôt effectués.';
?></p>
<?php
		// affichage des doléances
		$properso['id']	= intval(substr($data["client"],5));
		$properso['pro']= substr($data["client"],0,4) == "prof";
		
		$query = " SELECT *
			   FROM personne_properso
			   WHERE ".( $properso['pro']
			          ? "fctorgid = ".$properso['id']
			          : "id = ".$properso['id']." AND fctorgid IS NULL");
		$request = new bdRequest($bd,$query);
		$rec = $request->getRecord();
		
		echo '<p class="merci">';
		echo 'Merci ';
		echo htmlsecure($rec["titre"].' '.$rec["prenom"].' '.$rec["nom"]);
		echo '<a href="ann/fiche.php?id='.intval($rec["id"]).'&view"> </a>';
		echo '</p><p class="vdir">';
		echo 'à bientôt pour le retour des <a href="evt/bill/vdir.php">ventes directes</a>...';
		echo '</p>';
		
		$request->free();
?>
<div class="rappel">
	<p class="responsable">
		<span class="titre">Responsable de la sortie du dépôt&nbsp;:</span>
		<span class="nom"><?php echo htmlsecure($user->getUserName()) ?></span>
		<span class="id">(id: <?php echo $user->getId() ?>)</span>
	</p>
	<p class="depot">
		<span class="titre">Référence du dépôt&nbsp;:</span>
		<span class="ref">#<?php echo '<a href="evt/bill/vdir.php?t='.htmlsecure($data["numtransac"]).'">'.htmlsecure($data["numtransac"]).'</a>' ?></span>
	</p>
</div>
<p class="back">&lt;&lt; <a href="<?php echo $_SERVER["PHP_SELF"] ?>">Retour</a> à l'accueil des dépôts</p>
</div>
<?php
 	includeLib("footer");
?>
