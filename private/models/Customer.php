<?php
    require_once(PRIVATE_PATH.'/models/User.php');

    class Customer extends User
    {
        public static $table_name = 'customers';
        public static $has_one = [['status', 'Status'], ['plan', 'Plan']];
        public static $CLASS = 'Customer';
    }

?>