<?php 

class User {

	// definér properties
	private $db;
	private $userID;
	private $username;
	private $password;

	// forbind til databasen
	public function setDatabaseConnection($db) {
		$this->db = $db;
	}

	// Find en brugers navn baseret på dennes ID-nummer:
	public function getNameFromID($userID) {

		// gå ind i databasen, og vælg fra User-tabellen det userID, der svarer til userID'et på
		// den bruger, der er logget ind 
		$sql = "SELECT * FROM  `User` WHERE  `userID` = ".$userID.";";		
    	$this->db->query($sql);
		// hvis der bliver returneret nogle resultater:
		if($this->db->countRows()>0) {
			// tag resultaterne og læg dem ind i variablen $results (disse lagres automatisk som en array)
			$results = $this->db->loadRows();
			
			//echo resultatet i array-nummer 0 (der bør kun være et resultat, derfor nummer 0)
			echo $results[0]['username'];

		}else{
		
			// Intet brugernavn kan findes til dette ID
			echo "N/A";
		}
	}

	// Set brugerens variabler vha. deres brugernavn og password:
	public function setUser($userName,$userPassword) {
		// Gå ind i databasen, og vælg fra User-tabellen de resultater hvor brugernavn OG password svarer
		// til det, brugeren har indtastet
		$sql = "SELECT * FROM  `User` WHERE  `username` = '".$userName."' AND `password` = '".$userPassword."';";
    	$this->db->query($sql);
		// hvis der bliver returneret nogle resultater:
		if($this->db->countRows()>0) {	
			// tag resultaterne og læg dem ind i variablen $results
			$results = $this->db->loadRows();
			
			// set userID
			$this->userID = $results[0]['userID'];
			// set username
			$this->username = $results[0]['username'];
			// set password
			$this->password = $results[0]['password'];

		}
	}

	// hent nuværende brugers ID-nummer:
	public function getUserID() {
		return $this->userID;
	}

	// hent nuværende brugers navn:
	public function getUsername() {
		return $this->username;
	}

	// hent nuværende brugers password:
	public function getPassword() {
		return $this->password;
	}

	// Test om en bruger findes i databasen - bruges til at logge ind med
	public function testLogin($userName, $userPassword) {
		// Gå ind i databasen, og vælg fra User-tabellen de resultater hvor brugernavn OG password svarer
		// til det, brugeren har indtastet
		$sql = "SELECT * FROM  `User` WHERE  `username` = '".$userName."' AND `password` = '".$userPassword."';";
    	$this->db->query($sql);
		// hvis der bliver returneret nogle resultater:
		if($this->db->countRows()>0) {	
			
			//returner resultatet TRUE
			return TRUE;


		// Ellers returner resultatet FALSE
		}else{
			return FALSE;
		}
	}
}
?>
