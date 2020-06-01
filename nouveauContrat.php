<?php
// nouveauContrat.php

/*
$_POST :
	newAction : nouveauContrat
	annee : idAnnee
	periode : idPeriode 1 2 3 4
	origineNouveauContrat : accueil.php ou comptes.php
	
	
*/
	session_start();
	include('inc/init.inc.php');

//var_dump($_SESSION); 
//var_dump($_POST); die;

/*
SESSION
array(4) {
  ["auj"]=>
  string(10) "2020-05-15"
  ["anneeCouranteId"]=>
  string(1) "2"
  ["quiAfficher"]=>
  string(9) "adherents"
  ["scriptOrigine"]=>
  string(11) "comptes.php"
}
POST
array(5) {
  ["newAction"]=>
  string(5) "annee" (periode) 1:annee 2: semestre1 3: semestre2 4: mensuel OU enregistrer
  ["idCompte"]=>
  string(1) "4" // => idCompte défini par admin ou adhérent enregistré
  ["origineNouveauContrat"]=>
  string(11) "comptes.php"
  ["divId"]=>
  string(8) "contrats"
  ["nouveau"]=>
  array(1) {
    ["annee"]=>
    string(1) "2"
  }
  etapte
}
*/
	// étape 1 ?
	if (!isset($_POST['etape'])) $_POST['etape'] = 1; // pseudo POST
	
	
	// idCompte ?
	if (!isset($_POST['idCompte'])) { 
		if (isset($_POST['select'])) $_POST['idCompte'] = $_POST['select'];
		else $_POST['idCompte'] = '';
	}
	
	switch ($_POST['newAction']) {
		case 'nouveauContrat' :
			afficherNouveauContrat($_POST['periode']);
			break;
		case 'enregistrerContrat' :
			$idContrat = enregistrerNouveauContrat();
			afficherNouveauxCheques($idContrat);
			break;
		case 'enregistrerCheques' :
			enregistrerNouveauxCheques($idCompte);
			terminer($_POST['origineNouveauContrat']);
			break;
		default :
			die('Accès interdit');
	}

	function terminer($origineNouveauContrat) {
?>
<!DOCTYPE html>
<html lang='fr-fr'>
	<body>
		<script>
			alert('Les chèques ont été enregistrés.');
			document.location.href="<?php echo "$origineNouveauContrat";?>"; 
		</script>
	</body>
</html>
<?php		
	} // fin function terminer

		
	function enregistrerNouveauContrat() {
		echo "on va enregistrer contrat";
	} // fin function enregistrerNouveauContrat()
	
	function enregistrerNouveauxCheques($idCompte) {
		echo "on va enregistrer chèques";
		
		// anneeId
		
		// cotisation déjà réglée ?
	}	
		
	function afficherNouveauContrat($periode) {
		$chequeAdhesion = FALSE;
		if ($_POST['idCompte']!='') {

			// chequeAdhesion
			$sql = "SELECT idChequeCotisation FROM PDM_chequeCotisation WHERE anneeId={$_POST['nouveau']['annee']} AND compteId={$_POST['idCompte']} ";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			$chequeAdhesion = mysqli_fetch_assoc($res);
			
			// titulaire principal
			$sql = "SELECT idPersonne, nom, adresse, telephone, courriel, compteId FROM PDM_personne, PDM_compte WHERE compteId=idCompte AND compteId={$_POST['idCompte']} AND titulairePrincipalId=idPersonne";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			$unTitulaire = mysqli_fetch_assoc($res);
			$titulaire[1]['nom'] = $unTitulaire['nom'];
			$titulaire[1]['adresse'] = $unTitulaire['adresse'];
			$titulaire[1]['telephone'] = $unTitulaire['telephone'];
			$titulaire[1]['courriel'] = $unTitulaire['courriel'];

			// co titulaire
			$sql = "SELECT idPersonne, nom, adresse, telephone, courriel, compteId FROM PDM_personne, PDM_compte WHERE compteId=idCompte AND compteId={$_POST['idCompte']} AND titulairePrincipalId!=idPersonne ORDER BY nom";
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			$i = 2;
			while ($unTitulaire = mysqli_fetch_assoc($res)) {
				$titulaire[$i]['nom'] = $unTitulaire['nom'];
				$titulaire[$i]['adresse'] = $unTitulaire['adresse'];
				$titulaire[$i]['telephone'] = $unTitulaire['telephone'];
				$titulaire[$i]['courriel'] = $unTitulaire['courriel'];
				$i++;
			}
			// complément
			for ($j=$i;$j<5;$j++) {
				$titulaire[$j]['nom'] = '';
				$titulaire[$j]['adresse'] = '';
				$titulaire[$j]['telephone'] = '';
				$titulaire[$j]['courriel'] = '';
			}
		} // fin si idCompte 
		else { // initialiser les pseudo POST
			for ($i=1;$i<5;$i++) {
				$titulaire[$i]['nom'] = '';
				$titulaire[$i]['prenom'] = '';
				$titulaire[$i]['adresse'] = '';
				$titulaire[$i]['telephone'] = '';
				$titulaire[$i]['courriel'] = '';
			}
		}

		// prix
		$sql = "SELECT idPrixAnneeTypeContrat, anneeId, typeContratId, prix FROM PDM_prixAnneeTypeContrat WHERE anneeId={$_POST['nouveau']['annee']} ORDER BY idPrixAnneeTypeContrat";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($unPrix = mysqli_fetch_assoc($res)) {
			$prix[] = $unPrix; 
		}
		$prixPanier = $prix[0]['prix'];
		$prixPanierEuro = number_format($prixPanier,2,',',' ').' €';
		$prixBoite = $prix[1]['prix'];
		$prixBoiteEuro = number_format($prixBoite,2,',',' ').' €';

		// toutes les distributions
		// et dates sans distribution et avec distribution double
		$sql = "SELECT idDistribution, date, nombreUnites FROM PDM_distribution WHERE anneeId={$_POST['nouveau']['annee']} ORDER BY idDistribution";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while ($uneDistribution = mysqli_fetch_assoc($res)) {
			$distribution[] = $uneDistribution;
			if ($uneDistribution['nombreUnites']!=1) {
				switch ($i) {
					case 0 :
						$dateAvantNoel = dateJJMMAAAA($uneDistribution['date']);
						$idDistributionAvantNoel = $uneDistribution['idDistribution'];
						$i++;
						break;
					case 1 :
						$dateNoel = dateJJMMAAAA($uneDistribution['date']);
						$idDistributionNoel = $uneDistribution['idDistribution'];
						$i++;
						break;
					case 2 :
						$dateJourAn = dateJJMMAAAA($uneDistribution['date']);
						$idDistributionJourAn = $uneDistribution['idDistribution'];
						$i++;
						break;
					case 3 :
						$dateApresJourAn = dateJJMMAAAA($uneDistribution['date']);
						$idDistributionApresJourAn = $uneDistribution['idDistribution'];
						$i++;
						break;
				}
			}
		}

		// $idDebutPeriode
		$idDebutPeriode[1] = $distribution[0]['idDistribution'];
		$idDebutPeriode[2] = $distribution[0]['idDistribution'];
		$idDebutPeriode[3] = $distribution[26]['idDistribution'];
		$idDebutPeriode[4] = 0;

		// année
		$sql = "SELECT idAnnee, courante, nom, nombreSemaines, jourDistribution, datePremiereDistribution, cotisation, cotisationPM, cotisationPPM FROM PDM_annee WHERE idAnnee='{$_POST['nouveau']['annee']}'";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$uneAnnee = mysqli_fetch_assoc($res);
		
		// cotisation
		$cotisation = $uneAnnee['cotisation'];
		$cotisationEuro = number_format($cotisation,2,',',' ').' €';
		$cotisationPM = $uneAnnee['cotisationPM'];
		$cotisationPMEuro = number_format($cotisationPM,2,',',' ').' €';
		$cotisationPPM = $uneAnnee['cotisationPPM'];
		$cotisationPPMEuro = number_format($cotisationPPM,2,',',' ').' €';

		
		// $dateDebutPeriode[1 2 3 ]
		$dateDebutPeriode[1] = date("d/m/Y", strtotime($uneAnnee['datePremiereDistribution']));
		$dateDebutPeriode[2] = $dateDebutPeriode[1];
		$dateDebutPeriode[3] = date("d/m/Y", strtotime($uneAnnee['datePremiereDistribution']." +25 week"));
		$dateDebutPeriode[4] = 0;

		// dateDerniereDistribution[1 2 3 ]
		$dateDerniereDistribution[1] = date("d/m/Y", strtotime($uneAnnee['datePremiereDistribution']." +51 week"));
		$dateDerniereDistribution[2] = date("d/m/Y", strtotime($uneAnnee['datePremiereDistribution']." +25 week"));
		$dateDerniereDistribution[3] = $dateDerniereDistribution[1];
		$dateDerniereDistribution[4] = 0;
		
		// dateAvantDerniereDistribution[1 2 3 ]
		$dateAvantDerniereDistribution[1] = date("d/m/Y", strtotime($uneAnnee['datePremiereDistribution']." +50 week"));
		$dateAvantDerniereDistribution[2] = date("d/m/Y", strtotime($uneAnnee['datePremiereDistribution']." +24 week"));
		$dateAvantDerniereDistribution[3] = $dateAvantDerniereDistribution[1];
		$dateAvantDerniereDistribution[4] = 0;
		
		// idDerniereDistribution[1 2 3 ]
		$sql = "SELECT idDistribution FROM PDM_distribution WHERE date='{$uneAnnee['datePremiereDistribution']}'";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$uneDistribution = mysqli_fetch_assoc($res);
		$idDerniereDistribution[1] = $uneDistribution['idDistribution']+51;
		$idDerniereDistribution[2] = $uneDistribution['idDistribution']+25;
		$idDerniereDistribution[3] = $uneDistribution['idDistribution']+51;
		$idDerniereDistribution[4] = 0;
		
		// $frequence
		$frequence = '
			<select name="frequence" id="frequence" onChange="mettreAJour();">
				<option value="1" selected="selected">par semaine</option>
				<option value="2">par quinzaine</option>
			</select>';
			
		// les 6 distributions futures possibles
		// selon public ou admin
		if ($_POST['origineNouveauContrat']=="accueil.php") $premiereDate = premierMercrediPostCommande($_SESSION['auj']);
		else $premiereDate = premierMercredi($_SESSION['auj']);
		$sql = "SELECT idDistribution, date FROM PDM_distribution WHERE date>='$premiereDate' AND  nombreUnites>0 AND anneeId={$_POST['nouveau']['annee']} ORDER BY date";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while (($uneDistribution = mysqli_fetch_assoc($res)) && $i<6) {
			$distribution[$i] = $uneDistribution;
			$distribution[$i]['dateJJMM'] = dateJJMM($distribution[$i]['date']);
			$i++;
		}
		

		// select date première distribution
		$datePemiereDistribution = <<<EOT
					<select name="idPremiereDistribution" id="idPremiereDistribution"  onChange="mettreAJour();">
EOT;
		for ($i=0; $i<6; $i++) {
			$dateDebut = dateJJMMAAAA($distribution[$i]['date']);
			$idDistribution = $distribution[$i]['idDistribution'];
			$datePemiereDistribution .= <<<EOT
						<option value="$idDistribution">$dateDebut</option>
EOT;
		}
		
		$datePemiereDistribution .= <<<EOT
					</select>
EOT;
		// select nombre de paniers
		$nombrePaniers = <<<EOT
					<select name="nombrePaniers" id="nombrePaniers" onChange="mettreAJour();">
						<option value="1" selected="selected">1 panier</option>
						<option value="2" >2 paniers</option>
						<option value="3" >3 paniers</option>
					</select>
		
EOT;

		// select nombre de boîtes
		$nombreBoites = <<<EOT
					<select name="nombreBoites" id="nombreBoites" onChange="mettreAJour();">
						<option value="0" selected="selected">aucune boîte</option>
						<option value="1" >1 boîte</option>
						<option value="2" >2 boîtes</option>
						<option value="3" >3 boîtes</option>
					</select>
		
EOT;

		// input rythme des chèques
		
		$jsMettreAJour = <<<EOT
			<script type="text/javascript">
			
				function formatEuro(number) {
					return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(number); 
				}
				
				function mettreAJour() {
					var periode = $periode;
					// les variables saisies
					var frequence = document.getElementById('frequence').value;
					var idPremiereDistribution = parseInt(document.getElementById('idPremiereDistribution').value,10);
//					var nombrePaniers = document.getElementById('nombrePaniers').value;
//					var rythmeChequesPaniers = document.getElementById('rythmeChequesPaniers').value;
//					var nombreBoites = document.getElementById('nombreBoites').value;
					
					// les valeurs calculées
					
					// idDerniereDistribution
					var idDerniereDistributionPeriode = {$idDerniereDistribution[$periode]}; 
					var idDerniereDistribution;
					
					if (frequence==2 && (idPremiereDistribution%2==1)) idDerniereDistribution = idDerniereDistributionPeriode-1;
					else idDerniereDistribution = idDerniereDistributionPeriode;
					// cas du contrat d'essai
					if (periode==4) {
						idDerniereDistribution = idPremiereDistribution+3;
						if (frequence==2) idDerniereDistribution = idPremiereDistribution+2;
					}
					
					// dateDerniereDistribution
					var dateDerniereDistribution;
					if (periode<4) { // année ou semestre
						if (frequence==2 && (idPremiereDistribution%2==1)) { // quinzaine et id impaire (Q2)
							dateDerniereDistribution = '{$dateAvantDerniereDistribution[$periode]}';
						}
						else {
							dateDerniereDistribution = '{$dateDerniereDistribution[$periode]}';
						}
					}
					else { // essai
						var sel = document.getElementById('idPremiereDistribution');
						var jjmmaaa = sel.options[sel.selectedIndex].text;
						var tab = jjmmaaa.split('/');
						var aaammjj = tab[2]+'-'+tab[1]+'-'+tab[0];
						var datePemiereDistribution = new Date(aaammjj);
						var datePemiereDistributionUNIX = datePemiereDistribution.getTime();
						var dateDerniereDistributionUNIX = datePemiereDistributionUNIX+(idDerniereDistribution-idPremiereDistribution+1)*7*24*3600*1000+3600*1000;
						var dda = new Date(dateDerniereDistributionUNIX);
						var jour, mois;
						var an = dda.getFullYear();
						var m = dda.getMonth()+1;
						if (m<10) mois = '0'+m.toString(); 
						else mois = m.toString();
						var j = dda.getDate();
						if (j<10) jour = '0'+j.toString();
						else jour = j.toString();
						dateDerniereDistribution = jour+'/'+mois+'/'+an;
					}
					document.getElementById('dateDerniereDistribution').innerHTML = dateDerniereDistribution;
				
					// nombrePaniersDistribues
					var nombrePaniersDistribues = idDerniereDistribution-idPremiereDistribution+1;
					if (frequence==1) nombrePaniersDistribues = idDerniereDistribution-idPremiereDistribution+1;
					else nombrePaniersDistribues = (idDerniereDistribution-idPremiereDistribution)/2+1;
					document.getElementById('nombrePaniersDistribues').innerHTML = nombrePaniersDistribues;
					
					// intervalle
					var intervalle;
					if (frequence==1) intervalle = 'par semaine';
					else intervalle = 'par quinzaine';
					document.getElementById('intervallePaniers').innerHTML = intervalle;
					document.getElementById('intervalleBoites').innerHTML = intervalle;
					
					// règlement Panier
					var nombrePaniersPris = document.getElementById('nombrePaniers').value;
					var nombrePetitsChequesPanier;
					var nombreGrosChequesPanier;
					var montantPetitsChequesPanier;
					var montantGrosChequesPanier;
					var petitsChequesPanier;
					var grosChequesPanier;
					if (periode==4) {
						nombrePetitsChequesPanier = 0;
						nombreGrosChequesPanier = 1;
						montantGrosChequesPanier = nombrePaniersDistribues*$prixPanier*nombrePaniersPris;
						montantGrosChequesPanier = Math.round(montantGrosChequesPanier*100)/100;
						petitsChequesPanier = false;
						grosChequesPanier = true;
					}
					else {
						
						// petits chèques
						var sel = document.getElementById("idPremiereDistribution");
						var datePremiereDistribution = sel.options[sel.selectedIndex].text;
						var tabDatePremiereDistribution = datePremiereDistribution.split('/');
						var moisPremiereDistribution = parseInt(tabDatePremiereDistribution[1],10);
						var tabDateDerniereDistribution = dateDerniereDistribution.split('/');
						var moisDerniereDistribution = parseInt(tabDateDerniereDistribution[1],10);
						// à cheval sur 2 ans
						if (moisDerniereDistribution<moisPremiereDistribution) moisDerniereDistribution = moisDerniereDistribution+12;
						var nombreMoisEntiers = moisDerniereDistribution-moisPremiereDistribution;
						nombrePetitsChequesPanier = nombreMoisEntiers;
						montantPetitsChequesPanier = (nombrePaniersDistribues*$prixPanier*nombrePaniersPris)/nombreMoisEntiers;
						montantPetitsChequesPanier = Math.round(montantPetitsChequesPanier*100)/100;
						// gros chèques
						if (periode==1) {
							nombreGrosChequesPanier = 2;
							montantGrosChequesPanier = (nombrePaniersDistribues*$prixPanier)/2;
							montantGrosChequesPanier = Math.round(montantGrosChequesPanier*100)/100;
							grosChequesPanier = true;
							if (nombrePetitsChequesPanier<=2) petitsChequesPanier = false;
							else petitsChequesPanier = true;
						}
						else {
							nombreGrosChequesPanier = 1;
							montantGrosChequesPanier = (nombrePaniersDistribues*$prixPanier);
							montantGrosChequesPanier = Math.round(montantGrosChequesPanier*100)/100;
							grosChequesPanier = true;
							if (nombrePetitsChequesPanier<=1) petitsChequesPanier = false;
							else petitsChequesPanier = true;
						}
					}
					var reglementPaniers = '<p> <input name="choixReglement" value="gros" checked="checked" type="radio" style="position: relative; top: 4px;"> Je règle par <b>'
					reglementPaniers += nombreGrosChequesPanier; 
					reglementPaniers += ' chèque';
					if (periode==1) reglementPaniers += 's';
					reglementPaniers += ' de ';
					reglementPaniers += formatEuro(montantGrosChequesPanier); 
					reglementPaniers += '</b></p>';
					if (petitsChequesPanier) {
						reglementPaniers += '<p><input name="choixReglement" value="petits" type="radio"  style="position: relative; top: 4px;"> Je règle par <b>'
						reglementPaniers += nombrePetitsChequesPanier;
						reglementPaniers += ' chèques de '
						reglementPaniers += formatEuro(montantPetitsChequesPanier);
						reglementPaniers += '</b></p>';
					}
					
					document.getElementById('reglementPaniers').innerHTML = reglementPaniers;
					
					// règlements boite
					
					if (periode!=4) {
						var nombreBoitesPrises = document.getElementById('nombreBoites').value;
						if (nombreBoitesPrises!='0') {
							var reglementBoites;
							var nombreGrosChequesBoite;
							var montantGrosChequesBoite;
							// gros chèques
							if (periode==1) {
								nombreGrosChequesBoite = 2;
								montantGrosChequesBoite = (nombrePaniersDistribues*$prixBoite*nombreBoitesPrises)/2;
								montantGrosChequesBoite = Math.round(montantGrosChequesBoite*100)/100;
							}
							else {
								nombreGrosChequesBoite = 1;
								montantGrosChequesBoite = (nombrePaniersDistribues*$prixBoite*nombreBoitesPrises);
								montantGrosChequesBoite = Math.round(montantGrosChequesBoite*100)/100;
							}
							
							reglementBoites = '<p> Je règle par <b>'
							reglementBoites += nombreGrosChequesBoite; 
							reglementBoites += ' chèque';
							if (periode==1) reglementBoites += 's';
							reglementBoites += ' de ';
							reglementBoites += formatEuro(montantGrosChequesBoite); 
							reglementBoites += '</b></p>';
							
							document.getElementById('reglementBoites').innerHTML = reglementBoites;
							
							// chequesBoite
							
						}
					}
					
				} // fin function mettreAJour
			</script>
EOT;



		$html =  <<<EOT
<!DOCTYPE html>
<html lang='fr-fr'>
	<head>
		<meta content="text/html; charset=UTF-8" http-equiv="content-type"> <!--ISO-8859-1 -->
		<title>Nouveau contrat</title>

		<link rel="stylesheet" type="text/css" href="css/normalize.css">
		<link rel="stylesheet" type="text/css" href="css/gestionPDM.css">
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
		$jsMettreAJour
	</head>
	<body style='font-family: sans-serif; font-size:small; padding: 10px;' onLoad='mettreAJour();' >
	<form method='POST' action='{$_POST['origineNouveauContrat']}' name="formAction" id='formAction' >
		<input type='hidden' name='newAction' id='newAction' value=''>
		<input type='hidden' name='origineNouveauContrat' id='origineNouveauContrat' value='{$_POST['origineNouveauContrat']}'>
		<input type='hidden' name='nouveau[annee]' id='nouveau[annee]' value='{$_POST['nouveau']['annee']}'>
		<input type='hidden' name='select' id='select' value='{$_POST['idCompte']}'>
		<input type='hidden' name='periode' id='periode' value='{$_POST['periode']}'>
		<input type='hidden' name='divId' id='divId' value='contrats'>
EOT;

		if ($periode!=4) {

			$html .= <<<EOT
		<h1>Contrat de partenariat solidaire entre le {$dateDebutPeriode[$periode]} et le {$dateDerniereDistribution[$periode]} inclus</h1>
EOT;
		}
		else  {
			$html .= <<<EOT
		<h1>Contrat de partenariat solidaire d'essai sur 4 semaines</h1>
EOT;
		}
		$html .= <<<EOT
		<img style="float: right; margin: 10px; height: 105px; width: 105px;" src="images/AB.png" alt="AB">
		<p class="grasgros">Association "Le Panier de la Plaine du Mont" adhérente des "Paniers Marseillais"</p>
		<p>contact.pdm@lespaniersmarseillais.org</p>
		<br>
		<p class="grasgros">Loïc Péré, agriculteur bio (label AB certifié par Ecocert) à Saint Gilles (Gard)</p>
		<ul>
			<li>s'engage à fournir chaque semaine (ou une semaine sur deux pour les contrats « quinzaine ») un panier de légumes cultivés par ses soins, pour une famille de deux personnes.</li>
			<li>s'engage à maintenir, la certification "agriculture biologique" de son exploitation.</li>
			<li>adhère aux principes de la Charte des "Paniers Marseillais".</li>
		</ul>
		<br>
		<p class="grasgros">Je soussigné[e]</p>
		
		<table > <!-- style="width: 100%;" -->
			<tbody>
				<tr>
					<th> NOM Prénom </th>
					<th> adresse </th>
					<th> n° de mobile </th>
					<th> adresse de courriel </th>
				</tr>
				<tr>
					<td> <input name="titulaire[1][nom]" type="text" size="20" value="{$titulaire[1]['nom']}"  ></td>
					<td> <input name="titulaire[1][adresse]" type="text" size="40"  value="{$titulaire[1]['adresse']}" ></td>
					<td> <input name="titulaire[1][telephone]" type="text" size="10" value="{$titulaire[1]['telephone']}"  ></td>
					<td> <input name="titulaire[1][courriel]" type="text" size="40" value="{$titulaire[1]['courriel']}"  ></td>
				</tr>
			</tbody>
		</table>
EOT;
		// co-titulaires
		$html .= <<<EOT
		<p>co-titulaires éventuels :</p>
		<table > <!-- style="width: 100%;" -->
			<tbody>
				<tr>
					<th> &nbsp;</th>
					<th> NOM Prénom </th>
					<th> adresse </th>
					<th> n° de mobile </th>
					<th> adresse de courriel </th>
				</tr>
				<tr>
					<td style="text-align: center;"> 1 </td>
					<td> <input name="titulaire[2][nom]" type="text" size="20" value="{$titulaire[2]['nom']}"  ></td>
					<td> <input name="titulaire[2][adresse]" type="text" size="40"  value="{$titulaire[2]['adresse']}" ></td>
					<td> <input name="titulaire[2][telephone]" type="text" size="10" value="{$titulaire[2]['telephone']}"  ></td>
					<td> <input name="titulaire[2][courriel]" type="text" size="40" value="{$titulaire[2]['courriel']}"  ></td>
				</tr>
EOT;
			$html .= <<<EOT
				<tr>
					<td style="text-align: center;"> 2 </td>
					<td> <input name="titulaire[3][nom]" type="text" size="20" value="{$titulaire[3]['nom']}"  ></td>
					<td> <input name="titulaire[3][adresse]" type="text" size="40"  value="{$titulaire[3]['adresse']}" ></td>
					<td> <input name="titulaire[3][telephone]" type="text" size="10" value="{$titulaire[3]['telephone']}"  ></td>
					<td> <input name="titulaire[3][courriel]" type="text" size="40" value="{$titulaire[3]['courriel']}"  ></td>
				</tr>
EOT;
			
			$html .= <<<EOT
				<tr>
					<td style="text-align: center;"> 3 </td>
					<td> <input name="titulaire[4][nom]" type="text" size="20" value="{$titulaire[4]['nom']}"  ></td>
					<td> <input name="titulaire[4][adresse]" type="text" size="40"  value="{$titulaire[4]['adresse']}" ></td>
					<td> <input name="titulaire[4][telephone]" type="text" size="10" value="{$titulaire[4]['telephone']}"  ></td>
					<td> <input name="titulaire[4][courriel]" type="text" size="40" value="{$titulaire[4]['courriel']}"  ></td>
				</tr>
EOT;
		$html .= <<<EOT
			</tbody>
		</table>
EOT;
		
		
		$html .= <<<EOT
		
		<ul>
			<li>m’engage pour la durée précisée ci-dessous à soutenir Loïc Péré dans sa démarche de production maraîchère bio. En cas de résiliation du contrat de ma part, je dois en faire la notification par écrit (courriel) 1 mois avant la première distribution du mois suivant. Les chèques non encaissés me seront restitués).</li>
			<li>accepte les conséquences sur la production des difficultés inhérentes à ce type de production et serai solidaire de Loïc Péré en cas de catastrophe climatique.</li>
			<li>m’engage à venir prendre mon panier chaque semaine ou chaque quinzaine ou à le faire récupérer par une personne de mon choix ; en cas d’oubli, mon panier ne sera ni remplacé ni remboursé. Je peux reporter un panier en prévenant au moins 7 jours à l'avance (pas plus de 4 paniers par an) ou demander la liste des personnes intéressées ponctuellement par un ou plusieurs de mes paniers "orphelins", charge à moi de m'entendre avec une de ces personnes personne pour la récupération de mon (mes) panier(s) et le partage des frais. Pas de report dans les 15 jours qui précèdent ou suivent les dates de renouvellement de contrat annoncées dans la lettre hebdo.</li>
			<li>m’engage à être bénévole 3 fois au moins par semestre pour assurer la distribution des légumes.</li>
			<li>adhère aux principes de la Charte des "Paniers Marseillais".</li>
		</ul>
EOT;

		if ($periode!=4 && !$chequeAdhesion) {
			$html .= <<<EOT
		<div id="adhesion">
		<br>
		<p class="grasgros">Adhésion à l'Association Panier de la Plaine du Mont pour l'année {$uneAnnee['nom']} - montant de la cotisation : $cotisationEuro </p>
		<p>J'adhère à la charte des Paniers Marseillais et je règle ma cotisation annuelle par chèque à l'ordre de "Panier de la Plaine du Mont".</p>
		<p> Un seul chèque de $cotisationEuro (Pour info, répartition : $cotisationPMEuro pour l'association Panier de la Plaine du Mont et $cotisationPPMEuro pour les Paniers Marseillais)</p>
		<table style="width: 75%; margin: auto;">
			<tbody>
				<tr style="text-align: center;">
					<th>
						tireur
					</th>
					<th>
						banque
					</th>
					<th>
						numéro
					</th>
					<th>
						montant
					</th>
				</tr>
				<tr style="text-align: center;">
					<td>
						<input type="text" name="chequeCotisationTireur">
					</td>
					<td>
						<input type="text" name="chequeCotisationBanque" required>
					</td>
					<td>
						<input type="text" name="chequeCotisationNumero" required>
					</td>
					<td>
						<input type="text" name="chequeCotisationMontant" style="text-align: right;" value="$cotisation" readonly> €
					</td>
				</tr>
			</tbody>
		</table>
		
EOT;
		}
			$html .= <<<EOT
		
		</div>
EOT;
		
		$html .= <<<EOT
		<br>
		<p><span  class="grasgros">Dates et lieu de distribution</span> : chaque mercredi de 19h00 à 20h, en face du cinéma La Baleine, 59 cours Julien</p>
		
EOT;
		$html .= <<<EOT
		<br>
		<p class="grasgros">Fréquence et date de la première distribution</p>
		<p>Je souhaite participer à une distribution {$frequence} </p>
		<p>à partir du mercredi {$datePemiereDistribution} la dernière distribution aura lieu le <span id="dateDerniereDistribution" style="font-weight: bold;"></span> soit un total de <span  style="font-weight: bold;" id="nombrePaniersDistribues"></span> paniers</p>
		<br>
EOT;
		if ($periode==1 || $periode==3) {
			$html .= <<<EOT
		<p style="font-style: italic;">
		 Pas de distribution le $dateNoel, ni le $dateJourAn mais
		</p>
		<ul style="margin-top:0px; font-style: italic;">
			<li>pour les paniers "semaine", distribution de 2 paniers le $dateAvantNoel et de 2 paniers le $dateApresJourAn.</li>
			<li>pour les paniers "quinzaine", distribution de 2 paniers le $dateAvantNoel ou de  2 paniers le $dateApresJourAn, selon la quinzaine paire ou impaire.</li>         
		</ul>
EOT;
		}
		
		$html .= <<<EOT
		<br>
		<p class="grasgros">Panier légumes et fruits - prix du panier : $prixPanierEuro</p>
		<p>Je souhaite prendre $nombrePaniers <span id="intervallePaniers"></span>. </p>
EOT;
			$html .= <<<EOT
		<div id="reglementPaniers">
		</div>
EOT;
			$html .= <<<EOT
		<div id="chequesPaniers">
		</div>
EOT;

		if ($periode!=4) {
			$html .= <<<EOT
		<br>
		<p class="grasgros">Option œufs - prix de la boîte de 6 œufs : $prixBoiteEuro</p>
		<p>produits par M. Péré, le fils de Loïc Péré, label AB, prix de la boîte de 6 œufs : mmm€
		<p style="font-styl: italic;">pas de report possible pour les œufs</p>
		<p>Je souhaite prendre $nombreBoites <span id="intervalleBoites"></span> </p>
EOT;
			$html .= <<<EOT
		<div id="reglementBoites">
		</div>
EOT;
			$html .= <<<EOT
		<div id="chequesBoites">
		</div>
EOT;

		}
		// barre de commande 
		$html .= <<<EOT
		<hr>
		<p style="text-align: center;">
				<input style="font-weight:bold; "  name="abandonNouveauContrat" value="Abandonner" type="button" onClick=" this.form.newAction.value='afficherLeCompte'; this.form.submit();
				">
				 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
				<input style="font-weight:bold; "  name="enregistrerContrat" value="Enregistrer le contrat" type="button" onClick="this.form.action='nouveauContrat.php'; this.form.newAction.value='enregistrerContrat'; this.form.submit();">
		
		</p>
EOT;

		
		$html .= <<<EOT
	</form>
	</body>
</html>
EOT;
		echo $html;
	} // fin afficherNouveauContrat
	
	function afficherNouveauxCheques($idCompte) {
?>
		<table style="width: 75%; margin: auto;">
			<tbody>
				<tr style="text-align: center;">
					<th>
						tireur
					</th>
					<th>
						banque
					</th>
					<th>
						numéro
					</th>
					<th>
						montant
					</th>
				</tr>
				<tr style="text-align: center;">
					<td>
						<input type="text" name="chequeCotisationTireur">
					</td>
					<td>
						<input type="text" name="chequeCotisationBanque" required>
					</td>
					<td>
						<input type="text" name="chequeCotisationNumero" required>
					</td>
					<td>
						<input type="text" name="chequeCotisationMontant" style="text-align: right;" value="{$uneAnnee['cotisation']}" readonly> €
					</td>
				</tr>
			</tbody>
		</table>

<?php
	} // fin function enregistrerNouveauxCheques
	
?>

