<?php
// cheques.php
///////////////////////////////////////////////////////////////////////////////
//	application gestionPDM
// février 2020
///////////////////////////////////////////////////////////////////////////////
/*
adhésion :
	listes des chèques (encaissés ou non)
	choix des chèques à encaisser
	export du bordereau d'encaissement
paniers : sur choix d'un mois :
	liste des chèques mensuels
	liste des gros chèques (déjà datés + à choisir)
	calcul auto du total avec moyenne (total des chèques /12)
	export du bordereau d'encaissement
export csv de tous les chèques
*/

	session_start();
	include('inc/init.inc.php');
	

	$GLOBALS['titrePage'] = "Gestion des chèques";
	
?>
En travaux !