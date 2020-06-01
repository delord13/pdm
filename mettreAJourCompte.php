<?php 
// mettreAJourCompte.php
/*
 POST = array(3) {
  ["newAction"]=>
  string(11) "enregistrer" // abandonner
  ["idCompte"]=>
  string(1) "4"
  ["divId"]=>
  string(11) "volontariat" // atributs
}
*/
//	var_dump($_POST); die;

	session_start();
	include('inc/init.inc.php');		

	if (isset($_POST['divId'])) {
		// agir selon POST newAcion
		switch ($_POST['divId']) {
			case 'content' :
				mettreAJourContent();
				break;
			case 'attributs' :
				mettreAJourAttributs();
				die();
				break;
			case 'volontariat' :
				mettreAJourVolontariat();
				die();
				break;
			case 'reports' :
				mettreAJourReports();
				die();
				break;
			case 'ajouts' :
				mettreAJourAjouts();
				die();
				break;
			case 'contrats' :
				mettreAJourContrats();
				die();
				break;
			case 'cheques' :
				mettreAJourCheques();
				die();
				break;
			case 'courriels' :
				mettreAJourCheques();
				die();
				break;
		}
	}
	else {
		die('Accès interdit');
	}
		
		
	
	function genererHtmlAttributs($message) {
	{ // code php
		$idCompte = $_POST['idCompte'];
		if ($message!='') $htmlMessage = <<<EOT
					<p style="color: red; margin-bottom: 10px;">
					$message
					</p>
			
EOT;
		else $htmlMessage = '';
		// infos sur le compte et ses titulaires
		$sql = "SELECT idCompte, adherent, titulairePrincipalId, commentaire FROM PDM_compte WHERE idCompte=$idCompte";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$unCompte = mysqli_fetch_assoc($res);
		
		$unCompte['commentaire'] .= ajoutAbsences($unCompte['idCompte']);
		if (strpos($unCompte['commentaire'],'<br>')!=FALSE) {
			$unCompte['commentaire'] = str_replace('<br>',"\n",$unCompte['commentaire']);
		}
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
	}
	{ // code html
	$html = <<<EOT
				<hr>
				<h2>Attributs du compte</h2>
				$htmlMessage	
				<p style="color: red; margin-bottom: 10px;">
				Attention ! La suppression du titulaire principal entraîne la suppression du compte et de tous ses co-titulaires.<br>Ce n'est possible qu'en l'absence de tout contrat et de tout chèque enregistré pour ce compte.
				</p>
				
				<form method='POST' action='mettreAJourCompte.php' id="form_attributs">
					<input type='hidden' name='newAction' value=''>
					<input type='hidden' name='idCompte' value='{$_POST['idCompte']}'>
					<input type='hidden' name='divId' value='attributs'>
				<table border="1" style="width: 100 %; margin: auto;">
					<tr>
					 <td class="bordure" >
						<table class='hoverTable' border='0' style='width: 100%; margin: 0px; '>
							<tbody>
								<tr>
									<th class="bordure"  style="width: 80px;">
										titulaire principal
									</th>
									<th class="bordure"  style="width: 300px;">
										nom
									</th>
									<th class="bordure"  style="width: 230px;">
										téléphone
									</th>
									<th class="bordure"  style="width: 300px;">
										courriel
									</th>
									<th class="bordure"  style="width: 85px;">
										à supprimer
									</th>
								</tr>
EOT;
			
		$htmlTitulaire = '';
		foreach ($titulaire as $i =>$unTitulaire) { 
			if ($unTitulaire['idPersonne']==$unCompte['titulairePrincipalId']) $titulaireChecked = 'checked="checked"';
			else $titulaireChecked = ' ';
			$htmlTitulaire .= <<<EOT

						<tr>
							<td class="bordure"  style="text-align: center;">
								<input name='principal' value='{$unTitulaire['idPersonne']}'  type='radio' $titulaireChecked >
							</td>
							<td class="bordure" >
								<input name='nom[{$unTitulaire['idPersonne']}]' value='{$unTitulaire['nom']}'  type='text'  style='width: 280px;'>
							</td>
							<td class="bordure" >
								<input name='telephone[{$unTitulaire['idPersonne']}]' value='{$unTitulaire['telephone']}'  type='text'  style='width: 220px;'>
							</td>
							<td class="bordure" >
								<input name='courriel[{$unTitulaire['idPersonne']}]' value='{$unTitulaire['courriel']}'  type='text'  style='width: 280px;'>
							</td>
							<td class="bordure"  style="text-align: center;">
								<input name="supprimer[{$unTitulaire['idPersonne']}]" value="supprimer" type="checkbox" >
							</td>
EOT;
			$htmlTitulaire .= <<<EOT
						</tr>
EOT;
	
		}
		
		$html .= $htmlTitulaire;
		$html .= <<<EOT
								<tr>
									<td class="bordure"  style="text-align: center;">
										&nbsp;
									</td>
									<td class="bordure" >
										<input name='nouveauNom' value='' ' type='text' style='width: 280px;' >
									</td>
									<td class="bordure" >
										<input name='nouveauTelephone' value=''  type='text'  style='width: 220px;'>
									</td>
									<td class="bordure" >
										<input name='nouveauCourriel' value=''  type='text'  style='width: 280px;'>
									</td>
									<td class="bordure" >
										&nbsp;
									</td>
								</tr>
								<tr>
									<th class="bordure"  colspan="2" style="text-align: right; vertical-align: top; width: 150px;" >
										adhérent :
									</th>
									<td class="bordure"  colspan="3">
										&nbsp; <input name="adherent" value="oui" $adherentChecked type="checkbox">
									</td>
								</tr>

								<tr>
									<th class="bordure"  colspan="2" style="text-align: right; vertical-align: top;">
										commentaire : 
									</th>
									<td class="bordure"  colspan="3">
										<textarea  name="commentaire" style="width: 99%; height: 150px;">{$unCompte['commentaire']}</textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					

					<td rowspan="100" valign="middle" style="text-align: center; font-weight:bold; width: 150px; background-color: #009688;">
						<input style="font-weight:bold; "  name="enregistrer" value="Enregistrer" type="button" onClick="this.form.newAction.value='enregistrer';
						mettreAJourCompte('attributs');
						">
						<br><br>
						<input style="font-weight:bold; "  name="abandonner" value="Abandonner" type="button" onClick="this.form.newAction.value='abandonner';
						mettreAJourCompte('attributs');
						">
					</td>
				</table>
			</form>
EOT;
	}
		return $html;
	} 
	
	function genererHtmlVolontariat($message) {
	{ // code php nécessaire pour l'affichage de la section
		$idCompte = $_POST['idCompte'];
		if ($message!='') $htmlMessage = <<<EOT
				<p style="color: red; margin-bottom: 10px;">
				$message
				</p>
		
EOT;
		else $htmlMessage = '';
		
		// les personnes du comptes
		$sql = " SELECT idPersonne, nom FROM PDM_personne WHERE compteId = $idCompte ORDER BY nom";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($unePersonne = mysqli_fetch_assoc($res)) {
			$personne[$unePersonne['idPersonne']]['nom'] = $unePersonne['nom'];
			$tabIdPersonne[] = $unePersonne['idPersonne'];
		}
		
		// les 6 distributions futures possibles
		$premiereDate = premierMercrediPostCommande($_SESSION['auj']);
		$sql = "SELECT idDistribution, date FROM PDM_distribution WHERE date>='$premiereDate' AND  nombreUnites>0 ORDER BY date";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while (($uneDistribution = mysqli_fetch_assoc($res)) && $i<6) {
			$distribution[$i] = $uneDistribution;
			$distribution[$i]['dateJJMM'] = dateJJMM($distribution[$i]['date']);
			$i++;
			$tabIdDistribution[] = $uneDistribution['idDistribution'];
		}
		
		$ligne = array();
		// les volontariats Distribution enregistrés
		$sql = "SELECT personneId, distributionId FROM PDM_volontaireDistribution";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($unVolontariat = mysqli_fetch_assoc($res)) {
			if (in_array($unVolontariat['personneId'], $tabIdPersonne) && in_array( $unVolontariat['distributionId'], $tabIdDistribution)) {
				$ligne[$unVolontariat['personneId']][$unVolontariat['distributionId']] = 'distribution';
			}
		}
		// les volontariats Emargement enregistrés
		$sql = "SELECT personneId, distributionId FROM PDM_volontaireEmargement";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($unVolontariat = mysqli_fetch_assoc($res)) {
			if (in_array($unVolontariat['personneId'], $tabIdPersonne) && in_array( $unVolontariat['distributionId'], $tabIdDistribution)) {
				$ligne[$unVolontariat['personneId']][$unVolontariat['distributionId']] = 'émargement';
			}
		}
//var_dump($personne); var_dump($ligne); die();
	} // fin code php nécessaire pour l'affichage de la section
	
	{ // code html de la section
		
		
		$html = <<<EOT
			<div id="volontariat">
				<hr>
				<h2>Volontariat</h2>
			</div>
EOT;

		$html = <<<EOT
				<hr>
				<h2>Volontariat</h2>
				$htmlMessage	
				<p style="color: red; margin-bottom: 10px;">
				
				</p>
				
				<form method='POST' action='mettreAJourCompte.php' id="form_volontariat"> <!-- inscrire le nom de section -->
					<input type='hidden' name='newAction' value=''>
					<input type='hidden' name='idCompte' value='{$_POST['idCompte']}'>
					<input type='hidden' name='divId' value='volontariat'>
				
				<table border="1" style="width: 100 %; margin: auto;"> <!-- la table englobant le contenu et les commandes -->
					<tr>
					 <td class="bordure" >
EOT;

		// 1ère table contenu : volontariat
		$html .= <<<EOT
						<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  1ère table de contenu -->
							<tbody>
								<!-- tableau des volontariats existants -->
								<tr>
									<th colspan="10" style="text-align: left;">
										Volontariat
									</th>
								</tr>
								<tr>
									<th style="">
										volontaires
									</th>
EOT;
		foreach ($distribution AS $uneDistribution) { 
		$html .= <<<EOT
									<th>
										{$uneDistribution['dateJJMM']}
									</th>
EOT;
		}

		$html .= <<<EOT
								</tr>
EOT;

		
		foreach ($ligne AS $idPersonne =>$uneLigne) {
				

			$html .= <<<EOT

						<tr>
							<th style="text-align: center;">
								{$personne[$idPersonne]['nom']}
							</th>
EOT;
			foreach ($distribution AS  $uneDistribution) {
				$selected0 = '';
				$selected1 = '';
				$selected2 = '';
				if (isset($ligne[$idPersonne][$uneDistribution['idDistribution']])) {
					if ($ligne[$idPersonne][$uneDistribution['idDistribution']]=='distribution') $selected1 = " selected ";
					if ($ligne[$idPersonne][$uneDistribution['idDistribution']]=='émargement') $selected2 = " selected ";
				}
				else $selected = "";
				$indice = "[$idPersonne][{$uneDistribution['idDistribution']}]";
				$html .= <<<EOT
						<td style="text-align: center">
							<select name="volontaire$indice">
								<option value="0" $selected0> </option>
								<option value="1" $selected1>distribution</option>
								<option value="2" $selected2>émargement</option>
							</select>
						</td>
EOT;
					
				}
			}
				$html .= <<<EOT
						</tr>
EOT;

		$html .= <<<EOT
					</tbody>
				</table>
EOT;
		

		$html .= <<<EOT
					</td> <!-- fin de la cellule contenant la table du contenu de la section -->
					
					<!-- la cellule de droite contenant les commandes enregistrer et abandonner Attention renseigner le nom de section -->
					
					<td rowspan="100" valign="middle" style="text-align: center; font-weight:bold; width: 150px;background-color: #009688;">
						<input style="font-weight:bold; "  name="enregistrer" value="Enregistrer" type="button" onClick="this.form.newAction.value='enregistrer';
						mettreAJourCompte('volontariat');
						">
						<br><br>
						<input style="font-weight:bold; "  name="abandonner" value="Abandonner" type="button" onClick="this.form.newAction.value='abandonner';
						mettreAJourCompte('volontariat');
						">
					</td>
				</table>
			</form>
EOT;

	} // fin code html de la section
		return $html;
	} // fin genererHtmlVolontariat
	
	function genererHtmlReports($message) {
		{ // code php nécessaire pour l'affichage de la section
		$idCompte = $_POST['idCompte'];
		$htmlMessage = <<<EOT
				<p style="color: red;">
				 Attention ! La validité des nouveaux reports n'est pas vérifiée.
				</p>
		
EOT;
		if ($message!='') $htmlMessage .= <<<EOT
				<p style="color: red; ">
				 $message
				</p>
EOT;
		
		
		// les reports enregistrés du compte
		$sql = "SELECT idReport, quantite, origine, destination FROM PDM_report, PDM_contrat WHERE compteId=$idCompte AND contratId=idContrat ORDER BY origine";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while ($unReport = mysqli_fetch_assoc($res)) {
			$report[$i] = $unReport;
			// dates des distributions d'origine et de destination
			$sql1 = "SELECT date FROM PDM_distribution WHERE idDistribution={$report[$i]['origine']}";
			$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
			$dateOrigine = mysqli_fetch_assoc($res1);
			$report[$i]['origine'] = dateJJMM($dateOrigine['date']);
			$sql1 = "SELECT date FROM PDM_distribution WHERE idDistribution={$report[$i]['destination']}";
			$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
			$dateDestination = mysqli_fetch_assoc($res1);
			$report[$i]['destination'] = dateJJMM($dateDestination['date']);

			$i++;
		}
		$nbReports = $i;
//var_dump($report);die;		
		// le contrat panier actif pour ce compte (NB 1 seul contrat panier actif à la fois)
		$sql = "SELECT DISTINCTROW idContrat, dateDebut, dateFin, quantiteContratId FROM PDM_contrat, PDM_compte WHERE compteId=$idCompte AND typeContratId=1 AND dateFin>='{$_SESSION['auj']}' ";

		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$leContrat = mysqli_fetch_assoc($res);
		
		// les 7 distributions futures possibles
		$premiereDate = premierMercrediPostCommande($_SESSION['auj']);
		$sql = "SELECT idDistribution, date FROM PDM_distribution WHERE date>='$premiereDate' AND  nombreUnites>0 ORDER BY date";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while (($uneDistribution = mysqli_fetch_assoc($res)) && $i<7) {
			$distribution[$i] = $uneDistribution;
			$distribution[$i]['dateJJMM'] = dateJJMM($distribution[$i]['date']);
			$i++;
		}
		
		// select quantite
		$selectQuantite = <<<EOT
					<select name="nouveauQuantite">
EOT;
		for ($n=1; $n<=$leContrat['quantiteContratId']; $n++) {
			$selectQuantite .= <<<EOT
						<option value="$n">$n</option>
EOT;
		}
		$selectQuantite .= <<<EOT
					</select>
EOT;
//die(htmlentities($selectQuantite));
		// select origine
		$selectOrigine = <<<EOT
					<select name="nouveauOrigine">
EOT;
		for ($i=0; $i<6; $i++) {
			$dateOrigine = dateJJMM($distribution[$i]['date']);
			$idDistribution = $distribution[$i]['idDistribution'];
			$selectOrigine .= <<<EOT
						<option value="$idDistribution">$dateOrigine</option>
EOT;
		}
		
		$selectOrigine .= <<<EOT
					</select>
EOT;
		
// select destination
		$selectDestination = <<<EOT
					<select name="nouveauDestination">
EOT;
		for ($i=1; $i<7; $i++) {
			$dateDistribution = dateJJMM($distribution[$i]['date']);
			$idDistribution = $distribution[$i]['idDistribution'];
			$selectDestination .= <<<EOT
						<option value="$idDistribution">$dateDistribution</option>
EOT;
		}
		
		$selectDestination .= <<<EOT
					</select>
EOT;

	
		

		
		} // fin code

		{ // code html de la section
		$html = <<<EOT
				<hr>
				<h2>Reports</h2>
				$htmlMessage	
				<p style="color: red; margin-bottom: 10px;">
				
				</p>
				
				<form method='POST' action='mettreAJourCompte.php' id="form_reports"> <!-- inscrire le nom de section -->
					<input type='hidden' name='newAction' value=''>
					<input type='hidden' name='idCompte' value='{$_POST['idCompte']}'>
					<input type='hidden' name='idContrat' value='{$leContrat['idContrat']}'>
					<input type='hidden' name='divId' value='reports'>
				
				<table border="1" style="width: 100 %; margin: auto;"> <!-- la table englobant le contenu et les commandes -->
					<tr>
					 <td class="bordure" >
EOT;

		// 1ère table contenu : reports existants
		$html .= <<<EOT
						<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  1ère table de contenu -->
							<tbody>
								<!-- tableau des reports existants -->
								<tr>
									<th colspan="10" style="text-align: left;">
										Reports enregistrés
									</th>
								</tr>
								<tr>
									<th style="">
										nombre de paniers
									</th>
									<th style="">
										distribution à reporter
									</th>
									<th style="">
										distribution cible
									</th>
									<th style="">
										à supprimer
									</th>
								</tr>

EOT;

		if (isset($report)) { // si le nbre de reports est supérieur à 0
			foreach ($report AS $idReport => $unReport) {
				

			$html .= <<<EOT

						<tr>
							<td style="text-align: center;">
								{$unReport['quantite']}
							</td>
							<td style="text-align: center;">
								{$unReport['origine']}
							</td>
							<td style="text-align: center;">
								{$unReport['destination']}
							</td>
							<td style="text-align: center;">
								<input name="supprimer[{$unReport['idReport']}]" value="supprimer" type="checkbox">
							</td>
						</tr>
EOT;

			}
		}
		else {
		$html .= <<<EOT
						<tr>
							<td colspan="10">
								aucun report enregistré
							</td>
						</tr>
EOT;
		}
		$html .= <<<EOT
					</tbody>
				</table>
EOT;

		// 2e table contenu
		if ($nbReports<4) {
			$html .= <<<EOT
					<br>
					<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  2e table de contenu -->
						<tbody>
						<tr>
							<th colspan="10" style="text-align: left;">
								Nouveau report
							</th>
						</tr>
						<tr>
							<th style="">
								nombre de paniers
							</th>
							<th style="">
								distribution à reporter
							</th>
							<th style="">
								distribution cible
							</th>
							<th>
								à ajouter
							</th>
						</tr>
						<tr>
							<td style="text-align: center;">
								$selectQuantite
							</td>
							<td style="text-align: center;">
								$selectOrigine
							</td>
							<td style="text-align: center;">
								$selectDestination
							</td>
							<td style="text-align: center;">
								<input name='aAjouterNouveau' value='oui' type='checkbox'> 
							</td>
							
						</tr>
							</tbody>
					</table> <!--  fin 2e table de contenu partie A -->
EOT;
						
		
		} // fin 

		$html .= <<<EOT
					</td> <!-- fin de la cellule contenant la table du contenu de la section -->
					
					<!-- la cellule de droite contenant les commandes enregistrer et abandonner Attention renseigner le nom de section -->
					
					<td rowspan="100" valign="middle" style="text-align: center; font-weight:bold; width: 150px;background-color: #009688;">
						<input style="font-weight:bold; "  name="enregistrer" value="Enregistrer" type="button" onClick="this.form.newAction.value='enregistrer';
						mettreAJourCompte('reports');
						">
						<br><br>
						<input style="font-weight:bold; "  name="abandonner" value="Abandonner" type="button" onClick="this.form.newAction.value='abandonner';
						mettreAJourCompte('reports');
						">
					</td>
				</table>
			</form>
EOT;
		} // fin code HTML
		return $html;
	} // fin genererHtmlReports

/*
	function genererHtmlAjouts($message) {
		{ // code php nécessaire pour l'affichage de la section
		$idCompte = $_POST['idCompte'];
		if ($message!='') $htmlMessage = <<<EOT
				<p style="color: red; margin-bottom: 10px;">
				$message
				</p>
		
EOT;
		else $htmlMessage = '';
		
		
		}

		{ // code html de la section
		$html = <<<EOT
			<div id="ajouts">
				<hr>
				<h2>Ajouts</h2>
			</div>
EOT;
		}
		return $html;
	} // fin genererHtmlAjouts
*/	
	
	function genererHtmlAjouts($message) {
		{ // code php nécessaire pour l'affichage de la section
		$idCompte = $_POST['idCompte'];
		$htmlMessage = <<<EOT
				<p style="color: red;">
				 Attention ! Pour augmenter ou diminuer le nombre de paniers ajoutés enregistré, il faut commencer par supprimer l'ajout enregistré.
				</p>
		
EOT;
		if ($message!='') $htmlMessage .= <<<EOT
				<p style="color: red; ">
				 $message
				</p>
EOT;
		
		
		// les ajouts enregistrés du compte
		$sql = "SELECT idAjout, distributionId, quantite FROM PDM_ajout, PDM_contrat WHERE compteId=$idCompte AND contratId=idContrat ORDER BY distributionId";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while ($unAjout = mysqli_fetch_assoc($res)) {
			$ajout[$i] = $unAjout;
			// dates de la distributionn
			$sql1 = "SELECT date FROM PDM_distribution WHERE idDistribution={$ajout[$i]['distributionId']}";
			$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
			$dateAjout = mysqli_fetch_assoc($res1);
			$ajout[$i]['dateAjout'] = dateJJMM($dateAjout['date']);

			$i++;
		}
		$nbAjouts = $i;
//var_dump($report);die;		
		// le contrat panier actif pour ce compte (NB 1 seul contrat panier actif à la fois)
		$sql = "SELECT DISTINCTROW idContrat, dateDebut, dateFin, quantiteContratId FROM PDM_contrat, PDM_compte WHERE compteId=$idCompte AND typeContratId=1 AND dateFin>='{$_SESSION['auj']}' ";

		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$leContrat = mysqli_fetch_assoc($res);
		
		// les 6 distributions futures possibles
		$premiereDate = premierMercrediPostCommande($_SESSION['auj']);
		$sql = "SELECT idDistribution, date FROM PDM_distribution WHERE date>='$premiereDate' AND  nombreUnites>0 ORDER BY date";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while (($uneDistribution = mysqli_fetch_assoc($res)) && $i<6) {
			$distribution[$i] = $uneDistribution;
			$distribution[$i]['dateJJMM'] = dateJJMM($distribution[$i]['date']);
			$i++;
		}
		
		// select quantite
		$selectQuantiteAjout = <<<EOT
					<select name="nouveauQuantite">
EOT;
		for ($n=1; $n<=3; $n++) {
			$selectQuantiteAjout .= <<<EOT
						<option value="$n">$n</option>
EOT;
		}
		$selectQuantiteAjout .= <<<EOT
					</select>
EOT;

		// select date ajout
		$selectDateAjout = <<<EOT
					<select name="nouveauIdDistribution">
EOT;
		for ($i=0; $i<6; $i++) {
			$dateAjout = dateJJMM($distribution[$i]['date']);
			$idDistribution = $distribution[$i]['idDistribution'];
			$selectDateAjout .= <<<EOT
						<option value="$idDistribution">$dateAjout</option>
EOT;
		}
		
		$selectDateAjout .= <<<EOT
					</select>
EOT;
		
		
		} // fin code

		{ // code html de la section
		$html = <<<EOT
				<hr>
				<h2>Ajouts</h2>
				<p style="color: red; margin-bottom: 10px;">
					$htmlMessage	
				</p>
				
				<form method='POST' action='mettreAJourCompte.php' id="form_ajouts"> <!-- inscrire le nom de section -->
					<input type='hidden' name='newAction' value=''>
					<input type='hidden' name='idCompte' value='{$_POST['idCompte']}'>
					<input type='hidden' name='idContrat' value='{$leContrat['idContrat']}'>
					<input type='hidden' name='divId' value='ajouts'>
				
				<table border="1" style="width: 100 %; margin: auto;"> <!-- la table englobant le contenu et les commandes -->
					<tr>
					 <td class="bordure" >
EOT;

		// 1ère table contenu : reports existants
		$html .= <<<EOT
						<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  1ère table de contenu -->
							<tbody>
								<!-- tableau des ajouts existants -->
								<tr>
									<th colspan="10" style="text-align: left;">
										Ajouts enregistrés
									</th>
								</tr>
								<tr>
									<th style="">
										nombre de paniers
									</th>
									<th style="">
										distribution
									</th>
									<th style="">
										à supprimer
									</th>
								</tr>

EOT;

		if (isset($ajout)) { // si le nbre de reports est supérieur à 0
			foreach ($ajout AS $idAjout => $unAjout) {
				

			$html .= <<<EOT

						<tr>
							<td style="text-align: center;">
								{$unAjout['quantite']}
							</td>
							<td style="text-align: center;">
								{$unAjout['dateAjout']}
							</td>
							<td style="text-align: center;">
								<input name="supprimer[{$unAjout['idAjout']}]" value="supprimer" type="checkbox">
							</td>
						</tr>
EOT;

			}
		}
		else {
		$html .= <<<EOT
						<tr>
							<td colspan="10">
								aucun ajout enregistré
							</td>
						</tr>
EOT;
		}
		$html .= <<<EOT
					</tbody>
				</table>
EOT;

		// 2e table contenu
		$html .= <<<EOT
				<br>
				<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  2e table de contenu -->
					<tbody>
					<tr>
						<th colspan="10" style="text-align: left;">
							Nouvel ajout
						</th>
					</tr>
					<tr>
						<th style="">
							nombre de paniers
						</th>
						<th style="">
							distribution
						</th>
						<th>
							à ajouter
						</th>
					</tr>
					<tr>
						<td style="text-align: center;">
							$selectQuantiteAjout
						</td>
						<td style="text-align: center;">
							$selectDateAjout
						</td>
						<td style="text-align: center;">
							<input name='aAjouterNouveau' value='oui' type='checkbox'> 
						</td>
						
					</tr>
						</tbody>
				</table> <!--  fin 2e table de contenu partie A -->
EOT;
						
		

		$html .= <<<EOT
					</td> <!-- fin de la cellule contenant la table du contenu de la section -->
					
					<!-- la cellule de droite contenant les commandes enregistrer et abandonner Attention renseigner le nom de section -->
					
					<td rowspan="100" valign="middle" style="text-align: center; font-weight:bold; width: 150px;background-color: #009688;">
						<input style="font-weight:bold; "  name="enregistrer" value="Enregistrer" type="button" onClick="this.form.newAction.value='enregistrer';
						mettreAJourCompte('ajouts');
						">
						<br><br>
						<input style="font-weight:bold; "  name="abandonner" value="Abandonner" type="button" onClick="this.form.newAction.value='abandonner';
						mettreAJourCompte('ajouts');
						">
					</td>
				</table>
			</form>
EOT;
		} // fin code HTML
		return $html;
	} // fin genererHtmlAjouts
	
	function genererHtmlContrats($message) {
	
	{ // code php nécessaire pour l'affichage de la section
		$idCompte = $_POST['idCompte'];
		if ($message!='') $htmlMessage = <<<EOT
				<p style="color: red; margin-bottom: 10px;">
				$message
				</p>
		
EOT;
		else $htmlMessage = '';

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
{
	}
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
		
		// code nécessaire pour l'affichage des contrats existants

		
		// code nécessaire pour l'affichage d'un nouveau contrat
		// pour JS : 
/*
	<input name="numero" id="numero" value="789456123" readonly="readonly" type="text">
	<select name="type" id="type" disabled="disabled">
		<option value="1">panier</option>
		<option value="2" selected="selected">oeufs</option>
	</select>
	<button name="go" value="change" 
		onclick="document.getElementById('numero').value='123456' ; document.getElementById('type').selectedIndex=0;">CHANGE
	</button>
*/
		// les intitulés et select
		// =======================

		
		// type
		$sql = "SELECT idTypeContrat, type FROM PDM_typeContrat ";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($uneLigne = mysqli_fetch_assoc($res)) 
			$type[$uneLigne['idTypeContrat']] = $uneLigne['type'];
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

		// annee $anneeCourante['nom'] et id et Suivante
		$select['annee'] = "
		    <select name='nouveau[annee]'>
				<option value='{$anneeCourante['id']}' $selected >{$anneeCourante['nom']}</option>
      ";
		// seulement dernier mois :
		if ($estDernierMoisAnneeCourante) {
			$select['annee'] .= "<option value='{$anneeSuivante['id']}' $selected >{$anneeSuivante['nom']}</option>";
		}
      $select['annee'] .= "
			</select>
		";

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
		
		
	} // fin code php nécessaire pour l'affichage de la section
	
	{ // code html de la section
		$html = <<<EOT
				<hr>
				<h2>Contrats</h2>
				$htmlMessage	
				<p style="color: red; margin-bottom: 10px;">
				Attention ! La modification de la date de début et la suppression d'un contrat en cours doivent être limitées aux cas d'erreur de saisie.
				</p>
				
				<form method='POST' action='mettreAJourCompte.php' id="form_contrats"> <!-- inscrire le nom de section -->
					<input type='hidden' name='newAction' value=''>
					<input type='hidden' name='idCompte' value='{$_POST['idCompte']}'>
					<input type='hidden' name='divId' value='contrats'>
				
				<table border="1" style="width: 100 %; margin: auto;"> <!-- la table englobant le contenu et les commandes -->
					<tr>
					 <td class="bordure" >
EOT;

		// 1ère table contenu : contrats existants
		$html .= <<<EOT
						<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  1ère table de contenu -->
							<tbody>
								<!-- tableau des contrats existants -->
								<tr>
									<th colspan="11" style="text-align: left;">
										Contrats en cours
									</th>
								</tr>
								<tr>
									<th style="">
										n°
									</th>
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
								</tr>

EOT;

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
				$select['dateDebut'] = "
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
				// NB
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
//die("prix unitaire : $prixUnitaire ; quantité numérique : $quantiteNumerique ; dateDebut : {$unContrat['dateDebut']} ; strDebut : $strDebut; dateFin : {$unContrat['dateFin']} ; strFin : $strFin ;  nbIntervalles : $nbIntervalles ; prixTotal : $prixTotal");				
				/*
				if ($estSupprimable$estDernierMoisAnneeCouranteModifiable) $checkBoxASupprimer = "<input name='aSupprimer[{$unContrat['idContrat']}]' value='oui' type='checkbox'>";
				else $checkBoxASupprimer = "&nbsp";
				*/
				// toujours supprimable
				$checkBoxASupprimer = "<input name='aSupprimer[{$unContrat['idContrat']}]' value='oui' type='checkbox'>";
				
				$checkBoxValide = "<input name='valide[{$unContrat['idContrat']}]' value=1 ";
				if ($unContrat['valide']==1) $checkBoxValide .=  "checked ";
				$checkBoxValide .= "type='checkbox'>";

			$html .= <<<EOT

						<tr>
							<td style="text-align: center;">
								{$unContrat['idContrat']}

							<td style="text-align: center;">
								{$type[$unContrat['typeContratId']]}
							</td>
							<td style="text-align: center;">
								{$quantite[$unContrat['quantiteContratId']]}
							</td>
							<td style="text-align: center;">
								{$annee[$unContrat['anneeId']]['nom']}
							</td>
							<td style="text-align: center;">
								{$periode[$unContrat['periodeContratId']]}
							</td>
							<td style="text-align: center;">
								{$frequence[$unContrat['frequenceContratId']]}
							</td>
							<td style="text-align: center;">
								{$select['dateDebut']} 
							</td>
							<td style="text-align: center;">
								{$select['dateFin']}
							</td>
							<td style="text-align: center;">
								<span id="prix[{$unContrat['idContrat']}]"> $prixTotal €</span>
							</td>
							<td style="text-align: center;">
								$checkBoxValide
							</td>
							<td style="text-align: center;">
								$checkBoxASupprimer
							</td>
						</tr>
EOT;

		}
	}
	else {
		$html .= <<<EOT
						<tr>
							<td colspan="10">
								aucun contrat en cours
							</td>
						</tr>
EOT;
	}

		$html .= <<<EOT
					</tbody>
				</table>
EOT;
		// 2e table contenu 
		// 

		$html .= <<<EOT
				<br>
				<br>				
EOT;

		$html .= <<<EOT
					</td> <!-- fin de la cellule contenant la table du contenu de la section -->
					
					<!-- la cellule de droite contenant les commandes enregistrer et abandonner Attention renseigner le nom de section -->
					
					<td rowspan="100" valign="middle" style="text-align: center; font-weight:bold; width: 150px;background-color: #009688;">
						<input style="font-weight:bold; "  name="enregistrer" value="Enregistrer" type="button" onClick="this.form.newAction.value='enregistrer';
						mettreAJourCompte('contrats');
						">
						<br><br>
						<input style="font-weight:bold; "  name="abandonner" value="Abandonner" type="button" onClick="this.form.newAction.value='abandonner';
						mettreAJourCompte('contrats');
						">
					</td>
				</table>
				</form>
			
			<form method='POST' action='nouveauContrat.php' id="form_nouveauContrats"> 
				<input type='hidden' name='newAction' value='nouveauContrat'>
				<input type='hidden' name='periode' value=''>
				<input type='hidden' name='idCompte' value='{$_POST['idCompte']}'>
				<input type='hidden' name='origineNouveauContrat' value='comptes.php'>
				<input type='hidden' name='divId' value='contrats'>

				<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  table sans commandes à droite-->
					<tbody>
						<tr>
							<th colspan="11" style="text-align: left;">
								Nouveau contrat
							</th>
						</tr>
						<tr>
							<td style="text-align: center;">
								année : {$select['annee']}
							</td>
							<td style="text-align: center;">
								
								<input style="font-weight:bold; "  name="btnAnnee" value="Contrat Année" type="button" onClick="this.form.periode.value='1'; this.form.submit();">
								
								&nbsp; &nbsp; &nbsp; &nbsp;
								<input style="font-weight:bold; " name="btnSemestre1" value="Contrat Semestre n°1" type="button" onClick="this.form.periode.value='2'; this.form.submit();">
								
								&nbsp; &nbsp; &nbsp; &nbsp;
								<input style="font-weight:bold; "  name="btnDemestre2" value="Contrat Semestre n°2" type="button" onClick="this.form.periode.value='3'; this.form.submit();"> 
								
								&nbsp; &nbsp; &nbsp; &nbsp;
								<input style="font-weight:bold; " name="btnEssai" value="Contrat Essai" type="button" onClick="this.form.periode.value='4'; this.form.submit();">
							
							
							</td>
							
						</tr>
						</tbody>
				</table> <!--  fin table sans commande à droite -->
					
					</tbody>
				</table>
				
			</form>
EOT;

		return $html;

		} // fin code html de la section
	}
	
	function genererHtmlCheques($message) {
		{ // code php nécessaire pour l'affichage de la section
		$idCompte = $_POST['idCompte'];
		
		// chèque de cotisation du compte
		$sql = "SELECT idChequeCotisation, compteId, banque, numero, montant, dateEncaissement, dateModification FROM PDM_chequeCotisation WHERE compteId=$idCompte ORDER BY idChequeCotisation";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$chequeCotisation = mysqli_fetch_assoc($res);
		
		// les contrats du compte
		$contrat = array();
		$sql = "SELECT idContrat, compteId, anneeId, typeContratId, quantiteContratId, frequenceContratId, periodeContratId, dateDebut, dateFin, valide, nom FROM PDM_annee, PDM_contrat WHERE idAnnee=anneeId AND compteId=$idCompte ORDER BY idContrat";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($unContrat = mysqli_fetch_assoc($res)) $contrat[] = $unContrat;
		
		// chèques de chaque contrat panier
		$chequePanier = array();
		foreach ($contrat AS $unContrat) {
			if ($unContrat['typeContratId']==1) {
				$idContrat = $unContrat['idContrat'];
				$sql = "SELECT idChequeContrat, contratId, banque, numero, montant, dateEncaissement, dateModification FROM PDM_chequeContrat WHERE contratId=$idContrat ORDER BY contratId, idChequeContrat";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				while ($unCheque = mysqli_fetch_assoc($res)) {
					$chequePanier[] = $unCheque;
				}
			}
		}
		

		// chèques de chaque contrat oeufs
		$chequeOeufs = array();
		foreach ($contrat AS $unContrat) {
			if ($unContrat['typeContratId']==2) {
				$idContrat = $unContrat['idContrat'];
				$sql = "SELECT idChequeContrat, contratId, banque, numero, montant, dateEncaissement, dateModification FROM PDM_chequeContrat WHERE contratId=$idContrat ORDER BY contratId, idChequeContrat";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				while ($unCheque = mysqli_fetch_assoc($res)) {
					$chequeOeufs[] = $unCheque;
				}
			}
		}
{		
/*
		// les ajouts enregistrés du compte
		$sql = "SELECT idAjout, distributionId, quantite FROM PDM_ajout, PDM_contrat WHERE compteId=$idCompte AND contratId=idContrat ORDER BY distributionId";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while ($unAjout = mysqli_fetch_assoc($res)) {
			$ajout[$i] = $unAjout;
			// dates de la distributionn
			$sql1 = "SELECT date FROM PDM_distribution WHERE idDistribution={$ajout[$i]['distributionId']}";
			$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
			$dateAjout = mysqli_fetch_assoc($res1);
			$ajout[$i]['dateAjout'] = dateJJMM($dateAjout['date']);

			$i++;
		}
		$nbAjouts = $i;
//var_dump($report);die;		
		// le contrat panier actif pour ce compte (NB 1 seul contrat panier actif à la fois)
		$sql = "SELECT DISTINCTROW idContrat, dateDebut, dateFin, quantiteContratId FROM PDM_contrat, PDM_compte WHERE compteId=$idCompte AND typeContratId=1 AND dateFin>='{$_SESSION['auj']}' ";

		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$leContrat = mysqli_fetch_assoc($res);
		
		// les 6 distributions futures possibles
		$premiereDate = premierMercrediPostCommande($_SESSION['auj']);
		$sql = "SELECT idDistribution, date FROM PDM_distribution WHERE date>='$premiereDate' AND  nombreUnites>0 ORDER BY date";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$i = 0;
		while (($uneDistribution = mysqli_fetch_assoc($res)) && $i<6) {
			$distribution[$i] = $uneDistribution;
			$distribution[$i]['dateJJMM'] = dateJJMM($distribution[$i]['date']);
			$i++;
		}
		
		// select quantite
		$selectQuantiteAjout = <<<EOT
					<select name="nouveauQuantite">
EOT;
		for ($n=1; $n<=3; $n++) {
			$selectQuantiteAjout .= <<<EOT
						<option value="$n">$n</option>
EOT;
		}
		$selectQuantiteAjout .= <<<EOT
					</select>
EOT;

		// select date ajout
		$selectDateAjout = <<<EOT
					<select name="nouveauIdDistribution">
EOT;
		for ($i=0; $i<6; $i++) {
			$dateAjout = dateJJMM($distribution[$i]['date']);
			$idDistribution = $distribution[$i]['idDistribution'];
			$selectDateAjout .= <<<EOT
						<option value="$idDistribution">$dateAjout</option>
EOT;
		}
		
		$selectDateAjout .= <<<EOT
					</select>
EOT;
		
*/	
}
		} // fin code

		{ // code html de la section
		if ($message!='') $htmlMessage = <<<EOT
		<p style="color: red; ">
			$message
		</p>
EOT;
		else $htmlMessage = '';
		
		$html = <<<EOT
				<hr>
				<h2>Chèques</h2>
				$htmlMessage
				<form method='POST' action='mettreAJourCompte.php' id="form_cheques"> <!-- inscrire le nom de section -->
					<input type='hidden' name='newAction' value=''>
					<input type='hidden' name='idCompte' value='{$_POST['idCompte']}'>
					<input type='hidden' name='divId' value='cheques'>
				
				<table border="1" style="width: 100 %; margin: auto;"> <!-- la table englobant le contenu et les commandes -->
					<tr>
					 <td class="bordure" >
EOT;

		// 1ère table contenu : cheque cotisation
		$html .= <<<EOT
						<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  1ère table de contenu -->
							<tbody>
								<!-- chèque cotisation -->
								<tr>
									<th colspan="10" style="text-align: left;">
										Chèque cotisation
									</th>
								</tr>
								<tr>
									<th style="">
										compte n°
									</th>
									<th style="">
										banque
									</th>
									<th style="">
										n° du chèque
									</th>
									<th style="">
										montant
									</th>
									<th style="">
										date d'encaissement
									</th>
									<th style="">
										à supprimer
									</th>
								</tr>

EOT;

		if ($chequeCotisation) { // s'il existe une chèque cotisation
		//	foreach ($chequeCotisation AS $idChequeCotisation => $unChequeCotisation) {
			$chequeCotisation['dateEncaissement'] = substr($chequeCotisation['dateEncaissement'],0,10);
	

			$html .= <<<EOT

						<tr>
							<td style="text-align: center;">
								{$chequeCotisation['compteId']}
							</td>
							<td style="text-align: center;">
								<input name="chequeCotisation[{$chequeCotisation['idChequeCotisation']}][banque]" value="{$chequeCotisation['banque']}" type="text"  style="text-align: center;">
							</td>
							<td style="text-align: center;">
								<input name="chequeCotisation[{$chequeCotisation['idChequeCotisation']}][numero]" value="{$chequeCotisation['numero']}" required="required" type="text" style="text-align: center;">
							</td>
							<td style="text-align: center;">
								<input name="chequeCotisation[{$chequeCotisation['idChequeCotisation']}][montant]" value="{$chequeCotisation['montant']}" required="required" type="text" style="text-align: right;"> €
							</td>
							<td style="text-align: center;">
								<input name="chequeCotisation[{$chequeCotisation['idChequeCotisation']}][dateEncaissement]" value="{$chequeCotisation['dateEncaissement']}" type="date" style="text-align: center;">
								
							</td>
							<td style="text-align: center;">
								<input name="supprimerChequeCotisation[{$chequeCotisation['idChequeCotisation']}]" value="supprimer" type="checkbox">
							</td>
						</tr>

EOT;

//			}
		}
		else {
		$html .= <<<EOT
						<tr>
							<td colspan="10">
								aucun chèque cotisation enregistré
							</td>
						</tr>
EOT;
		}
		$html .= <<<EOT
					</tbody>
				</table>
EOT;

		
		// 2e table contenu chequePanier
		$html .= <<<EOT
						<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  1ère table de contenu -->
							<tbody>
								<!-- chèques Panier -->
								<tr>
									<th colspan="10" style="text-align: left;">
										Chèques contrat Panier
									</th>
								</tr>
								<tr>
									<th style="">
										contrat n°
									</th>
									<th style="">
										banque
									</th>
									<th style="">
										n° du chèque
									</th>
									<th style="">
										montant
									</th>
									<th style="">
										date d'encaissement
									</th>
									<th style="">
										à supprimer
									</th>
								</tr>

EOT;

		if ($chequePanier) { // s'il existe des chèques panier
			foreach ($chequePanier AS $unChequePanier) {
				$unChequePanier['dateEncaissement'] = substr($unChequePanier['dateEncaissement'],0,10);

			$html .= <<<EOT

						<tr>
							<td style="text-align: center;">
								{$unChequePanier['contratId']}
							</td>
							<td style="text-align: center;">
								<input name="chequeContrat[{$unChequePanier['idChequeContrat']}][banque]" value="{$unChequePanier['banque']}" type="text"  style="text-align: center;">
							</td>
							<td style="text-align: center;">
								<input name="chequeContrat[{$unChequePanier['idChequeContrat']}][numero]" value="{$unChequePanier['numero']}" required="required" type="text" style="text-align: center;">
							</td>
							<td style="text-align: center;">
								<input name="chequeContrat[{$unChequePanier['idChequeContrat']}][montant]" value="{$unChequePanier['montant']}" required="required" type="text" style="text-align: right;"> €
							</td>
							<td style="text-align: center;">
								<input name="chequeContrat[{$unChequePanier['idChequeContrat']}][dateEncaissement]" value="{$unChequePanier['dateEncaissement']}" type="date" style="text-align: center;">
								
							</td>
							<td style="text-align: center;">
								<input name="supprimerChequeContrat[{$unChequePanier['idChequeContrat']}]" value="supprimer" type="checkbox">
							</td>
						</tr>
EOT;

			}
		}
		else {
		$html .= <<<EOT
						<tr>
							<td colspan="10">
								aucun chèque contrat Panier enregistré
							</td>
						</tr>
EOT;
		}
		$html .= <<<EOT
					</tbody>
				</table>
EOT;



		// 3e table contenu chequeOeufs
		$html .= <<<EOT
						<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  1ère table de contenu -->
							<tbody>
								<!-- chèques Oeufs -->
								<tr>
									<th colspan="10" style="text-align: left;">
										Chèques contrat Œufs
									</th>
								</tr>
								<tr>
									<th style="">
										contrat n°
									</th>
									<th style="">
										banque
									</th>
									<th style="">
										n° du chèque
									</th>
									<th style="">
										montant
									</th>
									<th style="">
										date d'encaissement
									</th>
									<th style="">
										à supprimer
									</th>
								</tr>

EOT;

		if ($chequeOeufs) { // s'il existe une chèque Oeufs
			
			
			foreach ($chequeOeufs AS  $unChequeOeufs) {
			$unChequeOeufs['dateEncaissement'] = substr($unChequeOeufs['dateEncaissement'],0,10);
			$html .= <<<EOT

						<tr>
							<td style="text-align: center;">
								{$unChequeOeufs['contratId']}
							</td>
							<td style="text-align: center;">
								<input name="chequeContrat[{$unChequeOeufs['idChequeContrat']}][banque]" value="{$unChequeOeufs['banque']}" type="text"  style="text-align: center;">
							</td>
							<td style="text-align: center;">
								<input name="chequeContrat[{$unChequeOeufs['idChequeContrat']}][numero]" value="{$unChequeOeufs['numero']}" required="required" type="text" style="text-align: center;">
							</td>
							<td style="text-align: center;">
								<input name="chequeContrat[{$unChequeOeufs['idChequeContrat']}][montant]" value="{$unChequeOeufs['montant']}" required="required" type="text" style="text-align: right;"> €
							</td>
							<td style="text-align: center;">
								<input name="chequeContrat[{$unChequeOeufs['idChequeContrat']}][dateEncaissement]" value="{$unChequeOeufs['dateEncaissement']}" type="date" style="text-align: center;">
								
							</td>
							<td style="text-align: center;">
								<input name="supprimer[{$unChequeOeufs['idChequeContrat']}]" value="supprimer" type="checkbox">
							</td>
						</tr>
EOT;

			}
		}
		else {
		$html .= <<<EOT
						<tr>
							<td colspan="10">
								aucun chèque contrat Œufs enregistré
							</td>
						</tr>
EOT;
		}
		$html .= <<<EOT
					</tbody>
				</table>
EOT;

		// 4e table contenu ajout de chèque cotisation
		if (TRUE) { // $chequeCotisation==NULL
			$html .= <<<EOT
							<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  1ère table de contenu -->
								<tbody>
									<!-- ajouter chèque cotisation -->
									<tr>
										<th colspan="10" style="text-align: left;">
											Ajouter un chèque de cotisation
										</th>
									</tr>
									<tr>
										<th style="">
											compte n°
										</th>
										<th style="">
											banque
										</th>
										<th style="">
											n° du chèque
										</th>
										<th style="">
											montant
										</th>
										<th style="">
											date d'encaissement
										</th>
										<th style="">
											à enregistrer
										</th>
									</tr>

EOT;

				$html .= <<<EOT

							<tr>
								<td style="text-align: center;">
									$idCompte
								</td>
								<td style="text-align: center;">
									<input name="nouveauChequeCotisation[banque]" value="" type="text"  style="text-align: center;">
								</td>
								<td style="text-align: center;">
									<input name="nouveauChequeCotisation[numero]" value="" type="text" style="text-align: center;">
								</td>
								<td style="text-align: center;">
									<input name="nouveauChequeCotisation[montant]" value="" type="text" style="text-align: right;"> €
								</td>
								<td style="text-align: center;">
									<input name="nouveauChequeCotisation[dateEncaissement]" value="" type="date" style="text-align: center;">
									
								</td>
								<td style="text-align: center;">
									<input name="ajouterChequeCotisation" value="ajouter" type="checkbox">
								</td>
							</tr>

EOT;

				$html .= <<<EOT
						</tbody>
					</table>
EOT;
			}
						
		// 5e table contenu ajout de chèque contrat
		if (!empty($contrat)) {
			// select contrat
			$selectIdContrat = <<<EOT
									<select name="nouveauChequeContrat[idContrat]">
EOT;
			foreach ($contrat AS $unContrat) {
				$selectIdContrat .= <<<EOT
										<option value="{$unContrat['idContrat']}">{$unContrat['idContrat']}</option>
EOT;
				
			}
			$selectIdContrat .= <<<EOT
									</select>
EOT;
			$html .= <<<EOT
							<table class='hoverTable' border='1' style='width: 100%; margin: 0px; '> <!--  1ère table de contenu -->
								<tbody>
									<!-- ajouter chèque contrat -->
									<tr>
										<th colspan="10" style="text-align: left;">
											Ajouter un chèque pour un contrat existant
										</th>
									</tr>
									<tr>
										<th style="">
											contrat n°
										</th>
										<th style="">
											banque
										</th>
										<th style="">
											n° du chèque
										</th>
										<th style="">
											montant
										</th>
										<th style="">
											date d'encaissement
										</th>
										<th style="">
											à enregistrer
										</th>
									</tr>

EOT;

				$html .= <<<EOT

							<tr>
								<td style="text-align: center;">
									$selectIdContrat
								</td>
								<td style="text-align: center;">
									<input name="nouveauChequeContrat[banque]" value="" type="text"  style="text-align: center;">
								</td>
								<td style="text-align: center;">
									<input name="nouveauChequeContrat[numero]" value="" type="text" style="text-align: center;">
								</td>
								<td style="text-align: center;">
									<input name="nouveauChequeContrat[montant]" value="" type="text" style="text-align: right;"> €
								</td>
								<td style="text-align: center;">
									<input name="nouveauChequeContrat[dateEncaissement]" value="" type="date" style="text-align: center;">
									
								</td>
								<td style="text-align: center;">
									<input name="ajouterChequeContrat" value="ajouter" type="checkbox">
								</td>
							</tr>

EOT;

				$html .= <<<EOT
						</tbody>
					</table>
EOT;
			
		}

		

		$html .= <<<EOT
					</td> <!-- fin de la cellule contenant la table du contenu de la section -->
					
					<!-- la cellule de droite contenant les commandes enregistrer et abandonner Attention renseigner le nom de section -->
					
					<td rowspan="100" valign="middle" style="text-align: center; font-weight:bold; width: 150px;background-color: #009688;">
						<input style="font-weight:bold; "  name="enregistrer" value="Enregistrer" type="button" onClick="this.form.newAction.value='enregistrer';
						mettreAJourCompte('cheques');
						">
						<br><br>
						<input style="font-weight:bold; "  name="abandonner" value="Abandonner" type="button" onClick="this.form.newAction.value='abandonner';
						mettreAJourCompte('cheques');
						">
					</td>
				</table>
			</form>
EOT;
		} // fin code HTML
		return $html;
	} 
	
	function genererHtmlCourriels($message) {
		$html =  <<<EOT
			<div id="rcourriels">
				<hr>
				<h2>Courriels</h2>
			</div>
EOT;
		return $html;
	} 
	
	function mettreAJourContent() {
		$html = "";
		$html .= ' <div id="attributs"> '.genererHtmlAttributs('').'</div>';
		$html .= ' <div id="volontariat"> '.genererHtmlVolontariat('').'</div>';
		$html .= ' <div id="reports"> '.genererHtmlReports('').'</div>';
		$html .= ' <div id="ajouts"> '.genererHtmlAjouts('').'</div>';
		$html .= ' <div id="contrats"> '.genererHtmlContrats('').'</div>';
		$html .= ' <div id="cheques"> '.genererHtmlCheques('').'</div>';
		$html .= ' <div id="courriels"> '.genererHtmlCourriels('').'</div>';
		echo $html;
	} // fin mettreAJourContent

	function mettreAJourAttributs() {

		function enregistrerAttributs() {
			//var_dump($_POST);die();
			$message = '';
			// traitement des modifications du compte (adherent, principal, commentaire)
			$idCompte = $_POST['idCompte'];
			if (isset($_POST['adherent'])) $valeurAdherent = 'oui';
			else $valeurAdherent = 'non';
			$valeurTitulairePrincipalId = $_POST['principal'];
			$valeurCommentaire = $_POST['commentaire'];
			
			$tabCommentaire = explode('\r\n', $valeurCommentaire);
			$valeurCommentaire = '';
			foreach ($tabCommentaire AS $i =>$laLigne){
				if (strpos($laLigne,"Absent :")===FALSE) {
					$valeurCommentaire .= $laLigne."\r\n";
				}
			}
			// enlever le dernier\r\n
			$valeurCommentaire = trim($valeurCommentaire);
			
			// contrôle de l'existence du nom du titulaire principal
			if ($_POST['nom'][$valeurTitulairePrincipalId]!='') {
				$sql = "UPDATE PDM_compte SET adherent = '$valeurAdherent', titulairePrincipalId = $valeurTitulairePrincipalId, commentaire = '$valeurCommentaire', dateModification = CURRENT_TIME() WHERE idCompte = $idCompte;";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				$nomTitulairePrincipal = $_POST["nom"][$valeurTitulairePrincipalId];
				$message .= "Le compte $nomTitulairePrincipal a été mis à jour. ";
			}
			else { // le titulaire principal n'a pas de nom !!! => on quitte
				$message .= "Le compte n'a pas été mis à jour car le titulaire principal proposé n'avait pas de nom ! ";
				return $message;
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
								$sql1 = "DELETE FROM PDM_volontaireEmargement WHERE personneId=$idPersonne";
								$res1 = mysqli_query($GLOBALS['lkId'],$sql1);
								$sql1 = "DELETE FROM PDM_volontaireDistribution WHERE personneId=$idPersonne";
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
						$sql = "DELETE FROM PDM_personne WHERE idPersonne=$idPersonneSuppimer
						";
						$res = mysqli_query($GLOBALS['lkId'],$sql);
						$sql = "DELETE FROM PDM_volontaireEmargement WHERE idVolontaire=$idPersonneSuppimer
						";
						$res = mysqli_query($GLOBALS['lkId'],$sql);
						$sql = "DELETE FROM PDM_volontaireDistribution WHERE idVolontaire=$idPersonneSuppimer
						";
						$res = mysqli_query($GLOBALS['lkId'],$sql);
						$nomSupprimer = $_POST['nom'][$idPersonneSuppimer];
						$message .= "<br>$nomSupprimer a été supprimé.";
					}
				}
			}
			return $message;
//$message = 'fin enregistrer';
//die($message);			
				
		} // fin enregistrerAttributs

//var_dump($_POST);die();

		if ($_POST['newAction']=='enregistrer') {
			// enregistrement
			$message = enregistrerAttributs();
		}
		else $message = '';
		// génération de la div
		$html = genererHtmlAttributs($message);
		str_replace('\n',' ',$html);
		echo $html;
	} // fin mettreAJourAttributs
	
	function mettreAJourVolontariat() {
		function enregistrerVolontariat() {
			foreach ($_POST['volontaire'] AS $idPersonne => $volontairePersonne) {
				foreach ($volontairePersonne AS $idDistribution => $volontairePersonneDistribution) {
					$sql = "DELETE FROM PDM_volontaireEmargement WHERE personneId=$idPersonne AND distributionID=$idDistribution";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$sql = "DELETE FROM PDM_volontaireDistribution WHERE personneId=$idPersonne AND distributionID=$idDistribution";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					switch ($volontairePersonneDistribution) {
						case 1 :
							$sql = "INSERT INTO PDM_volontaireDistribution(idVolontaire, personneId, distributionId, dateModification) VALUES (NULL, $idPersonne, $idDistribution, CURRENT_TIMESTAMP)";
							$res = mysqli_query($GLOBALS['lkId'],$sql);
							break;
						case 2 :
							$sql = "INSERT INTO PDM_volontaireEmargement(idVolontaire, personneId, distributionId, dateModification) VALUES (NULL, $idPersonne, $idDistribution, CURRENT_TIMESTAMP)";
							$res = mysqli_query($GLOBALS['lkId'],$sql);
							break;
					}
				}
			}
			$message = "Le volontariat a été mis à jour.";
			return $message;
				
		} // fin enregistrer
		
		$message = '';
		if ($_POST['newAction']=='enregistrer') {
			// enregistrement
			$message = enregistrerVolontariat();
		}
		// génération de la div
		$html = genererHtmlVolontariat($message);
		echo $html;
	} // fin mettreAJourVolontariat
	
	function mettreAJourReports() {
		function enregistrerReports() {
			// INSERT INTO `PDM_report` (`idReport`, `contratId`, `quantite`, `origine`, `destination`, `dateModification`) VALUES (NULL, '25', '2', '3', '5', CURRENT_TIMESTAMP);
			
			// array(8) { ["newAction"]=> string(11) "enregistrer" ["idCompte"]=> string(2) "25" ["divId"]=> string(7) "reports" ["supprimer"]=> array(1) { [1]=> string(9) "supprimer" } ["nouveauQuantite"]=> string(1) "1" ["nouveauOrigine"]=> string(1) "5" ["nouveauDestination"]=> string(2) "6}" ["aAjouterNouveau"]=> string(3) "oui" }
			
			// array(8) { ["newAction"]=> string(11) "enregistrer" ["idCompte"]=> string(2) "25" ["idContrat"]=> string(2) "10" ["divId"]=> string(7) "reports" ["nouveauQuantite"]=> string(1) "1" ["nouveauOrigine"]=> string(1) "5" ["nouveauDestination"]=> string(2) "6}" ["aAjouterNouveau"]=> string(3) "oui" } 
			
			$message= '';
			// supprimer si nécessaire
			if (isset($_POST['supprimer'])){
				foreach ($_POST['supprimer'] AS $cle => $unSupprimer) {
					$sql = "DELETE FROM PDM_report WHERE idReport=$cle";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$message = "Les reports ont été mis à jour.";
				}
			}
			// ajouter si nécessaire !!!!!PB si on ajoute le même ????
			if (isset($_POST['aAjouterNouveau'])) {
				$sql = "INSERT INTO PDM_report (idReport, contratId, quantite, origine, destination, dateModification) VALUES (NULL, {$_POST['idContrat']}, {$_POST['nouveauQuantite']}, {$_POST['nouveauOrigine']}, {$_POST['nouveauDestination']}, CURRENT_TIMESTAMP);";
//die($sql);
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				$message = "Les reports ont été mis à jour.";
			}
			return $message;
		} // fin enregistrer

		if ($_POST['newAction']=='enregistrer') {
			// enregistrement
			$message = enregistrerReports();
		}
		// génération de la div
		$html = genererHtmlReports($message);
		echo $html;
	} // fin mettreAJourReports
	
	function mettreAJourAjouts() {
		function enregistrerAjouts() {
			
			$message= '';
			// supprimer si nécessaire
			if (isset($_POST['supprimer'])){
				foreach ($_POST['supprimer'] AS $cle => $unSupprimer) {
					$sql = "DELETE FROM PDM_ajout WHERE idAjout=$cle";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$message = "Les ajouts ont été mis à jour.";
				}
			}
			// ajouter si nécessaire 
			if (isset($_POST['aAjouterNouveau'])) {
				$sql = "INSERT INTO PDM_ajout (idAjout, contratId, distributionId, quantite, dateModification) VALUES (NULL, {$_POST['idContrat']}, {$_POST['nouveauIdDistribution']}, {$_POST['nouveauQuantite']}, CURRENT_TIMESTAMP);";
//die($sql);
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				$message = "Les reports ont été mis à jour.";
			}
			return $message;
			
		} // fin enregistrer

		if ($_POST['newAction']=='enregistrer') {
			// enregistrement
			$message = enregistrerAjouts();
		}
		// génération de la div
		$html = genererHtmlAjouts($message);
		echo $html;
	} // fin mettreAJourAjouts
	
	function mettreAJourContrats() {

		function enregistrerModificationContrats() {
			$message = "raté";
			// enregistrer modifications même si on supprime plus loin !
			foreach ($_POST['dateFin'] AS $idContrat => $dateFin) {
				$sql = "UPDATE PDM_contrat SET dateFin='$dateFin' WHERE idContrat=$idContrat";
				$res = mysqli_query($GLOBALS['lkId'],$sql);
				// valide ?
				if (isset($_POST['valide'][$idContrat])) $valide = 1;
				else $valide = 0;
				$sql = "UPDATE PDM_contrat SET valide=$valide WHERE idContrat=$idContrat";
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
/*			// validé ? (NB ne peut pas être invalidé)
			if (isset($_POST['valide'])) {
				foreach ($_POST['valide'] AS $idContrat => $dateDebut) {
					$sql = "UPDATE PDM_contrat SET valide=1 WHERE idContrat=$idContrat";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$message = "Modification(s) enregistrée(s).";
				}
			}
*/			
			// supprimer ?
			if (isset($_POST['aSupprimer'])) {
				foreach ($_POST['aSupprimer'] AS $idContrat =>$valeur) {
					// enregistrement de la suppression dans les commentaires du compte
					$sql = "UPDATE PDM_compte SET commentaire=CONCAT(commentaire,' Attention ! un contrat (au moins) a été supprimé ! ') WHERE idCompte = (SELECT compteId FROM PDM_contrat WHERE idContrat=$idContrat)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					// suppression du contrat
					$sql = "DELETE FROM PDM_contrat WHERE idContrat=$idContrat";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
				}
				$message .= " Suppression(s) de contrat effectuée(s).";
			}
			return $message;
		}
		
		function enregistrerNouveauContrat() {
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
					$whereSemaine = "AND semaine = 'Q2'";
					break;
				case 3 :
					$whereSemaine = "AND semaine = 'Q1'";
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
//die($sql);
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

			// insertion
			$sql = "
			INSERT INTO PDM_contrat(idContrat, compteId, anneeId, typeContratId, quantiteContratId, frequenceContratId, periodeContratId, dateDebut, dateFin, valide, dateCreation, dateModification) VALUES (NULL,{$_POST['idCompte']},{$_POST['nouveau']['annee']},{$_POST['nouveau']['type']},$quantite,{$_POST['nouveau']['frequence']},{$_POST['nouveau']['periode']},'$dateDebut','$dateFin',0,CURRENT_TIMESTAMP,NULL)
			";
//die($sql);
			$res = mysqli_query($GLOBALS['lkId'],$sql);
			// message et retour
			$message = "Le nouveau contrat a été enregistré.";
			return $message;
		}
		

		if ($_POST['newAction']=='enregistrer') {
			// enregistrement
			$message = enregistrerModificationContrats();
			if (isset($_POST['aAjouterNouveau'])) {
				$message .= enregistrerNouveauContrat();
			}
		}
		// génération de la div
		$html = genererHtmlContrats($message);
		echo $html;
	} // fin mettreAJourContrats
	
	function mettreAJourCheques() {
		function enregistrerCheques() {
			$message = '';
			$idCompte = $_POST['idCompte'];
			// update chaque cotisation
			if (isset($_POST['chequeCotisation'])) {
				foreach($_POST['chequeCotisation'] AS $idCheque => $unCheque) {
					$unCheque['montant'] = str_replace(',','.',$unCheque['montant']);
					if ($unCheque['dateEncaissement']=='') $unCheque['dateEncaissement'] = 'NULL';
					else $unCheque['dateEncaissement'] = "'{$unCheque['dateEncaissement']}'";
					$sql = "UPDATE PDM_chequeCotisation SET banque='{$unCheque['banque']}', numero={$unCheque['numero']},montant={$unCheque['montant']},dateEncaissement={$unCheque['dateEncaissement']},dateModification=CURRENT_TIMESTAMP WHERE idChequeCotisation=$idCheque";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
				}
				$message .= "Le chèque cotisation a été mis à jour. ";
			}
			
			// uptdate cheques contrat
			if (isset($_POST['chequeContrat'])) {
				$n = 0;
				foreach($_POST['chequeContrat'] AS $idCheque => $unCheque) {
					$unCheque['montant'] = str_replace(',','.',$unCheque['montant']);
					if ($unCheque['dateEncaissement']=='') $unCheque['dateEncaissement'] = 'NULL';
					else $unCheque['dateEncaissement'] = "'{$unCheque['dateEncaissement']}'";
					$sql = "UPDATE PDM_chequeContrat SET banque='{$unCheque['banque']}', numero={$unCheque['numero']},montant={$unCheque['montant']},dateEncaissement={$unCheque['dateEncaissement']},dateModification=CURRENT_TIMESTAMP WHERE idChequeContrat=$idCheque";
					$res = mysqli_query($GLOBALS['lkId'],$sql);

					$n++;
				}
				if ($n>1) $message .= "Le chèque contrat a été mis à jour. ";
				else $message .= "Les chèques contrat ont été mis à jour. ";
			}
			
			// suppression cheque cotisation
			if (isset($_POST['supprimerChequeCotisation'])) {
				foreach($_POST['supprimerChequeCotisation'] AS $idCheque =>$valeur) {
					$sql = "DELETE FROM PDM_chequeCotisation WHERE idChequeCotisation=$idCheque";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
				}
				$message .= "Le chèque cotisation coché a été supprimé. ";
			}
			
			// suppression cheques contrat
			if (isset($_POST['supprimerChequeContrat'])) {
				$n = 0;
				foreach($_POST['supprimerChequeContrat'] AS $idCheque =>$valeur) {
					$sql = "DELETE FROM PDM_chequeContrat WHERE idChequeContrat=$idCheque";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$n++;
				}
				if ($n>1) $message .= "Le chèque contrat coché a été supprimé. ";
				else $message .= "Les chèques contrat cochés ont été supprimés. ";
			}
			
			// ajout chèque cotisation
			if (isset($_POST['ajouterChequeCotisation'])) {
				if ($_POST['nouveauChequeCotisation']['numero']!='' && is_numeric($_POST['nouveauChequeCotisation']['montant'])) {
					$_POST['nouveauChequeCotisation']['montant'] = str_replace(',','.',$_POST['nouveauChequeCotisation']['montant']);
					if ($_POST['nouveauChequeCotisation']['dateEncaissement']=='') $_POST['nouveauChequeCotisation']['dateEncaissement'] = 'NULL';
					else $_POST['nouveauChequeCotisation']['dateEncaissement'] = "'{$_POST['nouveauChequeCotisation']['dateEncaissement']}'";
					$sql = "INSERT INTO PDM_chequeCotisation(idChequeCotisation, compteId, banque, numero, montant, dateEncaissement, dateModification) VALUES (NULL,$idCompte ,'{$_POST['nouveauChequeCotisation']['banque']}',{$_POST['nouveauChequeCotisation']['numero']}, {$_POST['nouveauChequeCotisation']['montant']}, '{$_POST['nouveauChequeCotisation']['dateEncaissement']}',CURRENT_TIMESTAMP)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$message .= "Le nouveau chèque cotisation a été enregistré. ";
				}
			}
			
			// ajout chèque contrat
			if (isset($_POST['ajouterChequeContrat'])) {
				if ($_POST['nouveauChequeContrat']['numero']!='' && is_numeric($_POST['nouveauChequeContrat']['montant'])) {
					$_POST['nouveauChequeContrat']['montant'] = str_replace(',','.',$_POST['nouveauChequeContrat']['montant']);
					if ($_POST['nouveauChequeContrat']['dateEncaissement']=='') $_POST['nouveauChequeContrat']['dateEncaissement'] = 'NULL';
					else $_POST['nouveauChequeCotisation']['dateEncaissement'] = "'{$_POST['nouveauChequeCotisation']['dateEncaissement']}'";					
					$sql = "INSERT INTO PDM_chequeContrat(idChequeContrat, contratId, banque, numero, montant, dateEncaissement, dateModification) VALUES (NULL,{$_POST['nouveauChequeContrat']['idContrat']},'{$_POST['nouveauChequeContrat']['banque']}',{$_POST['nouveauChequeContrat']['numero']},{$_POST['nouveauChequeContrat']['montant']}, {$_POST['nouveauChequeContrat']['dateEncaissement']},CURRENT_TIMESTAMP)";
					$res = mysqli_query($GLOBALS['lkId'],$sql);
					$message .= "Le nouveau chèque contrat a été enregistré. ";
				}
			}
			
			return $message;
		} // fin enregistrer
//var_dump($_POST); die;



		if ($_POST['newAction']=='enregistrer') {
			// enregistrement
			$message = enregistrerCheques();
		}
		// génération de la div
		$html = genererHtmlCheques($message);
		echo $html;
	} // fin mettreAJourCheques
	
	function mettreAJourCourriels() {
		function enregistrerCourriels() {
			
		} // fin enregistrer

		if ($_POST['newAction']=='enregistrer') {
			// enregistrement
			enregistrerCourriels();
		}
		// génération de la div
		$html = genererHtmlCourriels();
		echo $html;
	} // fin mettreAJourCourriels
	
?>