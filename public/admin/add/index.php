<?php
    require_once('../../../private/init.php');
    require_once('../../../private/dbConfig.php');
    require_once(PRIVATE_PATH.'/AuthGuard.php');
    require_once(PRIVATE_PATH.'/DBManager.php');

    if(!AuthGuard::UserIsAdmin()){
        header('location: ../../');
    }
    

    $dbMngr = DBManager::getInstance();

    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        require(SHARED_PATH.'/html/addUserForm.html');
    }else{

        $args = [
            'email'=>$_POST['email'], 
            //password_hash($_POST['password'], PASSWORD_DEFAULT), 
            'password'=>$_POST['password'],
            'name'=>$_POST['name'], 
            'phone'=>$_POST['phone'], 
            'birthdate'=>$_POST['birthDate'], 
            'status'=>$_POST['active'] == 'on'? '1':'2'
        ];
        if($_POST['user'] == 'customer'){
            $target = 'customer';
            $args['plan'] = $_POST['plan'];
        }else if($_POST['user'] == 'admin'){
            $target = 'admin';
        }

        echo $dbMngr->addUser($target, $args);

        header('location: ../');
    }
?>