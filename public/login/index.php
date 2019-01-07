<?php
    require_once('../../private/init.php');
    require_once(PRIVATE_PATH.'/dbConfig.php');
    require_once(PRIVATE_PATH.'/DBManager.php');
    require_once(PRIVATE_PATH.'/AuthGuard.php');

    if(AuthGuard::UserIsCustomer()){
        header('location: ../customer');
    }
    if(AuthGuard::UserIsAdmin()){
        header('location: ../admin');
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $dbMngr = DBManager::getInstance();

        $user = $dbMngr->getUser($_POST['type'], 
            ['email'=>$_POST['email'], 'password'=>$_POST['password']]
        );

        if($user == null){
            header('location: ../index.php');
        }else{
            AuthGuard::saveUser($user);
            if(AuthGuard::userIsAdmin()){
                header('location: ../admin');
            }else if(AuthGuard::userIsCustomer()){
                if(AuthGuard::getCurrentUser()->status == '2'){
                    AuthGuard::abortSession();
                    echo "
                        <h1> sorry your account is been deactivated </h1>
                    ";
                }else{
                    header('location: ../customer');
                }
            }
        }
    }else{
        require(SHARED_PATH.'/html/loginForm.html');
    }
?>

