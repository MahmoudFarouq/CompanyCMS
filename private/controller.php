<?php
    require_once('init.php');
    require_once('dbConfig.php');
    require_once('DBManager.php');
    require_once('AuthGuard.php');

    if(!AuthGuard::UserIsAdmin()){
        header('location: ../public');
    }

    $dbMngr = DBManager::getInstance();

    $task = $_REQUEST['task'];

    if($_SERVER['REQUEST_METHOD'] == 'GET' && $task == 'getUsers'){
        if(isset($_GET['target'])){
            $dbMngr->set($_GET);
            echo json_encode(
                $dbMngr->getUsers($_GET['target'])
            );
        }
    }
    
    else if($_SERVER['REQUEST_METHOD'] == 'POST' && $task == 'updateStatus'){
        $dbMngr->changeUserStatus($_POST['target'], $_POST['id'], $_POST['newStatusId']);
    }

    else if($_SERVER['REQUEST_METHOD'] == 'POST' && $task == 'updatePlan'){
        $dbMngr->changeUserPlan($_POST['target'], $_POST['id'], $_POST['newPlan']);
    }
    
    else if($_SERVER['REQUEST_METHOD'] == 'POST' && $task == 'addUser'){
        $args = [
            'name'=>$_POST['name'],
            'email'=>$_POST['email'],
            'phone'=>$_POST['phone'],
            'birthdate'=>$_POST['birthdate'],
            'status'=>$_POST['status']
        ];
        if($$_POST['target'] == 'customer')
            $args['plan'] = $_POST['plan'];
        $dbMngr->addUser($_POST['target'], $args);
    }

    else if($_SERVER['REQUEST_METHOD'] == 'POST' && $task == 'updateUser'){
        $args = [
            'id'=>$_POST['id'],
            'name'=>$_POST['name'],
            'email'=>$_POST['email'],
            'phone'=>$_POST['phone'],
            'birthdate'=>$_POST['birthDate'],
            'status'=>$_POST['status']
        ];
        if($_POST['target'] == 'customer')
            $args['plan'] = $_POST['plan'];
        $dbMngr->updateUser($_POST['target'], $args);
    }
    
    else if($_SERVER['REQUEST_METHOD'] == 'POST' && $task == 'deleteUser'){
        $dbMngr->deleteUser($_POST['target'], $_POST['id']);
    }
    
    else if($_SERVER['REQUEST_METHOD'] == 'GET' && $task == 'getUsersCount'){
        echo json_encode(
            $dbMngr->getUsersCount($_GET['target'])
        );
    }

?>