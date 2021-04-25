<?php
// Start the session
session_start();

// Check if the user is already logged in, if yes then redirect him to the dashboard
if(!isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] !== true)
{
    header("Location: login.php");
    exit;
}

// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Include config file
    require_once "config.php";
    require_once "head.php";
    
    // Prepare a select statement
    $sql = "SELECT * FROM courses WHERE id = :id";
    
    if($stmt = $pdo->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Retrieve individual field value
                $name = $row["name"];
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: error.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
            
        }
    }
     
    // Close statement
    unset($stmt);
    
    // Close connection
    unset($pdo);
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="page-header">
                    <h1>View course</h1>
                </div>
                <div class="form-group">
                    <label>Course name</label>
                    <p class="form-control-static"><?php echo $row["name"]; ?></p>
                </div>
                <p><a href="dashboard.php" class="btn btn-danger">Back</a></p>
            </div>
        </div>        
    </div>
</body>