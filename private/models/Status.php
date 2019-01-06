<?php
    
    require_once(PRIVATE_PATH.'/models/Model.php');

    class Status extends Model
    {
        static $table_name = 'statuses';
        public static $CLASS = 'Status';

    }

?>