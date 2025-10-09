<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <center>
        <h1>Hello</h1>
        <?php
            if(isset($_GET["statusCode"])){
                if($_GET["statusCode"]=="201"){
                    echo "<b> page Created </b>";
                }
            }
        ?>

        <form action="login_user_logic.php" method="post">
            <input type="text" name="name" id="name" placeholder="name">
            <br>
            <input type="email" name="email" id="email" placeholder="email">
            <br>
            <input type="password" name="password" id="password" placeholder="password">
            <br>
            <input type="submit" value="login" name="login" >
        
        </form>
    </center>
</body>
</html>