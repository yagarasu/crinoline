<?php

    class HomePresenter extends Presenter {
        
        public function main($args) {
            echo "main";
            var_dump(plg('CRSession')->hasKey());
        }
        
        public function loginDo($args) {
            echo "do login!!!";
            
            plg('CRSession')->grantKey();
            plg('CRSession')->setData('role', 'user');
            
            echo '<a href="../">Back</a>';
        }

        public function login($args)
        {
            echo 'Do login: <a href="http://localhost/crinoline/example/login.do">now!!!</a>';
            var_dump(plg('CRRoles')->getRoles());
        }

        public function logout($args) {
            plg('CRSession')->revokeKey();
            relocate('/');
        }

        public function youCant($args) {
            if(!plg('CRRoles')->userCan('action2')) throw new Exception('User "' . plg('CRRoles')->userIs() . '" can not to this.');
            echo "User ".plg('CRRoles')->userIs()." can do this";
        }

        public function changeRole($args) {
            plg('CRRoles')->changeRole($args['role']);

            echo "changed";
            
            echo '<a href="../">Back</a>';
        }
        
    }

?>