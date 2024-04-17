<?php
    session_start();

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] == 'POST'   ){
        $cmd = $_POST['cmd'] ?? '' ; // login, reg, logout

        switch ($cmd) {
            case 'login':
                $username = $_POST['username'] ?? '' ;
                $password = $_POST['password'] ?? '' ;
                
                if ( loginUser($username,$password) ) {
                    echo json_encode(['success'=>true,'message'=>'Sikeres belépés']);
                } else {
                    echo json_encode(['success'=>false,'message'=>'Sikertelen belépés']);
                }
                break;
            case 'reg':
                $username = $_POST['username'] ?? '' ;
                $password = $_POST['password'] ?? '' ;
                
                if ( registerUser($username,$password) ){
                    echo json_encode(['success'=>true,'message'=>'Sikeres regisztráció']);
                } else {
                    echo json_encode(['success'=>false,'message'=>'Sikertelen regisztráció']);
                }
                
                break ;
            case 'logout':
                logoutUser();
                echo json_encode(['success'=>true,'message'=>'Sikeres kilépés']);
                break;
            
        }
    }

    function registerUser($username,$password){
        if ( findUserByUsername($username) ) {
            return false;
        } 
        $users = loadUsers(); // [  ...  ]   {username,password}
        $users[] = ['username' => $username, 'password' => $password];
        saveUsers($users);
        $_SESSION['loggedinuser']=$username;
        return true;
    }

    function saveUsers( $users ) {
        $json = json_encode($users,JSON_PRETTY_PRINT);
        file_put_contents('data/users.json',$json);
    }

    function loadUsers() {
        if (file_exists('data/users.json')){
            $json = file_get_contents('data/users.json');
            return json_decode($json,true);
        }
        return [];
    }

    function findUserByUsername($username){
        $users = loadUsers();
        foreach ($users as $user) {
            if ( $user['username'] === $username){
                return $user;
            }
        }
        return null;
    }


    function loginUser($username,$password){
        $user = findUserByUsername($username);
        if ( $user && $user['password']===$password ) {
            $_SESSION['loggedinuser']=$username;
            return true;
        }
        return false;
    }    

    function logoutUser(){
        unset($_SESSION['loggedinuser']);
    }
?>