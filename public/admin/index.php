<?php
    require_once('../../private/init.php');
    require_once(PRIVATE_PATH.'/AuthGuard.php');

    if(!AuthGuard::hasLoggedIn()){
        header('location: ../');
    }
    if(AuthGuard::UserIsCustomer()){
        header('location: ../customer');
    }

    require(SHARED_PATH.'/html/adminHome.html');
?>
