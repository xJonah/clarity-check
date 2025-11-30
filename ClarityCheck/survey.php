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
   


   

    <!-- Form and CSS styling copied from https://www.w3schools.com/howto/howto_css_login_form.asp -->


    <?php
    if ($_SESSION['login'] === true) {
        echo "<h1>Please Complete the below Eye Prescription Survey</h1>";
        $html =
            "
            <form action='index.php' method='POST'>
            <button type='submit' name='logout.php' class='round-button-top-right'>LogOut</button>
            </form>
            <form method='POST' action=''>


            <label for='age'>Your Age (between 0 to 100):</label>
            <input type='number' name='age' required><br>
    
    
    
            <br>
            <label for='irritation'>Do You feel any irritation in your eyes?</label>
            <input type='radio' name='irritation' value='Yes' required>Yes
            <input type='radio' name='irritation' value='No'>No<br>
            <br>
    
            <label required for='glasses'>Do you currently wear glasses?</label>
            <input type='radio' name='glasses' value='Yes' required>Yes
            <input type='radio' name='glasses' value='No'>No<br>
    
            <img src='eye-test.png' alt='eye-test-image'/>
    
            <h4> Without reading glasses, position yourself 16 inches/ 40 cm away from the computer screen or smartphone.
            </h4>
            <h4>Test each eye separately by covering the eye not being tested.</h4>
            <h4>Choose the reading glasses strength Lens Power next to the lowest (smallest) letters you can read easily for
            each eye. Fill the data in the inputs below</h4>
          
            <label for='right-eye'>Your Right Eye Value (between 0 to 5):</label>
            <input type='float' name='right-eye' required><br>
            <br>
    
            <label for='left-eye'>Your Left Eye Value (between 0 to 5):</label>
            <input type='float' name='left-eye' required><br>
    
    
    
    
    
            <button class='default-button' name='submit' type='submit'>Submit</button>
        </form>
    
    "

        ;
        echo $html;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['submit'])) {
            

                $username = $_SESSION["username"];

                // do some server side validation before inserting into database
                $age = $_POST["age"];
                $irritation = $_POST["irritation"];
                $glasses = $_POST["glasses"];
                $right_eye = $_POST["right-eye"];
                $left_eye = $_POST["left-eye"];
                
                if (!filter_var($age, FILTER_VALIDATE_INT, ["options" => ["max_range" <= 100],["min_range" => 1]]) !== false) {
                    echo "<h1 style='color: red'>Invalid age range, must be between 0 to 100</h1>";
                    exit(); 
                } 
                if ($irritation !== "Yes" && $irritation !== "No") {
                    echo "<h1 style='color: red'>Invalid irritation value, must be Yes or No</h1>";
                    exit(); 
                }
                if ($glasses !== "Yes" && $glasses !== "No") {
                    echo "<h1 style='color: red'>Invalid glasses value, must be Yes or No</h1>";
                    exit(); 
                }
                if (!filter_var($right_eye, FILTER_VALIDATE_FLOAT, ["options" => ["max_range" => 5],["min_range" > 0]]) !== false) {
                    echo "<h1 style='color: red'>Invalid right eye range value, must be below 5 and above 0</h1>";
                    exit(); 
                }
                if (!filter_var($left_eye, FILTER_VALIDATE_FLOAT, ["options" => ["max_range" => 5],["min_range" > 0],["decimal" < 2]]) !== false) {
                    echo "<h1 style='color: red'>Invalid left eye range value, must be below 5 and above 0</h1>";
                    exit(); 
                }
                
                $left_eye = number_format($left_eye, 2);
                $right_eye = number_format($right_eye, 2);

                
                $data_array = array($age, $irritation, $glasses, $right_eye, $left_eye);
                
              
 
               

                $data = implode(",",$data_array);

           
                # Insecure code - Query vulnerable to SQL Injection
                # $sql = "INSERT INTO user_prescriptions VALUES('". $username . "', '" . $data . "');";
                # $result = mysqli_query($con, $sql);
        
                // Mitigation: Prepared statement
                
                $query = "INSERT INTO user_prescriptions (username, data) VALUES(?, ?)";
                $con->execute_query($query,[$username,$data]);
                
                header("Location: logged_in.php");
            }
    
        }

    } else {
        header("Location: index.php");
    }


    ?>



</body>

</html>