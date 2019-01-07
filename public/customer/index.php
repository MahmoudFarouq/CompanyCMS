<?php
    require_once('../../private/init.php');
    require_once(PRIVATE_PATH.'/AuthGuard.php');
    require_once(PRIVATE_PATH.'/models/Customer.php');
    require_once(PRIVATE_PATH.'/models/Plan.php');
    require_once(PRIVATE_PATH.'/models/Status.php');

    if(!AuthGuard::hasLoggedIn()){
        header('location: ../');
    }
    if(AuthGuard::UserIsAdmin()){
        header('location: ../admin');
    }

    $user = AuthGuard::getCurrentUser();
    require(SHARED_PATH.'/html/customerHome.html');
?>
