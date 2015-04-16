<?php

	/**
	 * Returns the roles to be used
	 * @return array Roles to be used
	 */
	function myapp_roles_get() {
		return array(
			'USER' => array(
				'user-details-view',
				'contacts-admin',
			),
			'ADMIN' => array(
				'global-admin',
				'contacts-admin',
			)
		);
	}

?>