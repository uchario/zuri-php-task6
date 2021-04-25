<?php
 
require_once "config.php";
require_once 'head.php';
require_once 'validation.php';

// define variables and set to empty values
$email = $password = '';
$email_err = $password_err = '';

if (isset($_POST['submitted']))
{
    $email = test_input($_POST["email"]);

    if (empty($_POST["email"])) {
        $email_err = "* Email is required";
    } else {
    
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql))
        {
        
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email);
            
            // Set parameters
            $param_email = $email;

            // Attempt to execute the prepared statement
            if($stmt->execute())
            {
                if(!$stmt->rowCount() == 1)
                {
                    $email_err = "This email doesn't exists.";
                } else{
                    $email = $email;
                }
            }
        } else{
            echo "<div class='container'><div class='row'><div class='col-xs-6'><div class='alert alert-danger'>Something went wrong. Please try again later.</div></div></div></div>";
        }

        // Close statement
        unset($stmt);
    };

    
    if (empty($_POST["password"])) {
        $password_err = "* Password is required";
    } else {
        $password = md5(test_input($_POST["password"]));
    }

    // Check is the user details passes validation
    if (empty($email_err) && empty($password_err))
    {
        // Prepare an update statement
        $sql = "UPDATE users SET password = :password WHERE email = :email";

        if($stmt = $pdo->prepare($sql))
        {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":password", $param_password);
            $stmt->bindParam(":email", $param_email);

            // Set parameters
            $param_password = $password;
            $param_email = $email;

            // Attempt to execute the prepared statement
            if($stmt->execute())
            {
                //Password updated successfully.
                echo "<div class='container'><div class='row'><div class='col-xs-6'><div class='alert alert-success'>Password changed successfully.</div></div></div></div>"; 
            } else{
                echo "<div class='container'><div class='row'><div class='col-xs-6'><div class='alert alert-danger'>Something went wrong. Please try again later.</div></div></div></div>"; 
            }

            // Close statement
            unset($stmt);
        }
    }

    // Close connection
    unset($pdo);
}
?>

<html>
    <body>
        <div class="container">
            <div class="jumbotron">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="page-header text-danger">
                            <h2>Password Reset</h2>
                        </div>
                        <form class="form-horizontal" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <div class="form-group">
                                <label for="email" class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Email">
                                    <span class="error text-danger"><?php echo $email_err;?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-sm-2 control-label">New password</label>
                                <div class="col-sm-10">
                                    <input type="password" name="password" class="form-control" id="password" placeholder="New password">
                                    <span class="error text-danger"><?php echo $password_err;?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-danger" name="submitted">Reset</button>
                                    <a href="login.php" role="button" class="btn btn-primary">Back</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>