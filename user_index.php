<?php
// Start session, som vi bruger til at checke om brugeren er logget ind.
session_start();
?>

<!DOCTYPE html>
<html>
	<head>
		<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT:700' rel='stylesheet' type='text/css'>	
		<meta charset="utf-8">
		<title>Cirkus Tværs</title>
		<link type="text/css" rel="stylesheet" href="style.css" />
		<!-- Det lille fane-ikon -->
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	</head>
	<body>
		<div id="wrapper">
			<div class="ramme_lang">
				<div id="ramme_top">				
					<div id="content_top">
					
					</div> <!--content_top slut-->				
				</div> <!--ramme_top slut-->
				
				<div id="ramme_midt">
				
					<div id="content_midt">
						<img src="billeder/content_midte_dobbelt.png" alt="Content">
						<img src="billeder/content_midte_tripple.png" id="midte1" alt="Content">
						<img src="billeder/content_midte_tripple.png" id="midte2" alt="Content">
					</div> <!--content_midt slut-->
				
				</div> <!--ramme_midt slut-->
				
				<div id="ramme_bund">			
					<div id="content_bund">
					</div> <!--content_bund slut-->
			</div> <!--ramme_bund slut-->
			
			<div class="splat_lang">
				<?php
					// Inkluder dokumentet class_database.php
					include 'classes/class_database.php';
					// Lav en instance af database-klassen
					$db = new Database(); 
					// Lav forbindelse til serverne vha. disse oplysninger
					$db->initiate("localhost","root","root","Circus"); 
					// Forbind til databasen
					$db->connect();

					include 'classes/class_post.php';
					$post = new Post;
					$post->setDatabaseConnection($db);

					include 'classes/class_user.php';
					$user = new User;
					$user->setDatabaseConnection($db);

					include 'classes/class_calendar.php';
					$calendar = new Calendar;
					$calendar->setDatabaseConnection($db);

					// Check om brugeren er igang med at logge ind
					if ( isset($_GET["brugernavn"]) ){
						
						// Gør brug af testLogin-funktionen som ligger i klassen user, til at checke om
						// brugeren har de rigtige login-oplysninger
						if ( $user->testLogin($_GET["brugernavn"], $_GET["password"]) == TRUE ){
							// Hvis resultatet bliver TRUE , brug funktionen setUser til at "sette" brugernavn og password
							$user->setUser($_GET["brugernavn"], $_GET["password"]);
							// Start session loggedOn
							$_SESSION['loggedOn'] = 1;
							// Hent brugernavn
							$_SESSION['userName'] = $user->getUsername();
							// Hent brugerens ID
							$_SESSION['userID'] = $user->getUserID();
						// Hvis brugeren ikke indtaser rigtigt brugernavn og password:
						}else{
							echo "FORKERT LOGIN!";
						}
					}

					// Hvis brugeren klikker på log ud-knappen
					if ( isset($_GET["logout"]) ){
						// unset session loggedOn
						unset($_SESSION['loggedOn']);
						// destroy session
						session_destroy();
					}

					// hvis session er sat til loggedOn (hvis brugeren er logget ind)
					if ( isset( $_SESSION['loggedOn'] ) ) {
						
						// Hvis page ikke er sat, vis siden newPost
						if (!isset($_GET['page']) ) {
							$page = "newPost";
						// ellers vis den valgte side
						}else{
							$page = $_GET['page'];
						}
						
						// Hvis de ser siden newPost
						if ( $page == "newPost" ){		
				?>
				
							<!-- Vis link til at logge ud - hvis man klikker på linket, sæt logout=yes -->
							<a href='user_index.php?logout=yes'>Log ud</a>
																
							<h3>Skriv en nyhed:</h3>
							
							<!-- Formular til at skrive nyhed -->
							<form name="input" action="user_index.php" method="GET">
							<p class="formular">Titel:</p> <input type="text" name="postTitle" size="54">	
							<p class="formular">Tekst:</p> <textarea name="postText" rows="8" cols="40"></textarea>
							<!--Send skjult værdi, gå til page savePost -->
							<input type="hidden" name="page" value="savePost">
							<!--Send skjult værdi, hvilken user der er logget ind -->
							<input type="hidden" name="userID" value="<?php echo $_SESSION['userID']; ?>">
							<input type="submit" value="Gem">
							</form>
							
							<h3>Sidste nyt:</h3>													
				<?php
							// Gør brug af insertLatestPosts funktionen, som ligger post-klassen, 
							// til at indsætte de nyeste posts
							$post->insertLatestPosts();
				?>							
							<h3>Skriv en kalender post:</h3>
						
							<!-- Formular til at skrive kalender-post -->
							<form name="input" action="user_index.php" method="GET">
							<p class="formular">Dato:</p> <input type="text" name="calendarDate" size="54">
							<p class="formular">Tekst:</p> <textarea name="calendarText" rows="8" cols="40"></textarea>
							<!--Send skjult værdi, gå til page saveCalendar -->
							<input type="hidden" name="page" value="saveCalendar">
							<!--Send skjult værdi, hvilken user der er logget ind -->
							<input type="hidden" name="userID" value="<?php echo $_SESSION['userID']; ?>">
							<input type="submit" value="Save">
							</form>
							
							<h3>Kommende arrangementer:</h3>														
				<?php	
						// Gør brug af insertLatestPosts funktionen, som ligger calendar-klassen, 
						// til at indsætte de nyeste posts
						$calendar->insertLatestPosts();
													
						// Hvis brugeren gemmer en nyhed (page == savePost):
						}else if( $page == "savePost" ){
						
							// Tilføj nyhed og userID til databasen
							$post->savePost($_GET['postTitle'], $_GET['postText'], $_GET['userID']);						
							echo "Nyhed oprettet";
				?>
							<br />							
							<a href='user_index.php'>Tilbage</a>
				<?php
							// Hvis brugeren gemmer en kalender-post (page == saveCalendar):
						}else if( $page == "saveCalendar" ){
						
							// Tilføj kalender-post og userID til databasen
							$calendar->savePost($_GET['calendarDate'], $_GET['calendarText'], $_GET['userID']);
							
							echo "Kalender-post oprettet";
				?>						
							<br /> 
							<a href='user_index.php'>Tilbage</a>
				<?php
						// Hvis brugeren sletter en nyhed (page == deletePost):
						}else if( $page == "deletePost" ){
						
							// Sletter den nyhed, hvor man har tykket på slet-knappen (registreres via postID)
							$post->deletePost($_GET['postID']);							
							echo "Nyhed slettet";
				?>
							<br /> 
							<a href='user_index.php'>Tilbage</a>							
				<?php
						}
						
						// Hvis brugeren sletter en kalender-post (page == deleteCalendar):
						else if( $page == "deleteCalendar" ){
						
						
							// Sletter den kalender-post, hvor man har tykket på slet-knappen (registreres via calendarID)
							$calendar->deletePost($_GET['calendarID']);
							
							echo "Kalender slettet";
							?>
							<br /> 
							<a href='user_index.php'>Tilbage</a>
							<?php
						}
						
						// Hvis brugeren ikke er logget ind ELLER hvis brugeren lige er blevet logget ud:
					}else{
						// Vis login-form:
				?>
						<form name="input" action="user_index.php" method="GET">
						Brugernavn: <input type="text" name="brugernavn">
						Password: <input type="password" name="password">
						<input type="submit" value="Log ind">
						</form>
				<?php
					}
				?>
			</div> <!--splat slut-->

			<!-- Footer -->
			<div id="footer">
				<p>
					Gudrunsvej 78 │ 8210 Århus V │ Tlf: 8625 8780 │ cirkus@mail.dk │ <a href="user_index.php">Log ind</a>
					<a href="https://www.facebook.com/pages/Cirkus-Tv%C3%A6rs/555565437809990?fref=ts" target='_blank'><img id="facebook" class="ikon" src="billeder/facebook.png" alt="facebook"></a>
					<a href="http://www.youtube.com/user/cirkustvaers" target='_blank'><img class="ikon" src="billeder/youtube.png" alt="Youtube"></a>
					<a href="http://www.linkedin.com/profile/view?id=220105462&authType=NAME_SEARCH&authToken=PNZi&locale=da_DK&srchid=915479441369731501307&srchindex=12&srchtotal=12&trk=vsrp_people_res_name&trkInfo=VSRPsearchId%3A915479441369731501307%2CVSRPtargetId%3A220105462%2CVSRPcmpt%3Aprimary" target='_blank'><img class="ikon" src="billeder/linkedin.png" alt="Linkedin"></a>
				</p>
			</div> <!-- Footer slut -->

			<!--Menu-->
			<div id="menu">
				<div class="menu_fixed_lang">
					<div id="billet">
						<ul>
							<li><a href="tilbyder.php"><h3>Vi tilbyder</h3></a></li>
							<li><a href="kalender.php"><h3>Kalender</h3></a></li>
							<li><a href="galleri.php"><h3>Galleri</h3></a></li>
							<li><a href="os.php"><h3>Om os</h3></a></li>
							<li><a href="kontakt.php"><h3>Kontakt</h3></a></li>
						</ul>
					</div>
				</div>
			</div>
			
			<!--Logo-->
			<a href="index.php"><div class="logo_lang">
			</div></a> <!--logo slut-->

			<!--Pige-->
			<div id="pige_container">
				<div class="pige_fixed_lang">
					<div id="pige">
					</div> <!--pige slut-->
				</div> <!--pige_fixed slut-->
			</div> <!--pige_container slut-->
	 	</div> <!--ramme slut-->
		</div><!-- wrapper slut -->
	</body>
</html>
