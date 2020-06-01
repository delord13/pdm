<?php
// moulinetteDB.php
// importe les contenus des tables spip dans les tables PDM
// toutes les tables sont dans la même base sur le même serveur

//////////////////
// Attention !! //
//////////////////

// importe les chèques à partir du fichier cheques.csv = export csv de la première feuille "remise de chèques" sans les 4 prmeières ligne (de titrre) et ajout en 1ère colonne des idCompte

//	error_reporting(E_ALL);
//	ini_set("display_errors", 1);
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	
	session_start();
	include('inc/init.inc.php');
	
	$id_contrat = 6; // id du contrat légumes de l'année à récupérer 6 pour 20-21
	
	$GLOBALS['titrePage'] = "Importation des données de l'ancienne application";
	
	
	?>
<!DOCTYPE html>
<html lang='fr-fr'>
<?php	
		include('inc/headHTML.inc.php');
?>
	<body style='font-family: sans-serif; font-size:small; padding: 0px;'  >
		<form method='POST' action='comptes.php' id='formAction' >
			<input type='hidden' name='newAction' id='newAction' value='afficher'>

		<div id='haut'>
<?php
			include('inc/divEnTete.inc.php');
?>
		</div>
		<div id='content' style='width: 90%; margin: auto;'>
<!--			<div style='width: 90%; margin: auto; overflow: auto;'> -->
		
<?php

	
	/////////////////////////////////////////////////////
	// distributions
	/////////////////////////////////////////////////////
{
	// la table des distribution reste intacte
}	
	
	/////////////////////////////////////////////////////
	// comptes et personnes
	/////////////////////////////////////////////////////
{	
	$sql = "TRUNCATE PDM_compte";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	$sql = "TRUNCATE PDM_personne";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	
	$sql = "SELECT id_adherent, nom,tel, email, comment, maj FROM spip_adherents ORDER BY id_adherent";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	while ($unAdherent = mysqli_fetch_assoc($res)) {
		$unAdherent['nom'] = mysqli_real_escape_string($GLOBALS['lkId'],$unAdherent['nom']);
		$unAdherent['comment'] = mysqli_real_escape_string($GLOBALS['lkId'],$unAdherent['comment']);
		// insère compte sans titulairePrincipalId
		$sql1 = 
		"INSERT INTO PDM_compte (idCompte, titulairePrincipalId, commentaire, adherent, dateCreation, dateModification) VALUES ({$unAdherent['id_adherent']},NULL,'{$unAdherent['comment']}','non',NULL,'{$unAdherent['maj']}')";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		$idCompte = mysqli_insert_id($GLOBALS['lkId']);
		
		// ? adhérent oui si au moins un contrat pour anneeId = 2
		$sql2 = "SELECT idContrat FROM PDM_contrat WHERE compteId=$idCompte AND anneeId=2";
		$res2 = mysqli_query($GLOBALS['lkId'],$sql2);
		if ($unContrat = mysqli_fetch_assoc($res2)) {
			$sql2 = "UPDATE PDM_compte SET adherent='oui' WHERE idCompte=$idCompte";
			$res2 = mysqli_query($GLOBALS['lkId'],$sql2);
		}
		
		// insère chaque personne avec lastId comme id de compte
		$tabNom = explode(' et ',$unAdherent['nom']);
//if ($unAdherent['id_adherent']==4)	{echo $unAdherent['nom'];var_dump($tabNom);die();}	

		// INSERT INTO PDM_personne(idPersonne, nom, telephone, courriel, compteId) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5])
		$i = 0;
		foreach ($tabNom AS $unNom) {
			$unNom = trim($unNom);
			// mettre le nom en majuscule 
			$tabNomPrenom = explode(' ',$unNom);
			$tabNomPrenom[0] = mb_strtoupper($tabNomPrenom[0]);
			if ($tabNomPrenom[0]=='DE')  $tabNomPrenom[1] = mb_strtoupper($tabNomPrenom[1]);
			$nomMajuscule = '';
			foreach ($tabNomPrenom AS $j => $unNomPrenom) {
				if ($j>0) $nomMajuscule .= ' ';
				$nomMajuscule .= $unNomPrenom;
			}
			
			$sql2 = "INSERT INTO PDM_personne(idPersonne, nom, telephone, courriel, compteId) VALUES (NULL,'$nomMajuscule','{$unAdherent['tel']}','{$unAdherent['email']}',$idCompte)";
			$res2 = mysqli_query($GLOBALS['lkId'],$sql2);
			if ($i==0) $idTitulairePrincipal = mysqli_insert_id($GLOBALS['lkId']);
			$i++;
//if ($unAdherent['id_adherent']==4)	die($sql2);
		}
		
		// mettre à jour le titulairePrincipalId avec lastId de la première personne
		$sql1 = "UPDATE PDM_compte SET titulairePrincipalId=$idTitulairePrincipal WHERE idCompte=$idCompte";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		
		// on a id_adherent et idCompte correspondant => il faut faire les contrats et les reports

//if ($unAdherent['id_adherent']==4)	die($sql1);	
		
	}
	echo "<hr><b>Comptes et titulaires mis à jour.</b>\n ";
}	
	/////////////////////////////////////////////////////
	// contrats
	/////////////////////////////////////////////////////
{	
	echo "<hr><b>Contrats mis à jour (Ne pas tenir compte des messages suivants :).</b><br>\n ";
	$sql = "TRUNCATE PDM_contrat";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	
	$sql = "SELECT id_adherent, AC.id_contrat, id_quantite, id_frequence, id_periode, C.id_contrat, nom, debut_valid, fin_valid, midterm FROM spip_adherents_contrats AS AC, spip_contrats AS C WHERE AC.id_contrat=C.id_contrat AND AC.id_contrat>=$id_contrat "; // pas les contrats antérieurs à 2020-2021
	$res = mysqli_query($GLOBALS['lkId'],$sql);

	$k = 1;

	while ($unContrat = mysqli_fetch_assoc($res)) {
		
		// légumes ou oeufs ?
		$anneeId = (int ) ($unContrat['id_contrat']/2) -1;
		if (($unContrat['id_contrat']%2)==0) $typeContratId = 1;
		else $typeContratId = 2;
		// SELECT MAX(AD.id_distribution) AS max, MIN(AD.id_distribution), date_distrib FROM spip_distributions AS D, spip_adherents_distribution AS AD WHERE max=D.id_distribution AND AD.id_contrat>=6 AND AD.id_adherent=18
		
		// début = date première distrib
		$sql1 = "SELECT date_distrib, D.id_distribution FROM spip_distributions AS D WHERE D.id_distribution IN (SELECT MIN(id_distribution) AS min FROM spip_adherents_distribution WHERE id_contrat={$unContrat['id_contrat']} AND id_adherent={$unContrat['id_adherent']} AND quantite>0)";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		$uneDate = mysqli_fetch_assoc($res1);
		$dateDebut = $uneDate['date_distrib'];
		$idDistributionDebut = $uneDate['id_distribution'];
		
		// fin = date dernière distrib
		$sql1 = "SELECT date_distrib, D.id_distribution FROM spip_distributions AS D WHERE D.id_distribution IN (SELECT MAX(id_distribution) AS max FROM spip_adherents_distribution WHERE id_contrat={$unContrat['id_contrat']} AND id_adherent={$unContrat['id_adherent']} AND quantite>0)";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		$uneDate = mysqli_fetch_assoc($res1);
		$dateFin = $uneDate['date_distrib'];
		$idDistributionFin = $uneDate['id_distribution'];
		// SELECT MAX(id_distribution) AS max, MIN(id_distribution) AS min FROM spip_adherents_distribution WHERE id_contrat>=6 AND id_adherent=18 
		// c'est là le problème compte_id != id_adherent

		// contrat d'essai hebdo => parité différente des distrib début et fin
		if (($idDistributionDebut%2)==($idDistributionFin%2) && $unContrat['id_periode']==4 && $unContrat['id_frequence']==1) echo "il y a un problème avec le contrat d'essai hebdo du compte {$unContrat['id_adherent']} CORRIGÉ !<br>\n";
		
		// contrat d'essai quinzo => parité identique des distrib début et fin
		if (($idDistributionDebut%2)!=($idDistributionFin%2) && $unContrat['id_periode']==4 && $unContrat['id_frequence']>1) echo "il y a un problème avec le contrat d'essai quinzomadaire du compte {$unContrat['id_adherent']} <br>\n";
		
		
		// il faut corriger les dates de fin pour compenser les incohérences de Denis sur les quinzomadaires après les fêtes
	// calcul des dates de fin théoriques
	// dateFin [periode], [frequence]
	$tabdateFin[1][1] = '2021-03-24';
	$tabdateFin[1][2] = '2021-03-24';
	$tabdateFin[1][3] = '2021-03-17';
	$tabdateFin[2][1] = '2020-09-23';
	$tabdateFin[2][2] = '2020-09-23';
	$tabdateFin[2][3] = '2020-09-16';
	$tabdateFin[3][1] = '2021-03-24';
	$tabdateFin[3][2] = '2021-03-24';
	$tabdateFin[3][3] = '2021-03-17';
	
		if ($unContrat['id_periode']!=4) $dateFinCorrige = $tabdateFin[$unContrat['id_periode']][$unContrat['id_frequence']];
		else $dateFinCorrige = $dateFin;
		
		if ($dateFinCorrige!=$dateFin && $unContrat['id_periode']!=4) {
			echo "$k) date de fin corrigée $dateFin => $dateFinCorrige pour compte {$unContrat['id_adherent']}  type : $typeContratId ; période : {$unContrat['id_periode']} ; fréquence : {$unContrat['id_frequence']}<br>\n";
			$k++;
		}
		
		$sql1 = "INSERT INTO PDM_contrat(idContrat, compteId, anneeId, typeContratId, quantiteContratId, frequenceContratId, periodeContratId, dateDebut, dateFin, valide, dateCreation, dateModification) VALUES (NULL, {$unContrat['id_adherent']}, $anneeId, $typeContratId, {$unContrat['id_quantite']}, {$unContrat['id_frequence']}, {$unContrat['id_periode']}, '$dateDebut', '$dateFinCorrige', 1, '$dateDebut', CURRENT_TIMESTAMP)";
//die($sql1);
//if ($unContrat['id_adherent']==281) die($sql1);		
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		
	}
}	
	
	/////////////////////////////////////////////////////
	// reports et ajouts
	/////////////////////////////////////////////////////
{
	// les ajouts sont directement répercutés dans la table spip_adherents_distribution
	// les reports sont visibles dans la table spip_adherents_action AC, spip_distributions D WHERE AC.id_distribution= D.id_distribution AND id_contrat>=$id_contrat AND 
	// SELECT * FROM `spip_adherents_action` WHERE `reporte` IS NOT NULL 
	
	// on traite d'abord les reports et on annule leur effet sur la table spip_adherents_distribution
	// reporte !NULL = destination ; id_distribution = source ; quantite = nbre de paniers reportés
	// SELECT id_adherent, id_distribution FROM spip_adherents_action WHEREAC, spip_distributions D WHERE AC.id_distribution= D.id_distribution AND id_contrat>=$id_contrat AND reporte IS NOT NULL 
	$sql = "TRUNCATE PDM_report";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	$sql = "TRUNCATE PDM_ajout";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	
	// copie de la table spip_adherents_distribution
	$sql = "CREATE TABLE spip_adherents_distribution_COPIE LIKE spip_adherents_distribution";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	$sql = "INSERT INTO spip_adherents_distribution_COPIE SELECT * FROM spip_adherents_distribution";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	
	$sql = "SELECT id_adherent, AC.id_distribution, reporte, quantite FROM spip_adherents_action AC, spip_distributions D WHERE AC.id_distribution= D.id_distribution AND id_contrat>=$id_contrat AND reporte IS NOT NULL ";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	while ($uneAction = mysqli_fetch_assoc($res)) {
		$quantite = $uneAction['quantite'];
		$id_adherent = $uneAction['id_adherent'];
		$id_origine = $uneAction['id_distribution'];
		$id_destination = $uneAction['reporte'];
		$idOrigine = id_dTOidD($id_origine);
		$idDestination = id_dTOidD($id_destination);
		
		// date de la distribution idOrigine
		$sql1 = "SELECT date FROM PDM_distribution WHERE idDistribution=$idOrigine";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		$uneDistribution = mysqli_fetch_assoc($res1);
		$dateOrigine = $uneDistribution['date'];
		
		// ? contrat = contrat légumes du compte id_adherent (=idCompte) valide à la date de la distribution idOrigine
		$sql1 = "SELECT idContrat FROM PDM_contrat WHERE compteId=$id_adherent AND '$dateOrigine' BETWEEN dateDebut AND dateFin";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		$unContrat = mysqli_fetch_assoc($res1);
		$idContrat = $unContrat['idContrat'];
		
		// enregistrer
		// INSERT INTO PDM_report(idReport, contratId, quantite, origine, destination, dateModification) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6])
		$sql1 = "INSERT INTO PDM_report(idReport, contratId, quantite, origine, destination, dateModification) VALUES (NULL,$idContrat,$quantite,$idOrigine,$idDestination,CURRENT_TIMESTAMP)";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
/*
if ($id_adherent==173) {
	echo "#222 $sql1<br>\n ";
	die;
}
*/		

		// retrait de l'effet des reports sur la table spip_adherents_distribution_COPIE
		// pour pouvoir repérer les ajouts
		
		// -1 à id_destination dans spip_adherents_distribution
		$sql1 = "UPDATE spip_adherents_distribution_COPIE SET quantite=quantite-1 WHERE id_adherent=$id_adherent AND id_distribution=$idDestination";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);

		// +1 à id_origine dans spip_adherents_distribution
		$sql1 = "UPDATE spip_adherents_distribution_COPIE SET quantite=quantite+1 WHERE id_adherent=$id_adherent AND id_distribution=$idOrigine";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		
	} // fin des reports pour chaque action

	//on peut alors rechercher les quantités en trop dans la table table spip_adherents_distribution_COPIE pour trouver les ajouts
	// pour chaque adherent/distribution
	$sql = "SELECT quantite, id_contrat, id_adherent, id_distribution FROM spip_adherents_distribution_COPIE WHERE id_contrat=$id_contrat AND id_distribution!=318 AND id_distribution!=319  ORDER BY id_adherent ";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	while ($uneDistribution = mysqli_fetch_assoc($res)) {
		$quantite = $uneDistribution['quantite'];
		$idCompte = $uneDistribution['id_adherent'];
		$id_distribution = $uneDistribution['id_distribution'];
		$idDistribution = id_dTOidD($id_distribution);
		
		// distribution PDM_distribution
		$sql1 = "SELECT date, nombreUnites, semaine FROM PDM_distribution WHERE idDistribution=$idDistribution";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		$uneLigne = mysqli_fetch_assoc($res1);
		$dateDistribution = $uneLigne['date'];
		$nombreUnites = $uneLigne['nombreUnites'];
		$semaine = $uneLigne['semaine']; // Q1 ou Q2
// !!!!! ATTENTION contrat Q1 ou Q2 : ici on compt e même les semaines sans !!! IL FAUT CORRIGER		
		// ? contrat = contrat légumes du compte id_adherent (=idCompte) valide à la date de la distribution idDistribution `typeContratId`, `quantiteContratId`, `frequenceContratId`,
		/*
		$sql1 = "SELECT idContrat, quantiteContratId, frequenceContratId  FROM PDM_contrat WHERE compteId=$idCompte AND typeContratId=1 AND (frequenceContratId=1 OR (frequenceContratId=2 AND $semaine='Q2')  OR (frequenceContratId=3 AND $semaine='Q1')) AND '$dateDistribution' BETWEEN dateDebut AND dateFin ";
		*/
		if ($semaine=='Q1') {
		$sql1 = "SELECT idContrat, quantiteContratId, frequenceContratId, compteId  FROM PDM_contrat WHERE compteId=$idCompte AND typeContratId=1 AND (frequenceContratId=1 OR frequenceContratId=2 ) AND '$dateDistribution' BETWEEN dateDebut AND dateFin ";
		}
		else {
		$sql1 = "SELECT idContrat, quantiteContratId, frequenceContratId, compteId  FROM PDM_contrat WHERE compteId=$idCompte AND typeContratId=1 AND (frequenceContratId=1 OR frequenceContratId=3 ) AND '$dateDistribution' BETWEEN dateDebut AND dateFin ";
			
		}
//if ($idCompte==261 && $idDistribution==5) die($sql1);
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		if ($unContrat = mysqli_fetch_assoc($res1)) {
			$idContrat = $unContrat['idContrat'];
			$quantiteContrat = $unContrat['quantiteContratId'] * $nombreUnites;
			$idCompteContrat = $unContrat['compteId'];
			$frequenceContrat = $unContrat['frequenceContratId']; //(1 hebdo; 2 Q1; 3 Q2)
			//if ($frequenceContrat==1 OR ($frequenceContrat==2 AND $semaine='Q2') OR ($frequenceContrat==3 AND $semaine='Q1')) $quantiteTheorique = $quantiteContrat;
			//else $quantiteTheorique = 0;
			$quantiteTheorique = $quantiteContrat;
			$quantiteAjout = $quantite-$quantiteTheorique;
			
if ($quantite<$quantiteTheorique && ($id_distribution<317) ) echo "Problème sur le compte : $idCompteContrat; contrat : $idContrat ; pour la distribution : $id_distribution ; frequence : $frequenceContrat ; <b>quantité lue : $quantite ; quantité calculée : $quantiteTheorique </b><br>\n ";

			if ($quantite>$quantiteTheorique) { // il y a eu ajout de $quantite-$quantiteTheorique
				$sql1 = "INSERT INTO PDM_ajout(idAjout, contratId, distributionId, quantite, dateModification) VALUES (NULL,$idContrat,$idDistribution,$quantiteAjout,CURRENT_TIMESTAMP)";
				$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
//	echo "contrat : $idContrat ; distribution : $idDistribution ; quantité ajoutée : $quantiteAjout<br>\n";			
			}
			else {
				//if ($quantite<$quantiteTheorique)  echo "#166 $quantite<$quantiteTheorique : Y a un bug !! .<br>\n "; 
			}
	}
		
	} // fin des ajouts pour chaque adhérent distribution
	
	// on peut supprimer la table spip_adherents_distribution_COPIE
	$sql = "DROP spip_adherents_distribution_COPIE";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	
///////////////////////////////////////
// on corrige le 301 qui est incohérent
$sql2 = "UPDATE PDM_contrat SET dateDebut = '2020-04-29', dateFin = '2020-05-13' WHERE compteId = 301 AND periodeContratId=4";
$res2 = mysqli_query($GLOBALS['lkId'],$sql2);
///////////////////////////////////////

	echo "<hr><b>Reports et ajouts mis à jour.</b>\n ";
} // fin reports et ajouts	
	
	/////////////////////////////////////////////////////
	// volontaires
	/////////////////////////////////////////////////////
{
	// pas trouvés devraient être dans spip_adherents_action "distribue" et "inscrit"
	// distrib => inscrit = 1
	// emarge => gere = 1
	$sql = "TRUNCATE volontaireEmargement";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	$sql = "TRUNCATE PDM_volontaireDistribution";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	
	$sql = "SELECT gere, inscrit, id_adherent, AC.id_distribution FROM spip_adherents_action AC, spip_distributions D WHERE AC.id_distribution= D.id_distribution AND id_contrat>=$id_contrat AND (gere=1 OR inscrit=1)";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	while ($uneAction = mysqli_fetch_assoc($res)) {
		
		// recherche de l'idPersonne du titulaire principal (id_adherent = idCompte)
		$sql1 = "SELECT titulairePrincipalId FROM PDM_compte WHERE idCompte={$uneAction['id_adherent']}";
		$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		$unePersonne = mysqli_fetch_assoc($res1);
		$idVolontaire = $unePersonne['titulairePrincipalId'];
		
		// dDistribution en fonction de id_distribution	
		$idDistribution = id_dTOidD($uneAction['id_distribution']);
		
		// traitement des volontariats
		if ($uneAction['gere']==1) {
			$sql1 = "INSERT INTO PDM_volontaireEmargement(idVolontaire, personneId, distributionId, dateModification) VALUES (NULL,$idVolontaire,$idDistribution,CURRENT_TIMESTAMP)";
			$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		}
		if ($uneAction['inscrit']==1) {
			$sql1 = "INSERT INTO PDM_volontaireDistribution(idVolontaire, personneId, distributionId, dateModification) VALUES (NULL,$idVolontaire,$idDistribution,CURRENT_TIMESTAMP)";
			$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		}
	}
	echo "<hr><b>Volontariat mis à jour.</b>\n ";
}	

?>
			<hr>
		

<?php
	function id_dTOidD($id_distribution) {
		// retourne de idDistribution en fonction de id_distribution
		$sql = "SELECT idDistribution FROM PDM_distribution WHERE date IN (SELECT date_distrib FROM spip_distributions WHERE id_distribution=$id_distribution)"; 
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$uneDistribution = mysqli_fetch_assoc($res);
		return $uneDistribution['idDistribution'];
	}
?>

<?php
	/////////////////////////////////////////////////////
	// importation des chèques
	/////////////////////////////////////////////////////
	
	echo "<b>Traitement des chèques</b><br>\n ";
	// on vide les tables
	$sql = "TRUNCATE PDM_chequeCotisation";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	$sql = "TRUNCATE PDM_chequeContrat";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	
	
	$fichierCSV = fopen('cheques.csv', 'r');
	if ($fichierCSV==FALSE) die("le fichier cheques.csv n'existe pas.");
	
	// lecture du fichier ligne par ligne à partir de la 5e
	$i = 0;
	while (!feof($fichierCSV) ) {
		$i++;
		$ligneOrigine[] = fgetcsv($fichierCSV, 0, ';');
	}
	fclose($fichierCSV);

	
	// exclusion des lignes sans idCompte
	foreach ($ligneOrigine AS $numLigne =>$uneLigne) {
		if ($ligneOrigine[0]=='0' || $ligneOrigine[0]=='') {
			echo "Les chèques de {$ligneOrigine[0]} (sans identifiant de compte) n'ont pas été traités<br>\n";
		}
		else $ligne[] = $uneLigne;
	}
	
//var_dump($ligne); die;
	
	// traitement des lignes avec comptes
	foreach ($ligne AS $numLigne =>$uneLigne) { // test des numéros de chèque par catégorie
		if ($uneLigne[25]!=0) traiterCotisation($numLigne,$uneLigne);
		if ($uneLigne[33]!=0) traiterOeufs($numLigne,$uneLigne);
		if ($uneLigne[10]!=0) traiterPanier($numLigne,$uneLigne);
	}
	
	// traitement des cotisations
	function traiterCotisation($numLigne,$uneLigne) {
		$uneLigne[26] = str_replace(',','.',$uneLigne[26]);
		$sql = "INSERT INTO PDM_chequeCotisation(idChequeCotisation, compteId, banque, numero, montant, dateEncaissement, dateModification) VALUES (NULL,{$uneLigne[0]},'{$uneLigne[24]}','{$uneLigne[25]}','{$uneLigne[26]}',NULL, CURRENT_TIMESTAMP)";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		echo "le chèque de cotisation de {$uneLigne[1]} a été enregsitré.<br>\n";
	}
	
	// traitement des oeufs
	function traiterOeufs($numLigne,$uneLigne) {
		// recherche du contrat oeufs 
		$sql = "SELECT idContrat FROM PDM_contrat WHERE compteId={$uneLigne[0]} AND typeContratId=2";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$reponse = mysqli_fetch_assoc($res);
		$idContrat = $reponse['idContrat'];
		// encaissement mai
		$dateEncaissement = 'NULL';
		if ($uneLigne[35]!=0) $dateEncaissement = "2020-05-05";
		if ($uneLigne[36]!=0) $dateEncaissement = "2020-06-05";
		$uneLigne[32] = str_replace(',','.',$uneLigne[32]);
		$sql = "INSERT INTO PDM_chequeContrat(idChequeContrat, contratId, banque, numero, montant, dateEncaissement, dateModification) VALUES (NULL,$idContrat,'{$uneLigne[34]}','{$uneLigne[33]}',{$uneLigne[32]},$dateEncaissement,CURRENT_TIMESTAMP)";
//if ($idContrat==2) die($sql);
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		echo "Le chèque du contrat Oeufs du compte {$uneLigne[1]} a été enregistré.";
		// si contrat à l'année : 2e chèque
		if ($uneLigne[29]=='A') {
			$numCheque = $uneLigne[33]+1;
			$sql = "INSERT INTO PDM_chequeContrat(idChequeContrat, contratId, banque, numero, montant, dateEncaissement, dateModification) VALUES (NULL,$idContrat,'{$uneLigne[34]}','$numCheque',{$uneLigne[32]},NULL,CURRENT_TIMESTAMP)";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
		
			echo "<br>\nLe deuxième chèque du contrat Oeufs du compte {$uneLigne[1]} a été enregistré.";
		}
		echo "<br>\n";
	}
	
	// traitement des paniers
	function traiterPanier($numLigne,$uneLigne) {
		// si essai : 1 seul chèque
		if ($uneLigne[29]=='ESSAI') { // essai => 1 seul chèque
			$nbCheque = 1;
		}
		// sinon si >200
		else { // pas essai
			if ($uneLigne[11]<200) { // petit chèque
				if ($uneLigne[29]=='A') { // année => 12 chèques
					$nbCheque = 12;
				}
				else {// semestre =>6 chèques
					$nbCheque = 6;
				}
			}
			else { // >=200
				if ($uneLigne[29]=='A') { // année => 2 chèques
					$nbCheque = 2;
				}
				else {// semestre => 1 chèque
					$nbCheque = 1;
				}
			}
		}
		
		// recherche idContrat panier
		$sql = "SELECT idContrat FROM PDM_contrat WHERE compteId={$uneLigne[0]} AND typeContratId=1";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$reponse = mysqli_fetch_assoc($res);
		$idContrat = $reponse['idContrat'];
		$uneLigne[11] =  str_replace(',','.',$uneLigne[11]);
		
		for ($n=1;$n<=$nbCheque;$n++) {
			switch ($n) {
				case 1 :
					if ($uneLigne[47]!='') $dateEncaissement = "'2020-04-05'";
					else $dateEncaissement = 'NULL';
					break;
				case 2 :
					if ($uneLigne[49]!='') $dateEncaissement = "'2020-04-05'";
					else $dateEncaissement = 'NULL';
					break;
				default :
					$dateEncaissement = 'NULL';
			} 
			$numero = (int)$uneLigne[10]+$n-1;
			$sql = "INSERT INTO PDM_chequeContrat(idChequeContrat, contratId, banque, numero, montant, dateEncaissement, dateModification) VALUES (NULL, $idContrat, '{$uneLigne[45]}', '$numero', $uneLigne[11], $dateEncaissement, CURRENT_TIMESTAMP)";
//if ($n==3) die($sql);
			$res = mysqli_query($GLOBALS['lkId'],$sql);
		}

		echo "Contrat 'Paniers' $nbCheque chèque(s) de $uneLigne[1] ont été enregistré(s)<br>\n";

	} // fin traiterPanier

	
?>
			<hr>
		</div>
<!--		</div> -->
		<div id='bas'>
			<p style="text-align: center; margin-top: 10px; margin-bottom: 10 px;">
								<input style="font-weight:bold; margin-bottom: 10 px;"  name="retour" value="Retour" type="button" title="retourner au menu principal" onClick=" document.location.href='index.php'
				">

			</p>
		</div>

	</body>
		<script>
			$(document).ready(
				function(){
					// redimmensionner
					redim();
				}
			);
		</script>
</html>