<?php
// Start a session
session_start();

// Check if the user is already logged in, if yes then redirect him to the dashboard
if(!isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] !== true)
{
    header("Location: login.php");
    exit;
}

require_once "config.php";
require_once "validation.php";
require_once "head.php";

$course_name = $course_name_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    if (empty($_POST["course_name"])) {
      $course_name_err = "* course name is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $course_name)) {
      $course_name_err = "Only letters and white space allowed";
    }
    else {
        $course_name = test_input($_POST["course_name"]);
        
        // Prepare a select statement
        $sql = "SELECT id FROM courses WHERE name = :name AND user_id = :user_id";
        
        if($stmt = $pdo->prepare($sql))
        {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":name", $param_name);
            $stmt->bindParam(":user_id", $param_user_id);

            // Set parameters
            $param_name = $course_name;
            $param_user_id = $_SESSION['id'];

            // Attempt to execute the prepared statement
            if($stmt->execute())
            {
                if($stmt->rowCount() == 1)
                {
                $course_name_err = "This course already exists.";
                } else{
                $course_name = $course_name;
                }
            }
            } else{
            echo "<div class='container'><div class='row'><div class='col-xs-6'><div class='alert alert-danger'>Something went wrong. Please try again later.</div></div></div></div>";
            }

            // Close statement
            unset($stmt);
        }

    // Check input errors before inserting in database
    if(empty($course_name_err))
    {
        // Prepare an insert statement
        $sql = "INSERT INTO courses (name, user_id) VALUES (:name, :user_id)";

        if($stmt = $pdo->prepare($sql))
        {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":name", $param_course_name);
        $stmt->bindParam(":user_id", $param_user_id);
        
        // Set parameters
        $param_course_name = $course_name;
        $para_user_id = $_SESSION["id"];
    
        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Records created successfully. Redirect to landing page
            echo "<div class='container'><div class='row'><div class='col-xs-6'><div class='alert alert-success'>Course created successfully.</div></div></div></div>";
            header("location: dashboard.php");
            exit();
        } else{
            echo "<div class='container'><div class='row'><div class='col-xs-6'><div class='alert alert-danger'>Something went wrong. Please try again later.</div></div></div></div>";
        }
    }
        
    // Close statement
    unset($stmt);
        }

    // Close connection
    unset($pdo);
}

?>

</html>
    <body>
    <div class="container">
        <div class="jumbotron">
            <div class="row">
                <div class="col-xs-6">
                <div class="page-header">
                    <h1>Create course</h1>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                    <div class="form-group">
                        <label for="first_name">Course name</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" placeholder="Course name">
                        <span class="error text-danger"><?php echo $course_name_err;?></span>
                    </div>
                    <button type="submit" name="submit" class="btn btn-success">Add</button>
                    <a href="dashboard.php" role="button" class="btn btn-danger">Back</a>
                </form>
                </div>
            </div>
        </div>
    </div>
    </body>