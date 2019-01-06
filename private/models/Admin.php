<?php
    require_once(PRIVATE_PATH.'/models/User.php');

    class Admin extends User
    {
        public static $table_name = 'admins';
        public static $has_one = [['status', 'Status']];
        public static $CLASS = 'Admin';

    }

?>