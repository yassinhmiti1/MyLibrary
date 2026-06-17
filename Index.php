<?php
ob_start();
session_start();
require_once("config.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Welcome page</title>
  <link rel="stylesheet" href="stylewel.css">
  <script src="scriptwel.js" defer></script>
</head>
<body>
  <div class="container">

    <div class="hero">
      <div class="textwel">
        <div class="logowelc">
          <img src="/image/Untitled130_20260518214254.png" style="width: 50px;">
          <h1>MyLibrary</h1>
        </div>
      </div>
      <img src="image/aménager-une-bibliothèque.jpg">
    </div>
    <div class="logsign">
      <div id="sign" class="sign">
        <form method="post">
          <h1>Sign in</h1>
          <br>
          <p>
            Do you have an account? <a href="#" id="tologin">Log in</a>
          </p>
          <label for="signname">Nom:</label>
          <input type="text" name="signname" id="signname">
          <br>
          <label for="signprename">Prenom:</label>
          <input type="text" name="signprename" id="signprename">
          <br>
          <label for="signusern">Username:</label>
          <input type="text" name="signusern" id="signusern">
          <br>
          <label for="signemail">Email:</label>
          <input type="email" name="signemail" id="signemail">
          <br>
          <label for="signpass">Mot de passe:</label>
          <input type="text" name="signpass" id="signpass">
          <br>
          <div class="buttons">
            <button id="signinbtn" name="signinbtn">Sign In</button>
            <input type="reset" value="Annuler">
          </div>
        </form>

        <?php
        if (isset($_POST["signinbtn"])) {
          $ni = $_POST["signname"];
          $pi = $_POST["signprename"];
          $ui = $_POST["signusern"];
          $ei = $_POST["signemail"];
          $pai = $_POST["signpass"];
          $sql0 = "select * from users where LoginUser= ?";
          $eci = $cnx->prepare($sql0);
          $reponse = $eci->execute([$ui]);
          $row = $eci->fetch(PDO::FETCH_BOTH);
          if ($ui == $row["LoginUser"]) {
            echo("<span class='alert'>this user are already exists!</span>");
          }
          if (empty($ni) and empty($pi) and empty($ui) and empty($ei) and empty($pai)) {
            echo("<span class='alert'>il faut saisir tout les champs!</span>");
          } else {
            $sqli = "INSERT INTO `users`(`NomUser`, `PrenomUser`, `LoginUser`, `EmailUser`, `PasswordUser`, `id_role`) VALUES ('$ni','$pi','$ui','$ei','$pai',2)";
            $request = $cnx->prepare($sqli);
            $reponse = $request->execute();
            if ($reponse) {
              echo("<span class='message'>user add succesfuly!</span>");
            } else {
              echo("<span class='alert'>Add error!</span>");
            }
          }
        }
        ?>
      </div>
      <div id="login" class="login hidden">
        <form method="post">
          <h1>Log In</h1>
          <br>
          <p>
            you don't have an account? <a href="#" id="tosignup">Sign up</a>
          </p>
          <label for="logusern">Username:</label>
          <input type="text" name="logusern" id="logusern">
          <br>
          <label for="logpass">Mot de passe:</label>
          <input type="password" name="logpass" id="logpass">
          <br>
          <div class="buttons">
            <button name="logbtn" id="logbtn">Log In</button>
            <input type="reset" value="Annuler">
          </div>
        </form>
        <?php

        if (isset($_POST["logbtn"])) {

          $lu = $_POST["logusern"];
          $lp = $_POST["logpass"];
          $sql = "SELECT * FROM users WHERE LoginUser = ?";
          $request = $cnx->prepare($sql);
          $request->execute([$lu]);
          $user = $request->fetch(PDO::FETCH_ASSOC);
          if (empty($lu) && empty($lp) || empty($lp) || empty($lu)){
            echo('<script>alert("il faut saisir tout les champs!")</script>');
          }
          if ($user) {

            if ($lp == $user["PasswordUser"]) {
              $_SESSION["logged_in"] = true;
              $_SESSION["userid"] = $user["id_user"];
              $_SESSION["loginusern"] = $user["LoginUser"];
              $_SESSION["loginrole"] = $user["id_role"];

              header("Location: welcome.php");
              exit();

            } else {

              echo("<span class='alert'>Incorrect Password!</span>");

            }

          } else {

            echo("<span class='alert'>User not found!</span>");

          }

        }
        ?>

      </div>
    </div>
  </div>
</body>
</html>