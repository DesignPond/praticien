<?php

	/*================================
		Get the data of list
	==================================*/

	function getUsersByRole( $roles ) {
		global $wpdb;
			if ( ! is_array( $roles ) ) {
							$roles = explode( ",", $roles );
							array_walk( $roles, 'trim' );
			}
			$sql = 'SELECT	ID, display_name
							FROM		' . $wpdb->users . ' INNER JOIN ' . $wpdb->usermeta . '
							ON		' . $wpdb->users . '.ID				=		' . $wpdb->usermeta . '.user_id
							WHERE	' . $wpdb->usermeta . '.meta_key		=		\'' . $wpdb->prefix . 'capabilities\'
							AND		( ';
							
				$i = 1;
				foreach ( $roles as $role ) {
							$sql .= ' ' . $wpdb->usermeta . '.meta_value	LIKE	\'%"' . $role . '"%\' ';
							if ( $i < count( $roles ) ) $sql .= ' OR ';
							$i++;
				}
				$sql .= ' ) ';
				$sql .= ' ORDER BY display_name ';
				$userIDs = $wpdb->get_col( $sql );
				return $userIDs;
	}
		
	function getUsersAbos( $ids ) {
			global $wpdb;
			$gotAbo = array();
						
				foreach($ids as $user_id){
				$userCategories = $wpdb->get_results('SELECT * FROM wp_user_abo WHERE refUser = "'.$user_id.'" ');
					if ( $userCategories )
					{ 
						foreach( $userCategories as $iduser)
						{
							$gotAbo[$iduser->refUser][] = $iduser->refCategorie; 
						}
					}
				}
		return $gotAbo;
	}
 
	
	