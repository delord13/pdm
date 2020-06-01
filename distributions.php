<?php
// distributions.php
///////////////////////////////////////////////////////////////////////////////
//	application gestionPDM
// février 2020
///////////////////////////////////////////////////////////////////////////////
/*
affichage des distributions en colonne (focus : la prochaine)
pour chaque distribution
	stats
	volontaires
*/
/*
$_SESSION['auj']
$_SESSION['accesAutorise']
$_SESSION['anneeCouranteId']
*/

	session_start();
	include('inc/init.inc.php');
//error_reporting(E_ERROR);

	// définition du script de retour pour afficherLeCompte
	$_SESSION['scriptOrigine'] = 'distributions.php';

	$GLOBALS['titrePage'] = "Gestion des distributions";
	
	
	// focus sur la distribution du jour ou prochaine $_SESSION('idDistributionFocus')
	$sql = "SELECT idDistribution FROM PDM_distribution WHERE nombreUnites>0 AND date>='{$_SESSION['auj']}' ";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	$laDistribution = mysqli_fetch_assoc($res);
	$_SESSION['idDistributionFocus'] = $laDistribution['idDistribution'];
	
	
	if (isset($_POST['message'])) $message = $_POST['message'];
	else $message = '';		
	
	if (isset($_POST['newAction'])) {
		// agir selon POST newAcion
		switch ($_POST['newAction']) {
			case 'afficher' :
				afficherDistributions($message);
				break;
			case 'afficherLaDistribution' :
				afficherLaDistribution();
				break;
			case 'enregistrerDisributions' :
				enregistrerDistributions('');
				break;
			case 'listeEmargementPDF' :
				listeEmargementPDF();
				break;
			case 'listeVolontairesPDF' :
				listeVolontairesPDF();
				break;
			case 'exportDistributions' :
				exportDistributions();
				break;
			case 'enregistrerAbsences' :
				enregistrerAbsences();
				afficherLaDistribution();
				break;
			case 'abandonnerModificationCompte' :
				afficherLaDistribution();
				break;
			case 'retourDistribution' :
				afficherDistributions('');
				break;
			case 'retour' :
				header("Location: index.php");
				exit;
				break;
			
		}
	}
	else {
		afficherDistributions($message);
	}
	
	function afficherDistributions($message) {
		// charger toutes les distributions depuis la précédente de $_SESSION['idDistributionFocus']
		// toutes les distribution avec nbUnites>0;
		$sql = "SELECT * FROM PDM_distribution WHERE nombreUnites>0 AND anneeID={$_SESSION['anneeCouranteId']}";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		
		
		while ($laLigne = mysqli_fetch_assoc($res)) {
			$distribution[$laLigne['idDistribution']] = $laLigne;
		}
/*
		$n = 1;
		$premiereAGarder = 0;
		while ($laLigne = mysqli_fetch_assoc($res)) {
			if ($laLigne['idDistribution']<$_SESSION['idDistributionFocus']) $laLignePrecedente = $laLigne;
			else {
				if ($laLigne['idDistribution']==$_SESSION['idDistributionFocus']) {
					$distribution[$laLignePrecedente['idDistribution']] = $laLignePrecedente;
					$idPremiereDistribution = $laLignePrecedente['idDistribution'];
					$n++;
				}
				$distribution[$laLigne['idDistribution']] = $laLigne;
				$n++;
			}
		}
*/		
		// charger les volontaires Distribution de ces distributions
		$sql = "SELECT * FROM PDM_volontaireDistribution, PDM_personne  WHERE  personneId=idPersonne";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$volontaireDistribution = FALSE;
		while ($laLigne = mysqli_fetch_assoc($res)) {
			$volontaireDistribution[$laLigne['distributionId']][$laLigne['personneId']] = $laLigne;
		}

		// charger les volontaires Emargement de ces distributions
		$sql = "SELECT * FROM PDM_volontaireEmargement, PDM_personne  WHERE personneId=idPersonne";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$volontaireEmargement = FALSE;
		while ($laLigne = mysqli_fetch_assoc($res)) {
			$volontaireEmargement[$laLigne['distributionId']][$laLigne['personneId']] = $laLigne;
		}

		// charger la liste des personnes susceptibles d'être volontaire ie : personnes reliées à un contrat au moins
		$sql = "SELECT idPersonne, nom FROM PDM_personne, PDM_compte, PDM_contrat WHERE PDM_personne.compteId=idCompte AND PDM_contrat.compteId=PDM_personne.compteId GROUP BY idPersonne ORDER BY nom";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($laLigne = mysqli_fetch_assoc($res)) {
			$personne[$laLigne['idPersonne']] = $laLigne['nom'];
		}
		
		// calcul des montants des commandes de paniers et de boîtes d'oeufs
		foreach ($distribution AS $idDistribution => $uneDistribution) {
			$reponse = calculerDistributionTotaux($idDistribution);
			$totalPaniers[$idDistribution] = $reponse['paniers'];
			$totalBoites[$idDistribution] = $reponse['boites'];
			
		}
/*		
		$distribution[$laLigne['idDistribution']]
			$reponse = calculerListeEmargement($idPremiereDistribution,0);
			$totalPaniers = $reponse['totalPaniers'];
			$totalBoites = $reponse['totalBoites'];;
*/
			

		
?>
<!DOCTYPE html>
<html lang='fr-fr'>
<?php	
		include('inc/headHTML.inc.php');
?>
	<body style='font-family: sans-serif; font-size:small; padding: 0px;'  >
		<form method='POST' action='distributions.php' id='formAction' >
			<input type='hidden' name='newAction' id='newAction' value='afficher'>
			<input type='hidden' name='idDistribution' id='idDistribution' value=''>

		<div id='haut'>
<?php
			include('inc/divEnTete.inc.php');
?>
			<table border="0" width="100%" >
				<tbody>
					<tr>
						<td class="td0" style="width: 100%; padding: 0px; vertical-align: middle; text-align: left; padding-left: 10px; color: red;"> 
							<?php echo('<p>'.$message.'</p>');?> 
						</td>
					</tr>
				</tbody>
			</table>
			<p style="color:red; margin: 10px;">
				cocher le(s) volontaire(s) à supprimer
			</p>

		</div>
		
		<div id='content' style='overflow: auto;'>
			<table class='hoverTable' border='1' style='width: 100%; margin: auto;'>
				<tbody>
					<tr>
						<th>
							&nbsp;
						</th>
						<th>
							date
						</th>
						<th>
							volontaires distribution
						</th>
						<th>
							volontaires émargement
						</th>

						<th>
							total paniers
						</th>
						<th>
							total œufs
						</th>

					</tr>
	<?php
		$i = 0;
		foreach ($distribution AS $idDistribution => $uneDistribution) {
?>
					<tr>
						<td>
							<input type='radio' name='idDistribution' value='<?php echo($idDistribution); ?>'
							<?php if ($idDistribution==$_SESSION['idDistributionFocus']) echo " checked";?>>
						</td>
						<td style="font-weight: bold; text-align: center;">
							<?php echo dateJJMMAA($uneDistribution['date']).' ('.$uneDistribution['semaine'].' '.$uneDistribution['idDistribution'].')' ?>
						</td>
						<td> <!-- distribution -->
							<?php
								if ($volontaireDistribution) {
									foreach ($volontaireDistribution AS $idDistributionVolontaire => $unvolontaireDistribution) {
										if ($idDistributionVolontaire==$idDistribution) {
											foreach ($unvolontaireDistribution AS $idPersonne => $unVolontaire) {
												if ($unVolontaire['distributionId']==$idDistribution) {
													echo "<input type='checkbox' name='supprimerVolontaireDistribution[$idDistribution][$idPersonne]' style='position: relative; top: 3px;'> &nbsp;";
													echo $unVolontaire['nom'].'&nbsp;<br>';
												}
											}
										}
									}
								}
							?>
							ajouter un volontaire : 
							<select name='volontaireDistribution<?php echo "[$idDistribution]"; ?>'>
								<option value='0' > </option>
							<?php
								foreach ($personne AS $idPersonne => $nom) {
									echo "<option value='$idPersonne' >$nom</option>";
								}
							?>
							</select>
							<?php
							?>
						</td>
						<td> <!-- émargement -->
							<?php
								if ($volontaireEmargement) {
									foreach ($volontaireEmargement AS $idDistributionVolontaire => $unvolontaireEmargement) {
										if ($idDistributionVolontaire==$idDistribution) {
											foreach ($unvolontaireEmargement AS $idPersonne => $unVolontaire) {
												if ($unVolontaire['distributionId']==$idDistribution) {
													echo "<input type='checkbox' name='supprimerVolontaireEmargement[$idDistribution][$idPersonne]' style='position: relative; top: 3px;'> &nbsp;";
													echo $unVolontaire['nom'].'&nbsp;<br>';
												}
											}
										}
									}
								}
							?>
							ajouter un volontaire : 
							<select name='volontaireEmargement<?php echo "[$idDistribution]"; ?>'>
								<option value='0' > </option>
							<?php
								foreach ($personne AS $idPersonne => $nom) {
									echo "<option value='$idPersonne' >$nom</option>";
								}
							?>
							</select>
							<?php
							?>
						</td>

						<td style="text-align: center; font-size: 16px; font-weight: bold;">
							<?php echo $totalPaniers[$idDistribution]; ?> <img src="images/panier20.png"  style="width: 5.36mm; height: auto;">
						</td>
						<td style="text-align: center; font-size: 16px; font-weight: bold;">
							<?php echo $totalBoites[$idDistribution]; ?> <img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;">
						</td>

					</tr>
<?php
			$i++;
		}
		
	?>
				</tbody>
			</table>
			<hr>
		</div>
		
		<div id='bas'>
			<hr>
			<p style="text-align: center;">
				pour la sélection :
				<input style="font-weight:bold;"  name="afficherLaDistribution" value="afficher la distribution" type="button" title="afficher la distribution" onClick=" document.getElementById('newAction').value='afficherLaDistribution'; document.getElementById('formAction').submit();
				">
				&nbsp;
				<input style="font-weight:bold;"  name="listeEmargementPDF" value="liste d'émargement PDF" type="button" title="Liste d'émargement en pdf"  onClick=" document.getElementById('newAction').value='listeEmargementPDF';   document.getElementById('formAction').submit();
				">
				&nbsp;
				<input style="font-weight:bold;"  name="listeVolontairesPDF" value="liste des volontaires PDF" type="button" title="Liste des volontaires en pdf"  onClick=" document.getElementById('newAction').value='listeVolontairesPDF';  document.getElementById('formAction').submit();
				">
				
				&nbsp; &nbsp; &nbsp;
				<input style="font-weight:bold; "  name="enregistrer" value="Enregistrer tout" type="button" title="enregistrer les modifications" onClick=" document.getElementById('newAction').value='enregistrerDisributions'; document.getElementById('formAction').submit();
				">
				&nbsp;
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
					document.getElementsByName('volontaireDistribution[<?php echo $_SESSION['idDistributionFocus'] ;?>]')[0].focus();
					//if (document.getElementsByName('volontaireDistribution[<?php //echo $_SESSION['idDistributionFocus']+4 ;?>]')) document.getElementsByName('volontaireDistribution[<?php //echo $_SESSION['idDistributionFocus']+4 ;?>]')[0].focus(); 
				}
			);
		</script>
</html>
<?php

	} // fin afficherDistributions
	
	function afficherLaDistribution() {
		$idDistribution = (int) $_POST['idDistribution'];

		$reponse = calculerListeEmargement($idDistribution,1);
		$distribution = $reponse['distribution'];
		$ligne = $reponse['ligne'];
		
		$reponse = calculerDistributionTotaux($idDistribution);
		$nbTotalPaniers = $reponse['paniers'];
		$nbTotalBoites = $reponse['boites'];

		// ajouter les absences à chaque ligne
		// charger toutes les absences à la distribution
		$sql = "SELECT compteId FROM PDM_absence WHERE distributionId=$idDistribution";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$absence[0] = FALSE;
		if ($res)
			while ($uneAbsence = mysqli_fetch_assoc($res)) {
				$absence[] = $uneAbsence['compteId'];
			}

		foreach ($ligne AS $i => $uneLigne) {
			$ligne[$i]['absence'] = in_array($uneLigne['idCompte'], $absence);
		}
		
		if (!isset($_POST['message'])) $message = '';
		else $message = $_POST['message'];
		$tabCle = array_keys($distribution);
//var_dump($tabCle);die();
		$dateJJMMAA = dateJJMMAA($distribution[$tabCle[0]]['date']);
		
		$GLOBALS['titrePage'] = "Distribution du $dateJJMMAA ".$distribution[$tabCle[0]]['semaine'];
		
		// enregistrer les paniers pas pris et ajouter la mention dans le commentaire
?>
<!DOCTYPE html>
<html lang='fr-fr'>
<?php	
		include('inc/headHTML.inc.php');
?>
	<body style='font-family: sans-serif; font-size:small; padding: 0px;'  >
		<form method='POST' action='distributions.php' id='formAction' >
			<input type='hidden' name='newAction' id='newAction' value='afficher'>
			<input type='hidden' name='idDistribution' id='idDistribution' value='<?php echo $_POST['idDistribution'];?>'>
			<input type='hidden' name='select' id='select'>

		<div id='haut'>
<?php
			include('inc/divEnTete.inc.php');
?>
			<p style="color: red; margin: 10px;">
				Cliquez sur le nom d'un compte pour  modifier ses attributs : commentaire, ajout, report, volontaire...
			</p>
			<table border="0" width="100%" >
				<tbody>
					<tr>
						<td class="td0" style="width: 100%; padding: 0px; vertical-align: middle; text-align: left; padding-left: 10px; color: red;"> 
							<p><?php echo($message); ?></p> 
						</td>
					</tr>
				</tbody>
			</table>

		</div>
		
		<div id='content' style='overflow: auto;'>
			

			<table cellspacing="0" cellpadding="1" border="1"  style="width:100%;"  >
				<thead>
					<tr align="center" style="font-weight: bold;" >
					<tr style="background-color: $backgroundcolor; page-break-inside: avoid;">
						<th colspan="2">
							total
						</th>
					
						<th colspan="2">
<?php
	echo "$nbTotalPaniers <img src='images/panier20.png'  style='width: 5.36mm; height: auto;'><br> $nbTotalBoites  <img src='images/oeuf20.png'  style='width: 5.36mm; height: auto;'>";
?>
						</th>
					</tr>
						<th>
							compte 
						</th>
						<th>
							commentaire 
						</th>
						<th width="5%">
							<?php echo $dateJJMMAA;?> 
						</th>
						<th width="5%">
							absent 
						</th>
					</tr>
				</thead>
				<tbody>
<?php	
		
		$backgroundcolor = '#e5e5e5';
		
//var_dump($ligne); die;

		foreach($ligne AS $uneLigne) { 
			if ($backgroundcolor=='#d5d5d5') $backgroundcolor = '#ffffff';
			else $backgroundcolor = '#d5d5d5';
?>
			
					<tr style="background-color: $backgroundcolor; page-break-inside: avoid;">
						<td>
						<input style="font-weight:bold;"  name="afficherLeCompte" value="<?php echo $uneLigne['titulairesBouton'];?>" type="button" title="modifier commentaire, ajout, report, volontaire" onClick=" document.getElementById('newAction').value='afficherLeCompte';
						document.getElementById('select').value='<?php echo($uneLigne['idCompte']); ?>'
						document.getElementById('formAction').action='comptes.php';
						document.getElementById('formAction').submit();
						">
							 
						</td>
						<td>
							<?php echo $uneLigne['commentaire'];?> 
						</td>

						<td width="5%" style="text-align: center; font-weight: bold;">
<?php
			if ($uneLigne[$idDistribution]['paniers']>0) { 
?>
							<?php echo $uneLigne[$idDistribution]['paniers'];?>  <img src="images/panier20.png"  style="width: 5.36mm; height: auto;"><br>
<?php
			} 
			else { 
?>
							&nbsp;
<?php
			} 
			if ($uneLigne[$idDistribution]['boites']>0) { 
?>
							<?php echo $uneLigne[$idDistribution]['boites'];?>  <img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;">
<?php
			} 				
			else { 
?>
							&nbsp;
<?php
			} 
?>
						</td>
						<td style="text-align: center;">
							<input name="absent[<?php echo($uneLigne['idCompte']); ?>]" value="absent" type="checkbox" 
							<?php if ($uneLigne['absence']) echo ' checked '; ?> >
						</td>
					</tr>
<?php
	}
?>				</tbody>
			</table>
<?php
			
			
			
			
			
?>			
			
			
			
			
			
			
			
<!-- la table -->
			<hr>
		</div>
		
		<div id='bas'>
			<hr>
			<p style="text-align: center;">
				
				<input style="font-weight:bold;"  name="enregistrerAbsences" value="Enregistrer les absences" type="button" title="Enregistrer les absences à la distribution"  onClick=" document.getElementById('newAction').value='enregistrerAbsences';   document.getElementById('formAction').submit();
				">
				&nbsp; &nbsp;
				<input style="font-weight:bold; "  name="retour" value="Retour" type="button" title="retourner au menu principal" onClick=" document.getElementById('newAction').value='retourDistribution'; document.getElementById('formAction').submit();
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
				}
			);
		</script>
</html>
<?php
	}
	
	function enregistrerAbsences() {
		$idDistribution = $_POST['idDistribution'];
		// on insère toutes les absences cochées
		if (isset($_POST['absent'])) {
			foreach ($_POST['absent'] AS $idCompte => $valeur) {
				$sql = "INSERT INTO `PDM_absence` (`idAbsence`, `compteId`, `distributionId`, `dateModification`) VALUES (NULL, '$idCompte', '$idDistribution', CURRENT_TIMESTAMP);";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
			}
		}
		// on efface toutes les absences non cochées
		$sql = "SELECT idAbsence, compteId FROM PDM_absence WHERE distributionId=$idDistribution";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($uneAbsence = mysqli_fetch_assoc($res)) {
			if (!array_key_exists($uneAbsence['compteId'],$_POST['absent'])) {
				$sql1 = "DELETE FROM PDM_absence WHERE idAbsence={$uneAbsence['idAbsence']}";
				$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
			}
		}
	}
	
	function enregistrerDistributions() {
//var_dump($_POST);die;		
		// traitement des suppressions éventuelles
		if (isset($_POST['supprimerVolontaireDistribution'])) {
			foreach ($_POST['supprimerVolontaireDistribution'] AS $idDistribution => $uneSuppressionDistribution) {
				foreach ($uneSuppressionDistribution AS $idPersonne =>$uneSuppressionPersonne)
				$sql = "DELETE FROM PDM_volontaireDistribution WHERE distributionId=$idDistribution AND personneId=$idPersonne";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
			}
		}
		if (isset($_POST['supprimerVolontaireEmargement'])) {
			foreach ($_POST['supprimerVolontaireEmargement'] AS $idDistribution => $uneSuppressionDistribution) {
				foreach ($uneSuppressionDistribution AS $idPersonne =>$uneSuppressionPersonne)
				$sql = "DELETE FROM PDM_volontaireEmargement WHERE distributionId=$idDistribution AND personneId=$idPersonne";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
			}
		}
		
		// traitement des ajouts éventuels distribution
		foreach ($_POST['volontaireDistribution'] AS $idDistribution => $unVolontaire) {
			if ($unVolontaire!=0) {
				$sql = "INSERT INTO PDM_volontaireDistribution (idVolontaire, personneId, distributionId, dateModification) VALUES (NULL, $unVolontaire, $idDistribution, CURRENT_TIMESTAMP);";
//die($sql);
				$res = mysqli_query($GLOBALS['lkId'],$sql);
			}
		}
		
		// traitement des ajouts éventuels émargement
		foreach ($_POST['volontaireEmargement'] AS $idDistribution => $unVolontaire) {
			if ($unVolontaire!=0) {
				$sql = "INSERT INTO PDM_volontaireEmargement (idVolontaire, personneId, distributionId, dateModification) VALUES (NULL, $unVolontaire, $idDistribution,  CURRENT_TIMESTAMP);";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
			}
		}
		
		afficherDistributions("Modification(s) enregistrée(s)");
	} // fin enregistrerDistributions
	
	function listeEmargementPDF() {
		$reponse = calculerListeEmargement($_POST['idDistribution'],4);
		$distribution = $reponse['distribution'];
		$ligne = $reponse['ligne'];
		$totalPaniers = $reponse['totalPaniers'];
		$totalBoites = $reponse['totalBoites'];
//var_dump($distribution);
		$tabCle = array_keys($distribution);
//var_dump($tabCle);
//var_dump($totalPaniers);  die;	
/*		
		// initialisation des cumuls de paniers et de boites
		foreach ($distribution AS $idDistribution => $valeur) {
			$total[$idDistribution]['paniers'] = 0;
			$total[$idDistribution]['boites'] = 0;
		}
		
		// les totaux pour les 4 distributions
		foreach ($distribution AS $idDistribution => $laDistribution) {
			$reponse = calculerDistributionTotaux($idDistribution);
		}
*/		
		// virer les lignes sans panier et sans boîte
//var_dump($ligne);die;
		
//var_dump($ligne); die();
		
		// html
		$html = "";
		$html .= <<<EOT
			<style>
				tr:nth-of-type(even) {
					background-color: #e5e5e5;
				}

				tr:nth-of-type(odd) {
					background-color: #d5d5d5;
				}
			</style>
EOT;
		$html .= <<<EOT
			<table cellspacing="0" cellpadding="1" border="1"  style="width:100%; font-size: 9pt;  font-family: deajvusans;"  >
				<thead>
					<tr align="center" style="font-weight: bold;" >
						<th colsapn="2" width="80%"  style="font-size: 14pt;">
							Liste d'émargement
						</th>
						<th width="5%">
							{$distribution[$tabCle[0]]['jourMois']}
						</th>
						<th width="5%">
							{$distribution[$tabCle[1]]['jourMois']}
						</th>
						<th width="5%">
							{$distribution[$tabCle[2]]['jourMois']}
						</th>
						<th width="5%">
							{$distribution[$tabCle[3]]['jourMois']}
						</th>
					</tr>
				</thead>
				<tbody>
EOT;
		
		$backgroundcolor = '#e5e5e5';
		
		foreach($ligne AS $uneLigne) {
/*
			// cumul des paniers et des boites
			for ($i=0;$i<4;$i++) {
				$total[$tabCle[$i]]['paniers'] += $uneLigne[$tabCle[$i]]['paniers'];
				$total[$tabCle[$i]]['boites'] += $uneLigne[$tabCle[$i]]['boites'];
			}
*/			 
			if ($backgroundcolor=='#d5d5d5') $backgroundcolor = '#ffffff';
			else $backgroundcolor = '#d5d5d5';
			$html .= <<<EOT
					<tr style="background-color: $backgroundcolor; page-break-inside: avoid;">
						<td width="25%">
							{$uneLigne['titulaires']}
						</td>
						<td width="55%">
							{$uneLigne['commentaire']}
						</td>

						<td width="5%" style="text-align: center; font-weight: bold;">
EOT;
			if ($uneLigne[$tabCle[0]]['paniers']>0) {
				$html .= <<<EOT
							{$uneLigne[$tabCle[0]]['paniers']} <img src="images/panier20.png"  style="width: 5.36mm; height: auto;"><br>
EOT;
			}
			else {
				$html .= <<<EOT
							&nbsp;
EOT;
			}
			if ($uneLigne[$tabCle[0]]['boites']>0) {
				$html .= <<<EOT
							{$uneLigne[$tabCle[0]]['boites']} <img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;">
EOT;
			}				
			else {
				$html .= <<<EOT
							&nbsp;
EOT;
			}
				$html .= <<<EOT
						</td>
						
						<td width="5%" style="text-align: center; font-weight: bold;">
EOT;
			if ($uneLigne[$tabCle[1]]['paniers']>0) {
				$html .= <<<EOT
							{$uneLigne[$tabCle[1]]['paniers']} <img src="images/panier20.png"  style="width: 5.36mm; height: auto;"><br>
EOT;
			}
			else {
				$html .= <<<EOT
							&nbsp;
EOT;
			}
			if ($uneLigne[$tabCle[1]]['boites']>0) {
				$html .= <<<EOT
							{$uneLigne[$tabCle[1]]['boites']} <img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;">
EOT;
			}				
			else {
				$html .= <<<EOT
							&nbsp;
EOT;
			}
				$html .= <<<EOT
						</td>
						
						<td width="5%" style="text-align: center; font-weight: bold;">
EOT;
			if ($uneLigne[$tabCle[2]]['paniers']>0) {
				$html .= <<<EOT
							{$uneLigne[$tabCle[2]]['paniers']} <img src="images/panier20.png"  style="width: 5.36mm; height: auto;"><br>
EOT;
			}
			else {
				$html .= <<<EOT
							&nbsp;
EOT;
			}
			if ($uneLigne[$tabCle[2]]['boites']>0) {
				$html .= <<<EOT
							{$uneLigne[$tabCle[2]]['boites']} <img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;">
EOT;
			}				
			else {
				$html .= <<<EOT
							&nbsp;
EOT;
			}
				$html .= <<<EOT
						</td>
						
						<td width="5%" style="text-align: center; font-weight: bold;">
EOT;
			if ($uneLigne[$tabCle[3]]['paniers']>0) {
				$html .= <<<EOT
							{$uneLigne[$tabCle[3]]['paniers']} <img src="images/panier20.png"  style="width: 5.36mm; height: auto;"><br>
EOT;
			}
			else {
				$html .= <<<EOT
							&nbsp;
EOT;
			}
			if ($uneLigne[$tabCle[3]]['boites']>0) {
				$html .= <<<EOT
							{$uneLigne[$tabCle[3]]['boites']} <img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;">
EOT;
			}				
			else {
				$html .= <<<EOT
							&nbsp;
EOT;
			}


			$html .= <<<EOT
						</td>
					</tr>
EOT;
		}
		// ligne des totaux paniers
		$html .= <<<EOT
					<tr>
						<td colspan="2" style="text-align: right;">
							<img src="images/panier20.png"  style="width: 5.36mm; height: auto;"> nombre total : &nbsp;
						</td>
EOT;
		$html .= <<<EOT
						<td width="5%" style="text-align: center; font-weight: bold;">
							{$totalPaniers[$tabCle[0]]} <img src="images/panier20.png"  style="width: 5.36mm; height: auto;">
						</td>
						<td width="5%" style="text-align: center; font-weight: bold;">
							{$totalPaniers[$tabCle[1]]} <img src="images/panier20.png"  style="width: 5.36mm; height: auto;">
						</td>
						<td width="5%" style="text-align: center; font-weight: bold;">
							{$totalPaniers[$tabCle[2]]} <img src="images/panier20.png"  style="width: 5.36mm; height: auto;">
						</td>
						<td width="5%" style="text-align: center; font-weight: bold;">
							{$totalPaniers[$tabCle[3]]} <img src="images/panier20.png"  style="width: 5.36mm; height: auto;">
						</td>
EOT;

		$html .= <<<EOT
					</tr>
EOT;

		// ligne des totaux boites
		$html .= <<<EOT
					<tr>
						<td colspan="2" style="text-align: right;">
							<img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;"> nombre total : &nbsp;
						</td>
EOT;
		$html .= <<<EOT
						<td width="5%" style="text-align: center; font-weight: bold;">
							{$totalBoites[$tabCle[0]]} <img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;">
						</td>
						<td width="5%" style="text-align: center; font-weight: bold;">
							{$totalBoites[$tabCle[1]]} <img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;">
						</td>
						<td width="5%" style="text-align: center; font-weight: bold;">
							{$totalBoites[$tabCle[2]]} <img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;">
						</td>
						<td width="5%" style="text-align: center; font-weight: bold;">
							{$totalBoites[$tabCle[3]]} <img src="images/oeuf20.png"  style="width: 5.36mm; height: auto;">
						</td>
EOT;

		$html .= <<<EOT
					</tr>
EOT;






		$html .= <<<EOT
				</tbody>
			</table>
EOT;
//die($html);
		// passage en pdf
		require_once('pdfListeEmargementConfigAlt.php');
		require_once('TCPDF/tcpdf.php');

		// Extend the TCPDF class to create custom Header and Footer
		class MYPDF extends TCPDF {

			// Page footer
			public function Footer() {
				$cur_y = $this->y;
				$this->SetTextColorArray($this->footer_text_color);
				//set style for cell border
				$line_width = (0.85 / $this->k);
				$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
				//print document barcode
				$barcode = $this->getBarcode();
				if (!empty($barcode)) {
					$this->Ln($line_width);
					$barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
					$style = array(
						'position' => $this->rtl?'R':'L',
						'align' => $this->rtl?'R':'L',
						'stretch' => false,
						'fitwidth' => true,
						'cellfitalign' => '',
						'border' => false,
						'padding' => 0,
						'fgcolor' => array(0,0,0),
						'bgcolor' => false,
						'text' => false
					);
					$this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
				}
				$w_page = isset($this->l['w_page']) ? $this->l['w_page'].' ' : '';
				if (empty($this->pagegroups)) {
					$pagenumtxt = "Émargement ".$w_page.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
				} else {
					$pagenumtxt = "Émargement ".$w_page.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
				}
				$this->SetY($cur_y);
				//Print page number
				if ($this->getRTL()) {
					$this->SetX($this->original_rMargin);
					$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
				} else {
					$this->SetX($this->original_lMargin);
					$this->Cell(0, 0, $this->getAliasRightShift().$pagenumtxt, 'T', 0, 'R');
				}
			}
		}

		// create new PDF document
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('PAMA-PDM');
		$pdf->SetTitle('Liste d\'émargement');
		$pdf->SetSubject('Émargement');
		$pdf->SetKeywords('Émargement');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$pdf->setPrintHeader(false);

		$pdf->setFooterData(array(0,64,0), array(0,64,128));

		/*
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/fra.php')) { // eng
			require_once(dirname(__FILE__).'/lang/fra.php');	 // eng
			$pdf->setLanguageArray($l);
		}
		// remplacé par son contenu :
		*/

		// French

		global $l;
		$l = Array();

		// PAGE META DESCRIPTORS --------------------------------------

		$l['a_meta_charset'] = 'UTF-8';
		$l['a_meta_dir'] = 'ltr';
		$l['a_meta_language'] = 'fr';

		// TRANSLATIONS --------------------------------------
		$l['w_page'] = 'page';
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------
		// fin du remplacement

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 9, '', true); // 14

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();
/*
		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
*/

		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('liste_emargement.pdf', 'D'); // I
		
	} // fin listeEmargementPDF
	
	function listeVolontairesPDF() {
		// les 5 dates de distribution : date et nombre d'unités (5 pour les volontaires )
		$firstDistribution = $_POST['idDistribution'];
		$lastDistribution = $firstDistribution+7; //( à cause de Noël)
		$sql = "SELECT idDistribution, date, semaine, nombreUnites FROM PDM_distribution WHERE idDistribution BETWEEN $firstDistribution AND $lastDistribution AND nombreUnites>0 ORDER BY date";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while ($laLigne = mysqli_fetch_assoc($res)) {
			if ($i<5) {
				$distribution[$i]['idDistribution'] = $laLigne['idDistribution'];
				$distribution[$i]['date'] = $laLigne['date'];
				$distribution[$i]['nombreUnites'] = $laLigne['nombreUnites'];
				$distribution[$i]['semaine'] = $laLigne['semaine'];
				$i++;
			}
		}
/*		
		// les volontaires pour ces 4 distributions
		// charger les volontaires de ces 4 distributions
		$sql = "SELECT distributionId, nom, emargement, distribution FROM PDM_volontaire, PDM_personne  WHERE distributionId BETWEEN {$distribution[0]['idDistribution']} AND {$distribution[3]['idDistribution']} AND personneId=idPersonne";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($laLigne = mysqli_fetch_assoc($res)) {
			$volontaire[] = $laLigne;
		}
*/
		// charger les volontaires Distribution de ces distributions
		$sql = "SELECT * FROM PDM_volontaireDistribution, PDM_personne  WHERE distributionId BETWEEN {$distribution[0]['idDistribution']} AND {$distribution[3]['idDistribution']} AND personneId=idPersonne";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$volontaireDistribution = FALSE;
		while ($laLigne = mysqli_fetch_assoc($res)) {
			$volontaireDistribution[$laLigne['distributionId']][$laLigne['personneId']] = $laLigne;
		}

		// charger les volontaires Emargement de ces distributions
		$sql = "SELECT * FROM PDM_volontaireEmargement, PDM_personne  WHERE distributionId BETWEEN {$distribution[0]['idDistribution']} AND {$distribution[3]['idDistribution']} AND personneId=idPersonne";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$volontaireEmargement = FALSE;
		while ($laLigne = mysqli_fetch_assoc($res)) {
			$volontaireEmargement[$laLigne['distributionId']][$laLigne['personneId']] = $laLigne;
		}

		
		// tri par nom
		function compNom($a,$b) {
			if ($a['nom']<$b['nom']) return -1;
			else return 1;
		}
		if (isset($volontaire)) usort($volontaire,"compNom");
		// $html
		// table volontaires
		$htmlVolontaires = '';
		$htmlVolontaires .=  <<<EOT
EOT;

		$htmlVolontaires .= <<<EOT
			<table cellspacing="0" cellpadding="1" border="1"  style="width:100%; font-size: 12pt;  font-family: deajvusans;"  >
				<tbody>
					<tr>
						<td style="width: 50mm;"> 
							<img src="images/logoPaniersMarseillais.png" style="width: 15mm; height: auto;">
						</td>
						<td >
							PAMA La Plaine du Mont
						</td>
						<td style="font-size: 18pt; font-weight: bold;">
							Liste des volontaires
						</td>
					</tr>
				</tbody>
			<table>
			
			<table cellspacing="0" cellpadding="1" border="1"  style="width:100%; margin: auto; font-size: 12pt;  font-family: deajvusans;"  >
				<tbody>
					<tr  align="center" style="font-weight: bold;">
						<th>
							distribution du
						</th>
						<th>
							distribution
						</th>
						<th>
							émargement
						</th>
					</tr>
EOT;
		// les lignes du tableau
		foreach ($distribution AS $uneDistribution) {
			$idDistribution = $uneDistribution['idDistribution'];
			// date
			$tabDate = explode('-',$uneDistribution['date']);
			$laDate = $tabDate[2].'/'.$tabDate[1];
			// volontaires
			$nbVE = 0;
			$nbVD = 0;
			$volontairesEmargementHTML = ' <ul> ';
			$volontairesDistributionHTML = ' <ul> ';

			if (isset($volontaireDistribution[$idDistribution])) {
				foreach ($volontaireDistribution[$idDistribution] AS $volontairePersonne) {
					$volontairesDistributionHTML .= "<li>{$volontairePersonne['nom']}</li>";
					$nbVD++;
				}
			}
				
			if (isset($volontaireEmargement[$idDistribution])) {
				foreach ($volontaireEmargement[$idDistribution] AS $volontairePersonne) {
					$volontairesEmargementHTML.= "<li>{$volontairePersonne['nom']}</li>";
					$nbVE++;
				}
			}
				
/*				
			foreach ($volontaireDistribution AS $unVolontaire) {
				if ($unVolontaire['distributionId']==$idDistribution ) {
					$volontairesDistributionHTML .= "<li>{$unVolontaire['nom']}</li>";
					$nbVD++;
				}
			}
			if (isset($volontaireEmargement)) foreach ($volontaireEmargement AS $unVolontaire) {
				if ($unVolontaire['distributionId']==$idDistribution) {
					$volontairesEmargementHTML .= "<li>{$unVolontaire['nom']}</li>";
					$nbVE++;
				}
			}
*/
			// compléter listes
			for ($n=$nbVD; $n<4 ; $n++) {
				$volontairesDistributionHTML .= " <li>&nbsp;</li> ";
			}
			for ($n=$nbVE; $n<2 ; $n++) {
				$volontairesEmargementHTML .= " <li>&nbsp;</li> ";
			}
			
			$volontairesEmargementHTML .= ' </ul> ';
			$volontairesDistributionHTML .= ' </ul> ';
			$htmlVolontaires .= <<<EOT
					<tr>
						<td  align="center" style="font-weight: bold;">
							<br><br>$laDate
						</td>
						<td>
							$volontairesDistributionHTML
						</td>
						<td>
							$volontairesEmargementHTML
						</td>
					</tr>
EOT;
		}
		$htmlVolontaires .= <<<EOT
				</tbody>
			</table>
EOT;
//die($htmlVolontaires); 
	
	// pdf
		require_once('pdfListeVolontairesConfigAlt.php');
		require_once('TCPDF/tcpdf.php');

		// Extend the TCPDF class to create custom Header and Footer
		class MYPDF extends TCPDF {

			// Page footer
			public function Footer() {
				$cur_y = $this->y;
				$this->SetTextColorArray($this->footer_text_color);
				//set style for cell border
				$line_width = (0.85 / $this->k);
				$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
				//print document barcode
				$barcode = $this->getBarcode();
				if (!empty($barcode)) {
					$this->Ln($line_width);
					$barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
					$style = array(
						'position' => $this->rtl?'R':'L',
						'align' => $this->rtl?'R':'L',
						'stretch' => false,
						'fitwidth' => true,
						'cellfitalign' => '',
						'border' => false,
						'padding' => 0,
						'fgcolor' => array(0,0,0),
						'bgcolor' => false,
						'text' => false
					);
					$this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
				}
				$w_page = isset($this->l['w_page']) ? $this->l['w_page'].' ' : '';
				if (empty($this->pagegroups)) {
					$pagenumtxt = "Liste des volontaires ".$w_page.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
				} else {
					$pagenumtxt = "Liste des volontaires ".$w_page.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
				}
				$this->SetY($cur_y);
				//Print page number
				if ($this->getRTL()) {
					$this->SetX($this->original_rMargin);
					$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
				} else {
					$this->SetX($this->original_lMargin);
					$this->Cell(0, 0, $this->getAliasRightShift().$pagenumtxt, 'T', 0, 'R');
				}
			}
		}

		// create new PDF document
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('PAMA-PDM');
		$pdf->SetTitle('Liste des volontaires');
		$pdf->SetSubject('Volontaires');
		$pdf->SetKeywords('Volontaires');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$pdf->setPrintHeader(false);

		$pdf->setFooterData(array(0,64,0), array(0,64,128));

		/*
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/fra.php')) { // eng
			require_once(dirname(__FILE__).'/lang/fra.php');	 // eng
			$pdf->setLanguageArray($l);
		}
		// remplacé par son contenu :
		*/

		// French

		global $l;
		$l = Array();

		// PAGE META DESCRIPTORS --------------------------------------

		$l['a_meta_charset'] = 'UTF-8';
		$l['a_meta_dir'] = 'ltr';
		$l['a_meta_language'] = 'fr';

		// TRANSLATIONS --------------------------------------
		$l['w_page'] = 'page';
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------
		// fin du remplacement

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 9, '', true); // 14

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();
/*
		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
*/

		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->writeHTML($htmlVolontaires, true, false, true, false, '');
		$pdf->Output('liste_volontaires.pdf', 'D'); // I
		
	} // fin listeVolontairesPDF
	
	function exportDistributions() {
		
	} // fin exportDistributions

	// les personnes du compte
	function duCompte($tab) {
	return $tab['compteId']==$GLOBALS['param'];		
	}

	// tri par nom de titulaires
	function compTitulaires($a,$b) {
		if ($a['titulaires']<$b['titulaires']) return -1;
		else return 1;
	}

	
	function calculerListeEmargement($idDistribution,$limite) { // $limite = TRUE
		switch ($limite) {
			case 1 :
			$firstDistribution = $idDistribution;
			$lastDistribution = $firstDistribution; 
			$whereIdDistribution = "WHERE idDistribution BETWEEN $firstDistribution AND $lastDistribution";				
			break;
			case 4 :
			$firstDistribution = $idDistribution;
			$lastDistribution = $firstDistribution+3; //( à cause de Noël : NON)
			$whereIdDistribution = "WHERE idDistribution BETWEEN $firstDistribution AND $lastDistribution";				
			break;
			case 0 : // tous
			$firstDistribution = $idDistribution;
			$whereIdDistribution = "WHERE idDistribution >= $firstDistribution";
			break;
		}
/*
		if ($limite) {
			// les 5 dates de distribution : date et nombre d'unités 
			$firstDistribution = $idDistribution;
			$lastDistribution = $firstDistribution+5+1; //( à cause de Noël)
			$whereIdDistribution = "WHERE idDistribution BETWEEN $firstDistribution AND $lastDistribution";
		}
		else { // toutes
			$firstDistribution = $idDistribution;
			$whereIdDistribution = "WHERE idDistribution >= $firstDistribution";
		}
*/
		$sql = "SELECT idDistribution, date, semaine, nombreUnites FROM PDM_distribution $whereIdDistribution AND nombreUnites>0 ORDER BY date";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$n = 0;
		while ($uneDistribution = mysqli_fetch_assoc($res)) {
//			if ($n<5) {
					$j = $uneDistribution['idDistribution'];
					$distribution[$j]['idDistribution'] = $uneDistribution['idDistribution'];
					$distribution[$j]['date'] = $uneDistribution['date'];
					$tabDate = explode('-',$uneDistribution['date']);
					$distribution[$j]['jourMois'] = $tabDate[2].'/'.$tabDate[1];
					$distribution[$j]['nombreUnites'] = $uneDistribution['nombreUnites'];
					$distribution[$j]['semaine'] = $uneDistribution['semaine'];
					if ($n==0) $dateDebutPeriode = $distribution[$j]['date'];
					$dateFinPeriode = $distribution[$j]['date'];
				
//			}
			$n++;
		}
//var_dump($distribution);die;

		// les comptes qui ont au moins un contrat actif dans la période
		//$sql = "SELECT idContrat, compteId, typeContratId, quantiteContratId, frequenceContratId, periodeContratId, dateDebut, dateFin FROM PDM_contrat WHERE valide=1 AND dateDebut<='{$distribution[0]['date']}' AND dateFin>='{$distribution[3]['date']}";
		$sql = "SELECT idCompte, titulairePrincipalId, commentaire FROM PDM_compte, PDM_contrat WHERE idCompte=compteId AND idContrat IN (SELECT idContrat FROM PDM_contrat WHERE valide=1 AND (dateDebut<='$dateFinPeriode' AND dateFin>='$dateDebutPeriode')) GROUP BY idCompte";
//die($sql);
		$res = mysqli_query($GLOBALS['lkId'],$sql);
//		$i = 0;
		while ($unCompte = mysqli_fetch_assoc($res)) {
			$i = $unCompte['idCompte'];
			$ligne[$i]['idCompte'] = $unCompte['idCompte'];
			$ligne[$i]['commentaire'] = $unCompte['commentaire'];
			// ajout éventuel des absences aux commentaires fonction voir init.inc.php
			$ligne[$i]['commentaire'] .= ajoutAbsences($ligne[$i]['idCompte']);
			$ligne[$i]['commentaire'] = str_replace("\r\n",'<br>',$ligne[$i]['commentaire']);
			$ligne[$i]['titulairePrincipalId'] = $unCompte['titulairePrincipalId'];
//			$i++;
		}
//var_dump($ligne);die;		
		
		// toutes les personnes
		$sql = "SELECT idPersonne, nom, compteId FROM PDM_personne ORDER BY compteId, idPersonne";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($unePersonne = mysqli_fetch_assoc($res)) {
			$personne[] = $unePersonne;
		}
		
		
		// ajout des noms de tous les titulaires au tableau ligne 
		$n = 0;
		foreach ($ligne AS $i => $uneLigne) {
			$idCompte = $uneLigne['idCompte'];
			$GLOBALS['param'] = $idCompte;
			$ligne[$i]['titulaires'] = '';
			$ligne[$i]['titulairesBouton'] = '';
			// les personnes du comptes : titulaires
			$personneDuCompte = array_filter($personne, "duCompte");
			foreach ($personneDuCompte AS $unePersonneDuCompte) {
				if ($unePersonneDuCompte['idPersonne']
					==$ligne[$i]['titulairePrincipalId']) {
					$ligne[$i]['titulaires'] = "<b>{$unePersonneDuCompte['nom']}</b>".$ligne[$i]['titulaires'];
					$ligne[$i]['titulairesBouton'] = $unePersonneDuCompte['nom'].' '.$ligne[$i]['titulairesBouton'];
				}
				else {
					$ligne[$i]['titulaires'] = $ligne[$i]['titulaires']." {$unePersonneDuCompte['nom']}";
					$ligne[$i]['titulairesBouton'] = $ligne[$i]['titulairesBouton']." {$unePersonneDuCompte['nom']}";
				}
			}
			$n++;
		}
/*		
$i = $uneDistribution['idDistribution'];
$distribution[$i]['idDistribution'] = $uneDistribution['idDistribution'];
$distribution[$i]['date'] = $uneDistribution['date'];
$tabDate = explode('-',$uneDistribution['date']);
$distribution[$i]['jourMois'] = $tabDate[2].'/'.$tabDate[1];
$distribution[$i]['nombreUnites'] = $uneDistribution['nombreUnites'];
$distribution[$i]['semaine'] = $uneDistribution['semaine'];
if ($i==0) $dateDebutPeriode = $distribution[$i]['date'];
$dateFinPeriode = $distribution[$i]['date'];
*/
		// tri par nom
		
		// pour chaque date de distribution
		$totalPaniers = array();
//		$j = 0;
		foreach ($distribution AS $j => $uneDistribution) {
			$idDistribution = $uneDistribution['idDistribution'];
			$dateDistribution = $uneDistribution['date'];
			$semaineDistribution = $uneDistribution['semaine'];
			$nombreUnitesDistribution = $uneDistribution['nombreUnites'];
			
			$totalPaniers[$idDistribution] = 0;
			$totalBoites[$idDistribution] = 0;
			
			$whereFrequence = " frequenceContratId=1 ";
			if ($semaineDistribution=='Q2') $whereFrequence .= "OR frequenceContratId=3 ";
			else $whereFrequence .= "OR frequenceContratId=2 ";
			
			// tous les contrats valides ne sert à rien !!!!!!
			$sql = "SELECT idContrat FROM PDM_contrat WHERE valide=1 AND dateDebut<='$dateDistribution' AND dateFin>='$dateDistribution' AND $whereFrequence";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			while ($unContrat = mysqli_fetch_assoc($res)) {
				$contrat[] = $unContrat;
			}
			
			// pour chaque ligne (compte)
			foreach ($ligne AS $i => $uneLigne) {
				$idCompte = $uneLigne['idCompte'];
				
				// ajouter le nombre de paniers
				$sql = "SELECT SUM(MOD(quantiteContratId,3)) AS nbPaniers, idContrat FROM PDM_contrat WHERE valide=1 AND dateDebut<='$dateDistribution' AND dateFin>='$dateDistribution' AND ($whereFrequence) AND compteId=$idCompte AND typeContratId=1  GROUP BY idContrat";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				if ($unNombrePaniers = mysqli_fetch_assoc($res)) {
					$idContrat = $unNombrePaniers['idContrat'];
//if($idContrat==82) die($sql);
					$nbPaniers = $unNombrePaniers['nbPaniers'];
					if (isset($ligne[$i][$j]))
						$ligne[$i][$j]['paniers'] += $nbPaniers*$nombreUnitesDistribution;
					else $ligne[$i][$j]['paniers'] = $nbPaniers*$nombreUnitesDistribution;
					
					// ajouter le nombre de paniers ajoutés
					$sql = "SELECT quantite FROM PDM_ajout WHERE distributionId=$idDistribution AND contratId=$idContrat ";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					if ($ajout = mysqli_fetch_assoc($res)) {
						$ligne[$i][$j]['paniers'] += $ajout['quantite'];
					}
					$nombreReports = 0;
					// retirer le nombre de paniers reporté depuis cette date (un report = 1 panier)
					$sql = "SELECT count(idReport) AS nbReports FROM PDM_report WHERE contratId=$idContrat AND origine=$idDistribution";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					if ($retire = mysqli_fetch_assoc($res)) {
						if ($retire['nbReports']>0) {

							$nombreReports = $retire['nbReports']*$nombreUnitesDistribution;
							$ligne[$i][$j]['paniers'] -= $nombreReports;
							// rajoute ce report à sa destination
							$sql = "SELECT destination FROM PDM_report WHERE contratId=$idContrat AND origine=$idDistribution";


							$res = mysqli_query($GLOBALS['lkId'],$sql);
							$rajoute = mysqli_fetch_assoc($res);
							$idDestination = $rajoute['destination'];
							if (isset($ligne[$i][$idDestination]))
								$ligne[$i][$idDestination]['paniers'] += $nombreReports;
							else $ligne[$i][$idDestination]['paniers'] = $nombreReports;
/*
echo($sql);
var_dump($ligne[$i]);
die;
*/
						}
					}
				}
				else {
					if (!isset($ligne[$i][$j])) $ligne[$i][$j]['paniers'] = 0;
				}
					

				
				// ajouter le nombre de boîtes d'oeufs
				$sql = "SELECT SUM(quantiteContratId)-3 AS nbBoites FROM PDM_contrat WHERE valide=1 AND dateDebut<='$dateDistribution' AND dateFin>='$dateDistribution' AND ($whereFrequence) AND compteId=$idCompte AND typeContratId=2";
				
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				if ($laLigne = mysqli_fetch_assoc($res)) {
					$nbBoites = $laLigne['nbBoites'];
					$ligne[$i][$j]['boites'] = $nbBoites*$nombreUnitesDistribution;
				}
				else $ligne[$i][$j]['boites'] = 0;
				// pas d'ajout ou de report d'oeufs	
				
				$totalPaniers[$idDistribution] += $ligne[$i][$j]['paniers'];
				$totalBoites[$idDistribution] += $ligne[$i][$j]['boites'];
			} // fin pour chaque ligne	

//			$j++; 

		} // fin pour chaque distribution
		uasort($ligne,"compTitulaires");
		$reponse['distribution'] = $distribution;
		$reponse['ligne'] = $ligne;
		$reponse['totalPaniers'] = $totalPaniers;
		$reponse['totalBoites'] = $totalBoites;
//var_dump($totalBoites); die;
		return $reponse;

	} // fin calculerListeEmargement
	
	function calculerDistributionTotaux($idDistribution) {
		// la distribution
		$sql = "SELECT idDistribution, date, nombreUnites, semaine, semestre FROM PDM_distribution WHERE idDistribution=$idDistribution ";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$laLigne = mysqli_fetch_assoc($res);
		$nombreUniteseDistribution = $laLigne['nombreUnites'];
		$dateDistribution = $laLigne['date'];
		$semestreDistribution = $laLigne['semestre'];
		$semaineDistribution = $laLigne['semaine'];
		
		if ($semaineDistribution=='Q2') $whereFrequence = "(frequenceContratId=1 OR frequenceContratId=3)";
		else $whereFrequence = "(frequenceContratId=1 OR frequenceContratId=2)";
			
		// total paniers
		// contrats paniers actifs à cette date
		$sql = "SELECT SUM(quantiteContratId) AS SQP FROM PDM_contrat WHERE typeContratId=1 AND valide=1 AND $whereFrequence AND '$dateDistribution' BETWEEN dateDebut AND dateFin";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$laLigne = mysqli_fetch_assoc($res);
		$totalPaniers = $laLigne['SQP']*$nombreUniteseDistribution;
//if($idDistribution==6) echo "{$laLigne['SQP']} nb paniers contrats : $sql<br>\n";

		// ajout des ajouts
		// SELECT idAjout, contratId, distributionId, quantite, dateModification FROM PDM_ajout WHERE 1
		$sql = "SELECT SUM(quantite) AS SAP FROM PDM_ajout WHERE distributionId=$idDistribution";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$laLigne = mysqli_fetch_assoc($res);
		$totalPaniers += $laLigne['SAP'];
//if($idDistribution==6) echo "{$laLigne['SAP']} nb paniers ajoutés : $sql<br>\n";
		
		// retrait des reports origine
		// SELECT idReport, contratId, quantite, origine, destination, dateModification FROM PDM_report WHERE 1
		$sql = "SELECT SUM(quantite) AS SRP FROM PDM_report WHERE origine=$idDistribution";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$laLigne = mysqli_fetch_assoc($res);
		$totalPaniers -= $laLigne['SRP'];
//if($idDistribution==6) echo "{$laLigne['SRP']} nb paniers reports en moins : $sql<br>\n";
		
		// ajout des reports destination
		$sql = "SELECT SUM(quantite) AS SRP FROM PDM_report WHERE destination=$idDistribution";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$laLigne = mysqli_fetch_assoc($res);
		$totalPaniers += $laLigne['SRP'];
/*
if($idDistribution==6) {
	echo("{$laLigne['SRP']} nb paniers reports en plus : $sql<br>\n");
	die("total = $totalPaniers");
}
*/
		// total boites
		$sql = "SELECT SUM(quantiteContratId-3) AS SQB FROM PDM_contrat WHERE typeContratId=2 AND valide=1 AND $whereFrequence AND '$dateDistribution' BETWEEN dateDebut AND dateFin";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$laLigne = mysqli_fetch_assoc($res);
		$totalBoites = $laLigne['SQB']*$nombreUniteseDistribution;
		
		$nombreTotal['paniers'] = $totalPaniers;
		$nombreTotal['boites'] = $totalBoites;
		return $nombreTotal;
	}
?>