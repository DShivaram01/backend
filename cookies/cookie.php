<?php
  if (isset($_GET['action'])) {
    $action = $_GET['action'];
    // Handle cookie update
    // PUT YOUR CODE HERE

    // Handle cookie removal
    // PUT YOUR CODE HERE
    
    // Reload Page
    $cookie_name = "user";

    if ($action === "update" && isset($_GET['value'])) {
        $value = $_GET['value'];
        setcookie($cookie_name, $value, time() + 300, "/");
    }

    if ($action === "remove") {
        setcookie($cookie_name, $value, time() - 300, "/");
    }

    // Reload page
    header("Location: cookie.php");
    exit();
  }
?>

<html>
  <head>
    <link rel="stylesheet" href="custom_style.css">
  </head>
  <body>
  <div id="cookie_div">
  <?php
    $cookie_name = "user";
    if(!isset($_COOKIE[$cookie_name])) {
      echo "Cookie is not set for this site!";
    } else {
      echo "Cookie '" . $cookie_name . "' is set!<br>";
      echo "Value is: " . $_COOKIE[$cookie_name];
    }
    include "cookie_input.html";
  ?>
  </div>
  </body>
</html>