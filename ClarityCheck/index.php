
<?php
// Imports the mysql database connection settings then checks for the existence of the users table and user_prescriptions table. 
// If they do not exist, they are created.
session_destroy();
include("config/connection.php");

$table_users = $con->query("SHOW TABLES LIKE 'users'");


if ($table_users->num_rows <= 0) {

    $sql_users = "CREATE TABLE users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        password TEXT NOT NULL,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $con->query($sql_users);
    $table_users->free();
} 
$table_user_prescriptions = $con->query("SHOW TABLES LIKE 'user_prescriptions'");
if ($table_user_prescriptions->num_rows <= 0) {
    $sql_user_prescriptions = "CREATE TABLE user_prescriptions (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        data TEXT NOT NULL,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $con->query($sql_user_prescriptions);
    $table_user_prescriptions->free();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['logout.php'])){
    session_destroy();
   

}
}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title> Clarity Check Login </title>
    <link href="css/layout.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <!-- Form and CSS styling copied from https://www.w3schools.com/howto/howto_css_login_form.asp -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="container">
            <label for="username"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="username" required>

            <!-- Mitigation: Type password hides passwords from being shown in clear text and prevents shoulder surfing -->
            <label for="password"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password" required>

            <button class="default-button" name="submit" type="submit">Login</button>
            <a href="signup.php"> Don't have an account? </a>
        </div>
    </form>

    <?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['submit'])) {

            $username = $_POST["username"];
            $password = $_POST["password"];

            // Insecure code - Query vulnerbale to SQL Injection
            //$sql = "SELECT * FROM users WHERE username='". $username . "' AND password='" . $password . "';";
            //$result = mysqli_query($con, $sql);
    
            // Mitigation: Prepared statement
            $params = array($username);
            $result = $con->execute_query("SELECT * FROM users WHERE username=?", $params);

            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
                $hashed_password = $user_data["password"];

                if (password_verify($password, $hashed_password)) {

                    session_start();
                    $_SESSION['login'] = true;
                    $_SESSION['username'] = $username;
                    // header("Location: logged_in.php");
                    header("Location: logged_in.php");
                    
                } else {
                    echo "<h3 style='color: red;'>Password is Invalid</h3>";
                }

            } else {
                echo "<h3 style='color: red;'>Username or Password is invalid</h3>";
            }
        }

    }
    ?>

</body>

</html>