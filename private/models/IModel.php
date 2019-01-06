
<?php

    interface IModel{
        public static function find($id);
        public static function all($argsArray);
        public static function find_by($argsArray);
        public static function create($argsArray);
        public function save();
        public function delete();

        public static function constructObject($row);
        public static function constructArrayOfObjects($row);
        public static function loadRelationships($object);
        public static function unloadRelationships($object);

        public static function neededVariablesAreSet();

    }
    
?>