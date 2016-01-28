<?php
require 'database.php';

// $name should always contain logged in users name, unless it is first time user has visited the page
$name = filter_input(INPUT_COOKIE, 'name', FILTER_SANITIZE_STRING);

// Check to see if I'm being called from a POST request with 'name' being passed 
// in as a parameter. If I am, transfer the name into a persistent cookie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name'], $_POST['password'])) {
        // Won't be able to read the cookie until next time the page reloads, so update the global variable as well
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        
        $correctPasswordHash = getHashedPasswordForUser($db, $name);
        
        $passwordFromForm = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $hashedPasswordFromForm = hash('sha256', $passwordFromForm);
        
        if ($hashedPasswordFromForm != $correctPasswordHash) {
            logEvent($db, $name, 'Incorrect Password');
            print('<!DOCTYPE html><html><body><h1>Incorrect Password</h1><a href="' . ROOT_URL . 'login.php">Try again</a></body></html>');
            // Clear cookies
            // Set an error message that has a link back to this page to try again
            die();
        }
        
        // Successful login basically means carry on rendering page:
        //setcookie("name", $name, time() + 1800); // Half an hour for test purposes
        setcookie("name", $name, 2147483647);   // Max cookie age (19th Jan 2038)
        logEvent($db, $name, 'Successful login');
    }
}

if (is_null($name) || $name=='') {
    header("Location: " . ROOT_URL . "login.php");
    die();
}
include 'html_header.php';
?>
        <script>
            // Replaces body onload
            $(document).on("pageinit", "#main", pageLoad);

            // event occurs when user clicks either of the status buttons
            $(document).on('change', 'input[name=radio-choice-h-2]', function(e) {
                // Find the currently selected item
                var newStatus = $('input[name=radio-choice-h-2]:checked', '#radioForm').val();

                // Use ajax to submit the user and new status to the database
                $.post("action.php", "name=<?php print($name); ?>&status=" + newStatus,
                        function(data, status) {
                            //alert("Data: " + data + "\nStatus: " + status);
                            if (data == 'Success') {
                                // when ajax request returns, refresh this page
                                location.reload(true);
                            } else {
                                alert('return: ' + data);
                            }
                        });
            });

            function getUnixTimeStamp() {
                return Math.round(+new Date() / 1000);
            }
            function getPageLoadTimeStamp() {
                <?php getPageLoadTimeStamp($db) ?>
            }
            function checkDataRecent() {
                // If the difference between the cached server time and the current time from executing Javascript code
                // has more than 60 seconds between them, then show an error (we don't want old availability info shown to user)
                var timeDiff = Math.ceil(Math.abs(getUnixTimeStamp() - getPageLoadTimeStamp()) / 60);
                if (timeDiff > 5 && timeDiff < 55) {
                    alert("Warning: Data is " + timeDiff + " minutes old.");
                }
                if (timeDiff > 65) {
                    alert("Warning: Data is " + timeDiff + " minutes old.");
                }
                
                //var now = new Date;
                //var utc_timestamp = Date.UTC(now.getUTCFullYear(),now.getUTCMonth(), now.getUTCDate() , now.getUTCHours(), now.getUTCMinutes(), now.getUTCSeconds(), now.getUTCMilliseconds());
                //alert('getUnixTimeStamp: ' + getUnixTimeStamp() + '\ntoUTCString: ' + utc_timestamp + '\ngetTimezoneOffset: ' + new Date().getTimezoneOffset());
            }
            function pageLoad() {
                checkDataRecent();
                //getLocation();
            }
        </script>
        <style>
            .Available {
                color: #006400;
            }
            .Away {
                color: #E50000;
            }
            th {
                border-bottom: 1px solid #d6d6d6;
            }
            tr:nth-child(even) {
                background:#e9e9e9;
            }
            .ui-content {
                padding-left: 2px;
                padding-right: 2px;
            }
            .ui-table td {
                padding-left: 5px;
                padding-right: 5px;
            }
            /*
            .ui-table-columntoggle-btn {
                display: none;
            }
            */
            .ui-title {
                margin-right: 0px;
                margin-left: 0px;
            }
        </style>
    </head> 

    <body>
        <div id="main" data-role="page">

            <div data-role="header" data-position="fixed">
                <h1>RNLI Crewlist</h1>
            </div>

            <div data-role="main" class="ui-content">
                
                <?php if ($name != 'Guest') { ?>
                <form id="radioForm" action="index.php" method="GET">
                    <div data-role="fieldcontain">
                        <fieldset data-role="controlgroup" data-type="horizontal">
                            <legend><?php print($name) ?> is:</legend>
                            <?php
                            $stmt = $db->prepare('SELECT status FROM crew WHERE name = :name');
                            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                            $stmt->execute();
                            $row = $stmt->fetch();
                            if ($row['status'] == 'Available') {
                                ?>
                                <input name="radio-choice-h-2" id="radio-choice-h-2a" value="Available" checked="checked" type="radio">
                                <label for="radio-choice-h-2a">Available</label>
                                <input name="radio-choice-h-2" id="radio-choice-h-2b" value="Away" type="radio">
                                <label for="radio-choice-h-2b">Away</label>
                                <?php
                            } else {
                                ?>
                                <input name="radio-choice-h-2" id="radio-choice-h-2a" value="Available" type="radio">
                                <label for="radio-choice-h-2a">Available</label>
                                <input name="radio-choice-h-2" id="radio-choice-h-2b" value="Away" checked="checked" type="radio">
                                <label for="radio-choice-h-2b">Away</label>
                                <?php
                            }
                            ?>			
                        </fieldset>
                    </div>
                </form>
                <?php } ?>
                <?php displayNumCrewAvailable($db) ?>

                <table data-role="table" data-mode="columntoggle" class="ui-responsive" id="myTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th data-priority="1">Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php displayCrewStatusTableRows($db); ?>
                    </tbody>
                </table>
                <span style="font-size: 80%">
                    <?php displayLastUpdated($db) ?>
                </span>
                <a href="javascript:location.reload(true);" id="btnRefresh" class="ui-btn ui-corner-all ui-btn-inline ui-mini ui-icon-refresh ui-btn-icon-left">Refresh</a>
            </div>

            <div data-role="footer">
                <h1>&copy; Ralph Hughes 2014</h1>
            </div>
        </div>
    </body>
</html>
<?php
// Last bit of procedural PHP: Close database connection (optional)
$db = null;

function displayCrewStatusTableRows($db) {
    // Read status for all crew
    $stmt = $db->query('SELECT name, primaryRole, status FROM crew LEFT JOIN roles on crew.primaryRole = roles.role WHERE status IS NOT NULL ORDER BY status, roles.roleSortOrdinal, name');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output into table
    foreach ($result as $row) {
        print('<tr>');
        print('<td>' . $row['name'] . '</td>');
        print('<td>' . $row['primaryRole'] . '</td>');
        print('<td class="' . $row['status'] . '">' . $row['status'] . '</td>');
        print('</tr>');
    }
}

function displayNumCrewAvailable($db) {
    $stmt = $db->query("SELECT COUNT(name) as cnt FROM crew WHERE status = 'Available';");
    $stmt->execute();
    $row = $stmt->fetch();
    print ($row['cnt'] . ' crew available<br/>');
}

function getPageLoadTimeStamp($db) {
    // Get server side timestamp via database server
    $stmt = $db->query('SELECT UNIX_TIMESTAMP() as timestamp');
    $row = $stmt->fetch();

    // Write it to Javascript for client side retrieval
    print 'return ' . $row['timestamp'] . ';';
}

function displayLastUpdated($db) {
    $stmt = $db->query("select concat(dayname(now()), ' ', time(now())) as timestamp");
    $row = $stmt->fetch();
    print('Last updated: ' . $row['timestamp']);
}

function getHashedPasswordForUser($db, $name) {
    $stmt = $db->prepare('SELECT hash FROM crew WHERE name = :name');
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row['hash'];
}