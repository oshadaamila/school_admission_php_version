<?php
// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
        echo "<script type='text/javascript'>alert('Enter a username');window.location='../views/admin/add_new_admin.php';</script>";;
    } else{
        // Prepare a select statement
        $sql = "SELECT username FROM users WHERE username = ?";

        if($stmt = $db->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();

                if($stmt->num_rows == 1){
                    $username_err = "This username is already taken.";
                    echo "<script type='text/javascript'>alert('Username already taken');window.location='../views/admin/add_new_admin.php';</script>";;
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }

    // Validate password
    if(empty(trim($_POST['password']))){
        $password_err = "Please enter a password.";
        echo "<script type='text/javascript'>alert('Enter a password');window.location='../views/admin/add_new_admin.php';</script>";;
    } elseif(strlen(trim($_POST['password'])) < 6){
        $password_err = "Password must have atleast 6 characters.";
        echo "<script type='text/javascript'>alert('Password must have atleast 6 charactors');window.location='../views/admin/add_new_admin.php';</script>";;
    } else{
        $password = trim($_POST['password']);
    }

    // Validate confirm password
    if(empty(trim($_POST["repassword"]))){
        $confirm_password_err = 'Please confirm password.';
        echo "<script type='text/javascript'>alert('Please enter password again');window.location='../views/admin/add_new_admin.php';</script>";;
    } else{
        $confirm_password = trim($_POST['repassword']);
        if($password != $confirm_password){
            $confirm_password_err = 'Password did not match.';
            echo "<script type='text/javascript'>alert('Passwords did not match');window.location='../views/admin/add_new_admin.php';</script>";;
        }
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password,role) VALUES (?, ?,'admin')";

        if($stmt = $db->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $param_username, $param_password);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location:../views/login_view.php");
            } else{
                echo "<script type='text/javascript'>alert('Error');window.location='../views/admin/add_new_admin.php';</script>";;
            }
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $db->close();
}
?>