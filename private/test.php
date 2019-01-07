<?php
    
    require_once('init.php');
    require_once('dbConfig.php');
    require_once('DBManager.php');

    /*$c = Customer::find(1);
    echo json_encode($c->status)."\n";
    echo json_encode($c->plan)."\n";
    $c->plan = Plan::find(3);
    $c->status = Status::find(2);
    $c->save();

    $c = Customer::find(1);
    echo json_encode($c->status)."\n";
    echo json_encode($c->plan)."\n";
    */

    /*$users = array_merge(Admin::all(), Customer::all());

    foreach($users as $user){
        $user->password = password_hash("123", PASSWORD_DEFAULT);
        $user->save();
    }*/


?>