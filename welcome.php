<?php
session_start();
if(!isset($_SESSION["loginusern"])){
  header("Location: Index.php");
  exit();
}
$role = $_SESSION["loginrole"];
if($role == 1){
  header("refresh:3;url=dashboardbibio.php");
}
else{
  header("refresh:3;url=home.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Document</title>
    <style>
    @import url("https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Stack+Sans+Text:wght@200..700&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap");
    *{
      font-family: Montserrat;
    }
      .box{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin: auto;
        min-height: 100vh;
        text-align: center;
      }
      .box h1{
        font-size: 3rem;
      }
      .box p{
        color: #ef8d12;
      }
      .highlighted_text{
        text-align: center;
        color: #346739;
        display: flex;
      }
    </style>
</head>
<body>
  <div class="box">
    <h1>Merhba biik!<div class="highlighted_text"><?php echo($_SESSION["loginusern"]); ?></div></h1>
    <p>processing your pages.....</p>
  </div>
</body>
</html>