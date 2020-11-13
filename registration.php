<?php
// I include the configuration php file below
require_once "configuration.php";

// Define the variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_error = $password_error = $confirm_password_error = "";

//Proceesing the form data when it becomes submitted
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Validate username
    if(empty(trim($_POST["username"])))
    {
        $username_error = "Please enter a username...";
    }
    else
    {
        // A select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if($statemnt = mysql_prepared($link, $sql))
        {
            // Bind the variables to the prepared statement as parameters
            mysql_statement_bind_parameters($statemnt, "s", $parameters_username);

            // setting the parameters
            $parameters_username = trim($_POST["username"]);

            // attempting to perform the prepared statement
            if(mysql_statement_perform($statemnt))
            {
                // to retain data (information)
                mysql_statement_retain_data($statemnt);

                if(mysql_statement_num_rows($statemnt) == 1)
                {
                    $username_error = "This username has already benn taken.";
                }
                else
                {
                    $username = trim($_POST["username"]);
                }
                else
                {
                    echo "Uh Oh. There was an error. Try again.";
                }
                mysql_statement_close($statemnt);
            }
        }
        // Validating the password
        if(empty(trim($_POST["password"])))
        {
            $password_error = "Enter a password please.";
        }
        elseif(strlen(trim($_POST["password"])) < 10)
        {
            $password_error = "Password must have at least 10 characters.";
        }
        else
        {
            $password = trim($_POST["password"]);
        }
        // Validating the confirmation password
        if(empty(trim($_POST["password_confirmation"])))
        {
            $password_confirmation_error = "Please confirm your password.";
        }
        else
        {
            $password_confirmation = trim($_POST["password_confirmation"]);
            if(empty($password_error) && ($password != $password_confirmation))
            {
                $password_confirmation_error = "Password did not match at all.";
            }
        }
        // checking the input error before inserting into the database
        if(empty($username_error) && empty($password_error) && empty($password_confirmation_error))
        {
            // insert statement
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            if($statemnt = mysql_prepared($link, $sql))
            {
                // binding the variables to the prepared statement as parameters
                mysql_statement_bind_parameters($statemnt, "ss", $parameters_username, $parameters_password);

                // setting the parameters
                $parameters_username = $username;
                $parameters_password = password_hash($password, PASSWORD_DEFAULT); //PASSWORD_DEFAULT creates password hashes

                // attempting to perform the prepared statement
                if(mysql_statement_perform($statemnt))
                {
                    // it redirects to the login page 
                    header("location: login.php");
                }
                else
                {
                    echo "Something went terribly wrong. Try again later.";
                }
                mysql_statement_close($statemnt);
            } 
        }
        mysql_close($link);
    }
    ?>
<!-------------------------------HTML------------------------------->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="style.css">
       body
       { 
           font: 14px sans-serif; 
       }
        .wrapper
        {
            width: 350px;
            padding: 20px;
        }
        </style>
        </head>
        <body>
            <div class="wrapper">
                <h2>Sign Up</h2>
                <p>Please fill this form to create an account.</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_error)) ? 'has error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_error; ?> </span>
    </div>
    <div class="form-group <?php echo (!empty($password_error)) ? 'has-error' : ''; ?>">
    <label>Password</label>
    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
    <span class="help-block"><?php echo $password_error; ?></span>
    </div>

    <div class="form-group <?php echo (!empty($password_confirmation_error)) ? 'has-error' : ''; ?>">
    <label>Confirm Password</label>
    <input type="password" name="confirm password" class="form-control" value="<?php echo $password_confirmation; ?>">
    <span class="help-block"><?php echo $password_confirmation_error; ?></span>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <input type="reset" class="btn btn-default" value="Reset">
    </div>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
    </div>
    </body>
    </html> 