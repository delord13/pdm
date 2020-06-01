<?php
// init.inc.php
///////////////////////////////////////////////////////////////////////////////
//	application gestionPDM
// février 2020
///////////////////////////////////////////////////////////////////////////////
/*
$_SESSION['auj']
$_SESSION['accesAutorise']
$_SESSION['anneeCouranteId']
*/

	//$_SESSION['auj'] = '2021-02-24';
	// à remplacer par 
	$_SESSION['auj'] = date('Y-m-d');
	// $_SESSION['anneeCouranteId'] : voir plus bas #86
	
	
//////////////////////////////////////////////////////////////////////////////////////
// contrôle d'accès
//////////////////////////////////////////////////////////////////////////////////////

	if (!isset($_SESSION['accesAutorise'])) {
		if (!isset($_COOKIE['adminPDM'])) {
			header('Location: authentification.php');
		}
	}
//////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////
// MYSQL
//////////////////////////////////////////////////////////////////////////////////////

// selon hébergeur
//die("-".$_SERVER['SERVER_NAME']."-");
	switch ($_SERVER['SERVER_NAME']) {
	case "localhost":
				$host = "localhost";
				$base    = "pamaPDM";  // nom de la base mysql
				$user    = "pamaPDM";  // nom de l'utilisateur mysql ayant les droits sur la base
				$passwd  = "7rutabaga";  // mot de passe de l'utilisateur de la base
				$php = 5;
		break;
	case "127.0.0.1":
				$host = "localhost";
				$base    = "pamaPDM";  // nom de la base mysql
				$user    = "pamaPDM";  // nom de l'utilisateur mysql ayant les droits sur la base
				$passwd  = "7rutabaga";  // mot de passe de l'utilisateur de la base
				$php = 5;
		break;
	case "intranet.lespaniersmarseillais.org":
//die("www.istresrando.lautre.net ");
				$host = "lespaniegspip.mysql.db";
				$base    = "lespaniegspip";  // nom de la base mysql
				$user    = "lespaniegspip";  // nom de l'utilisateur mysql ayant les droits sur la base
				$passwd  = "7rutabaga";  // mot de passe de l'utilisateur de la base
				$php = 5;
		break;
	case "pdm.lespaniersmarseillais.org":
//die("www.istresrando.lautre.net ");
				$host = "lespaniegspip.mysql.db";
				$base    = "lespaniegspip";  // nom de la base mysql
				$user    = "lespaniegspip";  // nom de l'utilisateur mysql ayant les droits sur la base
				$passwd  = "7rutabaga";  // mot de passe de l'utilisateur de la base
				$php = 5;
		break;
	case "lespanieg.cluster002.ovh.net":
//die("www.istresrando.lautre.net ");
				$host = "lespaniegspip.mysql.db";
				$base    = "lespaniegspip";  // nom de la base mysql
				$user    = "lespaniegspip";  // nom de l'utilisateur mysql ayant les droits sur la base
				$passwd  = "7rutabaga";  // mot de passe de l'utilisateur de la base
				$php = 5;
		break;
	}

	
	
// connexion sql
	if (! $lkId=mysqli_connect($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['passwd'], $GLOBALS['base'])) {
			echo "Impossible d'établir la connexion à ",$GLOBALS['host'],"<br>";
			die;
	}
/* Modification du jeu de résultats en utf8 */

	if (function_exists('mysqli_set_charset')) {
			mysqli_set_charset($lkId, 'utf8');
	} 
	else {
			mysqli_query($lkId, 'SET NAMES utf8');
	}


// pour éviter les injections SQL, on échappe les POST
	if (isset($_POST)) {
		foreach($_POST as $index => $unPost) {
				if (is_string($unPost)) {
					$_POST[$index] = mysqli_real_escape_string($GLOBALS['lkId'],$unPost);
				}
		}
	}

//  fin MYSQL
//////////////////////////////////////////////////////////////////////////////////////


///////////////////////////////
// $_SESSION['anneeCouranteId']
///////////////////////////////
	$tab = explode('-',$_SESSION['auj']);
	$a = $tab[0];
	$ac = $a.'-'.($a+1);
	$sql = "SELECT * FROM PDM_annee WHERE nom='$ac'";
	$res = mysqli_query($GLOBALS['lkId'],$sql);
	$uneAnnee=mysqli_fetch_assoc($res);
	if ($_SESSION['auj']>=$uneAnnee['datePremiereDistribution']) $_SESSION['anneeCouranteId'] = $uneAnnee['idAnnee'];
	else $_SESSION['anneeCouranteId'] = $uneAnnee['idAnnee']-1;



///////////////////////////////
// fonctions utilitaires
///////////////////////////////

	
	function attributsDate($dateDistribution) {
	// retourne les attributs d'une date de distribution
		// $idAnnee
		$sql = "SELECT MAX(idAnnee) id FROM PDM_annee WHERE datePremiereDistribution<='$dateDistribution'";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$annee = mysqli_fetch_assoc($res);
		$idAnnee = $annee['id'];
		
		// date 1ère et nb de semaines dans PDM_annee
		$sql = "SELECT * FROM PDM_annee WHERE idAnnee=$idAnnee";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$annee = mysqli_fetch_assoc($res);// construction de la table 
		$datePremiereDistribution = $annee['datePremiereDistribution'];
		
		// nbre de semaines entre la date et la date de premère distribution
		$nbSemaines = (strtotime($dateDistribution)-strtotime($datePremiereDistribution))/(7*24*3600);
		// attributs sauf période de Noël
		if ($nbSemaines%2==1) $attribut['semaine'] = 'Q2';
		else  $attribut['semaine'] = 'Q1';
		if ($nbSemaines<26) $attribut['semestre'] = 1;
		else $attribut['semestre'] = 2;
		$attribut['nombreUnites'] = 1;
		
		// traitement de la période de Noël
		$moisJourPremièreDistribution = date('m-d',strtotime($datePremiereDistribution));
		$anPremiereDistribution = date('Y',strtotime($datePremiereDistribution));
		if ($moisJourPremièreDistribution<'12-25') $anneeNoel =  $anPremiereDistribution;
		else $anneeNoel = (string)($anPremiereDistribution+1);
		
		$dateNoël = $anneeNoel.'-12-25'; 
		$jourSemaineDateNoel = date('N',strtotime($dateNoël));
		$decalageMercrediSemaineNoel = 3-$jourSemaineDateNoel;
		$dateMercrediSemaineNoel = date('Y-m-d',strtotime($dateNoël)+($decalageMercrediSemaineNoel*24*3600));
//echo($dateMercrediSemaineNoel.'<br>');
		$dateMercrediPrecedantSemaineNoel = date('Y-m-d',strtotime($dateMercrediSemaineNoel)-(7*24*3600));
//echo($dateMercrediPrecedantSemaineNoel.'<br>');
		$dateMercrediSemaineJourAn = date('Y-m-d',strtotime($dateMercrediSemaineNoel)+(7*24*3600));
//echo($dateMercrediSemaineJourAn.'<br>');
		$dateMercrediSuivantSemaineJourAn = date('Y-m-d',strtotime($dateMercrediSemaineJourAn)+(7*24*3600));
//die($dateMercrediSuivantSemaineJourAn);
		switch ($dateDistribution) {
			case $dateMercrediPrecedantSemaineNoel :
				$attribut['nombreUnites'] = 2;
				break;
			case $dateMercrediSemaineNoel :
				$attribut['nombreUnites'] = 0;
				break;
			case $dateMercrediSemaineJourAn :
				$attribut['nombreUnites'] = 0;
				break;
			case $dateMercrediSuivantSemaineJourAn :
				$attribut['nombreUnites'] = 2;
				break;
		}
		return $attribut;
	} // fin function attributsDate($dateDistribution)
/*	
// test
// tous les mercredis année PDM 2020
$laDate = "2020-04-01";
for ($i=1;$i<=52;$i++) {
	echo($laDate.' : '); var_dump(attributsDate($laDate));echo('<br>');
	$laDate = date('Y-m-d',strtotime($laDate)+(7*25*3600)); // putain d'heure d'été
}

var_dump(tableDistribution(2));
die();
*/
	function premierMercrediPostCommande($date) {
		// premier mercredi séparé par un dimanche
		// jour de la semaine de la date
		$jS = date('N',strtotime($date));
		$decalage = 10-($jS%7); // décalage pour le premier mercredi avec un dimanche entre
		$pMPC = strtotime($date) + $decalage*25*60*60; // 25 heures à cause de l'heure d'hiver
		return date("Y-m-d",$pMPC);
	}
	
	function premierMercredi($date) {
		// jour de la semaine de la date
		$jS = date('N',strtotime($date));
		$decalage = 10-($jS+7); // décalage pour le premier mercredi
		if ($decalage==0) $decalage = 7;
		$pMPC = strtotime($date) + $decalage*25*60*60; // 25 heures à cause de l'heure d'hiver
		return date("Y-m-d",$pMPC);
		
	}
	
	function tableDistribution($idAnnee) { // les dates de distribution avec attributs sans Noël Jour de l'An où nombreUnites = 0
		// date 1ère et nb de semaines dans PDM_annee
		$sql = "SELECT * FROM PDM_annee WHERE idAnnee=$idAnnee";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$annee = mysqli_fetch_assoc($res);
		$datePremiereDistribution = $annee['datePremiereDistribution'];

		$sql = "SELECT * FROM PDM_distribution WHERE anneeId = {$annee['idAnnee']} AND nombreUnites!=0 ORDER BY idDistribution";
//die($sql);
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		while ($uneDistribution = mysqli_fetch_assoc($res)) {
			$distribution[] = $uneDistribution;
		}
//var_dump($distribution); die();
/*
 		$distribution[0]['date'] = $annee['datePremiereDistribution'];
		$attributs = attributsDate($distribution[0]['date']);
		$distribution[0]['nombreUnites'] = $attributs['nombreUnites'];
		$distribution[0]['semaine'] = $attributs['semaine'];
		$distribution[0]['semestre'] = $attributs['semestre'];

		$j = 0;
		$datePrecedente = $distribution[0]['date'];
		for ($i=1; $i<$annee['nombreSemaines']; $i++) {
		// 1 heure de plus à cause de l'heure d'hiver
			$laDate = date('Y-m-d',strtotime($datePrecedente) + (7*25*3600)) ;
         $attributs = attributsDate($laDate);
         if ($attributs['nombreUnites']!=0) {
            $j++;
            $distribution[$j]['date'] = $laDate;
            $distribution[$j]['nombreUnites'] = $attributs['nombreUnites'];
            $distribution[$j]['semaine'] = $attributs['semaine'];
            $distribution[$j]['semestre'] = $attributs['semestre'];
            
         }
         $datePrecedente = $laDate;
		}
*/
//var_dump($distribution); die();
      return $distribution;
	}
	
   // fonctions callback pour filtrage dans tableDateFinPossible et tableDebutPossible
      function semaineQ2($tab) {
			return $tab['semaine']=="Q2";
		}
		function semaineQ1($tab) {
			return $tab['semaine']=="Q1";
		}
		function semestre1($tab) {
			return $tab['semestre']=="1";
		}
		function semestre2($tab) {
			return $tab['semestre']=="2";
		}
		function annulable($tab) {
         $aujourdhui = date('Y-m-d',time());
         $premierDateAnnulable = premierMercrediPostCommande($aujourdhui);
         return $tab['date']>=$premierDateAnnulable;
		}

   function tableDateDebutPossible($unContrat) { // attention par rapport à la date du jour !!!
      $tableDistribution = tableDistribution($unContrat['anneeId']);
      // première date de début possible
      $premiereDate = premierMercrediPostCommande($_SESSION['auj']);
      // rang de la première date de debut possible
      $rangPremiereDateDebutPossible = array_search($premiereDate,$tableDistribution)+1;
		// table réduite
      $tableDistribution = array_slice($tableDistribution,$rangPremiereDateDebutPossible-1,NULL,TRUE);
      
      // filtre selon semestre
      if($unContrat['periodeContratId']==2) {
         $tableDistribution = array_filter($tableDistribution, "semestre1");
      }
      if($unContrat['periodeContratId']==3) {
         $tableDistribution = array_filter($tableDistribution, "semestre2");
      }
      // filtre selon semaine
      if($unContrat['frequenceContratId']==3) {
         $tableDistribution = array_filter($tableDistribution, "semaineQ2");
      }
      if($unContrat['frequenceContratId']==2) {
         $tableDistribution = array_filter($tableDistribution, "semaineQ1");
      }
      
      // retourne le résultat
      return $tableDistribution;
   }

		
   function tableDateFinPossible($unContrat) { // attention par rapport à la date du jour !!!
      $tableDistribution = tableDistribution($unContrat['anneeId']);
      // filtre depuis la première date de distribution annulable
      $premiereDate = premierMercrediPostCommande($unContrat['dateDebut']);
      // rang de la première date de fin possible
      $rangPremiereDateFinPossible = array_search($premiereDate,$tableDistribution)+1;
      // table réduite
      $tableDistribution = array_slice($tableDistribution,$rangPremiereDateFinPossible,NULL,TRUE);
      
      // pour un contrat mensuel : une seule date de fin possible selon semaine ou quinzaine
      if ($unContrat['periodeContratId']==4) {
         if ($unContrat['frequenceContratId']==1) { // semaine
            $dateFinMois = date('Y-m-d',strtotime($unContrat['dateDebut'])+3*7*25*3600);
            $rangDateFin = array_search($dateFinMois,array_column($tableDistribution, 'date'));
         }
         else { //quinzaine
           $dateFinMois = date('Y-m-d',strtotime($unContrat['dateDebut'])+2*7*25*3600);
            $rangDateFin = array_search($dateFinMois,array_column($tableDistribution, 'date'));
          }
//die("rang date fin : ".$rangDateFin);
         $tableDistribution = array_slice($tableDistribution,$rangDateFin,1,TRUE);
         return $tableDistribution;
      }
      
      // filtre selon date annulable
      $tableDistribution = array_filter($tableDistribution, "annulable");
      // filtre selon semestre
      if($unContrat['periodeContratId']==2) {
         $tableDistribution = array_filter($tableDistribution, "semestre1");
      }
      if($unContrat['periodeContratId']==3) {
         $tableDistribution = array_filter($tableDistribution, "semestre2");
      }
      // filtre selon semaine
      if($unContrat['frequenceContratId']==3) {
         $tableDistribution = array_filter($tableDistribution, "semaineQ2");
      }
      if($unContrat['frequenceContratId']==2) {
         $tableDistribution = array_filter($tableDistribution, "semaineQ1");
      }
      
      // retourne le résultat
      return $tableDistribution;
   }

//////////////////////////////////////////////////////////////////////////////////////
// fonction diverses
//////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////
// calcul des jours de distribution NB le paramètre date sert à définir l'année panier en cours
	function datePremiereDistribution($date) {
		$sql = "SELECT datePremiereDistribution FROM PDM_annee WHERE '$date' >= datePremiereDistribution ORDER BY idAnnee DESC ";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$laLigne = mysqli_fetch_assoc($res);
		return $laLigne['datePremiereDistribution'];
	}

	function datePremiereDistributionFrequenceContrat($date,$FrequenceContrat) { // et période ???
		$datePremiereDistributionAnnee = datePremiereDistribution($date);
		switch ($FrequenceContrat) {
			case 1 : // semaine
				return $datePremiereDistributionAnnee;
				break;
			case 2 : // semaine impaire
				return $datePremiereDistributionAnnee;
				break;
			case 3 : // semaine paire
				return date('Y-m-d',strtotime($datePremiereDistributionAnnee.' + 7 DAY'));
				break;
		}
	}

	
	function dateFin($datePremiereDistribution) {
		// timestamp de $datePremiereDistribution
		$tDate = explode('-',$datePremiereDistribution);
		
	}
	
	function dateJJMM($dateAAAAMMJJ) {
		$tabDate = explode('-',$dateAAAAMMJJ);
		$retour = $tabDate[2].'/'.$tabDate[1];
		return $retour;
	}

	function dateJJMMAA($dateAAAAMMJJ) {
		$tabDate = explode('-',$dateAAAAMMJJ);
		$AA = substr($tabDate[0],-2);
		$retour = $tabDate[2].'/'.$tabDate[1].'/'.$AA;
		return $retour;
	}

	function dateJJMMAAAA($dateAAAAMMJJ) {
		$tabDate = explode('-',$dateAAAAMMJJ);
		$retour = $tabDate[2].'/'.$tabDate[1].'/'.$tabDate[0];
		return $retour;
	}

	function joursDistribution () {
		// jour de la semaine SELECT jourDistribution FROM PDM_annee WHERE '2020-06-28' BETWEEN dateDebut AND dateFin
		
	}
	
	function ajoutAbsences($idCompte) {
		$sql = "SELECT date FROM PDM_absence, PDM_distribution WHERE compteId=$idCompte AND distributionId=idDistribution ORDER BY date" ;
//die($sql);
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$ajout = '';
		$i = 0;
		while ($laLigne = mysqli_fetch_assoc($res)) {
			if ($i==0) $ajout = '<br>Absent : ';
			$tabDate = explode('-',$laLigne['date']);
			$ajout .= $tabDate[2].'/'.$tabDate[1].' ';
		}
		return $ajout;
	}
?>