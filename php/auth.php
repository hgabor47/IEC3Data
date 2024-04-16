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
                
                echo json_encode(registerUser($username,$password));
                
                break ;
            case 'logout':
                logoutUser();
                echo json_encode(['success'=>true,'message'=>'Sikeres kilépés']);
                break;
            
        }
    }

    function registerUser($username,$password){
        $passwordValidation = validatePassword($password);
        if (!$passwordValidation['success']) {
            return $passwordValidation;
        }

        if ( findUserByUsername($username) ) {
            return ['success' => false, 'message' => 'Ez a felhasználónév már foglalt.'];
        } 
        $users = loadUsers(); // [  ...  ]   {username,password}
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $users[] = ['username' => $username, 'password' => $hashedPassword];
        saveUsers($users);
        $_SESSION['loggedinuser']=$username;
        return ['success' => true];
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
        if ( $user && password_verify($password, $user['password']) ) {
            $_SESSION['loggedinuser']=$username;
            return true;
        }
        return false;
    }    

    function logoutUser(){
        unset($_SESSION['loggedinuser']);
    }


    function validatePassword($password) {
        $minLength = 8;
        if (strlen($password) < $minLength) {
            return ['success' => false, 'message' => 'A jelszó túl rövid. Legalább 8 karakter hosszúnak kell lennie.'];
        }
        if (!preg_match('/\d/', $password)) {
            return ['success' => false, 'message' => 'A jelszónak tartalmaznia kell számot.'];
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return ['success' => false, 'message' => 'A jelszónak tartalmaznia kell nagybetűt.'];
        }
        if (!preg_match('/[a-z]/', $password)) {
            return ['success' => false, 'message' => 'A jelszónak tartalmaznia kell kisbetűt.'];
        }
        if (!preg_match('/[\^$*.\[\]{}()?"!@#%&\/,><\':;|_~`\\-]/', $password)) {
            return ['success' => false, 'message' => 'A jelszónak tartalmaznia kell speciális karaktert.'];
        }
        return ['success' => true];
    }
    
    
?>