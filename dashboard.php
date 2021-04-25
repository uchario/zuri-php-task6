<?php
// Start a session
session_start();

// Check if the user is logged in, otherwise redirect to login.php
if (! isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true)
{
    header('Location: login.php');
}
 
require_once "head.php";

?>

<body>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <form action="<?php echo htmlspecialchars("logout.php");?>" method="POST">
                <button type="submit" class="btn btn-danger navbar-btn navbar-right" name="signout">Sign out</button>
            </form>
            <div>
                <a href="index.php" class="btn btn-info navbar-btn navbar-left" role="button">Home</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col-xs-6">
                <div class="alert alert-success">
                    <p>
                        <?php 
                        if($_SESSION['email'])
                        {
                            echo "Welcome " . $_SESSION['email'];
                        }
                        ?>
                    </p>
                </div><!-- .alert alert-success h4-->
            </div><!-- .col-xs-6 -->
            <div class="col-xs-6">
                <p><a href="create-course.php" role="button" class="btn btn-success">Add course</a></p>
            </div>
            </div><!-- .row -->
            <div class="row">
                <div class="col-xs-6">
                    <h1>Zuri courses</h1>
                </div>
            </div>

            <?php
            // Include config file
            require_once "config.php";
            
            // Attempt select query execution
            $sql = "SELECT * FROM courses WHERE user_id = :user_id";
            
            if($stmt = $pdo->prepare($sql))
            {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":user_id", $param_user_id);
        
                // Set parameters
                $param_user_id = $_SESSION["id"];

                if($stmt->execute())
                {
                    if($stmt->rowCount() > 0)
                    {
                        echo "<table class='table table-bordered table-striped'>";
                            echo "<thead>";
                                echo "<tr>";
                                    echo "<th>Course name</th>";
                                    echo "<th>Action</th>";
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while($row = $stmt->fetch()){
                                echo "<tr>";
                                    echo "<td>" . $row['name'] . "</td>";
                                    echo "<td>";
                                        echo "<a href='read.php?id=". $row['id'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                        echo "<a href='update.php?id=". $row['id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                        echo "<a href='delete.php?id=". $row['id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                    echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";                            
                        echo "</table>";
                        // Free result set
                        unset($stmt);
                    } else{
                        echo "<p class='lead'><em>No courses were found.</em></p>";
                    }
                } else{
                    echo "ERROR: Could not able to execute $sql. " . $mysqli->error;
                }
                
                // Close connection
                unset($pdo);
            }
            ?>
    </div><!--.container -->
</body>
