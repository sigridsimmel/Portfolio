<?php

class Calendar {

	// definér properties
	private $id;
	private $db;	


	// forbind til databasen
	public function setDatabaseConnection($db) {
		$this->db = $db;
	}


 	// Tilføjer en kalender-post til databasen:
	public function savePost($calendarDate, $calendarText, $userID) {
			// gå ind i databasen, og indsæt i Calendar-tabellen de værdier brugeren har indtastet 
			// samt hvilket userID der har indtastet det
			$sql = "INSERT INTO `Calendar` (`calendarID` , `calendarText` ,`calendarDate` , `userID`) VALUES (NULL ,  '".$calendarText."',  '".$calendarDate."', '".$userID."');";
			$this->db->query($sql);
	}


	// Sletter en kalender-post fra databasen:
	public function deletePost($calendarID) {
			// Gå ind i databasen og slet den nyhed, der har det valgte calendarID tilknyttet
			$sql = "DELETE FROM `Calendar` WHERE `Calendar`.`calendarID` = ".$calendarID;
			$this->db->query($sql);
	}


	//  Insætter de seneste kalender-posts:
	public function insertLatestPosts() {
		// Gå ind i databasen og vælg fra Calendar-tabellen de fem seneste kalender-posts
		$sql = "SELECT * FROM `Calendar` ORDER BY `calendarDate` ASC LIMIT 0, 5 ";		
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
					// Vis resultatet fra den række der indeholder calendarDate
					echo $row['calendarDate'];
				?>
					</h3>
				<?php 
					// Vis resultatet fra den række der indeholder calendarText
					echo $row['calendarText'];
				?>
					<p class="skrevet">
					skrevet af 
				<?php 
					// Brug funktionen getNameFromID, til at finde brugernavnet ud fra userID
					$user->getNameFromID($row['userID']);

					// Hvis brugeren er logget ind vises slet-knappen
				 	if ( isset( $_SESSION['loggedOn'] ) ) {
				 		// Vis link, når man klikker på linket sendes man til filen user_index.php og siden 
				 		// deleteCalendar (page == deleteCalendar). 
				 		// Der registreres hvilket calendarID der er tilknyttet den slettede kalender-post
				 		echo "<a href=\"user_index.php?page=deleteCalendar&calendarID=".$row['calendarID']."\"> slet</a><br />";
				 	}
				?>
				 	
				 	</p></div>

				<?php
			}
		
		}

	}
	
}


?>
