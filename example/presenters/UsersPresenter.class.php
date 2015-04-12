<?php

	class UsersPresenter extends Presenter {
		
		public function main($args) {
			// Relocate if not logged
			if(!plg('CRSession')->hasKey()) relocate(approot());

			plg('CRLaces')->loadAndRender('templates/user-detail.ltp');
		}

		public function logout($args) {
			plg('CRSession')->revokeKey();
			relocate( approot() );
		}

		public function login($args) {
			// LOGIN. Hardcoded. Use database.
			if(!isset($args['user'])||!isset($args['pass'])) throw new Exception('Missing parameters');

			if($args['user']==='user'&&$args['pass']==='user') {
				plg('CRSession')->grantKey();
				plg('CRSession')->setData('username', $args['user']);
				plg('CRSession')->setData('user', array(
					'name' => 'Chumel Laces',
					'email' => 'chumel@crinoline.com',
				));
				plg('CRRoles')->changeRole('USER');
			}

			if($args['user']==='admin'&&$args['pass']==='admin') {
				plg('CRSession')->grantKey();
				plg('CRSession')->setData('username', $args['user']);
				plg('CRSession')->setData('user', array(
					'name' => 'Carmen Crinoline',
					'email' => 'carmen@crinoline.com',
				));
				plg('CRRoles')->changeRole('ADMIN');
			}

			relocate( approot() );
			
		}

		public function admin($args) {
			// Relocate if not logged
			if(!plg('CRSession')->hasKey()) relocate(approot());
			// Relocate if can't do
			if(!plg('CRRoles')->userCan('global-admin')) relocate(approot());

			plg('CRLaces')->setIntoContext('$conf:welcome', app()->softconf->get('welcome', 'Please set one'));
			plg('CRLaces')->loadAndRender('templates/settings.ltp');
		}

		public function admin_update($args) {
			// Relocate if not logged
			if(!plg('CRSession')->hasKey()) relocate(approot());
			// Relocate if can't do
			if(!plg('CRRoles')->userCan('global-admin')) relocate(approot());

			if(!isset($args['msg'])) throw new Exception('Missing parameters.');
			
			app()->softconf->set('welcome', $args['msg']);

			relocate( approot() . 'admin/' );
		}
		
	}

?>