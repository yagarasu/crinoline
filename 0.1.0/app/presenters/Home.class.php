<?php
	
	/**
	 * Home Presenter
	 */
	class Home extends Presenter {
		public function main() {
			echo "home!!!";
			var_dump(AppSession()->hasKey());
		}
		public function login() {
			echo "login!";
			AppSession()->grantKey();
		}
		public function logout() {
			echo "logout!";
			AppSession()->revokeKey();
		}
	}

?>