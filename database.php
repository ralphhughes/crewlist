<?php
define("ROOT_URL", "http://ralphius.x10.mx/crewlist/");
define("SITE_TITLE", "RNLI Crewlist"); // Should be as short as possible for mobile screens
error_reporting(E_ALL);
try {
    // Connect to database (work settings)
    //$db = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', 'web_dev');
    // Connect to database (ralphius.x10.mx settings)
    $db = new PDO('mysql:host=127.0.0.1;dbname=ralphiu2_crewlist;charset=utf8', 'ralphiu2_webuser', '');
    
    
	// Connect to database (crewlist.co.uk settings)
    // $db = new PDO('mysql:host=127.0.0.1;dbname=crewstat_rnli_conwy;charset=utf8','crewstat_phpuser','');
} catch (Exception $e) {
    echo 'Error: ' .$e->getMessage();
    die();
}

// Set timezone for connection
date_default_timezone_set('Europe/London');
$bool = date('I'); // this will be 1 in DST or else 0
if ($bool == 1) {
    $sql = "set time_zone = '+01:00';";
    $db->exec($sql);
} else {
    $sql = "set time_zone = '+00:00';";
    $db->exec($sql);
}

function tableExists($dbh, $tablename) {
    $results = $dbh->query("SHOW TABLES LIKE '$tablename'");
    if (!$results) {
        die(print_r($dbh->errorInfo(), TRUE));
    }
    if ($results->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

function createTables($db) {
    print('First run, creating tables...');
    $createCrewSQL = "CREATE TABLE IF NOT EXISTS `crew` (
			`name` varchar(50) NOT NULL,
                        `hash` varchar(255) NULL,
                        `primaryRole` varchar(50) NULL,
			`status` varchar(25) DEFAULT NULL,
			PRIMARY KEY (`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $db->exec($createCrewSQL);
    
    $populateCrewTable = "INSERT INTO `crew` (`name`, `hash`, `primaryRole`, `status`) VALUES
                        ('Mr Test', '9e468662812f45d0558e113a4b263fe79a657a32ced55af2b02b50630e5533bc', NULL, NULL),
                        ('A N Other', '9e468662812f45d0558e113a4b263fe79a657a32ced55af2b02b50630e5533bc', NULL, NULL);";
    $db->exec($populateCrewTable);
            
    

    $createEventLogSQL = "CREATE TABLE IF NOT EXISTS `eventlog` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`eventTime` timestamp DEFAULT CURRENT_TIMESTAMP,
                        `ip` varchar(45) DEFAULT NULL,
			`name` varchar(50) DEFAULT NULL,
			`newValue` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $db->exec($createEventLogSQL);
    
    $createRolesTableSQL = "CREATE TABLE IF NOT EXISTS `roles` (
                        `role` varchar(50),
                        `roleSortOrdinal` int(11) NOT NULL,
                        PRIMARY KEY (`role`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $db->exec($createRolesTableSQL);
    
    $populateRolesTableSQL = "INSERT INTO `roles` (`role`, `roleSortOrdinal`) VALUES
                        ('Coxswain', '1'),
                        ('Mechanic', '2'),
                        ('Helm', '3'),
                        ('Crew', '4');";
     
}

function currentURL() {
     $pageURL = 'http';
     ($_SERVER["SERVER_PORT"] === 443) ? $pageURL .= "s" : '';
     $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }
     return $pageURL;
 }

function logEvent($db, $name, $description) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $sql = "INSERT INTO eventlog (eventTime, ip, name, newValue) VALUES (now(), :ip, :name, :newValue)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':newValue', $description, PDO::PARAM_STR);
    $stmt->execute();
}