<?php
// contrats.php
///////////////////////////////////////////////////////////////////////////////
//	application gestionPDM
// février 2020
///////////////////////////////////////////////////////////////////////////////
/*
stat sur les contrats : nb par type, fréquence, durée...
pour un inscrit sélectionné : gestion de ses contrats : ajout, modification, suppression
(Attention aux contrats déjà commencés)
*/

	session_start();
	include('inc/init.inc.php');
	

	$GLOBALS['titrePage'] = "Gestion des contrats";
	
?>
