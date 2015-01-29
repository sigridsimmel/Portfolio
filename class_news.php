<?php

class Post {
	
	// definér properties
	private $id;	
	private $db;	
 

	// forbind til databasen
	public function setDatabaseConnection($db) {
		$this->db = $db;
	}


 	// Tilføjer en nyhed til databasen:
	public function savePost($postTitle, $postText, $userID) {
			// gå ind i databasen, og indsæt i Post-tabellen de værdier brugeren har indtastet 
			// samt hvilket userID der har indtastet det
			$sql = "INSERT INTO `Post` (`postID`, `postTitle`, `postText`, `postDate`, `userID`) VALUES (NULL, '".$postTitle."', '".$postText."', CURRENT_TIMESTAMP, '".$userID."');";
			$this->db->query($sql);
	}


	// Sletter en nyhed fra databasen:
	public function deletePost($postID) {
			// Gå ind i databasen og slet den nyhed, der har det valgte postID tilknyttet
			$sql = "DELETE FROM `Post` WHERE `Post`.`postID` = ".$postID;
			$this->db->query($sql);
	}


	//  Insætter de seneste nyheder:
	public function insertLatestPosts() {
		// Gå ind i databasen og vælg fra Post-tabellen de tre seneste nyheder
		$sql = "SELECT * FROM `Post` ORDER BY `postID` DESC LIMIT 0, 3 ";		
    	$this->db->query($sql);
		// hvis der bliver returneret nogle resultater:
		if($this->db->countRows()>0) {
			// tag resultaterne og læg dem ind i variablen $results
			$results = $this->db->loadRows();

			// lav en instance af User-klassen
			$user = new User;
			// brug den allerede eksisterende database-forbindelse
			$user->setDatabaseConnection($this->db);

			
			// For hvert resultat, lav en række ($row)
			foreach ($results as $row) {
				?>
					<div class="post"><h3>
				<?php 
					// Vis resultatet fra den række der indeholder postTitle
					echo $row['postTitle'];
				?>
					</h3>
				<?php 
					// Vis resultatet fra den række der indeholder postText
					echo $row['postText'];
				?>
					<p class="skrevet">
					skrevet 
				<?php 
					// Vis timestamp 
					echo $row['postDate'];
				?> 
					af 
				<?php 
					// Brug funktionen getNameFromID, til at finde brugernavnet ud fra userID
					$user->getNameFromID($row['userID']);

				 	// Hvis brugeren er logget ind vises slet-knappen
				 	if ( isset( $_SESSION['loggedOn'] ) ) {
				 		// Vis link, når man klikker på linket sendes man til filen user_index.php og siden 
				 		// deletePost (page == deletePost). 
				 		// Der registreres hvilket postID der er tilknyttet den slettede nyhed
				 		?>
				 		<form name="slet" action="user_index.php" method="GET">
						<input type=hidden name="page" value="deletePost">
						<input type=hidden name="postID" value="<?php echo $row['postID']; ?>">
						<input type="submit" value="Slet">
						</form>
						
						<?php
				 	}
				?>
				 	
				 	</p></div>

				<?php
			}
		
		}

	}
	
}


?>
