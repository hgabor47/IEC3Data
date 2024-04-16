<?php
    session_start();
    header('Content-Type: application/json');

    if ( isset($_SESSION['loggedinuser']) ) {

        if ($_SERVER['REQUEST_METHOD'] == 'POST'   ){
            $cmd = $_POST['cmd'] ?? '' ; // booklist ...
            switch ($cmd) {
                case 'booklist':
                    echo loadBooks();
                    break;
                
            }
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Nem jogosult!']);
    }
    
    function loadbooks() {
        $f = 'data/konyvek.json';
        if (file_exists($f)) {
            $jsondata = file_get_contents($f);
            return $jsondata;
        }
        return json_encode(['success' => false, 'message' => 'A fájl nem található']);
    }