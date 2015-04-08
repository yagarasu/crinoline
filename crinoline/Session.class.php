<?php

	/**
	 * Session Wrapper
	 * 
	 * @version 2.0.2
	 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
	 */
	 class Session {
	 	
		private $sessionName = ""; // Change this. Use your app name, for example.
									// Only one session with the same name can be used.
									// If you have multiple user access points, you must use
									// different session names to be able to login in the same
									// browser.
									// Eg: You can use 'crinoline_admin' and 'crinoline_user'.
		
		public $sessionExpire = 172800;
		
		/**
		 * Constructor
		 * 
		 * @param $sessionName Name for this session
		 */
		public function __construct( $sessionName = "CR_Session" ) {
			ini_set('session.gc_maxlifetime', $this->sessionExpire);
			session_set_cookie_params($this->sessionExpire);
			session_start();
			$this->sessionName = $sessionName;
			$this->updateLastRequest();
		}
		
		/**
		 * updateLastRequest
		 * Updates the lastRequest value of the session
		 */
		private function updateLastRequest() {
			if( isset($_SESSION[$this->sessionName]['lastRequest']) ) {
				if( $this->hasKey() && $this->getLastRequest() + $this->sessionExpire < time() ) {
					$this->revokeKey();
				} else {
					$_SESSION[$this->sessionName]['lastRequest'] = time();
				}
			}
		}
		
		/**
		 * getLastRequest
		 * Returns the lastRequest value of the session
		 */
		private function getLastRequest() {
			return $_SESSION[$this->sessionName]['lastRequest'];
		}
		
		/**
		 * giveKey
		 * Grants access to the app
		 */
		public function grantKey() {
			// Override the expiration time
			if( isset($_COOKIE[$this->sessionName]) ) {
				setcookie($sessionName, $_COOKIE[$this->sessionName], time() + $this->sessionExpire, "/");
			}
			$_SESSION[$this->sessionName]['lastRequest'] = time();
			$_SESSION[$this->sessionName]['fingerprint'] = $this->getFingerprint();
		}
		
		/**
		 * hasKey
		 * Checks if the request has the session value and if the fingerprint matches
		 */
		public function hasKey() {
			return ( isset($_SESSION[$this->sessionName]['fingerprint']) && $_SESSION[$this->sessionName]['fingerprint'] === $this->getFingerprint() );
		}
		
		/**
		 * revokeKey
		 * Destroys the current session
		 */
		public function revokeKey() {
			unset($_SESSION[$this->sessionName]);
		}
		
		/**
		 * getFingerprint
		 * Calculates the current fingerprint
		 */
		private function getFingerprint() {
			return hash('sha256', 'saltedfinger__'.$_SERVER['REMOTE_ADDR'].'__'.$_SERVER['HTTP_USER_AGENT'].'__'.session_id());
		 }

		 /**
         * Returns whether the session has been started or not.
         * This function works with any version.
         * Based on http://php.net/manual/es/function.session-status.php#113468
         * 
         * @return bool The session status
         */
        private function sessionStarted() {
            if( php_sapi_name()==='cli' ) return false;
            if(version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? true : false;
            } else {
                return session_id() === '' ? false : true;
            }
        }
		
		/**
		 * setData
		 * Sets data into de session for later retrieval
		 * 
		 * @param $key Key for the data
		 * @param $value Value of the data to set
		 */
		public function setData( $key, $value ) {
			$_SESSION[$this->sessionName]['data'][$key] = $value;
		}
		
		/**
		 * getData
		 * Retrieves the data from the session
		 * 
		 * @param $key Key for the data
		 * @return Value stored or null if key doesn't exist
		 */
		public function getData( $key ) {
			if(isset($_SESSION[$this->sessionName]['data'][$key])) {
				return $_SESSION[$this->sessionName]['data'][$key];
			} else {
				return null;
			}
		}
		
		/**
		 * delData
		 * Destroys data in the session. Can not be undone.
		 * 
		 * @param $key Key for the data
		 * @return TRUE for success, FALSE for error
		 */
		public function delData( $key ) {
			if(isset($_SESSION[$this->sessionName]['data'][$key])) {
				unset( $_SESSION[$this->sessionName]['data'][$key] );
				return TRUE;
			} else {
				return FALSE;
			}
		}
		
		/**
		 * issetData
		 * Checks if the $key exists on session data
		 * 
		 * @param $key Key for the data
		 * @return TRUE if exists, FALSE if not
		 */
		public function issetData( $key ) {
			if(isset($_SESSION[$this->sessionName]['data'][$key])) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		/**
		 * Returns the raw array of data stored.
		 * @return array Raw array of data
		 */
		public function allData() {
			return $_SESSION[$this->sessionName]['data'];
		}
		
	 }

?>