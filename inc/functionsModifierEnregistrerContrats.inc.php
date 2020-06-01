<?php
// functionsModifierEnregistrerContrats.inc.php
/*
fichier destiné à être inclus dans comptes.php et contrats.php pour modifier puis enregistrer les contrats dun compte sélectionné : $_POST['select']
*/
/*
$date = "2020-03-08";
echo($date.' '.premierMercrediPostCommande($date));
die();
*/
///////////////////////////////
// fonctions principales
///////////////////////////////
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
	
	?>