<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title> Clarity Check Signup </title>
    <link href="css/layout.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <!-- Form and CSS styling copied from https://www.w3schools.com/howto/howto_css_login_form.asp -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="container">

            <!-- Validate username -->
            <label for="username"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="username" pattern="^[a-zA-Z0-9]*$"
                oninvalid="this.setCustomValidity('Username must only contain letters and numbers')"
                oninput="setCustomValidity('')" required>

            <!-- Mitigation: Client-side password validation -->
            <label for="password"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password"
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                oninvalid="this.setCustomValidity('Password must contain at least 8 characters including one uppercase and lowercase letter, one number, and one special character')"
                oninput="setCustomValidity('')" required>

            <button class="default-button" name="submit" type="submit">Create Account</button>
            <a href="index.php"> Already have an account? </a>
        </div>
    </form>
   
    <?php
    include("config/connection.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['submit'])) {
            $username = $_POST["username"];
            $password = $_POST["password"];

            $params_check_unique_username = array($username);
            $result = $con->execute_query("SELECT * FROM users WHERE username=?", $params_check_unique_username);

            if (mysqli_num_rows($result) > 0) {
                
                echo "<h3 style='color: red;'>Username already taken, Please try again with a different username</h3>";
           

            } else {
                // Mitigation: Server-side password validation (https://www.codexworld.com/how-to/validate-password-strength-in-php/)
                $uppercase = preg_match('@[A-Z]@', $password);
                $lowercase = preg_match('@[a-z]@', $password);
                $number = preg_match('@[0-9]@', $password);
                $specialChars = preg_match('@[^\w]@', $password);

                if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {

                    echo 'Password must contain at least 8 characters including one uppercase and lowercase letter, one number, and one special character';

                } else {
                    # Mitigation - Password hashing
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);



                    # Insecure code - Query vulnerbale to SQL Injection
                    # $sql = "INSERT INTO users VALUES('". $username . "', '" . $password . "');";
                    # $result = mysqli_query($con, $sql);
    
                    # Mitigation - Prepares, binds parameters, and executes SQL statement (https://www.php.net/manual/en/mysqli.execute-query.php)
                    $params = array($username, $hashed_password);
                    $query = "INSERT INTO users (username, password) VALUES(?, ?)";
                    $con->execute_query($query, $params);



                    session_start();
                    $_SESSION['login'] = true;
                    $_SESSION['username'] = $username;
                    header("Location: logged_in.php");
                }
            }


        }
    }
    ?>



</body>

</html>