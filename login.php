<?php
// Start a session
session_start();

// Check if the user is already logged in, if yes then redirect him to the dashboard
if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true)
{
    header("Location: dashboard.php");
    exit;
}

// Include config file
require_once 'config.php';
require_once 'head.php';
require_once 'validation.php';

// define variables and set to empty values
$email = $password = "";
$email_err = $password_err = "";

// Processing form data when form is submitted
if (isset($_POST['submitted']))
{
    $email = test_input($_POST["email"]);

    // Check if email is empty
    if (empty($_POST["email"])) {
        $email_err = "* Email is required";
    } else {
    $email = test_input($_POST["email"]);
    }
    
    // Check if password is empty
    if (empty($_POST["password"])) {
    $password_err = "* Password is required";
    } else {
    $password = md5(test_input($_POST["password"]));
    }

    // Check is the user details passes validation
    if(empty($email_err) && empty($password_err))
    {  

        // Prepare a select statement
        $sql = "SELECT * FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql))
        {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if($stmt->execute())
            { 
                // Check if email exists, if yes then verify password]
                if($stmt->rowCount() == 1)
                {
                    if($row = $stmt->fetch())
                    {
                        $id = $row["id"];
                        $first_name = $row["first_name"];
                        $last_name = $row["last_name"];
                        $email = $row["email"];
                        $hashed_password = $row["password"];
                        
                        if($password == $hashed_password)
                        {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedIn"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["first_name"] = $first_name;
                            $_SESSION["last_name"] = $last_name;
                            $_SESSION["email"] = $email;

                            // Redirect the user to dashboard
                            header("Location: dashboard.php");
                        } else{
                            // Password is invalid, display a generic error message
                            echo "<div class='container'><div class='row'><div class='col-xs-6'><div class='alert alert-danger'>Invalid email or password.</div></div></div></div>";
                        }
                    } 
                } else{
                    // Email doesn't exist, display a generic error message
                    echo "<div class='container'><div class='row'><div class='col-xs-6'><div class='alert alert-danger'>There Invalid email or password.</div></div></div></div>";
                }
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
                        <div class="page-header">
                            <h1>User login</h1>
                        </div>                  
                        <div class="clearfix">
                            <div class="pull-right">
                                <p><a href="index.php" role="button" class="btn btn-info">Home</a></p>
                            </div>
                        </div>  
                    </div>
                </div> 
                <div class="row"> 
                    <div class="col-xs-6">              
                        <form class="form-horizontal" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <div class="form-group">
                                <label for="email" class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Email">
                                    <span class="error text-danger"><?php echo $email_err;?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-sm-2 control-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                                    <span class="error text-danger"><?php echo $password_err;?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <a href="passwordreset.php" class="text-danger">Password reset</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary" name="submitted">Sign in</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>  
</html>