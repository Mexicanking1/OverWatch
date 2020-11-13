<?php
session_start();

if(is_set($_SESSION["logged in"]) && $_SESSION["logged in"] === true)
{
    header("location: welcome.php");
    exit;
}

require_once "configuration.php";

// defining the variables and initialize with empty values
$username = $password = "";
$username_error = $password_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if(empty(trim($_POST["username"])))
    {
        $username_error = "Please enter a username.";
    }
    else
    {
        $username = trim($_POST["username"]);
    }
    // it checks if the password field is empty
    if(empty(trim($_POST["password"])))
    {
        $password_error = "Please enter a password.";
    }
    else
    {
        $password = trim($_POST["password"]);
    }
    if(empty($username_error) && empty($password_error))
    {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        if($statement = mysql_prepared($link, $sql))
        {
            mysql_statement_bind_parameters($statement, "s", $parameters_username);

            $parameters_username = $username;

            if(mysql_statement_perform($statement))
            {
                mysql_statement_retain_data($statement);
                //checking if the username exists, if approve then do a password verification
                if(mysql_statement_num_rows($statement) == 1)
                {
                    mysql_statement_bind_result($statement, $id, $username, $password_hashed);
                    if(mysql_statement_get($statement))
                    {
                        if(password_verify($password, $password_hashed))
                        {
                            session_start();
                            $_SESSION["logged in"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            
                            //redirect the user to my greeting page
                            header("location.greeting.php");
                        }
                        else
                        {
                            //shows an error meesage box if the password in invalid
                            $password_error = "The password you entered was invalid.";
                        }
                    }
                }
                else
                {
                    //shows an error message box if the username does not exist
                    $username_error = "There is no account established with that username.";
                }
            }
            else
            {
                echo "Oops!!! Something went wrong. Try again in 5 minutes...";
            }
            mysql_statement_close($statement);
        }
    }
    mysql_close($link);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="style.css">
        body
            {
                font: 14px sans-serif;
                text align: center;
            }
            </style>
            </head>
        <body>
            <div class="page-header">
                <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]);
                ?></b>. Welcome to my website.</h1>
                </div>
                <p>
                    <a href="password_reset.php" class="btn btn-warning">Reset Your Password</a>
                    <a href="logout.php" class="btn btn-danger">Sign Out of The Account</a>
        </p>
        </body>
        </html>