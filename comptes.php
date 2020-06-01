<?php
// comptes.php
///////////////////////////////////////////////////////////////////////////////
//	application gestionPDM
// février 2020
///////////////////////////////////////////////////////////////////////////////

/*
les doublons : 11 sur 194
	ANAVI
	BAUDUIN
	BIGOT
	BONNIN
	COTTON
	CREPIN (triplet ? Brigitte la mère Pierre)
	DELORD
	GUSTIN DE CAROLIS
	Paternoga
	PEPINO
	SCAVINO
*/
/*
liste des comptes : 
	avec possibilité de choisir : tous, contrats en cours, contrats expirés, contrats anciens, sans contrat
pour l'compte sélectionné :
	modifier
	contrats (ajouter éditer)
	chèques (enregistrement des chèques)
	distribution (volontaires, report, abandon)
	courriel (liste des courriels envoyés et reçus ; ajouter reçu, composer et envoyer courriel)
ajouter un compte
export csv de tous

(adhérents et non adhérents dont preneurs de panier et contrats à l’essai)
liste alphabétique des adhérents (un adhérent par ligne) : on peut choisir : tous les enregistrés, ceux avec contrat en cours, ceux sans contrats en cours, ceux sans contrat (contacts), preneurs de panier seuls
	supprimer (l’adhérent sélectionné)
	ajouter
		un adhérent
		un non-adhérent contact
		un non-adhérent panier à l’essai
		un (non-)adhérent  panier ponctuel
		envoyer courriels de relance pour toutes les cotisations non réglées
		envoyer courriels de relance pour tous les chèques contrats non remis
	export CSV
pour un adhérent sélectionné : 
	éditer toutes les infos adhérents 
	enregistrer commentaires
	enregistrer volontariat pour distribution et émargement (une ou plusieurs) + nbre de volontaires pour un même compte
	enregistrer report et abandon de panier (option : courriel automatique contenant les coordonnées des preneurs de paniers ponctuels) y compris reports anticipés
	enregistrement des paniers supplémentaires pour une distribution
	enregistrement des chèques cotisation et contrats
	liste des chèques (coche automatique pour les chèques encaissés)
	éditer les chèques
	ajouter un chèque (avec proposition du n° de chèque suivant)
	courriel de relance cotisation non réglée
	courriel de relance chèque non remis
	courriel de relance à la fin d’un contrat
	courriel
	liste des relances suggérées, ac.id_contrat
	
	les statuts	
						contrat>1mois	contrat 1mois		sans contrat	preneur
	adhérent					x					-						x				x
	non adhérent			-					x						$sql = '
		SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
		FROM spipComptes a 
		WHERE a.idCompte NOT IN
		(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution)
		ORDER BY a.nom
		';
		x				x
	
contrats en cours avec détails des contrats
		$sql = 'SELECT a.idCompte, a.nom, a.tel, a.email, a.comment, c.nom contrat_nom, q.quantite, f.frequence, q.prix 
		FROM spipComptes_contrats ac		$sql = '
		SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
		FROM spipComptes a 
		WHERE a.idCompte NOT IN
		(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution)
		ORDER BY a.nom, ac.id_contrat
		';

		INNER JOIN spipComptes a ON ac.idCompte=a.idCompte
		INNER JOIN spip_contrats c ON ac.id_contrat=c.id_contrat
		INNER JOIN spip_quantites q ON ac.id_quantite=q.id_quantite
		INNER JOIN spip_frequences f ON ac.id_frequence=f.id_frequence
		WHERE ac.id_contrat IN 
		(SELECT id_contrat FROM spip_contrats c WHERE CURRENT_TIMESTAMP BETWEEN debut_valid AND fin_valid)
		AND a.idCompte IN
		(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.date_distrib>CURRENT_TIMESTAMP)
		ORDER BY a.nom, ac.id_contrat 
		';
		// = adhérent avec contrats actifs
		// il faut exclure les adhérents non présents dans les distributions futures

	
tous comptes :
		$sql = '
		SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
		FROM spipComptes a 
		ORDER BY a.nom
		';
contrats en cours :
		$sql = '
		SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
		FROM spipComptes a
		WHERE a.idCompte IN
		(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.date_distrib>CURRENT_TIMESTAMP)
		ORDER BY a.nom 
		';
contrats expirés :
		$sql = '
		SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
		FROM spipComptes a
		WHERE a.idCompte NOT IN
		(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.date_distrib>CURRENT_TIMESTAMP)
		AND a.idCompte IN
		(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND (d.id_contrat=4 OR d.id_contrat=5))
		ORDER BY a.nom 
		';
contrats anciens :
		$sql = '
		SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
		FROM spipComptes a
		WHERE a.idCompte NOT IN
		(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.date_distrib>CURRENT_TIMESTAMP)
		AND a.idCompte IN
		(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.id_contrat<4)
		ORDER BY a.nom 
		';
sans contrats
		$sql = '
		SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
		FROM spipComptes a 
		WHERE a.idCompte NOT IN
		(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution)
		ORDER BY a.nom
		';
		
		SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
		FROM spipComptes a
		WHERE a.idCompte IN
		(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.date_distrib>CURRENT_TIMESTAMP)
		ORDER BY a.nom 

  */

/*
$_SESSION['auj']
$_SESSION['accesAutorise']
$_SESSION['anneeCouranteId']
*/

	session_start();
	include('inc/init.inc.php');
//	include ("inc/functionsModifierEnregistrerContrats.inc.php");

	if (!isset($_SESSION['quiAfficher'])) $_SESSION['quiAfficher'] = 'adherents';
	
	$GLOBALS['titrePage'] = 'Gestion des comptes';
	
	if (isset($_POST['message'])) $message = $_POST['message'];
	else $message = '';		
	
	if (isset($_POST['newAction'])) {
		// agir selon POST newAcion
		if (isset($_POST['qui'])) $_SESSION['quiAfficher'] = $_POST['qui'];
		switch ($_POST['newAction']) {
			case 'afficher' :
				afficherListeComptes($message);
				break;
			case 'afficherLeCompte' :
				afficherLeCompte('');
				break;
			case 'supprimerCompte' :
				supprimerCompte();
				break;
			case 'ajouterCompte' :
				ajouterCompte();
				break;
			case 'exportComptes' :
				exportComptes();
				break;
			case 'retour' :
				header("Location: index.php");
				exit;
				break;
/*			
			case 'ajouterContratAnneeCourante' :
				modifierContrats($_POST['idCompte'],"ajouterContratAnneeCourante","");
				break;
			
			case 'ajouterContratAnneeSuivante' :
				modifierContrats($_POST['idCompte'],"ajouterContratAnneeSuivante","");
				break;
			
			case 'enregistrerModificationCompte' :
				enregistrerModificationCompte();
				break;
*/
			case 'abandonnerModificationCompte' :
				afficherListeComptes($message);
				break;
/*
			case 'enregistrerModificationContrats' :
				enregistrerModificationContrats();
				break;
			case 'enregistrerNouveauContrat' :
				enregistrerNouveauContrat();
				break;
			case 'abandonnerContrats' :
				afficherListeComptes($message);
				break;
*/
			case 'enregistrerAjoutCompte' :
				enregistrerAjoutCompte($_POST);
				afficherListeComptes($message);
				break;
			case 'abandonnerAjoutCompte' :
				afficherListeComptes($message);
				break;
			default :
				afficherListeComptes($message);
		}
	}
	else {
		afficherListeComptes($message);
	}
	
	function afficherListeComptes($message) {
		// définition du script de retour pour afficherLeCompte
		$_SESSION['scriptOrigine'] = 'comptes.php';
		
		// compte sélectionné ?
		if (isset($_POST['idCompte'])) $idCompte = $_POST['idCompte'];
		else $idCompte = FALSE;
		// recherche des infos à afficher :
		switch ($_SESSION['quiAfficher']) {
			case 'tousComptes' :
				$sql = '
				SELECT c.idCompte, c.titulairePrincipalId, c.adherent, c.commentaire, p.idPersonne, p.nom, p.telephone, p.courriel FROM PDM_personne p, PDM_compte c WHERE c.idCompte=p.compteId ORDER BY c.idCompte 
				';
				break;
			case 'adherents' :
				$sql = "
				SELECT c.idCompte, c.titulairePrincipalId, c.adherent, c.commentaire, p.idPersonne, p.nom, p.telephone, p.courriel FROM PDM_personne p, PDM_compte c WHERE c.idCompte=p.compteId AND c.adherent='oui' ORDER BY c.idCompte
				";
				break;
			case 'nonAdherents' :
				$sql = "
				SELECT c.idCompte, c.titulairePrincipalId, c.adherent, c.commentaire, p.idPersonne, p.nom, p.telephone, p.courriel FROM PDM_personne p, PDM_compte c WHERE c.idCompte=p.compteId AND c.adherent='non' ORDER BY c.idCompte
				";
				break;
/*			case 'contratsAnneeCourante' : // et futurs
				$sql = '
				SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
				FROM PDM_Compte a
				WHERE a.idCompte IN
				(SELECT ad.idCompte FROM PDM_distribution ad, PDM_distributions d WHERE ad.id_distribution=d.id_distribution AND d.date_distrib>CURRENT_TIMESTAMP)
				ORDER BY a.nom 
				';
				break;
			case 'contratsExpires' :
				$sql = "
				SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
				FROM spipComptes a
				WHERE a.idCompte NOT IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.date_distrib>CURRENT_TIMESTAMP)
				AND a.idCompte IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND (d.id_contrat={$_SESSION['id_contrat'][0]} OR d.id_contrat={$_SESSION['id_contrat'][1]}))
				ORDER BY a.nom 
				";

				break;
			case 'contratsAnciens' :
/*var_dump($contrat);
				$sql = "
				SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
				FROM spipComptes a
				WHERE a.idCompte NOT IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND (d.id_contrat={$_SESSION['id_contrat'][0]} OR d.id_contrat={$_SESSION['id_contrat'][1]}))
				AND a.idCompte IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.id_contrat<{$_SESSION['id_contrat'][0]})
				ORDER BY a.nom 
				";

				$sql = "
				SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
				FROM spipComptes a
				WHERE a.idCompte NOT IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.id_contrat>={$_SESSION['id_contrat'][0]})
				AND a.idCompte IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.id_contrat<{$_SESSION['id_contrat'][0]})
				ORDER BY a.nom 
				";
				break;
			case 'sansContrat' :
				$sql = '
				SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
				FROM spipComptes a 
				WHERE a.idCompte NOT IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution)
				ORDER BY a.nom
				';
				break;
*/
			
		}
		
//die($sql);
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$ligne = FALSE;
		$row = FALSE;
		$n = 0;
		while ($laLigne = mysqli_fetch_assoc($res)) {
			$ligne[] = $laLigne;
		}
//var_dump($ligne);die();		
		// contruction des lignes du tableau
		if ($ligne) {
			$idComptePrecedent = 0;
			$idRow = 0;
			foreach ($ligne AS $uneLigne) {
				if ($uneLigne['idCompte']!= $idComptePrecedent) {
					$idComptePrecedent = $uneLigne['idCompte'];
					$idRow++;
					$n++;
					$row[$idRow]['idCompte'] = $uneLigne['idCompte'];
					if ($uneLigne['adherent']=='oui') $row[$idRow]['adherent'] = 'adhérent';
					else $row[$idRow]['adherent'] = '&nbsp;';
					
					$row[$idRow]['commentaire'] = $uneLigne['commentaire'];
					$row[$idRow]['commentaire'] .= ajoutAbsences($row[$idRow]['idCompte']);
					
					$row[$idRow]['nom'] = '';
					$row[$idRow]['telephone'] = '';
					$row[$idRow]['courriel'] = '';
				}	
				if ($uneLigne['idPersonne']==$uneLigne['titulairePrincipalId']) {
					$row[$idRow]['nomTri'] = $uneLigne['nom'];
					$row[$idRow]['nom'] = '<b>'.$uneLigne['nom'].'</b>'.$row[$idRow]['nom'];
					$row[$idRow]['telephone'] = $uneLigne['telephone'].$row[$idRow]['telephone'];
					$row[$idRow]['courriel'] = $uneLigne['courriel'].$row[$idRow]['courriel'];
				}
				else {  
					$row[$idRow]['nom'] .= '<br>'.$uneLigne['nom'];
					$row[$idRow]['telephone'] .= '<br>'.$uneLigne['telephone'];
					$row[$idRow]['courriel'] .= '<br>'.$uneLigne['courriel'];
				}
			}
			// tri des lignes selon le nom :!!!!!!!!!!!!!!!ça marche pas à cause du <b> !!!!!!!!!!!!!!!!!!!
			$nomTri  = array_column($row, 'nomTri');
			array_multisort($nomTri, SORT_ASC, $row);
		}
//var_dump($row);die();
 
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
			<table border="0" width="100%" >
				<tbody>
					<tr>
						<td class="td0" style="width: 390px; padding: 0px; vertical-align: middle; text-align: left; padding-left: 10px; color: red;"> 
							<?php echo('<p>'.$message.'</p>');?> 
						</td>
						<td class="td0" style="vertical-align: middle; text-align: center; "> 
							afficher : 
							<select name="qui" onChange="document.getElementById('formAction').submit(); ">
								<option value="tousComptes" <?php if ($_SESSION['quiAfficher']=='tousComptes') echo ('selected="selected"');?>>Tous les comptes</option>
								<option value="adherents" <?php if ($_SESSION['quiAfficher']=='adherents') echo ('selected="selected"');?>>Comptes des adhérents</option>
								<option value="nonAdherents" <?php if ($_SESSION['quiAfficher']=='nonAdherents') echo ('selected="selected"');?>>Comptes des non-adhérents</option>
<!--
								<option value="contratsExpires" <?php if ($_SESSION['quiAfficher']=='contratsExpires') echo ('selected="selected"');?>>Comptes avec contrats de l'année expirés</option>
								<option value="contratsAnciens" <?php if ($_SESSION['quiAfficher']=='contratsAnciens') echo ('selected="selected"');?>>Comptes avec contrats anciens non renouvelés</option>
								<option value="sansContrat" <?php if ($_SESSION['quiAfficher']=='sansContrat') echo ('selected="selected"');?>>Comptes sans contrat</option>
-->
							</select>
							<?php echo(" $n affichés");?>
						</td>
					</tr>
				</tbody>
			</table>

		</div>
		
		<div id='content' style='overflow: auto;'>
			<table class='hoverTable' border='1' style='width: 100%; margin: auto;'>
				<tbody>
					<tr>
						<th>
							&nbsp;
						</th>
						<th>
							nom
						</th>
						<th>
							téléphone
						</th>
						<th>
							courriel
						</th>
						<th>
							adhérent
						</th>
						<th>
							commentaires
						</th>
					</tr>
	<?php

		if ($row) {
			foreach ($row AS $aRow) {
?>
					<tr>
						<td>
							<?php echo("<input name='select' value='{$aRow['idCompte']}' form='formAction' type='radio' id='radio{$aRow['idCompte']}'>");?>
						</td>
						<td >
							<?php echo($aRow['nom']); ?>
						</td>
						<td >
							<?php echo($aRow['telephone']); ?>
						</td>
						<td >
							<?php echo($aRow['courriel']); ?>
						</td>
						<td >
							<?php echo($aRow['adherent']); ?>
						</td>
						<td >
							<?php echo($aRow['commentaire']); ?>
						</td>
					</tr>
<?php
			}
		}
	?>
				</tbody>
			</table>
			<hr>
		</div>
		
		<div id='bas'>
			<hr>
			<p style="text-align: center; font-weight:bold;">
<?php 
	if($row) {
?>
				pour le compte sélectionné : 
				<input style="font-weight:normal; "  name="afficherLeCompte" value="Éditer" type="button" title="éditer le compte sélectionné" onClick="
				if (CheckRadio('select')) {
					document.getElementById('newAction').value='afficherLeCompte'; document.getElementById('formAction').submit();
				}
				else alert('Veuillez sélectionner un compte à modifier.');
				">
				<input style="font-weight:normal; "  name="supprimerCompte" value="Supprimer" type="button" title="supprimer le compte sélectionné" onClick="
				if (CheckRadio('select')) {
					document.getElementById('newAction').value='supprimerCompte'; document.getElementById('formAction').submit();
				}
				else alert('Veuillez sélectionner un compte à supprimer.');
				">
				
				&nbsp; &nbsp; &nbsp; &nbsp; 
<?php 
	}
?>
				
				<input style="font-weight:bold; "  name="ajouterCompte" value="Ajouter un compte" type="button" title="créer un nouveau compte" onClick=" document.getElementById('newAction').value='ajouterCompte'; document.getElementById('formAction').submit();
				">
				
				<input style="font-weight:bold; "  name="exportComptes" value="Export CSV" type="button" title="exporter les comptes affichés au format CSV" onClick=" document.getElementById('newAction').value='exportComptes'; document.getElementById('formAction').submit();
				">

				<input style="font-weight:bold; "  name="retour" value="Retour" type="button" title="retourner au menu principal" onClick=" document.getElementById('newAction').value='retour'; document.getElementById('formAction').submit();
				">
				
			</p>
			<hr>
		</div>
		
		</form>
	</body>
		<script>
			$(document).ready(
				function(){
					// redimmensionner
					redim();
					// focus et select le compte sélectionné auparavant
<?php 
	if ($idCompte) {
		$codeJS = <<<EOT
					document.getElementById('radio$idCompte').focus();
					document.getElementById('radio$idCompte').checked = true
EOT;
		echo $codeJS;
	}
?>
				}
			);
		</script>
</html>
<?php
	} // fin afficherListeComptes

	function afficherLeCompte($message) {
		$idCompte = $_POST['select'];
		if ($_SESSION['scriptOrigine']=='distributions.php') $idDistribution = $_POST['idDistribution']; 
		else $idDistribution = '';
//		$message = "<br> Attention ! La suppression du titulaire principal entraîne la suppression du compte et de tous ses co-titulaires.<br>Ce n'est possible qu'en l'absence de tout contrat et de tout chèque enregistré pour ce compte.<br><br>".$message;
		// infos sur le compte et ses titulaires
		$sql = "SELECT idCompte, adherent, titulairePrincipalId, commentaire FROM PDM_compte WHERE idCompte=$idCompte";
//die($sql);
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$unCompte = mysqli_fetch_assoc($res);
		
		$unCompte['commentaire'] = htmlentities($unCompte['commentaire']);
		
		if ($unCompte['adherent']=='oui') $adherentChecked = 'checked="checked"';
		else $adherentChecked = '';
		
		// infos les titulaires
		$sql = "SELECT idPersonne, nom, telephone, courriel FROM PDM_personne WHERE compteId=$idCompte ORDER BY nom";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while ($unePersonne = mysqli_fetch_assoc($res)) {
			$titulaire[$i] = $unePersonne;
			$titulaire[$i]['nom'] = htmlentities($titulaire[$i]['nom']);
			if ($titulaire[$i]['idPersonne']==$unCompte['titulairePrincipalId']) $unCompte['nom'] = $titulaire[$i]['nom'];
			$i++;
		}
//var_dump($titulaire); die();
	
		$GLOBALS['titrePage'] = "Modifier le compte {$unCompte['nom']}";
?>
<!DOCTYPE html>
<html lang='fr-fr'>
<?php	
		include('inc/headHTML.inc.php');
?>
	<body style='font-family: sans-serif; font-size:small; padding: 0px;'  >

		<div id='haut'>
		<?php
			include('inc/divEnTete.inc.php');
		?>
			<div id='allerA' style="width: 100%; color: black;">
				<table border="0"  style="width: 100%; ">
					<tbody>
						<tr>
							<td align="center" style="background-color: #009688;">
								<button type="button" onclick="document.getElementById('attributs').scrollIntoView(true);"> Attributs </button>
								<button type="button" onclick="document.getElementById('volontariat').scrollIntoView(true);"> Volontariat </button>
								<button type="button" onclick="document.getElementById('reports').scrollIntoView(true);"> Reports </button>
								<button type="button" onclick="document.getElementById('ajouts').scrollIntoView(true);"> Ajouts </button>
								<button type="button" onclick="document.getElementById('contrats').scrollIntoView(true);"> Contrats </button>
								<button type="button" onclick="document.getElementById('cheques').scrollIntoView(true);"> Chèques </button>
								<button type="button" onclick="document.getElementById('courriels').scrollIntoView(true);"> Courriels </button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<div id='content' style='overflow: auto;'>
			<div id="attributs">
			</div>

			<div id="volontariat">
				<hr>
				<h2>Volontariat</h2>

				<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
				Fin Volontariat
			</div>
		
			<div id="reports">
				<hr>
				<h2>Reports</h2>
				<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
				Fin Reports
			</div>
		
			<div id="ajouts">
				<hr>
				<h2>Ajouts</h2>
				<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
				Fin Ajouts
			</div>
		
			<div id="contrats">
				<hr>
				<h2>Contrats</h2>
				<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
				Fin Contrats
			</div>
		
			<div id="cheques">
				<hr>
				<h2>Chèques</h2>
				<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
				Fin Chèques
			</div>

			<div id="courriels">
				<hr>
				<h2>Courriels</h2>
				<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
				Fin Courriels
			</div>
			
			<hr>
		</div> <!-- content -->
		
		<div id='bas'>
			<form method='POST' action='' >
				<input type='hidden' name='newAction' value='abandonnerModificationCompte'>
				<input type='hidden' name='idCompte' value='<?php echo $idCompte;?>'>
				<input type='hidden' name='idDistribution' value='<?php echo $idDistribution;?>'>
				<input type='hidden' name='message' value=''>

				<hr>
				<p style="text-align: center; font-weight:bold;">
					<input style="font-weight:bold; "  name="retour" value="Retour" type="button" onClick="this.form.action='<?php echo $_SESSION['scriptOrigine']; ?>'; this.form.submit();
					">

				</p>
				<hr>
			</form>
		</div>
		
	</body>
		<script>
			function scroll() { 
				document.getElementById('contrats').scrollIntoView(true);
			} 

			$(document).ready(
				function(){
					// redimmensionner
					redim();
					initialiserCompte(<?php echo $_POST['select'] ?>);
<?php
	if (isset($_POST['divId'])) {
?>
					setTimeout(scroll, 500);
<?php
			}
?>
				}
			);
		</script>
</html>
<?php
	} // fin afficherLeCompte
	
	function modifierLeCompteAttributs() {
		
	}
	
	function modifierLeCompteVolontriat() {
		
	}
	
	function modifierLeCompteReportAjout() {
		
	}

	function modifierLeCompteContrats() {
		
	}
	
	function modifierLeCompteContratCheques() {
		
	}
	
	function enregistrerLeCompteAttributs() {
		
	}
	
	function enregistrerLeCompteVolontriat() {
		
	}
	
	function enregistrerLeCompteReportAjout() {
		
	}
	
	function enregistrerLeCompteContrats() {
		
	}
	
	function energistrerLeCompteContratCheques() {
		
	}

	function enregistrerModificationCompte() {
//var_dump($_POST); die();
		$message = '';
		// traitement des modifications du compte (adherent, principal, commentaire)
		$idCompte = $_POST['idCompte'];
		if (isset($_POST['adherent'])) $valeurAdherent = 'oui';
		else $valeurAdherent = 'non';
		$valeurTitulairePrincipalId = $_POST['principal'];
		$valeurCommentaire = $_POST['commentaire'];
		
		// contrôle de l'existence du nom du titulaire principal
		if ($_POST['nom'][$valeurTitulairePrincipalId]!='') {
			$sql = "UPDATE PDM_compte SET adherent = '$valeurAdherent', titulairePrincipalId = $valeurTitulairePrincipalId, commentaire = '$valeurCommentaire', dateModification = CURRENT_TIME() WHERE idCompte = $idCompte;";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			$nomTitulairePrincipal = $_POST["nom"][$valeurTitulairePrincipalId];
			$message .= "Le compte $nomTitulairePrincipal a été mis à jour. ";
		}
		else { // le titulaire principal n'a pas de nom !!! => on quitte
			$message .= "Le compte n'a pas été mis à jour car le titulaire principal proposé n'avait pas de nom ! ";
			afficherListeComptes($message);
			return;
		}
		
		
		// traitement des modifications des personnes
		// pour chaque personne du compte
		$sql = "SELECT idPersonne FROM PDM_personne WHERE compteId={$_POST['idCompte']} ";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while ($unePersonne = mysqli_fetch_assoc($res)) {
			$idPersonne = $unePersonne['idPersonne'];
			$nom = $_POST['nom'][$idPersonne];
			$telephone = $_POST['telephone'][$idPersonne];
			$courriel = $_POST['courriel'][$idPersonne];
			$compteId = $_POST['idCompte'];
			$sql1 = "
			UPDATE PDM_personne SET nom='$nom', telephone='$telephone', courriel='$courriel', compteId=$compteId WHERE idPersonne=$idPersonne
			";

			$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
		}
		
		
		// traitement de l'ajout éventuel d'un co-titulaire
		if ($_POST['nouveauNom']!='') {
			$sql = "
			INSERT INTO PDM_personne(idPersonne, nom, telephone, courriel, compteId) VALUES (NULL,'{$_POST['nouveauNom']}','{$_POST['nouveauTelephone']}','{$_POST['nouveauCourriel']}',{$_POST['idCompte']})
			";
//die($sql);
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			$message .= "\n\r{$_POST['nouveauNom']} a été ajouté au compte comme co-titulaire.";
		}
		
		
		// traitement des suppressions éventuelles
		if (isset($_POST['supprimer'])) {
			foreach ($_POST['supprimer'] AS $idPersonneSuppimer => $personneSupprimer) {
				
				// si suppression du titulaire prinicpal
				if ($idPersonneSuppimer==$valeurTitulairePrincipalId) {
					$suppressionAutorisee = TRUE;
					// ? chèque Ajout
					$sql = "
					SELECT idChequeAjout FROM PDM_chequeAjout, PDM_ajout, PDM_contrat WHERE ajoutId=idAjout AND contratId=idContrat AND compteId=$idCompte
					";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					if (mysqli_fetch_assoc($res)) $suppressionAutorisee = FALSE;
					// ? chèque contrat
					$sql = "
					SELECT idChequeContrat FROM PDM_chequeContrat, PDM_contrat WHERE contratId=idContrat AND compteId=$idCompte
					";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					if (mysqli_fetch_assoc($res)) $suppressionAutorisee = FALSE;
					// ? chèque cotisation
					$sql = "
					SELECT idChequeCotisation FROM PDM_chequeCotisation WHERE compteId=$idCompte
					";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					if (mysqli_fetch_assoc($res)) $suppressionAutorisee = FALSE;
					
					// ? contrat
					$sql = "
					SELECT idContrat FROM PDM_contrat WHERE compteId=$idCompte
					";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					if (mysqli_fetch_assoc($res)) $suppressionAutorisee = FALSE;
					
					if ($suppressionAutorisee) {
						// suppression du compte, des co-titulaires, des occasionnels, des volontaires
						//tous les co-titulaires
						$sql = "
						SELECT idPersonne FROM PDM_personne WHERE compteId=$idCompte
						";
						$res = mysqli_query($GLOBALS['lkId'],$sql);
						while ($personne = mysqli_fetch_assoc($res)) {
							$idPersonne = $personne['idPersonne'];
							$sql1 = "DELETE FROM PDM_volontaire WHERE personneId=$idPersonne";
							$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
							$sql1 = "DELETE FROM PDM_occasionnel WHERE compteeId=$idCompte";
							$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
							$sql1 = "DELETE FROM PDM_personne WHERE idPersonne=$idPersonne";
							$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
						}
						// le compte
						$sql1 = "DELETE FROM PDM_compte WHERE idCompte=$idCompte";
						$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
						$message .= "\r\nLe compte $nomTitulairePrincipal et tous les co-titulaires ont été supprimés.";
					}
					else {
						$message .= "\r\nLa suppression du titulaire principal n'a pas été effectuée car il existe au moins un chèque ou contrat pour ce compte.";
					}
				}
				// sinon co-titulaire
				else {
					$sql = "
					DELETE FROM PDM_personne WHERE idPersonne=$idPersonneSuppimer
					";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$sql = "
					DELETE FROM PDM_volontaire WHERE idVolontaire=$idPersonneSuppimer
					";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$nomSupprimer = $_POST['nom'][$idPersonneSuppimer];
					$message .= "<br>$nomSupprimer a été supprimé.";
				}
			}
		}
	
		afficherListeComptes($message);
	} // fin enregistrerModificationCompte
	
	function supprimerCompte() {
		$idCompte = $_POST['select'];
		$message = "";
		// recherche du nom du compte
		$sql = "SELECT titulairePrincipalId FROM PDM_compte WHERE idCompte=$idCompte";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$titulaire = mysqli_fetch_assoc($res);
		$idTitulairePrincipal = $titulaire['titulairePrincipalId'];
		$sql = "SELECT nom FROM PDM_personne WHERE idPersonne=$idTitulairePrincipal";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$personne = mysqli_fetch_assoc($res);
		$nomCompte = htmlentities($personne['nom']);
		$suppressionAutorisee = TRUE;
		// ? chèque Ajout
		$sql = "
		SELECT idChequeAjout FROM PDM_chequeAjout, PDM_ajout, PDM_contrat WHERE ajoutId=idAjout AND contratId=idContrat AND compteId=$idCompte
		";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		if (mysqli_fetch_assoc($res)) $suppressionAutorisee = FALSE;
		// ? chèque contrat
		$sql = "
		SELECT idChequeContrat FROM PDM_chequeContrat, PDM_contrat WHERE contratId=idContrat AND compteId=$idCompte
		";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		if (mysqli_fetch_assoc($res)) $suppressionAutorisee = FALSE;
		// ? chèque cotisation
		$sql = "
		SELECT idChequeCotisation FROM PDM_chequeCotisation WHERE compteId=$idCompte
		";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		if (mysqli_fetch_assoc($res)) $suppressionAutorisee = FALSE;
		
		// ? contrat
		$sql = "
		SELECT idContrat FROM PDM_contrat WHERE compteId=$idCompte
		";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		if (mysqli_fetch_assoc($res)) $suppressionAutorisee = FALSE;
		
		if ($suppressionAutorisee) {
			// suppression du compte, des co-titulaires, des occasionnels, des volontaires
			//tous les co-titulaires
			$sql = "
			SELECT idPersonne FROM PDM_personne WHERE compteId=$idCompte
			";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			while ($personne = mysqli_fetch_assoc($res)) {
				$idPersonne = $personne['idPersonne'];
				$sql1 = "DELETE FROM PDM_volontaire WHERE personneId=$idPersonne";
				$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
				$sql1 = "DELETE FROM PDM_occasionnel WHERE compteeId=$idCompte";
				$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
				$sql1 = "DELETE FROM PDM_personne WHERE idPersonne=$idPersonne";
				$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
			}
			// le compte
			$sql1 = "DELETE FROM PDM_compte WHERE idCompte=$idCompte";
			$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
			$message .= "Le compte $nomCompte et tous les co-titulaires ont été supprimés.";
		}
		else {
			$message .= "La suppression du compte $nomCompte n'a pas été effectuée car il existe au moins un chèque ou contrat pour ce compte.";
		}
		afficherListeComptes($message);
	} // fin function supprimerCompte

	
	function modifierContrats($idCompte, $ajouterContrat, $message) {
/*
SELECT date, DATE_ADD(date, INTERVAL 26 WEEK) AS fin FROM PDM_distribution WHERE idDistribution=1 
CURDATE()
*/
//		$id = $_POST['select'];

		// année courante : id nom date de première et de dernière distribution
		$sql = "SELECT idAnnee, nom, datePremiereDistribution FROM PDM_annee WHERE idAnnee={$_SESSION['anneeCouranteId']} ORDER BY nom";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$uneAnnee=mysqli_fetch_assoc($res);
		$annee[$uneAnnee['idAnnee']] = $uneAnnee;
		$anneeCourante['id'] = $uneAnnee['idAnnee'];
		$anneeCourante['nom'] = $uneAnnee['nom'];
		$anneeCourante['datePremiereDistribution'] = $uneAnnee['datePremiereDistribution'];
		$anneeCourante['dateDerniereDistribution'] = date("Y-m-d", strtotime($anneeCourante['datePremiereDistribution']." +51 week"));

		// année suivante : id nom date de première et de dernière distribution
		$sql = "SELECT idAnnee, nom, datePremiereDistribution FROM PDM_annee WHERE idAnnee={$anneeCourante['id']}+1 ORDER BY nom";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$uneAnnee=mysqli_fetch_assoc($res);
		$annee[$uneAnnee['idAnnee']] = $uneAnnee;
		$anneeSuivante['id'] = $uneAnnee['idAnnee'];
		$anneeSuivante['nom'] = $uneAnnee['nom'];
		$anneeSuivante['datePremiereDistribution'] = $uneAnnee['datePremiereDistribution'];
		$anneeSuivante['dateDerniereDistribution'] = date("Y-m-d", strtotime($anneeSuivante['datePremiereDistribution']." +51 week"));
		
		// $estDernierMoisAnneeCourante TRUE si on est dans le dernier mois de l'année en cours ; on peut alors ajouter un contrat pour l'année suivante $anneeCourante['datePremiereDistribution']
		$dateUnMoisAvant = date("Y-m-d", strtotime($anneeCourante['dateDerniereDistribution']." -1 month"));
		$estDernierMoisAnneeCourante = ($_SESSION['auj']>$dateUnMoisAvant) && ($_SESSION['auj']<$anneeSuivante['datePremiereDistribution']);
//echo("dateUnMoisAvant =  $dateUnMoisAvant  ; estDernierMoisAnneeCourante = $estDernierMoisAnneeCourante");
//die();
		
		// statitiques répartition par semaine semestre et année
		// année courante $stat['C...']
			// année courante légumes $stat['CL..']
				// année courante légumes semestre 1 $stat['CL1.']
					// année courante légumes semestre 1 semaine A $stat['CL1A']
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeCourante['id']} AND PDM_contrat.typeContratId=1 AND (periodeContratId=1 OR periodeContratId=2) AND (frequenceContratId=1 OR frequenceContratId=2)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['CL1A'] = $uneStat['nb'];
					// année courante légumes semestre 1 semaine B 
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeCourante['id']} AND PDM_contrat.typeContratId=1 AND (periodeContratId=1 OR periodeContratId=2) AND (frequenceContratId=1 OR frequenceContratId=3)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['CL1B'] = $uneStat['nb'];
				// année courante légumes semestre 2 $stat['CL2.']
					// année courante légumes semestre 2 semaine A
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeCourante['id']} AND PDM_contrat.typeContratId=1 AND (periodeContratId=1 OR periodeContratId=3) AND (frequenceContratId=1 OR frequenceContratId=2)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['CL2A'] = $uneStat['nb'];
					// année courante légumes semestre 2 semaine B 
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeCourante['id']} AND PDM_contrat.typeContratId=1 AND (periodeContratId=1 OR periodeContratId=3) AND (frequenceContratId=1 OR frequenceContratId=3)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['CL2B'] = $uneStat['nb'];
					
					
			// année courante oeufs $stat['CO..']
				// année courante oeufs semestre 1 $stat['CO1.']
					// année courante oeufs semestre 1 semaine A $stat['CO1A']
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeCourante['id']} AND PDM_contrat.typeContratId=2 AND (periodeContratId=1 OR periodeContratId=2) AND (frequenceContratId=1 OR frequenceContratId=2)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['CO1A'] = $uneStat['nb'];
					// année courante oeufs semestre 1 semaine B 
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeCourante['id']} AND PDM_contrat.typeContratId=2 AND (periodeContratId=1 OR periodeContratId=2) AND (frequenceContratId=1 OR frequenceContratId=3)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['CO1B'] = $uneStat['nb'];
				// année courante oeufs semestre 2 $stat['CO2.']
					// année courante oeufs semestre 2 semaine A
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeCourante['id']} AND PDM_contrat.typeContratId=2 AND (periodeContratId=1 OR periodeContratId=3) AND (frequenceContratId=1 OR frequenceContratId=2)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['CO2A'] = $uneStat['nb'];
					// année courante oeufs semestre 2 semaine B 
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeCourante['id']} AND PDM_contrat.typeContratId=2 AND (periodeContratId=1 OR periodeContratId=3) AND (frequenceContratId=1 OR frequenceContratId=3)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					
					
		// année suivante $stat['S...']
			// année suivante légumes $stat['SL..']
				// année suivante légumes semestre 1 $stat['SL1.']
					// année suivante légumes semestre 1 semaine A $stat['SL1A']
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeSuivante['id']} AND PDM_contrat.typeContratId=1 AND (periodeContratId=1 OR periodeContratId=2) AND (frequenceContratId=1 OR frequenceContratId=2)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['SL1A'] = $uneStat['nb'];
					// année suivante légumes semestre 1 semaine B 
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeSuivante['id']} AND PDM_contrat.typeContratId=1 AND (periodeContratId=1 OR periodeContratId=2) AND (frequenceContratId=1 OR frequenceContratId=3)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['SL1B'] = $uneStat['nb'];
				// année suivante légumes semestre 2 $stat['SL2.']
					// année suivante légumes semestre 2 semaine A
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeSuivante['id']} AND PDM_contrat.typeContratId=1 AND (periodeContratId=1 OR periodeContratId=3) AND (frequenceContratId=1 OR frequenceContratId=2)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['SL2A'] = $uneStat['nb'];
					// année suivante légumes semestre 2 semaine B 
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeSuivante['id']} AND PDM_contrat.typeContratId=1 AND (periodeContratId=1 OR periodeContratId=3) AND (frequenceContratId=1 OR frequenceContratId=3)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['SL2B'] = $uneStat['nb'];
					
					
			// année suivante oeufs $stat['SO..']
				// année suivante oeufs semestre 1 $stat['SO1.']
					// année suivante oeufs semestre 1 semaine A $stat['SO1A']
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeSuivante['id']} AND PDM_contrat.typeContratId=2 AND (periodeContratId=1 OR periodeContratId=2) AND (frequenceContratId=1 OR frequenceContratId=2)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['SO1A'] = $uneStat['nb'];
					// année suivante oeufs semestre 1 semaine B 
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeSuivante['id']} AND PDM_contrat.typeContratId=2 AND (periodeContratId=1 OR periodeContratId=2) AND (frequenceContratId=1 OR frequenceContratId=3)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['SO1B'] = $uneStat['nb'];
				// année suivante oeufs semestre 2 $stat['SO2.']
					// année suivante oeufs semestre 2 semaine A
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeSuivante['id']} AND PDM_contrat.typeContratId=2 AND (periodeContratId=1 OR periodeContratId=3) AND (frequenceContratId=1 OR frequenceContratId=2)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['SO2A'] = $uneStat['nb'];
					// année suivante oeufs semestre 2 semaine B 
					$sql = "SELECT SUM(quantiteNumerique) AS nb FROM PDM_contrat, PDM_quantiteContrat WHERE quantiteContratId=idquantiteContrat AND anneeId={$anneeSuivante['id']} AND PDM_contrat.typeContratId=2 AND (periodeContratId=1 OR periodeContratId=3) AND (frequenceContratId=1 OR frequenceContratId=3)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$uneStat=mysqli_fetch_assoc($res);
					$stat['SO2B'] = $uneStat['nb'];

		$message .= "<br>Attention ! La modification de la date de début et la suppression d'un contrat en cours doivent être limitées aux cas d'erreur de saisie.<br><br>";
		
		// infos sur le compte et ses titulaires
		$sql = "SELECT idCompte, titulairePrincipalId FROM PDM_compte WHERE idCompte=$idCompte";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$unCompte = mysqli_fetch_assoc($res);
		
		// nom du compte
		$sql = "SELECT idPersonne, nom FROM PDM_personne WHERE idPersonne={$unCompte['titulairePrincipalId']}";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$titulaire = mysqli_fetch_assoc($res);
		$nomCompte = $titulaire['nom'];
		
		// tous les contrats quelle que soit l'année
		$sql = "SELECT idContrat, compteId, anneeId, typeContratId, quantiteContratId, frequenceContratId, periodeContratId, dateDebut, dateFin, valide, nom FROM PDM_annee, PDM_contrat WHERE idAnnee=anneeId AND compteId=$idCompte ORDER BY idContrat";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($unContrat = mysqli_fetch_assoc($res)) $contrat[] = $unContrat;
		
		// les intitulés et select
		// =======================
		// type
		$sql = "SELECT idTypeContrat, type FROM PDM_typeContrat ";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($uneLigne = mysqli_fetch_assoc($res)) $type[$uneLigne['idTypeContrat']] = $uneLigne['type'];
		$select['type']= "
		    <select name=\"nouveau[type]\" onChange=\"var selectQT1=document.getElementById('selectQT1'); var selectQT2=document.getElementById('selectQT2'); if (this.value==1) {selectQT1.style.display = 'inline'; selectQT2.style.display = 'none';} else {selectQT2.style.display = 'inline'; selectQT1.style.display = 'none';}\">
      ";
      foreach ($type AS $id => $valeur) {
			if ($id==1) $selected = "selected='selected'";
			else $selected = "";
			$select['type'] .= "
				<option value='$id' $selected >$valeur</option>
			";
		}
      $select['type'] .= "
			</select>
		";

		// quantite
		$sql = "SELECT idQuantiteContrat, typeContratId, quantite FROM PDM_quantiteContrat ";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($uneLigne = mysqli_fetch_assoc($res)) {
			$quantite[$uneLigne['idQuantiteContrat']] = $uneLigne['quantite'];
			$ligne[$uneLigne['idQuantiteContrat']]['type'] = $uneLigne['typeContratId'];
			$ligne[$uneLigne['idQuantiteContrat']]['quantite'] = $uneLigne['quantite'];
		}
		// type 1 : légumes ; type 2 : oeufs
		
		$select[1]['quantite'] = "
		    <select name='nouveau[QT1]'>
      ";
 		$select[2]['quantite'] = "
		    <select name='nouveau[QT2]'>
      ";
     foreach ($ligne AS $id => $uneLigne) {
//				if ($id==1) $selected = "selected='selected'";
//				else $selected = "";
				$select[$uneLigne['type']]['quantite'] .= "
					<option value='$id' >{$uneLigne['quantite']}</option>
				";
		}
      $select[1]['quantite'] .= "
			</select>
		";
      $select[2]['quantite'] .= "
			</select>
		";

		// annee
		// voir #29

		// periode
		$sql = "SELECT idPeriodeContrat, periode FROM PDM_periodeContrat ";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($uneLigne = mysqli_fetch_assoc($res)) $periode[$uneLigne['idPeriodeContrat']] = $uneLigne['periode'];
		$select['periode'] = "
		    <select name='nouveau[periode]'>
      ";
      foreach ($periode AS $id => $valeur) {
			if ($id==1) $selected = "selected='selected'";
			else $selected = "";
			$select['periode'] .= "
				<option value='$id' $selected >$valeur</option>
			";
		}
      $select['periode'] .= "
			</select>
		";

		// frequence
		$sql = "SELECT idFrequenceContrat, frequence FROM PDM_frequenceContrat ";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($uneLigne = mysqli_fetch_assoc($res)) $frequence[$uneLigne['idFrequenceContrat']] = $uneLigne['frequence'];
		$select['frequence'] = "
		    <select name='nouveau[frequence]'>
      ";
      foreach ($frequence AS $id => $valeur) {
			if ($id==1) $selected = "selected='selected'";
			else $selected = "";
			$select['frequence'] .= "
				<option value='$id' $selected >$valeur</option>
			";
		}
      $select['frequence'] .= "
			</select>
		";
		
		// prix
		$sql = "SELECT idPrixAnneeTypeContrat, anneeId, typeContratId, prix FROM PDM_prixAnneeTypeContrat";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($uneLigne = mysqli_fetch_assoc($res)) $prix[$uneLigne['typeContratId']][$uneLigne['anneeId']] = $uneLigne['prix'];
		
		// calcul des contrats selon semaine (et semestre) pour l'année courante $anneeCourante'
		
		$GLOBALS['titrePage'] = "Modifier les contrats du compte $nomCompte";
?>
<!DOCTYPE html>
<html lang=fr-fr>
<?php	
		include("inc/headHTML.inc.php");
?>
	<body style="font-family: sans-serif; font-size:small; padding: 0px;"  >

		<form method=POST action=comptes.php id=formAction >
			<input type=hidden name=newAction id=newAction value='enregistrerContrats'>
			<input type=hidden name=nouveau[annee] id=nouveau[annee] value='<?php echo $anneeCourante['id'];?>'>
			<input type=hidden name=idCompte id=idCompte value='<?php echo($idCompte);?>'>
			<input type=hidden name=message id=message value=''>

		<div id=haut>
		<?php
			include("inc/divEnTete.inc.php");
		?>
			<div id=message style="width: 100%; display: <?php if ($message!='') echo('inline'); else echo('none'); ?>;">
				<table border="0"  style="width: 100%; ">
					<tbody>
						<tr>
							<td style="width: 100%; padding: 0px; vertical-align: middle; text-align: left; padding-left: 10px; color: red;"> 
								<?php echo($message);?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<div id=content >
		<br><br>
		<hr>
		<br>
			<table class=hoverTable border=1 style="width: 100%;" margin: auto;>
				<tbody>
					<tr>
						<td style="font-weight: bold; font-size: medium;"  colspan="10">
							Modifier les contrats existants
						</td>
					</tr>
					<tr>
						<th style="">
							type
						</th>
						<th style="">
							quantité
						</th>
						<th style="">
							année
						</th>
						<th style="">
							période
						</th>
						<th style="">
							fréquence
						</th>
						<th style="">
							date début
						</th>
						<th style="">
							date fin
						</th>
						<th style="">
							prix total
						</th>
						<th>
							validé
						</th>
						<th style="">
							à supprimer
						</th>
						<td rowspan="100" style="text-align: center; font-weight: bold; width: 180px;">
							<input style="font-weight:bold; "  name="enregistrerModificationContrats" value="Enregistrer" type="button" onClick="document.getElementById('newAction').value='enregistrerModificationContrats';   document.getElementById('formAction').submit();">
						</td>
					</tr>
<?php
	if (isset($contrat)) { // si le nbre de contrats est supérieur à 0
		foreach ($contrat as $i =>$unContrat) { 
		// pour chaque contrat : une ligne de tableau
			$aujourdhui = date('Y-m-d',time());
	//		$strDans7Jours = time()+(3600*24*7);
	//		$dans7Jours = date('Y-m-d',$strDans7Jours);
			$premierMercrediPostCommande = premierMercrediPostCommande($aujourdhui);
			// date fin : toutes les dates possibles en fonction de l'année d'aujourd'hui de la période, de  la fréquence
			// $estSupprimableModifiable TRUE si la date du jour est avant le dimanche précédent la date de début (3 jours avant) la suppression et la modification de date de début d'un contrat sont alors possible
			$dDPDD = strtotime($unContrat['dateDebut']) - (3*24-1)*60*60; // 25 heures à cause de l'heure d'hiver
			$dateDimanchePrecedentDateDebut = date("Y-m-d",$dDPDD);
			$estSupprimableModifiable = $_SESSION['auj']<$dateDimanchePrecedentDateDebut;
//die($estSupprimableModifiable);			
			
	/*
			// condition semaine selon fréquence
			switch ($unContrat['frequenceContratId']) {
				case 1 :
					$whereSemaine = "";
					break;
				case 2 :
					$whereSemaine = "AND semaine = 'A'";
					break;
				case 3 :
					$whereSemaine = "AND semaine = 'B'";
					break;
			}
			// condition semestre ou année
			switch ($unContrat['periodeContratId']) {
				case 1 :
					$wherePeriode = "";
					break;
				case 2 :
					$wherePeriode = "AND semestre = '1'";
					break;
				case 3 :
					$wherePeriode = "AND semestre = '2'";
					break;
			}
			// condition première date possible >= dans7Jours
			$wherePremiereDatePossible = "AND date>='$premierMercrediPostCommande'";
			
			// liste des dates possibles comme dates de fin
			$sql = "
			SELECT date FROM PDM_distribution WHERE anneeId={$unContrat['anneeId']} $whereSemaine $wherePeriode $wherePremiereDatePossible ORDER BY DATE
			";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			while ($uneLigne = mysqli_fetch_assoc($res)) $dateFinPossible[] = $uneLigne['date'];
	*/		
	//var_dump($dateFinPossible);die();
	
			// construction du select date début
			$dateDebutPossible = tableDateDebutPossible($unContrat);
			$select['dateDebut']= "
				<select name='dateDebut[{$unContrat['idContrat']}]' id='dateDebut[{$unContrat['idContrat']}]'>
			";
			foreach ($dateDebutPossible AS $laDate) {
				if ($laDate['date']==$unContrat['dateDebut'] ) {
					$selected = "selected='selected'";
				}
				else $selected = "";
				$select['dateDebut'] .= "
					<option value='{$laDate['date']}' $selected >{$laDate['date']}</option>
				";
			}
			$select['dateDebut'] .= "
				</select>
			";
	
			// construction du select date fin
			$dateFinPossible = tableDateFinPossible($unContrat);
			$select['dateFin']= "
				<select name='dateFin[{$unContrat['idContrat']}]' id='dateFin[{$unContrat['idContrat']}]'>
			";
			foreach ($dateFinPossible AS $laDate) {
				if ($laDate['date']==$unContrat['dateFin'] ) {
					$selected = "selected='selected'";
				}
				else $selected = "";
				$select['dateFin'] .= "
					<option value='{$laDate['date']}' $selected >{$laDate['date']}</option>
				";
			}
			$select['dateFin'] .= "
				</select>
			";

			// SELECT idContrat, compteId, anneeId, typeContratId, quantiteContratId, frequenceContratId, periodeContratId, dateDebut, dateFin, prix, dateCreation, dateModification FROM PDM_contrat WHERE 1
			// SELECT idDistribution, date, nombreUnites, semaine, semestre, anneeId, dateModification FROM PDM_distribution WHERE 1


			
			// calcul du prix du contrat en fonction des dates de début et de fin
			// prix unitaire en fonction de l'année du type et de la quantité numérique
			$sql = "SELECT prix FROM PDM_prixAnneeTypeContrat WHERE anneeId={$unContrat['anneeId']} AND typeContratId ={$unContrat['typeContratId']}";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			$uneLigne = mysqli_fetch_assoc($res);
			$prixUnitaire = $uneLigne['prix'];
			//quantité numérique
			$sql = "SELECT quantiteNumerique FROM PDM_quantiteContrat WHERE idQuantiteContrat={$unContrat['quantiteContratId']}";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			$uneLigne = mysqli_fetch_assoc($res);
			$quantiteNumerique = $uneLigne['quantiteNumerique'];
			// prix d'une distribution
			$prix = $prixUnitaire*$quantiteNumerique ;
			// prix total du contrat
			$strDebut = strtotime($unContrat['dateDebut']);
			$strFin = strtotime($unContrat['dateFin']);
			$strIntervalle = $strFin-$strDebut;
			if ($unContrat['frequenceContratId']==1) $nbIntervalles = round((($strFin-$strDebut)/(3600*24*7)));
			else $nbIntervalles = round((($strFin-$strDebut)/(3600*24*14)));
			$prixTotal = $prix*($nbIntervalles+1);
			
			/*
			if ($estSupprimableModifiable) $checkBoxASupprimer = "<input name='aSupprimer[{$unContrat['idContrat']}]' value='oui' type='checkbox'>";
			else $checkBoxASupprimer = "&nbsp";
			*/
			// toujours supprimable
			$checkBoxASupprimer = "<input name='aSupprimer[{$unContrat['idContrat']}]' value='oui' type='checkbox'>";
			
			$checkBoxValide = "<input name='valide[{$unContrat['idContrat']}]' value=1 ";
			// if ($unContrat['valide']==1) $checkBoxValide .=  "checked disabled ";
			$checkBoxValide .= "type='checkbox'>";
	?>
						<tr>
							<td style="text-align: center;">
								<?php echo($type[$unContrat['typeContratId']]); ?>
							</td>
							<td style="text-align: center;">
								<?php echo($quantite[$unContrat['quantiteContratId']]); ?>
							</td>
							<td style="text-align: center;">
								<?php echo($annee[$unContrat['anneeId']]['nom']); ?>
							</td>
							<td style="text-align: center;">
								<?php echo($periode[$unContrat['periodeContratId']]); ?>
							</td>
							<td style="text-align: center;">
								<?php echo($frequence[$unContrat['frequenceContratId']]); ?>
							</td>
							<td style="text-align: center;">
								<?php //echo($unContrat['dateDebut']); ?>
								<?php 
									// if ($estSupprimableModifiable) echo $select['dateDebut']; 
									// else echo($unContrat['dateDebut']);
									echo $select['dateDebut']; 
								?>
							</td>
							<td style="text-align: center;">
								<?php echo($select['dateFin']); ?>
							</td>
							<td style="text-align: center;">
								<span id="prix[<?php echo($unContrat['idContrat']); ?>]"><?php echo($prixTotal);?> €</span>
							</td>
							<td style="text-align: center;">
								<?php echo $checkBoxValide; ?>
							</td>
							<td style="text-align: center;">
								<?php echo($checkBoxASupprimer); ?>
							</td>
						</tr>
	<?php
		}
	}	
		
	// select année pour nouveau contrat : ne proposer l'année suivante que le dernier mois de l'année courante 
	// $select['annee'] 
		$select['annee'] = "
		    <select name='nouveau[annee]'>
      ";
      foreach ($annee AS $id => $uneAnnee) {
			$select['annee'] .= "
				<option value='$id' >{$uneAnnee['nom']}</option>
			";
		}
      $select['annee'] .= "
			</select>
		";
	

	
	// dernière date possible :SELECT MAX(date) maxDate FROM PDM_distribution, PDM_annee WHERE idannee=anneeId AND courante='oui'
	// première date possible
	// $dans7Jours
	
?>
				</tbody>
			</table>
			<br>
			<hr>
			<br>
<?php 
	if ($ajouterContrat=='ajouterContratAnneeCourante') {
?>
<!-- nouveau contrat année courante -->			
			<table class=hoverTable border=1 style="width: 100%; margin: auto;">
				<tbody>
					<tr>
						<td colspan="10" style="font-weight: bold; font-size: medium;">
							Nouveau contrat année en cours : <?php  echo $anneeCourante['nom']; ?>
						</td>
					</tr>
					<tr>
						<th style="">
							type
						</th>
						<th style="">
							quantité
						</th>
						<th style="">
							année
						</th>
						<th style="">
							période
						</th>
						<th style="">
							fréquence
						</th>
						<td rowspan="2" style="text-align: center; font-weight: bold; width: 180px;">
							<input style="font-weight:bold; "  name="enregistrerNouveauContrat" value="Enregistrer" type="button" onClick="document.getElementById('newAction').value='enregistrerNouveauContrat';
							document.getElementById('nouveau[annee]').value='<?php echo $anneeCourante['id'];?>'; document.getElementById('formAction').submit();">

						</td>
					</tr>
					<tr>
						<td style="text-align: center;">
							<?php echo $select['type'];?>
						</td>
						<td style="text-align: center;">
							<span id="selectQT1" style="display: inline;"><?php echo $select[1]['quantite'];?></span>
							<span id="selectQT2" style="display: none;"><?php echo $select[2]['quantite'];?></span>
						</td>
						<td style="text-align: center;">
							&nbsp;<?php echo $anneeCourante['nom'];?>
						</td>
						<td style="text-align: center;">
							<?php echo $select['periode'];  ?>
						</td>
						<td style="text-align: center;">
							<?php echo $select['frequence'];  ?>
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<table  border="1" style="width: 500px; background-color: #ffffff; margin-left: 0px;">
				<tbody>
					<tr>
						<td colspan="5" style=" font-size: x-small; background-color: #ffffff;">
							Information sur les nombres de contrats par semaine A ou B année en cours : <?php  echo $anneeCourante['nom']; ?>
						</td>
					</tr>
					<tr>
						<td style=" font-size: x-small; background-color: #ffffff;">
							&nbsp;
						</td>
						<td colspan="2" style=" font-size: x-small; background-color: #ffffff;">
							pendant le semestre 1
						</td>
						<td colspan="2" style=" font-size: x-small; background-color: #ffffff;">
							pendant le semestre 2
						</td>
					</tr>
					<tr>
						<td style=" font-size: x-small; background-color: #ffffff;">
							&nbsp;
						</td>
						<td style=" font-size: x-small; background-color: #ffffff;">
							semaine A
						</td>
						<td style=" font-size: x-small; background-color: #ffffff;">
							semaine B
						</td>
						<td style=" font-size: x-small; background-color: #ffffff;">
							semaine A
						</td>
						<td style=" font-size: x-small; background-color: #ffffff;">
							semaine B
						</td>
					</tr>
					<tr>
						<td style="font-size: x-small; text-align: center;  background-color: #ffffff;">
							contrats légumes
						</td>
						<td style="font-size: x-small; text-align: center;  background-color: #ffffff;">
							<?php echo($stat['CL1A']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['CL1B']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['CL2A']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['CL2B']) ?>
						</td>
					</tr>
					<tr>
						<td style="font-size: x-small; text-align: center;  background-color: #ffffff;">
							contrats oeufs
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['CO1A']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['CO1B']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['CO2A']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['CO1A']) ?>
						</td>
					</tr>
				</tbody>
			</table>
			<br>
<?php 
	} // fin nouveau contrat année en cours
	if ($ajouterContrat=='ajouterContratAnneeSuivante') {
?>
<!-- nouveau contrat année suivante -->			
			<table class=hoverTable border=1 style="width: 100%;" margin: auto;>
				<tbody>
					<tr>
						<td colspan="10" style="font-weight: bold; font-size: medium;">
							Nouveau contrat année suivante : <?php  echo $anneeSuivante['nom']; ?>
						</td>
					</tr>
					<tr>
						<th style="">
							type
						</th>
						<th style="">
							quantité
						</th>
						<th style="">
							année
						</th>
						<th style="">
							période
						</th>
						<th style="">
							fréquence
						</th>
						<td rowspan="2" style="text-align: center; font-weight: bold; width: 180px;">
							<input style="font-weight:bold; "  name="enregistrerNouveauContrat" value="Enregistrer" type="button" onClick="document.getElementById('newAction').value='enregistrerNouveauContrat';
							document.getElementById('nouveau[annee]').value='<?php echo $anneeSuivante['id'];?>'; document.getElementById('formAction').submit();">

						</td>
					</tr>
					<tr>
						<td style="text-align: center;">
							<?php echo $select['type'];?>
						</td>
						<td style="text-align: center;">
							<span id="selectQT1" style="display: inline;"><?php echo $select[1]['quantite'];?></span>
							<span id="selectQT2" style="display: none;"><?php echo $select[2]['quantite'];?></span>
						</td>
						<td style="text-align: center;">
							&nbsp;<?php echo $anneeSuivante['nom'];?>
						</td>
						<td style="text-align: center;">
							<?php echo $select['periode'];  ?>
						</td>
						<td style="text-align: center;">
							<?php echo $select['frequence'];  ?>
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<table  border=1 style="width: 500px; background-color: #ffffff; margin-left: 0px;">
				<tbody>
					<tr>
						<td colspan="5" style=" font-size: x-small; background-color: #ffffff;">
							Information sur les nombres de contrats par semaine A ou B année suivante : <?php  echo $anneeSuivante['nom']; ?>
						</td>
					</tr>
					<tr>
						<td style=" font-size: x-small; background-color: #ffffff;">
							&nbsp;
						</td>
						<td colspan="2" style=" font-size: x-small; background-color: #ffffff;">
							pendant le semestre 1
						</td>
						<td colspan="2" style=" font-size: x-small; background-color: #ffffff;">
							pendant le semestre 2
						</td>
					</tr>
					<tr>
						<td style=" font-size: x-small; background-color: #ffffff;">
							&nbsp;
						</td>
						<td style=" font-size: x-small; background-color: #ffffff;">
							semaine A
						</td>
						<td style=" font-size: x-small; background-color: #ffffff;">
							semaine B
						</td>
						<td style=" font-size: x-small; background-color: #ffffff;">
							semaine A
						</td>
						<td style=" font-size: x-small; background-color: #ffffff;">
							semaine B
						</td>
					</tr>
					<tr>
						<td style="font-size: x-small; text-align: center;  background-color: #ffffff;">
							contrats légumes
						</td>
						<td style="font-size: x-small; text-align: center;  background-color: #ffffff;">
							<?php echo($stat['SL1A']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['SL1B']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['SL2A']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['SL2B']) ?>
						</td>
					</tr>
					<tr>
						<td style="font-size: x-small; text-align: center;  background-color: #ffffff;">
							contrats oeufs
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['SO1A']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['SO1B']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['SO2A']) ?>
						</td>
						<td style="font-size: x-small; text-align: center; background-color: #ffffff;">
							<?php echo($stat['SO2B']) ?>
						</td>
					</tr>
				</tbody>
			</table>
			<br>
<?php
	} // fin nouveau contrat année suivante
?>
		</div>
		
		<div id=bas>
			<hr>
			<p style="text-align: center; font-weight:bold;">
				<input style="font-weight:bold; "  name="ajouterContratAnneeCourante" value="Ajouter un contrat pour l'année en cours" type="button" onClick=" document.getElementById('newAction').value='ajouterContratAnneeCourante'; document.getElementById('formAction').submit();
				">
<?php
	if ($estDernierMoisAnneeCourante) {
?>
				<input style="font-weight:bold; "  name="ajouterContratAnneeSuivante" value="Ajouter un contrat pour l'année suivante" type="button" onClick=" document.getElementById('newAction').value='ajouterContratAnneeSuivante'; document.getElementById('formAction').submit();
				">
<?php
	}
?>
				
				<input style="font-weight:bold; "  name="abandonnerContrats" value="Retour" type="button" onClick=" document.getElementById('newAction').value='abandonnerContrats'; document.getElementById('formAction').submit();
				">
			</p>
			<hr>
		</div>
		
		</form>
	</body>
		<script>
			$(document).ready(
				function(){
					// redimmensionner
					redim();
					//document.getElementById('prix[1]').innerHTML = "pas de prix";
				}
			);
			
			function majPrix() {
				var request = new XMLHttpRequest();
				request.onreadystatechange = function() {
					if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
						var response = JSON.parse(this.responseText);
						console.log(response.current_condition.condition);
					}
				};
				request.open("GET", "calculPrix.php?idcompte=$nomCompte");
				request.send();
			}
		</script>
</html>
<?php



	}
	
	function enregistrerModificationContrats() {
//var_dump($_POST); die();
		$message = "";
		// enregistrer modifications même si on supprime plus loin !
		foreach ($_POST['dateFin'] AS $idContrat => $dateFin) {
			$sql = "UPDATE PDM_contrat SET dateFin='$dateFin' WHERE idContrat=$idContrat";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			// non validé (sera corrigé plus loin)
				$sql = "UPDATE PDM_contrat SET valide=0 WHERE idContrat=$idContrat";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
			$message = "Modification(s) enregistrée(s).";
		}
		if (isset($_POST['dateDebut'])) {
			foreach ($_POST['dateDebut'] AS $idContrat => $dateDebut) {
				$sql = "UPDATE PDM_contrat SET dateDebut='$dateDebut' WHERE idContrat=$idContrat";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				$message = "Modification(s) enregistrée(s).";
			}
		}
		;
		// validé ? (NB ne peut pas être invalidé)
		if (isset($_POST['valide'])) {
			foreach ($_POST['valide'] AS $idContrat => $dateDebut) {
				$sql = "UPDATE PDM_contrat SET valide=1 WHERE idContrat=$idContrat";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				$message = "Modification(s) enregistrée(s).";
			}
		}
		
		// supprimer ?
		if (isset($_POST['aSupprimer'])) {
			foreach ($_POST['aSupprimer'] AS $idContrat =>$valeur) {
				// enregistrement de la suppression dans les commentaires du compte
				$sql = "UPDATE PDM_compte SET commentaire=CONCAT(commentaire,' Attention ! un contrat (au moins) a été supprimé ! ') WHERE idCompte = (SELECT compteId FROM PDM_contrat WHERE idContrat=$idContrat)";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				// suppression du contrat
				$sql = "DELETE FROM PDM_contrat WHERE idContrat=$idContrat";
				// $res = mysqli_query($GLOBALS['lkId'],$sql);
			}
			$message .= " Suppression(s) de contrat effectuée(s).";
		}
		$ajouterContrat = FALSE;
		modifierContrats($_POST['idCompte'], $ajouterContrat, $message);
		return;
	}
	
	function enregistrerNouveauContrat() {
//var_dump($_POST); die();
      // table des jours de distribution
      $tableDistribution = tableDistribution($_POST['nouveau']['annee']);
      //determination de la date de début et de la date de fin du contrat 
      // dateDebut = 1ère date possible selon semestre et semaine ; pour mensuel semaine imposée par date de début
		$aujourdhui = date('Y-m-d',time());
//		$strDans7Jours = time()+(3600*24*7);
//		$dans7Jours = date('Y-m-d',$strDans7Jours);
		$premierMercrediPostCommande = premierMercrediPostCommande($aujourdhui);
/*		
		// contrat mensuel
      if ($_POST['nouveau']['periode']==4) { // mensuel
         $dateDebut = $premierMercrediPostCommande;
         if ($_POST['nouveau']['frequence']==1) { // semaine
            $dateFinMois = date('Y-m-d',strtotime($dateDebut)+3*7*25*3600);
            $rangDateFin = array_search($dateFinMois,array_column($tableDistribution, 'date'));
         }
         else { //quinzaine
            $dateFinMois = date('Y-m-d',strtotime($dateDebut)+2*7*25*3600);
            $rangDateFin = array_search($dateFinMois,array_column($tableDistribution, 'date'));
            // on force la quinzaine selon la dateDebut
            $rangDateDebut = array_search($dateDebut,array_column($tableDistribution, 'date'));
            if ($tableDistribution[$rangDateDebut]['semaine']=='A') $_POST['nouveau']['frequence'] = 2;
            else $_POST['nouveau']['frequence'] = 3;
         }
         $dateFin = $tableDistribution[$rangDateFin]['date'];
      }
      else {   // pas mensuel
      
      }
*/
      // dateFin distinguer selon année/semestre semaine mensuel
		// date de début selon auj période fréquence
		$aujourdhui = date('Y-m-d',time());
//		$strDans7Jours = time()+(3600*24*7);
//		$dans7Jours = date('Y-m-d',$strDans7Jours);
		$premierMercrediPostCommande = premierMercrediPostCommande($aujourdhui);
		// date fin : toutes les dates possibles en fonction de l'année d'aujourd'hui de la période, de  la fréquence
		// condition semaine selon fréquence
		switch ($_POST['nouveau']['frequence']) {
			case 1 :
				$whereSemaine = "";
				break;
			case 2 :
				$whereSemaine = "AND semaine = 'A'";
				break;
			case 3 :
				$whereSemaine = "AND semaine = 'B'";
				break;
		}
		// condition semestre ou année
		switch ($_POST['nouveau']['periode']) {
			case 1 :
				$wherePeriode = "";
				break;
			case 2 :
				$wherePeriode = "AND semestre = '1'";
				break;
			case 3 :
				$wherePeriode = "AND semestre = '2'";
				break;
			case 4 : // mensuel
				$wherePeriode = "";
				break;
		}
		// condition première date possible >= dans7Jours
		$wherePremiereDatePossible = "AND date>='$premierMercrediPostCommande'";
		
		// dates de début et de fin
		$sql = "
		SELECT MIN(date) dateDebut, MAX(date) dateFin FROM PDM_distribution WHERE anneeId={$_POST['nouveau']['annee']} $whereSemaine $wherePeriode $wherePremiereDatePossible ORDER BY DATE
		";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$uneLigne = mysqli_fetch_assoc($res);
		$dateDebut = $uneLigne['dateDebut'];
		$dateFin = $uneLigne['dateFin'];
		
		// date de fin d'un contrat mensuel
		if ($_POST['nouveau']['periode']==4) {
			$sql = "
			SELECT date FROM PDM_distribution WHERE anneeId={$_POST['nouveau']['annee']} $whereSemaine $wherePeriode $wherePremiereDatePossible ORDER BY DATE
			";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			while ($uneLigne = mysqli_fetch_assoc($res)){
				$ligne[] = $uneLigne;
			}
			$dateFin = $ligne[3]['date'];
		}
		
		// quantité
		if ($_POST['nouveau']['type']==1) $quantite = $_POST['nouveau']['QT1'];
		else $quantite = $_POST['nouveau']['QT2'];
/*		
		// prix selon type
		$sql = "SELECT  prix FROM PDM_prixAnneeTypeContrat WHERE anneeId={$_POST['nouveau']['annee']} AND typeContratId={$_POST['nouveau']['type']}";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$uneLigne = mysqli_fetch_assoc($res);
		$prixUnitaire = $uneLigne['prix'];
		
		// calcul du prix du contrat (type, année) en fonction des dates de début et de fin
		$strDebut = strtotime($dateDebut);
		$strFin = strtotime($dateFin);
		$strIntervalle = $strFin-$strDebut;
		if ($_POST['nouveau']['frequence']==1) $nbIntervalles = round((($strFin-$strDebut)/(3600*24*7)));
		else $nbIntervalles = round((($strFin-$strDebut)/(3600*24*14)));
		$prix = $prixUnitaire*($nbIntervalles+1);
*/		
		// insertion
		$sql = "
		INSERT INTO PDM_contrat(idContrat, compteId, anneeId, typeContratId, quantiteContratId, frequenceContratId, periodeContratId, dateDebut, dateFin, valide, dateCreation, dateModification) VALUES (NULL,{$_POST['idCompte']},{$_POST['nouveau']['annee']},{$_POST['nouveau']['type']},$quantite,{$_POST['nouveau']['frequence']},{$_POST['nouveau']['periode']},'$dateDebut','$dateFin',0,CURRENT_TIMESTAMP,NULL)
		";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		// message et retour
		$message = "Le nouveau contrat a été enregistré.";
		
		$ajouterContrat = FALSE;

		modifierContrats($_POST['idCompte'], $ajouterContrat, $message);
		return;
		
	}
	
	
	
	function chequeCompte() {
	}
	
	function distributionsCompte() {
		// voir distributions.php après choix de l'compte ?
	}
	
	function courrielsCompte() {
	}
	
	function ajouterCompte() {
	
		$GLOBALS['titrePage'] = "Ajouter un compte";
?>
<!DOCTYPE html>
<html lang='fr-fr'>
<?php	
		include('inc/headHTML.inc.php');
?>
	<body style='font-family: sans-serif; font-size:small; padding: 0px;'  >
	
		<form method='POST' action='comptes.php' name="formAction" id='formAction' >
			<input type='hidden' name='newAction' id='newAction' value='enregistrerAjoutCompte'>
			
		<div id='haut'>
		<?php
			include('inc/divEnTete.inc.php');
		?>
		</div>
		
		<div id='content' style='overflow: auto;'>
		<br><br>
		<hr>
			<table class='hoverTable' border='1' style='width: 1000px; margin: auto;'>
				<tbody>
					<tr>
						<th style="width: 200px;">
							&nbsp;
						</th>
						<th style="width: 200px;">
							NOM 
						</th>
						<th style="width: 200px;">
							Prénom 
						</th>
						<th style="width: 150px;">
							téléphone
						</th>
						<th style="width: 250px;">
							courriel
						</th>
					</tr>
					<tr style="font-weight: bold;">
						<td style= "text-align: right;">
							titulaire principal : 
						</td>
						<td style="text-align: left;">
							<input name="titulaire[1][nom]" value="" style="width: 195px;" type="text" required="required"  placeholder="NOM en majuscule">
						</td>
						<td style="text-align: left;">
							<input name="titulaire[1][prenom]" value="" style="width: 195px;" type="text"   placeholder="Prénom">
						</td>
						<td style="text-align: left;">
							<input name="titulaire[1][telephone]" value="" style="width: 145px;" type="text">
						</td>
						<td style="text-align: left;">
							<input name="titulaire[1][courriel]" value="" style="width: 245px;" type="text">
						</td>
					</tr>
					<tr>
						<td style= "text-align: right;">
							autre titulaire : 
						</td>
						<td style="text-align: left;">
							<input name="titulaire[2][nom]" value="" style="width: 195px;" type="text"   placeholder="NOM en majuscule">
						</td>
						<td style="text-align: left;">
							<input name="titulaire[2][prenom]" value="" style="width: 195px;" type="text"   placeholder="Prénom">
						</td>
						<td style="text-align: left;">
							<input name="titulaire[2][telephone]" value="" style="width: 145px;" type="text">
						</td>
						<td style="text-align: left;">
							<input name="titulaire[2][courriel]" value="" style="width: 245px;" type="text">
						</td>
					</tr>
					<tr>
						<td style= "text-align: right;">
							autre titulaire : 
						</td>
						<td style="text-align: left;">
							<input name="titulaire[3][nom]" value="" style="width: 195px;" type="text"   placeholder="NOM en majuscule">
						</td>
						<td style="text-align: left;">
							<input name="titulaire[3][prenom]" value="" style="width: 195px;" type="text"  placeholder="Prénom">
						</td>
						<td style="text-align: left;">
							<input name="titulaire[3][telephone]" value="" style="width: 145px;" type="text">
						</td>
						<td style="text-align: left;">
							<input name="titulaire[3][courriel]" value="" style="width: 245px;" type="text">
						</td>
					</tr>
				</tbody>
			</table>
			
			<br>		
			
			<table class='hoverTable' border='1' style='width: 1000px; margin: auto;'>
				<tbody>
			
					<tr>
						<th style="text-align: right; vertical-align: top; width: 150px;" >
							adhérent :
						</th>
						<td colspan="3">
							&nbsp; <input name="adherent" value="oui"  type="checkbox">
						</td>
					</tr>

					<tr>
						<th style="text-align: right; vertical-align: top;">
							commentaire : 
						</th>
						<td colspan="3">
							<textarea  name="commentaire" style="width: 99%; height: 150px;"></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			<hr>
		</div>
		
		<div id='bas'>
			<hr>
			<p style="text-align: center; font-weight:bold;">
				<input style="font-weight:bold; "  name="abandonAjout" value="Abandonner l'ajout" type="button" onClick=" document.getElementById('newAction').value='abandonnerAjoutCompte'; document.getElementById('formAction').submit();
				">

				<input style="font-weight:bold; "  name="enregistrerAjoutCompte" value="Enregistrer l'ajout" type="submit">
			</p>
			<hr>
		</div>
		
		</form>
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
<?php

	} // fin ajouterCompte
	
	function enregistrerAjoutCompte() {
		if (isset($_POST['adherent'])) $adherent = 'oui';
		else $adherent = 'non';
		$commentaire = $_POST['commentaire'];
		$sql = "INSERT INTO PDM_compte(idCompte, titulairePrincipalId, commentaire, adherent, dateCreation, dateModification) VALUES (NULL,NULL,'$commentaire','$adherent',CURRENT_TIMESTAMP,NULL)";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$idCompte = mysqli_insert_id($GLOBALS['lkId']);
		foreach ($_POST['titulaire'] AS $i =>$unTitulaire) {
			if ($unTitulaire['nom']!="") {
				$nom = $unTitulaire['nom'].' '.$unTitulaire['prenom'];
				$telephone = $unTitulaire['telephone'];
				$courriel = $unTitulaire['courriel'];
				$sql = "INSERT INTO PDM_personne(idPersonne, nom, telephone, courriel, compteId) VALUES (NULL,'$nom','$telephone','$courriel',$idCompte)";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				if ($i==1) {
					$idTitulairePrincipal = mysqli_insert_id($GLOBALS['lkId']);
					$nomCompte = $nom;
				}
			}
		}
		// ajout du titulaire principal au compte
		$sql = "UPDATE PDM_compte SET titulairePrincipalId=$idTitulairePrincipal WHERE idCompte=$idCompte";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		
		$message = "Le compte $nomCompte a été ajouté.";
		afficherListeComptes($message);
	} // fin enregistrerAjoutCompte
	
	function exportComptes() {
		// recherche des infos à afficher :
		switch ($_SESSION['quiAfficher']) {
			case 'tousComptes' :
				$sql = '
				SELECT c.idCompte, c.titulairePrincipalId, c.adherent, c.commentaire, p.idPersonne, p.nom, p.telephone, p.courriel FROM PDM_personne p, PDM_compte c WHERE c.idCompte=p.compteId ORDER BY c.idCompte 
				';
				break;
			case 'adherents' :
				$sql = "
				SELECT c.idCompte, c.titulairePrincipalId, c.adherent, c.commentaire, p.idPersonne, p.nom, p.telephone, p.courriel FROM PDM_personne p, PDM_compte c WHERE c.idCompte=p.compteId AND c.adherent='oui' ORDER BY c.idCompte
				";
				break;
			case 'nonAdherents' :
				$sql = "
				SELECT c.idCompte, c.titulairePrincipalId, c.adherent, c.commentaire, p.idPersonne, p.nom, p.telephone, p.courriel FROM PDM_personne p, PDM_compte c WHERE c.idCompte=p.compteId AND c.adherent='non' ORDER BY c.idCompte
				";
				break;
			case 'contratsAnneeCourante' : // et futurs
				$sql = '
				SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
				FROM spipComptes a
				WHERE a.idCompte IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.date_distrib>CURRENT_TIMESTAMP)
				ORDER BY a.nom 
				';
				break;
			case 'contratsExpires' :
				$sql = "
				SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
				FROM spipComptes a
				WHERE a.idCompte NOT IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.date_distrib>CURRENT_TIMESTAMP)
				AND a.idCompte IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND (d.id_contrat={$_SESSION['id_contrat'][0]} OR d.id_contrat={$_SESSION['id_contrat'][1]}))
				ORDER BY a.nom 
				";

				break;
			case 'contratsAnciens' :
				$sql = "
				SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
				FROM spipComptes a
				WHERE a.idCompte NOT IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.id_contrat>={$_SESSION['id_contrat'][0]})
				AND a.idCompte IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution AND d.id_contrat<{$_SESSION['id_contrat'][0]})
				ORDER BY a.nom 
				";
				break;
			case 'sansContrat' :
				$sql = '
				SELECT a.idCompte, a.nom, a.tel, a.email, a.comment 
				FROM spipComptes a 
				WHERE a.idCompte NOT IN
				(SELECT ad.idCompte FROM spipComptes_distribution ad, spip_distributions d WHERE ad.id_distribution=d.id_distribution)
				ORDER BY a.nom
				';
				break;
		}
		

		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($laLigne = mysqli_fetch_assoc($res)) {
			$ligne[] = $laLigne;
		}
//var_dump($ligne); die();
		// contruction des lignes du tableau
		$idComptePrecedent = 0;
		$idRow = 0;
		$n = 0;
		foreach ($ligne AS $uneLigne) {
			if ($uneLigne['idCompte']!= $idComptePrecedent) {
				$idComptePrecedent = $uneLigne['idCompte'];
				$idRow++;
				$n++;
				$row[$idRow]['idCompte'] = $uneLigne['idCompte'];
				if ($uneLigne['adherent']=='oui') $row[$idRow]['adherent'] = 'adhérent';
				else $row[$idRow]['adherent'] = '&nbsp;';
				$row[$idRow]['commentaire'] = $uneLigne['commentaire'];
				$row[$idRow]['nom'] = '';
				$row[$idRow]['telephone'] = '';
				$row[$idRow]['courriel'] = '';
			}	
			if ($uneLigne['idPersonne']==$uneLigne['titulairePrincipalId']) {
				$row[$idRow]['nomTri'] = $uneLigne['nom'];
				$row[$idRow]['nom'] = '***'.$uneLigne['nom'].'***'.$row[$idRow]['nom'];
				$row[$idRow]['telephone'] = $uneLigne['telephone'].$row[$idRow]['telephone'];
				$row[$idRow]['courriel'] = $uneLigne['courriel'].$row[$idRow]['courriel'];
			}
			else {  
				$row[$idRow]['nom'] .= ' / '.$uneLigne['nom'];
				$row[$idRow]['telephone'] .= ' / '.$uneLigne['telephone'];
				$row[$idRow]['courriel'] .= ' / '.$uneLigne['courriel'];
			}
		}
//var_dump($row);die();
		// tri des lignes selon le nom :!!!!!!!!!!!!!!!ça marche pas à cause du <b> !!!!!!!!!!!!!!!!!!!
		$nomTri  = array_column($row, 'nomTri');
		array_multisort($nomTri, SORT_ASC, $row);
//var_dump($row);die();

		// construction de la chaîne CSV séparateur = , texte entouré ""
		$chaineCSV = "";
		foreach ($row AS $aRow) {
//			$chaineCSV .= '"'.$aRow['nom'].'","'.$aRow['telephone'].'","'.$aRow['courriel'].'","'.$aRow['commentaire'].'"\n';
			$chaineCSV .= "\"{$aRow['nom']}\",\"{$aRow['telephone']}\",\"{$aRow['courriel']}\",\"{$aRow['commentaire']}\"";
			$chaineCSV .= "\r\n";
		}
//die($chaineCSV);		
		// envoi de la chaîne CSV
		$contentType = "text/csv";
		$longueur = strlen($chaineCSV);

		$nom = "PDM_contrats_{$_SESSION['quiAfficher']}.csv";
/*		
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);
                header('Content-Type: application/octet-stream');
                header('Content-Description: File Transfer');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Content-Transfer-Encoding: binary');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		header('Content-Encoding: UTF-8');
		header("Content-Type: text/csv; charset=UTF-8");
		header("Content-Length: ".$longueur."\"");
		header('Content-Disposition: attachment; filename="'.$nom.'"');
//		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//		header('Pragma: public');
//		header('Cache-Control: private', false);
		//	header('Pragma:  no-cache');
 */		
		
// from https://ziplineinteractive.com/blog/proper-php-headers-for-csv-documents-all-browsers/		
	header("Pragma: public");

	header("Expires: 0");

	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

	header("Cache-Control: private",false);

	header("Content-Type: application/octet-stream");

	header("Content-Disposition: attachment; filename=\"$nom\";" );

	header("Content-Transfer-Encoding: binary"); 		
		
		echo $chaineCSV;
		exit();

	} // fin exportComptes

?>