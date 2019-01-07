<?php
    require_once('../private/init.php');
    require_once(PRIVATE_PATH.'/dbConfig.php');
    require_once(PRIVATE_PATH.'/AuthGuard.php');
    require_once(PRIVATE_PATH.'/DBManager.php');

    if(AuthGuard::hasLoggedIn())
    {
        if(AuthGuard::UserIsCustomer()){
            header('location: customer');
        }
        if(AuthGuard::UserIsAdmin()){
            header('location: admin');
        }
    }else{
        header('location: login');
    }
?>