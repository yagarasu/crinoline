<?php
/**
 * Login model class
 * 
 * Holds the business rules for login
 */
class LoginModel {
	/**
	 * isAuthorized
	 * 
	 * Checks for valid authorizations
	 * 
	 * @param string $user User or email to be checked
	 * @param string $password Password to check
	 * @return boolean true on authorized pair, false on error or unauthorized pair
	 */
	public function isAuthorized($username, $password) {
		$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if( !$db->connect() ) throw new Exception("Unable to connect to database", 200);
		$strUser = $db->escape($username);
		$user = $db->queryFirst("SELECT * FROM `users` WHERE username='{$strUser}' OR email='{$strUser}' LIMIT 1;");
		if( $user===false ) return false;
		$uHash = ( SEC_USESERVERSALT ) ? $password.$user['salt'].SEC_SERVERSALT : $password.$user['salt'];
		$uHash = hash( 'sha256' , $uHash );
		if( $user['password']===$uHash ) {
			return true;
		} else {
			return false;
		}
	}
}
?>