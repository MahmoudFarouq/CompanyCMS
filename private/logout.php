<?php

    require_once('AuthGuard.php');

    if($_SERVER['REQUEST_METHOD'] == 'POST' ){
        AuthGuard::abortSession();
        header('location: ../public/index.php');
    }

?>