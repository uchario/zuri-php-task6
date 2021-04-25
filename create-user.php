<?php
// Include config file
require_once "config.php";
require_once "validation.php";
require_once "head.php";
 
// Define variables and initialize with empty values
$first_name = $last_name = $email = $password = "";
$first_name_err = $last_name_err = $email_err = $password_err = "";
 
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $email = test_input($_POST["email"]);
  
  if (empty($_POST["first_name"])) {
    $first_name_err = "* First name is required";
  } elseif (!preg_match("/^[a-zA-Z ]*$/", $first_name)) {
    $first_name_err = "Only letters and white space allowed";
  }
  else {
    $first_name = test_input($_POST["first_name"]);
  }

  if (empty($_POST["last_name"])) {
    $last_name_err = "* Last name is required";
  } elseif (!preg_match("/^[a-zA-Z ]*$/", $last_name)) {
    $last_name_err = "Only letters and white space allowed";
  }
  else {
    $last_name = test_input($_POST["last_name"]);
  }
  if (empty($_POST["email"])) {
    $email_err = "* Email is required";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $email_err = "Invalid email format";
  }
  else {
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
        if($stmt->rowCount() == 1)
        {
          $email_err = "This email already exists.";
        } else{
          $email = $email;
        }
      }
    } else{
      echo "<div class='container'><div class='row'><div class='col-xs-6'><div class='alert alert-danger'>Something went wrong. Please try again later.</div></div></div></div>";
    }

    // Close statement
    unset($stmt);
  }
    
  // Check if password is empty
  if (empty($_POST["password"])) {
    $password_err = "* Password is required";
  } else {
    $password = test_input($_POST["password"]);
  }

  // Check input errors before inserting in database
  if(empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($password_err))
  {
    // Prepare an insert statement
    $sql = "INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)";

    if($stmt = $pdo->prepare($sql))
    {
      // Bind variables to the prepared statement as parameters
      $stmt->bindParam(":first_name", $param_first_name);
      $stmt->bindParam(":last_name", $param_last_name);
      $stmt->bindParam(":email", $param_email);
      $stmt->bindParam(":password", $param_password);
      
      // Set parameters
      $param_first_name = $first_name;
      $param_last_name = $last_name;
      $param_email = $email;
      $param_password = md5($password);
      
      // Attempt to execute the prepared statement
      if($stmt->execute()){
          // Records created successfully. Redirect to landing page
          echo "<div class='container'><div class='row'><div class='col-xs-6'><div class='alert alert-success'>User created successfully.</div></div></div></div>";
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
 
<body>
  <div class="container">
    <div class="jumbotron">
        <div class="row">
            <div class="col-xs-6">
              <div class="page-header">
                <h1>Zuri Form</h1>
              </div>
              <div class="clearfix">
                <div class="pull-right">
                  <a href="index.php" role="button" class="btn btn-info">Home</a>
                </div>
              </div>
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                <div class="form-group">
                  <label for="first_name">First name</label>
                  <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name">
                  <span class="error text-danger"><?php echo $first_name_err;?></span>
                </div>
                <div class="form-group">
                  <label for="last_name">Last name</label>
                  <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name">
                  <span class="error text-danger"><?php echo $last_name_err;?></span>
                </div>
                <div class="form-group">
                  <label for="email">Email address</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                  <span class="error text-danger"><?php echo $email_err;?></span>
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                  <span class="error text-danger"><?php echo $password_err;?></span>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Create</button>
              </form>
            </div>
        </div>
    </div>
  </div>
</body>