<?php
    require_once(PRIVATE_PATH.'/models/IModel.php');

    abstract class Model implements IModel{

        private static $pdo = null;
        protected static $table_name;
        protected static $CLASS;
        protected static $has_one;

        /**
         * makes sure if we created a new class Instance, give it an Id of -1
         * means it's not something from the Database
         */
        public function __construct(){
            $this->id = -1;
        }

        /**
         * makes sure the user provided a [table name] and a [class name]
         */
        public static function neededVariablesAreSet(){
            if(!isset(static::$table_name)){
                throw new Exception("please provide a table name");
            }
            if(!isset(static::$CLASS)){
                throw new Exception("please provide a Class name");
            }
            return true;
        }

        /**
         * to initialize the Database connection, MUST be called before anything
         */
        public static function initDB($host, $db, $username, $password){
            $dsn = "mysql:host=$host;dbname=$db;";
            Model::$pdo = new PDO($dsn, $username, $password);
        }

        /**
         * creates a new user and saves it in the database
         * @args:{
         *      argsArray: associated array with the needed [key, value]
         *      paires for the db Entry.
         * }
         */
        public static function create($argsArray){
            $keys = [];
            $values = [];
            $qMarks = [];
            foreach($argsArray as $key => $value){
                $keys[] = $key;
                $values[] = $value;
                $qMarks[] = '?';
            }
            $keys = "(".implode(", ", $keys).")";
            $qMarks = "(".implode(", ", $qMarks).")";
            $query = "insert into ".static::$table_name." ".$keys." values ".$qMarks;
            $stmt = Model::$pdo->prepare($query);
            $stmt->execute($values);
            //echo $stmt->debugDumpParams();
        }

        /**
         * finds a user usind it's id
         */
        public static function find($id){
            static::neededVariablesAreSet();
            $stmt = Model::$pdo->prepare("select * from ".static::$table_name." where id = ? limit 1");
            $stmt->execute([$id]);
            //echo $stmt->debugDumpParams();
            return static::constructObject($stmt->fetch());
        }

        /**
         * finds one or many users with some condition in the $argsArray
         * @args{
         *      $argsArray: array of arrays, 
         *      every array should contain 3 values -> ['column name', 'operator', 'value']
         *      example:[
         *          ['name'  , 'like', 'ma'],
         *          ['id'    , '>='  , '1' ],
         *          ['status', '='   , '2']
         *      ]
         * }
         */
        public static function find_by($argsArray){
            $argsArray['conditions'] = $argsArray;
            return static::all($argsArray);
        }

        /**
         * returns all users with some optional options.
         * @args{
         *      argsArray: associated array with the provided options.
         *      allowed options:
         *          1- condtions array
         *          2- select array
         *          3- limit
         *          4- offset
         *          5- order
         *      example:[
         *          'select'=>['id', 'name', 'email'],
         *          'conditions'=>[
         *              ['id'    , '>='  , '1' ],
         *              ['status', '='   , '2' ]
         *          ],
         *          'limit':2,
         *          'offset:5,
         *          'order':'id asc'
         *      ]
         * }
         *  
         */
        public static function all($argsArray=null){
            static::neededVariablesAreSet();
            $res = static::buildQuery($argsArray);
            $stmt = Model::$pdo->prepare($res['query']);
            $stmt->execute($res['params']);
            return static::constructArrayOfObjects($stmt->fetchall());
        }

        /**
         * helper function for building the query from the $argsArray passed to {all()}.
         * @args: argsArray
         * @returns: [query, params] for prepared statement
         */
        private static function buildQuery($argsArray){
            $query = "select ";
            $params = array();
            
            //what columns to select
            if(isset($argsArray['select'])){
                $query .= implode(", ", $argsArray['select']);
            }else{
                $query .= " * ";
            }

            $query .= " from ".static::$table_name." ";

            // select based on what
            if(isset($argsArray['conditions'])){
                $query .= " where ";
                $cons = array();
                foreach($argsArray['conditions'] as $condition){
                    $con = " ".$condition[0]." ".$condition[1]." ? ";
                    $cons[] = $con;
                    $params[] = $condition[2];
                }
                $query .= implode(" and ", $cons);
            }

            if(isset($argsArray['order'])){
                $query .= "order by ".$argsArray['order']." ";
            }

            if(isset($argsArray['limit'])){
                $query .= "limit ".$argsArray['limit']." ";
            }
            if(isset($argsArray['offset'])){
                $query .= "offset ".$argsArray['offset']." ";
            }
            
            return array(
                'query'=>$query,
                'params'=>$params
            );
        }

        /**
         * saves the current user to db,
         * if:
         *      he is already in db, it updates it
         * else:
         *      it inserts it
         */
        public function save(){
            static::neededVariablesAreSet();
            static::unloadRelationships($this);

            $keys = [];
            $values = [];
            $qMarks = [];
            foreach($this as $key => $value){
                $keys[] = $key;
                $values[] = $value;
                $qMarks[] = '?';
            }
            if($this->id == -1){
                $keys = "(".implode(", ", $keys).")";
                $qMarks = "(".implode(", ", $qMarks).")";
                $query = "insert into ".static::$table_name." ".$keys." values ".$qMarks;
            }else{
                $query = "update ".static::$table_name." set ";
                $query .= implode(" = ? ,", $keys);
                $query .= " = ? where id = ?";
                $values[] = $this->id;
            }
            $stmt = Model::$pdo->prepare($query);
            $stmt->execute($values);
            static::loadRelationships($this);
        }

        /**
         * deletes the current user object from db,
         * however, the object is still there.
         */
        public function delete(){
            static::neededVariablesAreSet();
            if($this->id != -1){
                $query = "delete from ".static::$table_name." where id = ?";
                $stmt = Model::$pdo->prepare($query);
                $stmt->execute([$this->id]);
            }
        }

        /**
         * takes a row from the database, Instantiates a suitable object,
         * assigns attributes to the object, calls loadRelationships
         * and return an Object.
         */
        public static function constructObject($row){
            $object = new static::$CLASS;
            if($row){
                foreach($row as $key => $value){
                    if(!is_numeric($key)){
                        $key = str_replace(["(", ')', '*'], "", $key);
                        $object->$key = $value;
                    }
                }
                return static::loadRelationships($object);
            }
            return null;
        }
        
        /**
         * takes rows from the database and calls [constructObject()]
         * on every one of them, and returns array of objects
         */
        public static function constructArrayOfObjects($rows){
            $objects = [];
            foreach($rows as $row){
                $objects[] = static::constructObject($row);
            }
            return $objects;
        }

        /**
         * loads relationships instead of foreign keys in the object
         * instead of status = 1, status becomes an Object of type Status
         */
        public static function loadRelationships($object){
            if(isset(static::$has_one)){
                foreach(static::$has_one as $relation){
                    $attribute_name = $relation[0];
                    $class_name = $relation[1];
                    // substitute the foreign key with an object
                    $object->$attribute_name = $class_name::find($object->$attribute_name);
                }
            }
            return $object;
        }

        /**
         * loads foreign key in object instead of objects in it
         * instead of status = Status object, it becomes the id of that object
         */
        public static function unloadRelationships($object){
            if(isset(static::$has_one)){
                foreach(static::$has_one as $relation){
                    $attribute_name = $relation[0];
                    $class_name = $relation[1];
                    // substitute the object with its foreign key 
                    $object->$attribute_name = $object->$attribute_name->id;
                }
            }
            return $object;
        }
    }

?>