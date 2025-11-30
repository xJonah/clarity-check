<?php

# Mitigation - Hide MySQL Connection details in seperate directory/file and restrict access through .htaccess file
# Reference 4.0 & 4.2 - https://www.baeldung.com/linux/apache-restrict-specific-directory#:~:text=Password%2Dprotect%20a%20Directory&text=We%20can%20configure%20the%20Apache2,restrict%20access%20to%20any%20directory.&text=If%20it's%20the%20first%20time,a%20password%20for%20the%20user.


# Connect to database

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "SuperStrongPassword^123?!?!";
$dbname = "claritycheck";

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}

?>
