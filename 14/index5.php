<?php

    if ( $_SERVER["REQUEST_METHOD"] =="GET" &&  isset($_GET['submit']) ){
        
        $login = isset($_GET['login'])  ?  $_GET['login'] : '';
        $pwd = isset($_GET['pwd'])  ?  $_GET['pwd'] : '';
        $pwd2 = isset($_GET['pwd2'])  ?  $_GET['pwd2'] : '';
        $email = isset($_GET['email'])  ?  $_GET['email'] : '';

        $error='';
        if ( empty($login) ) {
            $error=$error . "A login megadása kötelező<br>";
        }
        if ( empty($pwd) || $pwd!=$pwd2 ) {
            $error=$error . "Jelszavak nem lehet üresek és meg kell, hogy egyezzenek!<br>";
        }
        if ( empty($email) ) {
            $error=$error . "Az email megadása kötelező<br>";
        }

        
        if ( empty($error) ) {
            echo "<script> alert('Regisztráció sikeres!');</script>";
        } else {
            echo "<script> alert('Hiba:" . $error ."!');</script>" ;
        }

    } 

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
    <style>
        .info {
            color:red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Regisztráció</h1>
        <form method="get" action="" >
            Login: <input type="text" name="login"><br>
            Jelszó: <input type="password" name="pwd"><input type="password" name="pwd2"><br>
            Email: <input type="email" name="email"><br>
            <input type="submit" name="submit" value="Regisztráció">
        </form>
    </div>

    <div id="info" class="info">
    </div>
</body>
</html>