<?php

    session_start();

    class AuthGuard{

        public static function saveUser($user){
            $_SESSION["user"] = serialize($user);
        }

        public static function hasLoggedIn(){
            return ($_SESSION["user"] != null);
        }

        public static function getCurrentUser(){
            if(AuthGuard::hasLoggedIn()){
                return unserialize($_SESSION["user"]);
            }
            return null;
        }

        public static function userIsAdmin(){
            if(!AuthGuard::hasLoggedIn())
                return false;
            $user = AuthGuard::getCurrentUser();

            if($user instanceof Admin){
                return true;
            }else{
                return false;
            }
        }

        public static function userIsCustomer(){
            if(!AuthGuard::hasLoggedIn())
                return false;
            $user = AuthGuard::getCurrentUser();
            if($user instanceof Customer){
                return true;
            }else{
                return false;
            }
        }

        public static function abortSession(){
            $_SESSION["user"] = null;
            session_destroy();
        }

    }
?>