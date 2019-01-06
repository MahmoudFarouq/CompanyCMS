<?php    
    require_once(PRIVATE_PATH.'/models/Customer.php');
    require_once(PRIVATE_PATH.'/models/Admin.php');
    require_once(PRIVATE_PATH.'/models/Status.php');
    require_once(PRIVATE_PATH.'/models/Plan.php');

    require_once(PRIVATE_PATH.'/models/Model.php');
    Model::initDB(HOST, DATABASE, USERNAME, PASSWORD);


    class DBManager{

        private static $instance = null;

        private function __construct(){

            DBManager::$instance = $this;

            /*******************/
            /* to be used when type is given, we instantiate class by it's name, instead of ifs */
            /* see function getUser */
            $this->plan = 'Plan';
            $this->status = 'Status';
            $this->customer = 'Customer';
            $this->admin = 'Admin';
            /*******************/

            $this->offset = 0;
            $this->limit = 10;
            $this->order_by = "id";
            $this->order_dir = "ASC";
            $this->search_by = '';
            $this->search_term = '';
            $this->status_filter = '';
            $this->plan_filter = '';
        }
        public static function getInstance(){
            if(DBManager::$instance != null){
                return DBManager::$instance;
            }
            return new DBManager();
        }

        public function set($args){
            if(isset($args['offset'])       ){$this->offset = $args['offset'];}
            if(isset($args['limit'])        ){$this->limit = $args['limit'];}
            if(isset($args['order_by'])     ){$this->order_by = $args['order_by'];}
            if(isset($args['order_dir'])    ){$this->order_dir = $args['order_dir'];}
            if(isset($args['search_by'])    ){$this->search_by = $args['search_by'];}    
            if(isset($args['search_term'])  ){$this->search_term = $args['search_term'];}    
            if(isset($args['status_filter'])){$this->status_filter = $args['status_filter'];}
            if(isset($args['plan_filter'])  ){$this->plan_filter = $args['plan_filter'];}    
        }


        public function deleteUser($type, $id){
            $user = $this->$type::find($id);
            if($user){
                $user->delete();
            }
        }

        public function addUser($type, $params){
            $this->$type::create($params);
        }

        public function updateUser($type, $params){
            $user = $this->$type::constructObject($params);
            $user->save();
        }

        public function changeUserStatus($type, $id, $newStatusId){
            $user = $this->$type::find($id);
            $newStatus = Status::find($newStatusId);
            if($newStatus){
                $user->status = $newStatus;
            }
            $user->save();
        }
        public function changeUserPlan($type, $id, $newPlanId){
            if($type != 'customer')
                return;
            $user = $this->$type::find($id);
            $newPlan = Plan::find($newPlanId);
            if($newPlan){
                $user->plan = $newPlan;
            }
            $user->save();
        }

        /* for getting statuses or plans */
        public function get($type){
            if($type == 'status' || $type == 'plan')
                return $this->$type::all();
            return null;
        }

        public function getUsers($type){
            $args = array('select'=>['id', 'name', 'email', 'phone', 'status', 'birthdate']);
            if($type == 'customer'){
                $args['select'][] = 'plan';
            }

            $conditions = array();
            if($this->status_filter != ''){
                $conditions[] = array('status', '=', $this->status_filter);
            }
            if($this->plan_filter != ''){
                $conditions[] = array('plan', '=', $this->plan_filter);
            }
            if($this->search_term != ''){
                $conditions[] = array($this->search_by, 'like', "%".$this->search_term."%");
            }
            if(!empty($conditions)){
                $args['conditions'] = $conditions;
            }            
            $args['limit']  = $this->limit;
            $args['offset'] = $this->offset;
            $args['order']  = $this->order_by." ".$this->order_dir;
            return $this->$type::all($args);
        }

        public function getUsersCount($type){
            return $this->$type::all(['select'=>['count(*)']])[0];
        }

        public function getUser($type, $args){
            if($args['id']){
                return $this->$type::find($args['id']);
            }
            $users = $this->$type::find_by(['conditions'=>['email', '=', $args['email']]]);

            if(count($users) == 0){
                return null;
            }else{
                $user = $users[0];
                //if(password_verify($password, $user['password'])){
                //    return $user;
                //}
                if($args['password'] == $user->password){
                    return $user;
                }
                return null;
            }
        }
    }

?>