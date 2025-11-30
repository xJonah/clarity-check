<?php
// Imports the mysql database connection settings then checks for the existence of the users table and user_prescriptions table. 
// If they do not exist, they are created.
session_start();
include("config/connection.php");



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
    <form action="index.php" method="POST">
        <button type="submit" name="logout.php" class="round-button-top-right">LogOut</button>
    </form>


    <!-- Form and CSS styling copied from https://www.w3schools.com/howto/howto_css_login_form.asp -->


    <?php
    if ($_SESSION['login'] === true) {
        echo "<h1> Welcome: " . $_SESSION['username'] . "</h1>";
        $html =
            "
        
    
        <a href='survey.php'><h3>Take A New Eye Prescription Survey</h3></a>
        
        "

        ;
        echo $html;
        // Insecure code - Query vulnerbale to SQL Injection
        //$sql = "SELECT * FROM users WHERE username='". $username."';
        //$result = mysqli_query($con, $sql);
    
        // Mitigation: Prepared statement
        $username = $_SESSION['username'];
        $params = array($username);
        $result = $con->execute_query("SELECT data FROM user_prescriptions WHERE username=?", $params);

        if ($result->num_rows > 0) {
            echo "<h3> Your Previous Survey Results: </h3>";
            
            while ($row["data"] = $result->fetch_assoc()) {
            
                $code_usable_array = var_export($row["data"], true);
                $dataArray = explode(",", $code_usable_array);
        

                $value1 = $dataArray[0];
                $value2 = $dataArray[1];
                $value3 = $dataArray[2];
                $value4 = $dataArray[3];
                $value5 = $dataArray[4];

                // fixing unwanted final values formatting
                $new_value1 = explode("=> '", $value1);
                $new_value5 = trim($value5, "'");
                
                // secure against this xss attack
                $stored_xss_attack = "<script type='text/javascript' >
                            alert('Your Are Hacked!!');
                            </script>";
                $html = 
                "
                 
                 
                 Age: " . $new_value1[1] . ",
                 Do you feel any irritation in your eyes?: " . $value2 . ",
                 Do you currently wear glasses?: " . $value3 . ",
                 Right Eye: +" . $value4 . ",
                 Left Eye: +" . $new_value5 . "
               
                ";
                // code vulnerable to Stored XSS attack, when moving the variable $stored_xss_attack inside the  $html variable, the script is executed
                // echo $html;
                // MITIGATION, htmlspecialchars() function will not execute the script , instead will just print it out as a string
                echo '<h3><br>' .htmlspecialchars($html, ENT_QUOTES, 'UTF-8'). '</h3>';
            }

            // $user_data_array = explode(",",$user_data["data"]);
            // var_dump($user_data_array);
            // $html =
            //     "
            //     <h3> Your Previous Survey Results: </h3>
            //     <h4> Age: " . $user_data['data'] . "</h4>
            //     <h4> Do you feel any irritation in your eyes?: " . $user_data['irritation'] . "</h4>
            //     <h4> Do you currently wear glasses?: " . $user_data['glasses'] . "</h4>
    
            //     "
            // ;
            // echo $html;
    
        } else {
            echo "<h3> You have not taken a survey yet. </h3>";
        }




    } else {
        header("Location: index.php");
    }


    ?>



</body>

</html>