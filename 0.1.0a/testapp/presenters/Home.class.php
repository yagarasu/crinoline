<?php
	
	/**
	 * Home Presenter
	 */
	class Home extends Presenter {
		public function main() {
			echo "<p>home!!!</p>";
			echo "<p>".DB_MAIN_NAME."</p>";
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