<?php
session_start();

if (isset($_POST['signout']))
{
    if (isset($_SESSION['loggedIn']))
    {
        unset($_SESSION['loggedIn']);
        unset($_SESSION['id']);
        unset($_SESSION['first_name']);
        unset($_SESSION['last_name']);
        unset($_SESSION['email']);
        
        session_destroy();

        header("Location:login.php");
    }
}

?>