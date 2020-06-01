<?php
// headHTML.inc.php
?>

	<head>
		<meta content="text/html; charset=UTF-8" http-equiv="content-type"> <!--ISO-8859-1 -->
		<title><?php echo($GLOBALS['titrePage']); ?></title>

		<link rel="stylesheet" type="text/css" href="css/normalize.css">
		<link rel="stylesheet" type="text/css" href="css/gestionPDM.css">
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">

		<script src="js/jquery.js"></script>
		<script src="js/jquery-ui.min.js"></script>
		<script src="js/pdm.js"></script>
		
		

		<script type="text/javascript">
		
		function initialiserCompte(idCompte) {
			var idlaDiv = '#content';
				$.post(
					"mettreAJourCompte.php",
						{
							divId: 'content',
							idCompte: idCompte
						},
					function(data) {
						// alert(data);
						//$( idlaDiv ).html('');
						$( idlaDiv ).html(data);
//						document.getElementById(laDiv).innerHTML = data;
					}
				);
		}
		
		function mettreAJourCompte(laDiv) {
				var formlaDiv = '#form_'+laDiv;
				var idlaDiv = '#'+laDiv;

				$.post(
					"mettreAJourCompte.php",
					$(formlaDiv).serialize(),
					function(data) {
//						 alert(data);
//						$( idlaDiv ).html('');
						$( idlaDiv ).html(data);
//						document.getElementById(laDiv).innerHTML = data;
					}
				);

		}

			function CheckRadio(name) { 
				//recupere tous les objets qui ont le nom "name" 
				var objs=document.getElementsByName(name); 
				//Pour chaques objets.... 
				for(i=0;i<objs.length;i++) { 
					//Si l'objet en cours en coché on renvoie true 
					if (objs[i].checked==true) 
						return true; 
				} 
				//Si on arrive ici, aucun radio-bouton n'est coché, on renvoie false 
				return false; 
			}

			function attendre() {
				var text = '<!DOCTYPE html><html lang="fr-fr"><head><title>something</title></head><body><!DOCTYPE html><html lang="fr-fr"><head></head> <body style=" text-align:center; font-family:sans-serif;"><p><img alt="" src="images/waitblue.gif"></p> <p><br></p> <p>Veuillez patienter ; votre mot de passe est en cours d\'expédition.</p> </body></html>';

				var encodedText = encodeURIComponent(text);
				content.document.location.href = "javascript:(function(){document.open();document.write('"+encodedText +"'); document.close();})";	
			}
			// move and resize div #aide jquery-uid
			$(function() {
	//			$( "#fiche" ).draggable();
	//			$( "#fiche" ).resizable();
				
			});
			

		</script>
		
		<script type="text/javascript">    

			window.onresize = redim;

			function redimmensionner() {
	//			alert("on va recharger !");
	//			location.reload(true);
	//			location.replace("<?php echo($_SERVER['PHP_SELF'] );?>");
				redim();
			}
		
			function hauteur(obj) {
				if (obj.offsetHeight) {
					if (obj.offsetHeight==undefined) {return(0)}
					else return(obj.offsetHeight);
				}
				else {
					if (obj.style.pixelHeight){
						if (obj.style.pixelHeight==undefined) {return(0)}
						else return(obj.style.pixelHeight);
					}
					else return(0);
				}
			}

			function hauteurWindow() {
				if (window.innerHeight)  {return(window.innerHeight);}
				else if(document.body.clientHeight)  {return(document.body.clientHeight);}
			}
			function largeurWindow() {
				if (window.innerWidth)  {return(window.innerWidth);}
				else if(document.body.clientWidth)  {return(document.body.clientWidth);}
			}

			function reposAttendre() {
				attendre = document.getElementById("attendre");
				var largeurTotale = largeurWindow();
				var hauteurTotale = hauteurWindow();
				var left =Math.floor(largeurTotale/2)-50;
				var top =Math.floor(hauteurTotale/2)-50;
				attendre.style.left = left+"px";
				attendre.style.top = top+"px";
				attendre.style.display = 'inline';
			}
			
			function redim() {
			// redim de la fenêtre
				if(document.getElementById("haut") != null && document.getElementById("content") != null && document.getElementById("bas") != null) {
					var haut = document.getElementById("haut"); 
					var content = document.getElementById("content");
					var bas = document.getElementById("bas");
					
					var paddpx = document.body.style.paddingTop;
					var padd = paddpx.substr(0,paddpx.length-2);
					padd = parseInt(padd);
					if (hauteurWindow()<650)  {//768
	/*
						haut.style.position = "static";
						content.style.position = "static";
						content.style.width = "100%";
						bas.style.position = "static";
	*/
	//alert("Attention la fenêtre est trop petite !");
					}
						// on fixe les largeurs pour obtenir des hauteurs justes
						var largeurTotale = largeurWindow()-10; // 0 -20 -10  pour tenir compte du padding-left
						if(navigator.userAgent.indexOf('MSIE')>0) {
							largeurTotale = largeurTotale -10;
						}
						var largeurDivPx = String(largeurTotale)+'px'; // -8 pour tenir compte de la marge gauche de body					
						var hauteurTotale = hauteurWindow();
						var hauteurHaut = hauteur(haut) ;
						var hauteurBas = hauteur(bas);
						var hauteurContent = hauteurTotale-hauteurHaut-hauteurBas-0; // -20 -10 -30 -0!
						if (hauteurContent<20) {
							alert("Attention la fenêtre n'est pas assez grande. Veuillez l'agrandir.");
						}	
						var posContentPx = String(hauteurHaut+0)+'px'; //+pad
						var posBasPx = String(hauteurTotale-hauteurBas)+'px';
						var hauteurContentPx = String(hauteurContent)+'px'; 
						if (hauteurContent>0) {
							content.style.height = hauteurContentPx;
						}
						haut.style.width = largeurDivPx;
haut.style.margin = 'auto';
						content.style.width = largeurDivPx;
content.style.margin = 'auto';
						bas.style.width = largeurDivPx;
bas.style.margin = 'auto';
						document.body.style.overflow="hidden";
	//					content.style.top = posContentPx;
	//					content.style.bottom = posBasPx;
	//					bas.style.top = posBasPx;
				}
			} // fin redim

			
			// impression d'une div
			var gAutoPrint = true;
			function processPrint(printMe){
				if (document.getElementById != null){
				var html = '<HTML>\n<HEAD>\n';
	// à revoir :
					html += "\n<style type='text/css'>	 body {font-family:sans-serif; font-size:small; text-align: justify; }  </style>\n";

				html += '\n</HE' + 'AD>\n<BODY>\n';
				var printReadyElem = document.getElementById(printMe);

				if (printReadyElem != null) html +=  printReadyElem.innerHTML;
				else{
				alert("Erreur, rien à imprimer.");
				return;
				}

				html += '\n</BO' + 'DY>\n</HT' + 'ML>';
				var printWin = window.open("","processPrint", config='height=600, width=800, toolbar=yes, menubar=yes, scrollbars=yes, resizable=yes, location=yes, directories=yes, status=yes');
				printWin.document.open();
				printWin.document.write(html);
				printWin.document.close();

				if (gAutoPrint) printWin.print();
				} 
				else alert("Navigateur non supporté.");
			}

			// affiche une attente à la place de la page
			function afficherWait() {
				var wait = '<div id="attendre" class="attendre"><p><img alt="" src="images/waitblue.gif" style=" text-align:center; font-size:medium;"></p>	<p><br></p><p>Veuillez patienter ; 	votre mot de passe est en cours d\'expédition.</p></div>';
			document.body.innerHTML = wait;
			}
				
		</script>
	</head>
