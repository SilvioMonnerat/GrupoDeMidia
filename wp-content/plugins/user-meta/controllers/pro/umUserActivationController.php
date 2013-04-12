<?php

if( !class_exists( 'umUserActivationController' ) ) :
class umUserActivationController {
    
    function __construct(){  
        add_filter( 'user_row_actions',     array( $this, 'userRowActions' ), 10, 2 );  
        add_action( 'load-users.php',       array( $this, 'loadUsersPage' ) );        
        add_action( 'admin_footer-users.php', array( $this, 'userAdminCustomization' ) );   
        add_action( 'wp_authenticate_user', array( $this, 'authenticateUser' ) );     
        add_action( 'user_register',        array( $this, 'userRegister' ) );        
        add_action( 'admin_notices',        array( $this, 'adminNotice' ) );                                              
    }   
    
    /**
     * Addding acitvate/deactivate link under indivisual user.
     */
    function userRowActions( $actions, $user_object ){
        global $userMeta;
        
        //if( ! $this->isActivationRequire() )
            //return $actions;
                              
	if ( ( get_current_user_id() != $user_object->ID ) && current_user_can('edit_user', $user_object->ID) ) {
            
            if( $this->isUserInactive( $user_object->ID ) ){                
                $url = $userMeta->userActivationUrl( 'activate', $user_object->ID );				
				$actions[ 'um_user_status' ]	=	"<a href='{$url}'>" . __( 'Activate', $userMeta->name ) . "</a>";                
            }else{                
                $url = $userMeta->userActivationUrl( 'deactivate', $user_object->ID );				
				$actions[ 'um_user_status' ]	=	"<a href='{$url}'>" . __( 'Deactivate', $userMeta->name ) . "</a>";                
            }            			
	}           

        return $actions;
    }
       
    /**
     * Running user activation/deactivation while user admin page loaded, based on get paramater.
     */
    function loadUsersPage(){
        global $userMeta;
        
        //if( ! $this->isActivationRequire() ) return;     
        
        $user   = @$_REQUEST[ 'user' ];
        $users  = @$_REQUEST[ 'users' ];
        $action = @$_REQUEST[ 'action' ];
        
        // Only handle activate/deactivate action
        if( ! in_array( $action, array( 'activate', 'deactivate' ) ) )
            return;            

        // success message shown by admin_notices.
        if( isset( $_GET[ 'success' ] ) )
            return; 
        
        $count = 0;
        
        // Bulk activation/deactivation     
        if( is_array( $users ) && $users ){
            check_admin_referer( 'bulk-users' );
            foreach( $users as $userID ){
                $this->userAcitvateDeactivate( $userID, $action );
                $count++;
            }      
            
        // Single activation/deactivation. If nonoce field is set, do activation/deactivation. or attaempt to confirmation screen.
        }elseif( $user ){
            if( isset( $_GET[ '_wpnonce' ] ) ){
                check_admin_referer( 'um_activation' );
                $this->userAcitvateDeactivate( $user, $action );
            }else
                return;
        }
                        
        $this->_redirect( $action, $count );       
    }
    
    /**
     * Adding item to action dropdown for bulk update. Showing number of active and inactive users.
     */
    function userAdminCustomization(){
        global $wpdb, $userMeta;
        
        //if( ! $this->isActivationRequire() ) return;
        
        $countTotal = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");        
        $userQuery = new WP_User_Query( array(
            'meta_key'      => $userMeta->prefixLong . 'user_status',
            'meta_value'    => 'inactive',
        ) );        
        $countInactive  = $userQuery->get_total();
       
        $userQuery = new WP_User_Query( array(
            'meta_key'      => $userMeta->prefixLong . 'user_status',
            'meta_value'    => 'pending',
        ) );        
        $countInactive  = $countInactive + $userQuery->get_total();       
        
        
        $countActive    = $countTotal - $countInactive;
        
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('.actions select[name^="action"]').append('<option value="activate"><?php _e( 'Activate', $userMeta->name ); ?></option><option value="deactivate"><?php _e( 'Deactivate', $userMeta->name ); ?></option>');
            jQuery('.subsubsub').append('<?php echo "<li> | " . __( 'Active', $userMeta->name ) . "($countActive)" . "</li><li> | " . __( 'Inactive', $userMeta->name ) . "($countInactive)</li>"; ?>');
        });
        </script>        
        <?php
    }
    
            
    /**
     * Redirection for showing success message after activate/deactivate.
     */
    function _redirect( $action, $count ){
        $url = admin_url( 'users.php' );
        $url = add_query_arg( array( 
            'action'    => $action,
            'success'   => 1,
            'count'     => $count ? $count : 1,
         ), $url );
        wp_redirect( $url );
        exit();
    }
    
    /**
     * Authenticate a user while login.
     */ 
    function authenticateUser( $userdata ){
        global $userMeta;
        
        //if( ! $this->isActivationRequire() )
            //return $userdata;
        
        if ( is_wp_error( $userdata ) )
                return $userdata;
		
        if( $this->isUserInactive( $userdata->ID ) ){
                $userdata	=	new WP_Error(
                        'um_user_activation_error',
                        __('<strong>ERROR:</strong> your account is not yet activated.', $userMeta->name )
                );
	}
		
	return $userdata;        
    }
    
    /**
     * Set a user status as inactive while registration. And send email to admin for user approval.
     */
    function userRegister( $userID ){
        global $userMeta;
        if( ! $this->isActivationRequire() ) return;   
        
        $registration       = $userMeta->getSettings( 'registration' );
        $user_activation    = $registration[ 'user_activation' ];
        
        $status = 'inactive';
        $status = $user_activation == 'both_email_admin' ? 'pending' : $status;
        $status = current_user_can('add_users') ? 'active' : $status;

        update_user_meta( $userID, $userMeta->prefixLong . 'user_status', $status );       
    }
    
    /**
     * Let admin activate new user through email link.
     * And show success message after activation/deactivation.
     */
    function adminNotice(){
        global $userMeta, $pagenow;
        //if( ! $this->isActivationRequire() ) return;
        
        if ( $pagenow == 'users.php' ) {
            $action = @$_GET[ 'action' ];           
            $userID = @$_GET[ 'user' ];
            if( in_array( $action, array( 'activate', 'deactivate' ) ) && !isset( $_GET[ '_wpnonce' ] ) ){
                if( $userID ){
                    $user = get_user_by( 'id', $userID );               
                    $profileUrl = add_query_arg( array( 'user_id' => $userID ), admin_url( 'user-edit.php' ) );
                    $profileUrl = "<a href=\"$profileUrl\">$user->user_login</a>";                
                    
                    if( $this->isUserInactive( $userID ) ){
                        $html = "<div class=\"updated\"><p>";
                        $html .= sprintf( __( 'New user %s registered on your site.', $userMeta->name ), $profileUrl );
                        $html .= " <a class=\"button-secondary\" href=\"" . $userMeta->userActivationUrl( $action, $userID ) . "\">" . __( 'Activate', $userMeta->name ) . "</a>";
                        $html .= "</p></div>";                    
                    }else{
                        $html = "<div class=\"updated\"><p>";
                        $html .= sprintf( __( 'User %s is already activated.', $userMeta->name ), $profileUrl );
                        $html .= "<a class=\"button-secondary\" href=\"" . $userMeta->userActivationUrl( 'deactivate', $userID ) . "\">" . __( 'Deactivate', $userMeta->name ) . "</a>";
                        $html .= "</p></div>";                     
                    }                    
                }elseif( isset( $_GET[ 'success' ] ) ){
                    if( $action == 'activate' )
                        $status = __( 'activated', $userMeta->name );
                    elseif( $action == 'deactivate' ) 
                        $status = __( 'deactivated', $userMeta->name );
                        
                    if( @$_GET[ 'count' ] > 1 )
                        $status = sprintf( __( '%1$s users have been %2$s', $userMeta->name ), $_GET[ 'count' ], $status );
                    else
                        $status =  sprintf( __( 'User has been %s', $userMeta->name ),  $status );  
                    
                    $html = "<div class=\"updated\"><p>$status</p></div>";                
                }
                
                if( isset( $html ) )
                    echo $html;
                              
            }
        }
    }    
    
    /**
     * Do user activation/deactivation.
     */
    function userAcitvateDeactivate( $userID, $action ){
        global $userMeta;
        switch( $action ){
            case 'activate' :
                update_user_meta( $userID, $userMeta->prefixLong . 'user_status', 'active' );
                do_action( 'user_meta_user_activate', $userID );
            break;
            
            case 'deactivate' :
                update_user_meta( $userID, $userMeta->prefixLong . 'user_status', 'inactive' );
                do_action( 'user_meta_user_deactivate', $userID );
            break;
        }
    }    
       
    
    /**
     * Check if 'need user activation' enabled on user-meta settings
     */
    function isActivationRequire(){
        global $userMeta;
        $registration       = $userMeta->getSettings( 'registration' );
        $user_activation    = $registration[ 'user_activation' ];
        
        if( in_array( $user_activation, array( 'email_verification', 'admin_approval', 'both_email_admin' ) ) )
            return true;
        return false;
    }       
    
    /**
     * check if user is inactive
     */
    function isUserInactive( $userID ){
        global $userMeta;
        $status = get_user_meta( $userID, $userMeta->prefixLong . 'user_status', true );
        if( in_array( $status, array( 'inactive', 'pending' ) ) )
            return true;
        return false;
    }
                
}
endif;
?>