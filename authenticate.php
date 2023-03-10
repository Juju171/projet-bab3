<?php
session_start();
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'projetbab3');
 
/* Attempt to connect to MySQL database */
$con = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['surname'], $_POST['psw']) ) {
	// Could not get the data that should have been sent.
	exit('Please fill both the surname and password fields!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT ID, psw FROM tableau WHERE surname = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the ID is a int so we use "i"
	$stmt->bind_param('s', $_POST['surname']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($ID, $psw);
        $stmt->fetch();
        // Account exists, now we verify the password.
        if ($_POST['psw'] === $psw) {
            // Verification success! User has logged-in!
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['ID'] = $ID;
            header('Location: site.php');
        } else {
            // Incorrect password
            echo 'Incorrect surname and/or password!';
        }
    } else {
        // Incorrect username
        echo 'Incorrect surname and/or password!';
    }


	$stmt->close();
}
?>