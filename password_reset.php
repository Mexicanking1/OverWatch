<?php
session_start();

if(!is_set($_SESSION["logged in"]) || $_SESSION["logged in"] !== true)
{
    header("Location: login.php");
    exit;
}

require_once "configuration.php";
$new_password = $password_confirmation = "";
$new_password_error = $password_confirmation_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if(empty(trim($_POST["new_password"])))
    {
        $new_password_error = "Please enter a new password.";
    }
    elseif(strlen(trim($_POST["new_password"])) < 10)
    {
        $new_password = trim($_POST["new_password"]);
    }
    //substaniate password confirmation
    if(empty(trim($_POST["password_confirmation"])))
    {
        $password_confirmation_error = "Please confirm the password.";
    }
    else
    {
        $password_confirmation = trim($_POST["password_confirmation"]);
        if(empty($new_password_error) && ($new_password != $password_confirmation))
        {
            $password_confirmation_error = "This password didn't match.";
        }
    }
    if(empty($new_password_error) && empty($password_confirmation_error))
    {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        if($statement = mysql_prepared($link, $sql))
        {
            mysql_statement_bind_parameters($statement, "si", $parameters_password, $parameters_id);
            $parameters_password = password_hash($new_password, PASSWORD_DEFAULT);
            $parameters_id = $_SESSION["id"];

            if(mysql_statement_perform($statement))
            {
                session_destroy(); //the password update is successful. it destroys the seesion and avert to the login page
                header("location: login.php");
                exit();
            }
            else
            {
                echo "Alas! Something went awry. Please try again in 5 minutes.";
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
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="style.css">
        body
        {
             font: 14px sans-serif; 
        }
        .wrapper
        {
             width: 350px; padding: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group <?php echo (!empty($new_password_error)) ? 'has-error' : ''; ?>">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_error; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_confirmation_error)) ? 'has-error' : ''; ?>">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
            <span class="help-block"><?php echo $password_confirmation_error; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <a class="btn btn-link" href="greeting.php">Cancel</a>
    </div>
    </form>
    </div>
    </body>
    </html>