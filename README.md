# Crewlist
PHP application for monitoring the availability of a team. Responsive, cross-platform, mobile optimised web front end.

## Installation

Download entire contents of app to your server.


## Configuration 

Edit the file /database.php 
Fill in the following lines:
`define("ROOT_URL", "http://crewlist.co.uk");`
`define("SITE_TITLE", "Crewlist");`


Put in the host, username and password of your MySQL database in the following line:
`$db = new PDO('mysql:host=localhost;dbname=MyDatabase;charset=utf8', 'myUsername','myPassword');`
