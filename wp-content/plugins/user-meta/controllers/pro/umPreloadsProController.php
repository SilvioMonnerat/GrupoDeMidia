<?php

if( !class_exists( 'umPreloadsProController' ) ) :
class umPreloadsProController {
    
    function __construct(){
        global $userMeta;
                    
        /**
         * wp           : Do ExtraExecution: logout, lostpassword in loginPage.
         * the_title    : Set null title in case of lostpassword, ExtraExecution.
         * the_content  : Showing extra form by blocking default page content, Form: lostPasswordForm(), resetPasswordForm.
         */
        add_action( 'wp',           array( $this, 'loginPageExtraExecution' ) );
        add_filter( 'the_title',    array( $this, 'loginPageExtraExecutionTitle' ), 10, 2 );
        add_filter( 'the_content',  array( $this, 'loginPageEextraExecutionContent' ) );
        
        add_filter( 'authenticate', array( $this, 'changeLoginErrorMessage' ), 50, 3 );  
        
        //add_filter( 'login_redirect',               array( $this, 'loginRedirect' ), 10, 3 ); 
        //add_filter( 'logout_redirect',              array( $this, 'logoutRedirect' ) ); 
        //add_filter( 'registration_redirect',        array( $this, 'registrationRedirect' ), 10, 2 );  
        
        add_filter( 'login_url',                    array( $this, 'loginUrl' ),  10, 2 ); 
        add_filter( 'logout_url',                   array( $this, 'logoutUrl' ), 10, 2 ); 
        add_filter( 'lostpassword_url',             array( $this, 'lostpasswordUrl' ), 10, 2 );    
        
        add_action( 'login_init',       array( $this, 'disableDefaultLoginPage' ) );  
        add_action( 'user_meta_admin_notices',      array( $this, 'showSetLoginPageMsg' ) );                                                         
    }
    
    function disableDefaultLoginPage(){
        global $userMeta;  
        $login = $userMeta->getSettings( 'login' );
        
        if( !empty( $login[ 'login_page' ] ) && !empty( $login[ 'disable_wp_login_php' ] ) ){
            wp_redirect( get_permalink( $login[ 'login_page' ] ) );
            exit();
        }
    }
        
    
    function loginPageExtraExecution(){
        global $userMeta, $post;      
        
        // Login befor header sent to browser
        /*if( $userMeta->isLoginRequest() ){
            if( @$_REQUEST['is_ajax'] ) return;                        
            $userMeta->doLogin( true ); 
        }*/
          
        $action = @$_REQUEST[ 'action' ]; 
        $login = $userMeta->getSettings( 'login' );
        if( @$post->ID <> @$login[ 'login_page' ] )
            return;
                 
        switch ($action) {
            
            case 'logout':               
            	check_admin_referer('log-out');
                    
                $user = wp_get_current_user();
                if ( ! $user->exists() )
                        return false;
                               
            	wp_logout();
            
                $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;
                if( $userMeta->isFilterEnable( 'logout_redirect' ) )
                    $redirect_to = apply_filters('logout_redirect', $redirect_to, $redirect_to, $user);            
                
                if( !$redirect_to )
                    $redirect_to = get_permalink( $login[ 'login_page' ] );
                
            	wp_redirect( $redirect_to );
            	exit();                
            break;                
           
        }
    }
    
    function loginPageExtraExecutionTitle( $title, $id = 0 ){                        
        if( is_page() ){
            global $userMeta;
            
            $login = $userMeta->getSettings( 'login' );
            if( @$login[ 'login_page' ] == $id ){
                $action = @$_REQUEST[ 'action' ];
                if( in_array( $action, array( 'lostpassword', 'rp', 'email_verification' ) ))
                    $title = null;
            }                 
        }
    
        return $title;
    }
    
    function loginPageEextraExecutionContent( $content ){
        if( is_page() ){
            global $userMeta, $post;
            
            $login = $userMeta->getSettings( 'login' );
            if( @$login[ 'login_page' ] == $post->ID ){
                
                $userMeta->enqueueScripts( array( 
                    'plugin-framework', 
                    'user-meta',           
                    'validationEngine',
                    'password_strength',
                ) );                      
                $userMeta->runLocalization();               
                
                
                $action = @$_REQUEST[ 'action' ];
                switch ($action) {         
        
                    case 'lostpassword':
                    case 'retrievepassword' :
                        $content = $userMeta->lostPasswordForm( 'show' );
                    break;
                    
                    case 'resetpass' :
                    case 'rp' :
                        $content = $userMeta->resetPasswordForm();
                    break; 
                    
                    case 'email_verification' :
                        $content = $userMeta->emailVerification();
                    break;                         
                                 
                }   
            }                 
        }
    
        return $content;
    }
    
    function pluginUpdateNotice(){
        global $userMeta;
        $cache = get_transient( $userMeta->transient['cache'] );  
        if( @$cache['new_version'] ){
            $plugin = $userMeta->pluginSlug;
            $url = wp_nonce_url( "update.php?action=upgrade-plugin&plugin=$plugin", "upgrade-plugin_$plugin" );                
            $url = admin_url( $url );                                               
            echo $userMeta->showMessage( sprint( __( 'There is a new version <strong>%1$s</strong> of <strong>%2$s</strong> available. <a href="%3$s">update automatically.</a>', $userMeta->name ), $cache['new_version'], $userMeta->title, $url ) );
        }  
    }        
    
    function changeLoginErrorMessage( $user, $username, $password ){
        global $userMeta;
        
	if ( ! is_wp_error( $user ) )
            return $user;  
            
        if( ! in_array( $user->get_error_code(), array( 'invalid_username', 'incorrect_password' ) ) )   
            return $user;  
            
        $login = $userMeta->getSettings( 'login' );
        if( in_array( @$login[ 'login_by' ], array( 'user_email', 'user_login_or_email' ) ) ){
            $title  = $userMeta->loginByArray();
            
            // can be commented from 1.1.3rc2
            if( $user->get_error_code() == 'invalid_username' )
                return new WP_Error( 'invalid_username', sprintf(__('<strong>ERROR</strong>: Invalid %s.'), @$title[ $login[ 'login_by' ] ]));

            if( $user->get_error_code() == 'incorrect_password' )
                return new WP_Error('incorrect_password', sprintf(__('<strong>ERROR</strong>: Incorrect Password. <a href="%s" title="Password Lost and Found">Lost your password</a>?'), wp_lostpassword_url()));
        }

    }   
    
    function showSetLoginPageMsg(){
        global $userMeta;
        
        $login = $userMeta->getSettings( 'login' );
        if( empty( $login[ 'login_page' ] ) ){
            echo "<div id='um_login_page_error' class='error'><p>" . sprintf( __( 'Please set your login page from <a href="%s">here</a>. ', $userMeta->name ), admin_url( "admin.php?page=user-meta-settings#um_settings_login" ) ) . __( 'Login page will be used for login, lost password, reset password and email verification process.', $userMeta->name ) . "</p></div>";
        }
    }  
    
    /**
     * Filter 'login_redirect'. Determine login_redirect url based on settings.
     */
    /*function loginRedirect( $redirect_to, $request, $user  ){
        global $userMeta;
            
        $role = null;   
        if( $user && !is_wp_error( $user ) )    
            $role = $userMeta->getUserRole( $user->ID );            
                                    
        $redirect = $userMeta->getRedirectionUrl( $redirect_to, 'login', $role );   
        return $redirect ? $redirect : $redirect_to;               
    }
    
    function logoutRedirect( $redirect_to ){
        global $userMeta;                                      
        $redirect = $userMeta->getRedirectionUrl( $redirect_to, 'logout' );
        return $redirect ? $redirect : $redirect_to;        
    }                
    
    function registrationRedirect( $redirect_to, $user_id=0 ){
        global $userMeta;                   
        $role = $userMeta->getUserRole( $user_id );               
        return $userMeta->getRedirectionUrl( $redirect_to, 'registration', $role );                
    }*/
    
    function loginUrl( $login_url, $redirect ){
        global $userMeta;            
        $login = $userMeta->getSettings( 'login' );
        
        $loginPage = @$login[ 'login_page' ];
        if( $loginPage ){
            
        	$login_url = get_permalink( $loginPage );            
        	if ( !empty($redirect) )
        		$login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);           
        	//if ( $force_reauth )
        		//$login_url = add_query_arg('reauth', '1', $login_url);                
        }
                    
        return $login_url;
    }

    function logoutUrl( $logout_url, $redirect ){
        global $userMeta;           
        $login = $userMeta->getSettings( 'login' );    
        
        $loginPage = @$login[ 'login_page' ];
        if( $loginPage ){       
            //$redirect   = $_SERVER['REQUEST_URI'];
            $redirect = $userMeta->getRedirectionUrl( $redirect, 'logout' );
        	$args = array( 'action' => 'logout' );               
        	if ( !empty($redirect) ) 
        		$args['redirect_to'] = urlencode( $redirect );       
            
            $url = get_permalink( $loginPage );
            $url = add_query_arg( $args, $url);
            $logout_url = wp_nonce_url( $url, 'log-out' );
        }
     
        return $logout_url;                                    	        
    }
    
    function lostpasswordUrl( $lostpassword_url, $redirect ){
        global $userMeta;            
        $login = $userMeta->getSettings( 'login' );
        
        $loginPage = @$login[ 'login_page' ];
        if( $loginPage ){                  
        	$args = array( 'action' => 'lostpassword' );
        	if ( !empty($redirect) ) 
        		$args['redirect_to'] = $redirect;
        	
            $lostpassword_url = add_query_arg( $args, get_permalink( $loginPage ) );  
        }            
       
        return $lostpassword_url;
    }
    
}
endif;
?>