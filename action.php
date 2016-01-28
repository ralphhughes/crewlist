<?php

require 'database.php';

// Handles status changes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name']) && isset($_POST['status'])) {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

        if (is_null($name) || $name=='') {
            print('Error: name: ' . $name . ', status: ' . $status);
            die();
        } else {
            
            // Update crew table
            $sql = "UPDATE crew SET status = :status WHERE name = :name;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();

            // Add entry to eventlog
            logEvent($db, $name, $status);

            print('Success');
            die();
        }
        
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handles action.php?logout
    if (isset($_GET['logout'])) {
        logEvent($db, $_COOKIE['name'], 'Logged out');
        unset($_COOKIE['name']);
        setcookie('name', '', time() - 3600); // empty value and old timestamp
        print('Logged out.');
        die();
    }
    // Handles action.php?debug
    if(isset($_GET['debug'])) {
        $name = filter_input(INPUT_COOKIE, 'name', FILTER_SANITIZE_STRING);
        if ($name=='Ralph Hughes') {
            // Other debug:
            // select name, ip, count(ip) from eventlog group by name, ip ORDER BY count(ip) ASC 
            // select name, sum(time_to_sec(eventTime)) from eventlog where newValue='Available' group by name order by name
            $stmt = $db->query('SELECT eventTime, ip, name, newValue FROM eventlog ORDER BY eventTime DESC LIMIT 250');
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$results) {
                die();
            }
            
            print('<html><body><table>');

            // Output into table
            foreach ($results as $row) {
                print('<tr>');
                print('<td>' . $row['eventTime'] . '</td>');
                print('<td>' . $row['ip'] . '</td>');
                print('<td>' . $row['name'] . '</td>');
                print('<td>' . $row['newValue'] . '</td>');
                print('</tr>');
            }
            print('</table></body></html>');
            die();
        }
    }
}