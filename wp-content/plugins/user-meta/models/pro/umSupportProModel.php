<?php

if( !class_exists( 'umSupportProModel' ) ) :
class umSupportProModel {    
        
    function generateLoginForm( $formName ){
        global $userMeta;
        
        if( is_user_logged_in() )
            return $this->loginResponse();         
        
        $loginSettings  = $userMeta->getSettings( 'login' );
        $methodName     = 'Login';
        
        $output = null;                
            
        if( ! empty( $formName ) ){
            $form   = $userMeta->getFormData( $formName );
            if( is_wp_error( $form ) )
                return $userMeta->ShowError( $form );

            $form['form_class'] = 'um_login_form ' . !empty( $form['form_class'] ) ? $form['form_class'] : null;
            if( empty( $form['disable_ajax'] ) )
                $form['onsubmit']   = "umLogin(this);";

            $output .= $userMeta->renderPro( 'generateForm', array( 
                'form'          => $form,            
                'actionType'    => 'login',
                'methodName'    => $methodName,
            ) );   
            
        }else{
            $title  = $userMeta->loginByArray();
            if( isset( $userMeta->um_post_method_status->$methodName ) )
                $output .= $userMeta->um_post_method_status->$methodName;                

            $output .= $userMeta->renderPro( 'loginForm', array(
                //'loginBy'           => $loginSettings[ 'login_by' ] ,
                'loginTitle'        => @$title[ $loginSettings[ 'login_by' ] ],
                'disableAjax'       => @$loginSettings[ 'disable_ajax' ],
                'methodName'        => $methodName,
            ), 'login' );            
        }
        
        
        if( empty( $loginSettings['disable_lostpassword'] ) ){
            $output .= $userMeta->renderPro( 'lostPasswordForm', array(
                'disableAjax'   => @$loginSettings[ 'disable_ajax' ],
                'visibility'    => ! empty( $loginSettings['disable_ajax'] ) ? 'show' : 'hide',
            ), 'login' );            
        }
                 
        return $output;               
    }
    
    /**
     * Generate login form. show loginResponse() if user logedin
     * 
     * @author  Khaled Saikat
     * @since   1.1.2
     * @param   string : shortcode, widget, template.
     * @return  string containing the form
     */
    function lgoinForm(){
        global $userMeta;
        
        //if( is_user_logged_in() )
            //return $this->loginResponse();     
        
        $login = $userMeta->getSettings( 'login' );        
        $title  = $userMeta->loginByArray();
        
        return $userMeta->renderPro( 'loginForm', array(
            'loginBy'           => @$login[ 'login_by' ] ,
            'loginTitle'        => @$title[ $login[ 'login_by' ] ],
            'disableAjax'       => @$login[ 'disable_ajax' ],
        ), 'login' );
    }
    
    /**
     * Handle resetPassword request, key validation, password reset
     */
    function lostPasswordForm( $visibility=null ){
        global $userMeta;
        
        //if( is_user_logged_in() ) return;        
        
        $login = $userMeta->getSettings( 'login' );
        
        if( !$visibility )
            $visibility = @$login[ 'disable_ajax' ] ? 'show' : 'hide';
        
        $html = null;
        if( !@$_REQUEST['is_ajax'] && @$_REQUEST['method_name'] == 'lostpassword' )
            $html .= $userMeta->ajaxLostpassword();         
                
        $html .= $userMeta->renderPro( 'lostPasswordForm', array(
            'disableAjax'   => @$login[ 'disable_ajax' ],
            'visibility'    => $visibility,
        ), 'login' ); 
          
        return $html;     
    }    

    /**
     * LoggedIn Profile.
     * 
     * @author  Khaled Saikat
     * @uses    lgoinForm()
     * @since   1.1.2
     * @param   string : shortcode, widget, template.
     * @return  string containing the form
     */    
    function loginResponse( $user = null ){
        global $userMeta;
        
        if( empty( $user ) )
            $user = wp_get_current_user();
        
        $role = $userMeta->getUserRole( $user->ID );        
        $login = $userMeta->getSettings( 'login' );
        
        return $this->convertUserContent( $user, @$login[ 'loggedin_profile' ][ $role ]  );
    }
    
    
    function resetPasswordForm(){
        global $userMeta;
        $action = @$_GET[ 'action' ];
        
        if( !in_array( $action, array( 'resetpass', 'rp' ) ) )
            return false;
        
        $user = $userMeta->check_password_reset_key( @$_GET['key'], @$_GET['login'] );                    
    	if ( !is_wp_error($user) ){
            if( isset( $_POST['pass1'] ) && isset( $_POST['pass2'] ) ){
                if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] ) 
                    $errors = new WP_Error('password_reset_mismatch', $userMeta->getMsg( 'password_reset_mismatch' ) );
                elseif ( isset($_POST['pass1']) && !empty($_POST['pass1']) ){
                    $userMeta->reset_password($user, $_POST['pass1']);
                    return $userMeta->showMessage( $userMeta->getMsg( 'password_reseted' ), 'success', false );
                }                              
            }         	   
    	}else
            return $userMeta->showError( $user->get_error_message(), false );
            
                      
        return $userMeta->renderPro( 'resetPasswordForm', array(
            'error'     => @$errors,
        ), 'login' );
    }
    
    function emailVerification(){
        global $userMeta;
        
        $email  = @$_REQUEST[ 'email' ];
        $key    = @$_REQUEST[ 'key' ];
        $key    = urldecode( $key );
        
        if( !$email || !$key )
            return $userMeta->showError(  $userMeta->getMsg( 'invalid_parameter' )  );
        
        $user = get_user_by( 'email', $email );  
        if( !$user )
            return $userMeta->showError(  $userMeta->getMsg( 'email_not_found' )  );
            
        if( get_user_meta( $user->ID, $userMeta->prefixLong . 'user_status', true ) == 'active' )
            return $userMeta->showMessage( $userMeta->getMsg( 'user_already_activated' ) );
        
        $preSavedKey = get_user_meta( $user->ID, $userMeta->prefixLong . 'email_verification_code', true );        
        if( $preSavedKey == $key){
            $registration       = $userMeta->getSettings( 'registration' );
            $user_activation    = $registration[ 'user_activation' ];
            
            if( $user_activation == 'email_verification' ){
                $status = 'active';
            }elseif( $user_activation == 'both_email_admin' ){
                $status = 'inactive';
            }
            
            $status = $status == 'active' ? 'active' : @$status;                
            update_user_meta( $user->ID, $userMeta->prefixLong . 'user_status', $status );
            do_action( 'user_meta_email_verified', $user->ID );
            
            return $userMeta->showMessage( $userMeta->getMsg( 'email_verified' ) );         
        }else
            return $userMeta->showError( $userMeta->getMsg( 'invalid_key' ) ); 
    }
    

    
    /**
     * Do login if user not logged on.
     * @return onSuccess : redirect_url | onFailed : WP_Error or false
     */
    function doLogin( $creds=array() ){
        global $userMeta;
        
        if( is_user_logged_in() )
            return false;        
        
        $loginSettings	= $userMeta->getSettings('login');
        
        if( empty( $creds['user_login'] ) ){
            $user = self::findUserForLogin( $loginSettings );
            if( is_wp_error( $user ) )
                return $user;   
            $userName = $user->user_login;
        }else
            $userName = $creds['user_login'];
        
        
        $userPass   = ! empty( $creds['user_pass'] ) ? $creds['user_pass'] : @$_REQUEST['user_pass'];
        $remember   = ! empty( $creds['remember'] ) ? $creds['remember'] : @$_REQUEST['remember'];
                 
        $user   = wp_authenticate( $userName, $userPass );
        
        if( is_wp_error( $user ) )
            return $user;        

        // if Prevent user login for non-member of blog is set.
        if( is_multisite() ){
            global $blog_id;
            if( !empty( $loginSettings['blog_member_only'] ) ){
                $userID		= username_exists( sanitize_user($userName, true) );
                if( $userID ){
                    if( !is_user_member_of_blog($userID) )
                        return new WP_Error( 'not_member_of_blog', $userMeta->getMsg( 'not_member_of_blog' ) );
                }
            }
        }       
	                        
        $secure_cookie = '';
        
        if( force_ssl_admin() )        
            $secure_cookie = true;
        
        // If the user wants ssl but the session is not ssl, force a secure cookie.
        if( !force_ssl_admin() ){
            if ( $user = get_user_by('login', sanitize_user($userName) ) ) {
                if ( get_user_option('use_ssl', $user->ID) ) {
                    $secure_cookie = true;
                    force_ssl_admin(true);
                }
            }            
        }
        
	//if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		//$secure_cookie = false;        


        $user = wp_signon( array(
            'user_login'    => $userName,
            'user_password' => $userPass,
            'remember'      => $remember ? true : false,
        ), $secure_cookie );         

        
        if( is_wp_error( $user ) )
            return $user;
           
        $role = $userMeta->getUserRole( $user->ID ); 
        $redirect_to = $role == 'administrator' ? admin_url() : home_url();
        $redirect_to = $userMeta->getRedirectionUrl( $redirect_to, 'login', $role );   
        
        if( $userMeta->isFilterEnable( 'login_redirect' ) )
            $redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);            
                   
        $user->redirect_to = $redirect_to;                     
        
        return $user;
    }
    
    
    /**
     * fined user_login form user_login or user_email
     */
    function findUserForLogin( $loginSettings ){
        global $userMeta;
        
        $loginBy    = @$loginSettings[ 'login_by' ];
        $userLogin  = @$_REQUEST[ 'user_login' ];     
        
        if( $loginBy == 'user_login_or_email' ){
            $user = get_user_by( 'email', $userLogin );
            if( $user === false )
                $user = get_user_by( 'login', $userLogin );            
        }elseif( $loginBy == 'user_email' )
            $user = get_user_by( 'email', $userLogin );
        else
            $user = get_user_by( 'login', $userLogin );
        
        if( $user === false ){
            $title  = $userMeta->loginByArray();
            return new WP_Error( 'invalid_login', $userMeta->getMsg( 'invalid_login', @$title[ $loginBy ] ) );
        }
        
        return $user;
    }
    
    //sleep from version 1.1.3rc2
    /**
     * Determine, is it login request. Useed with http post request
     */
    function isLoginRequest(){                
        if( !is_user_logged_in() && @$_POST['action'] == 'um_login' && @$_POST['action_type'] == 'login' && @$_REQUEST['pf_nonce'] && isset( $_POST['user_login'] ) && isset( $_POST['user_pass'] ) )
            return true;
        return false;
    }
    
    function disableAdminRow( $id ){
        if( in_array( $id, array( 'heading_0', 'heading_1', 'heading_2', 'heading_3' ) ) ){
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    id = <?php echo str_replace( 'heading_', '', $id ); ?>;
                    jQuery( "h3:eq(" + id + ")" ).hide();
                });
            </script>               
            <?php             
        }else{
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery( "#<?php echo $id; ?>" ).parents( "tr" ).hide();
                });
            </script>               
            <?php               
        }
    }
    
    function isResetPasswordRequest(){
        $action = @$_GET[ 'action' ];
        if( in_array( $action, array( 'lostpassword', 'retrievepassword', 'resetpass', 'rp' ) ) )
            return true;
        return false;
        
    }
    
    function registerUser( $userData, $fileCache=null ){
        global $userMeta;
        
        /// $userData: array. 
        $userData = apply_filters( 'user_meta_pre_user_register', $userData );
        if( is_wp_error( $userData ) )
            return $userMeta->showError( $userData );      

        if( is_multisite() && wp_verify_nonce( @$_POST['um_newblog'], 'blogname' ) && !empty( $_POST['blogname'] ) ){
            $blogData = wpmu_validate_blog_signup($_POST['blogname'], $_POST['blog_title']); 
            if( $blogData['errors']->get_error_code() )
                return $userMeta->showError( $blogData['errors'] );			
        }    
		
        // If add_user_to_blog set true in UserMeta settings panel
        $userID = null;
        if( is_multisite() ){
            $registrationSettings = $userMeta->getSettings('registration');
            if( !empty( $registrationSettings['add_user_to_blog'] ) ){
                global $blog_id;
                $user_login = sanitize_user($userData['user_login'], true);
                $userID		= username_exists($user_login);
                if( $userID ){
                    if( !is_user_member_of_blog($userID) )
                        add_user_to_blog( $blog_id, $userID, get_option('default_role','subscriber') );
                    else
                        $userID	= null;
                }				
            }			
        }
                
        $response = $userMeta->insertUser( $userData, $userID );  
        if( is_wp_error( $response ) )
            return $userMeta->showError( $response );

        if( isset($blogData) ){
            $responseBlog = $this->registerBlog( $blogData, $userData );  
            if( is_wp_error( $responseBlog ) )
                return $userMeta->showError( $responseBlog );			
        }
        
        /// Allow to populate form data based on DB instead of $_REQUEST
        $userMeta->showDataFromDB = true;         
            
        $registrationSettings = $userMeta->getSettings( 'registration' );
        $activation = $registrationSettings[ 'user_activation' ];
        if( $activation == 'auto_active' )
            $msg    = $userMeta->getMsg( 'registration_completed' );
        elseif( $activation == 'email_verification' )
            $msg    = $userMeta->getMsg( 'sent_verification_link' );
        elseif( $activation == 'admin_approval' )
            $msg    = $userMeta->getMsg( 'wait_for_admin_approval' );
        elseif( $activation == 'both_email_admin' )
            $msg    = $userMeta->getMsg( 'sent_link_wait_for_admin' );
            
        if( $fileCache )
            $userMeta->removeCache( 'image_cache', $fileCache, false );
        
        if( $activation == 'auto_active' ){
            if( !empty( $registrationSettings['auto_login'] ) )
                self::doLogin( $response );
        }

        
        do_action( 'user_meta_after_user_register', (object) $response );                  
        
        $html = $userMeta->showMessage( $msg );

        if( isset($responseBlog) )
                $html .= $userMeta->showMessage( $responseBlog );
        
        $role = $userMeta->getUserRole( $response[ 'ID' ] );
        $redirect_to = $userMeta->getRedirectionUrl( null, 'registration', $role );
        
        if( $userMeta->isFilterEnable( 'registration_redirect' ) )
            $redirect_to = apply_filters( 'registration_redirect', $redirect_to, $response[ 'ID' ] );
        
        if( $redirect_to ){
            if( empty( $_REQUEST['is_ajax'] ) ){
                wp_redirect( $redirect_to );
                exit();
            }
            
            $timeout = $activation == 'auto_active' ? 3 : 5;
            $html .= $userMeta->jsRedirect( $redirect_to, $timeout );
        }
                   
        
        $html = "<div action_type=\"registration\">" . $html . "</div>";    
        return $userMeta->printAjaxOutput( $html );                          
    }
    

	function registerBlog( $blogData, $userData ){
		global $userMeta;					
		extract($blogData);
		
		$active_signup = get_site_option( 'registration' );
		if ( !$active_signup )
			$active_signup = 'all';

		$active_signup = apply_filters( 'wpmu_active_signup', $active_signup ); // return "all", "none", "blog" or "user"
		if ( ! ( $active_signup == 'all' || $active_signup == 'blog' ) )
			return false;

		if ( $errors->get_error_code() ) 
			return $errors;

		//$public = (int) $_POST['blog_public'];
		//$meta = array ('lang_id' => 1, 'public' => $public);
		//$meta = apply_filters( 'add_signup_meta', $meta );
		
		if( empty( $userData['user_login'] ) || empty( $userData['user_email'] ) )
			return new WP_Error( 'login_email_required', $userMeta->getMsg( 'login_email_required' ) );
		
		$meta = '';

		wpmu_signup_blog($domain, $path, $blog_title, $userData['user_login'], $userData['user_email'], $meta);
		
		$msg = null;
		$msg .= sprintf( __( 'Congratulations! Your new site, %s, is almost ready.' ), "<a href='http://{$domain}{$path}'>{$blog_title}</a>" );
		$msg .= __( 'But, before you can start using your site, <strong>you must activate it</strong>.' );
		$msg .= sprintf( __( 'Check your inbox at <strong>%s</strong> and click the link given.' ),  $userData['user_email']);
		$msg .= __( 'If you do not activate your site within two days, you will have to sign up again.' );

		$msg = apply_filters( 'user_meta_blog_signup_msg', $msg, "<a href='http://{$domain}{$path}'>{$blog_title}</a>", $userData['user_email'] );
		
                do_action( 'signup_finished' );
		return $msg;
	}
    
    function isInvalidateCaptcha(){
         global $userMeta;
         
         // Checking existance of captcha field
         if( !isset($_POST["recaptcha_challenge_field"]) )
            return false;
            
        // If key are not set then no validation
        $general    = $userMeta->getSettings( 'general' );
        if( ( !@$general['recaptcha_public_key'] ) || ( !@$general['recaptcha_private_key'] ) )
            return false;       
        
        //if( ! function_exists( 'recaptcha_check_answer' ) )             
            require_once( $userMeta->pluginPath . '/framework/helper/recaptchalib.php');
        
        $privateKey = null;
        if( isset( $general['recaptcha_private_key'] ) )
            $privateKey = $general['recaptcha_private_key'];
        
        $resp = recaptcha_check_answer ($privateKey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);
        if (!$resp->is_valid){
            $error = $resp->error;
            if( $error == 'incorrect-captcha-sol' )
                $error = $userMeta->getMsg( 'incorrect_captcha' );
            return $error;
        }
                   
        return false;         
    }    
    
    /**
     * Convert content for user provided by %field_name%
     * Supported Extra filter: blog_title, blog_url, avatar, logout_link, admin_link
     * @param $user: WP_User object
     * @param $data: (string) string for convertion
     * @return (string) converted string
     */
    function convertUserContent( $user, $data, $extra=array() ){
        preg_match_all( '/\%[a-zA-Z0-9_-]+\%/i', $data, $matches); 
        if( is_array( @$matches[0] ) ){
            $patterns = $matches[0];
            $replacements = array();
            foreach( $patterns as $key => $pattern ){
                $fieldName = strtolower( trim( $pattern, '%' ) );
                if( $fieldName == 'site_title' )
                    $replacements[ $key ] = get_bloginfo( 'name' );
                elseif( $fieldName == 'site_url' )
                    $replacements[ $key ] = site_url();
                elseif( $fieldName == 'avatar' )
                    $replacements[ $key ] = get_avatar( $user->ID );
                elseif( $fieldName == 'login_url' )
                    $replacements[ $key ] = wp_login_url();                    
                elseif( $fieldName == 'logout_url' )
                    $replacements[ $key ] = wp_logout_url();
                elseif( $fieldName == 'lostpassword_url' )
                    $replacements[ $key ] = wp_lostpassword_url();                                         
                elseif( $fieldName == 'admin_url' )
                    $replacements[ $key ] = admin_url();
                elseif( $fieldName == 'activation_url' )
                    $replacements[ $key ] = self::userActivationUrl( 'activate', $user->ID, false );
                elseif( $fieldName == 'email_verification_url' )
                    $replacements[ $key ] = self::emailVerificationUrl( $user );
                elseif( $fieldName == 'login_form' )
                    $replacements[ $key ] = self::lgoinForm();
                elseif( $fieldName == 'lostpassword_form' )
                    $replacements[ $key ] = self::lostPasswordForm();
                elseif( !empty( $user->$fieldName ) )
                    $replacements[ $key ] = is_array( $user->$fieldName ) ? implode( ',', $user->$fieldName ) : $user->$fieldName;
                else
                    $replacements[ $key ] = '';                                              
            }
            $data = str_replace($patterns, $replacements, $data);
        }    

        return $data;     
    }
    
    // TODO: referer
    /**
     * Get redirection url from settings.
     * @param $redirect_to: get $redirect_to from filter.
     * @param $action: login, logout or registration
     * @param $role: role name
     * @return $redirect_to: url
     */
    function getRedirectionUrl( $redirect_to, $action, $role=null ){
        global $userMeta, $user_ID;
        
        if( !$role )
            $role = $userMeta->getUserRole( $user_ID );
        
        $redirection       = $userMeta->getSettings( 'redirection' );
        
        if( @$redirection[ 'disable' ] )
            return $redirect_to;
        
        $redirectionType = @$redirection[ $action ][ $role ];
        
        $scheme = is_ssl() ? 'https://' : 'http://';
        
        if( $redirectionType == 'same_url' ){
            if( ! empty( $_REQUEST[ '_wp_http_referer' ] ) )
                $redirect_to = $scheme . esc_attr( $_SERVER[ 'HTTP_HOST' ] ) . esc_attr( $_REQUEST[ '_wp_http_referer' ] ); 
            elseif( ! empty( $_SERVER[ 'REQUEST_URI' ] ) )
                $redirect_to = $scheme . esc_attr( $_SERVER[ 'HTTP_HOST' ] ) . esc_attr( $_SERVER[ 'REQUEST_URI' ] );              
        }elseif( $redirectionType == 'referer' ){
            if( !empty( $_REQUEST['redirect_to'] ) )
                $redirect_to = esc_attr( $_REQUEST['redirect_to'] );
            elseif( ! empty( $_REQUEST[ 'pf_http_referer' ] ) )
                $redirect_to = esc_attr( $_REQUEST['pf_http_referer'] );
            elseif( ! empty( $_REQUEST[ '_wp_http_referer' ] ) )
                $redirect_to = $scheme . esc_attr( $_SERVER[ 'HTTP_HOST' ] ) . esc_attr( $_REQUEST[ '_wp_http_referer' ] );    
            elseif( ! empty( $_SERVER[ 'HTTP_REFERER' ] ) )
                $redirect_to = esc_attr( $_SERVER[ 'HTTP_REFERER' ] );
            elseif( ! empty( $_SERVER[ 'REQUEST_URI' ] ) )
                $redirect_to = $scheme . esc_attr( $_SERVER[ 'HTTP_HOST' ] ) . esc_attr( $_SERVER[ 'REQUEST_URI' ] );          
             
        }elseif( $redirectionType == 'home' )
            $redirect_to = home_url();
        elseif( $redirectionType == 'profile' )
            $redirect_to = $userMeta->getProfileLink();
        elseif( $redirectionType == 'dashboard' )
            $redirect_to = admin_url();
        elseif( $redirectionType == 'login_page' )
            $redirect_to = wp_login_url();         
        elseif( $redirectionType == 'custom_url' ){
            if( isset( $redirection[ $action . '_url' ][ $role ] ) )
                $redirect_to = $redirection[ $action . '_url' ][ $role ];
        } 
        
        return $redirect_to;    
    }  
    
    /**
     * Generate activation/deactivation link with or without nonce.
     */
    function userActivationUrl( $action, $userID, $addNonce = true ){
        $url    = admin_url( 'users.php' );
        $url    = add_query_arg( array(
			'action'	=>	$action,
			'user'		=>	$userID
		), $url);
        
        if( $addNonce )
		  $url  =	wp_nonce_url( $url, 'um_activation' ); 
           
        return $url;      
    }  
    
    /**
     * Generate activation/deactivation link with or without nonce.
     */
    function emailVerificationUrl( $user ){
        global $userMeta;
        $login  = $userMeta->getSettings( 'login' );        
        if( !$login[ 'login_page' ] ) return;
       
        $hash   = get_user_meta( $user->ID, $userMeta->prefixLong . 'email_verification_code', true ); 
        if( !$hash ){
            $hash   = wp_generate_password(30, false);
            update_user_meta( $user->ID, $userMeta->prefixLong . 'email_verification_code', $hash );
        }
               
        $url    = get_permalink( $login[ 'login_page' ] );
        $url    = add_query_arg( array(
            'action'	=> 'email_verification',
            'email'         => $user->user_email,
            'key'		=> urlencode( $hash ),
	), $url);
                           
        return $url;      
    }       
    
    
    /**
     * Generate role based email template
     * @param $slugs : array containing two value without keys. e.g array( 'registration', 'user_email' )
     * @param $data  : array containing data to populate
     * @return html
     */
    function buildRolesEmailTabs( $slugs=array(), $data=array() ){
        global $userMeta;        
        $roles  = $userMeta->getRoleList();
        
        foreach( $roles as $key => $val ){
            $forms[ $key ] = $userMeta->renderPro( 'singleEmailForm', array(
                'slug'      => "{$slugs[0]}[{$slugs[1]}][$key]",
                'from_email'=> @$data[ $slugs[0] ][ $slugs[1] ][ $key ][ 'from_email' ],
                'from_name' => @$data[ $slugs[0] ][ $slugs[1] ][ $key ][ 'from_name' ],
                'format'    => @$data[ $slugs[0] ][ $slugs[1] ][ $key ][ 'format' ],
                'subject'   => @$data[ $slugs[0] ][ $slugs[1] ][ $key ][ 'subject' ],
                'body'      => @$data[ $slugs[0] ][ $slugs[1] ][ $key ][ 'body' ],
                /*'after_form'=> $userMeta->createInput( null, 'checkbox', array(
                                    'label'         => __( 'Copy this form data to all others role', $userMeta->name ),
                                    'enclose'       => 'p',
                                    'onclick'       => 'copyFormData(this)',
                                    'class'         => 'asdf',
                                ) ),  */                  
            ), 'email' );
        }   
        
                     
        $html = $userMeta->jQueryRolesTab( "{$slugs[0]}-{$slugs[1]}", $roles, $forms );        
        $html .= $userMeta->createInput( "{$slugs[0]}[{$slugs[1]}][um_disable]", 'checkbox', array(
            'label'         => __( 'Disable this notification', $userMeta->name ),
            'value'         => @$data[ $slugs[0] ][ $slugs[1] ][ 'um_disable' ] ? true : false,
            'enclose'       => 'p',
        ) ); 
        
        return $html;
    }     
    
    /**
     * Callback hook for "pre_user_query". Filter users by registration date
     */
    function filterRegistrationDate( $query ){
            global $wpdb;           
            
            if ( ! empty( $_REQUEST['start_date'] ) )
                $query->query_where = $query->query_where . $wpdb->prepare( " AND $wpdb->users.user_registered >= %s", $_REQUEST['start_date'] );

            if ( ! empty( $_REQUEST['end_date'] ) )
                $query->query_where = $query->query_where . $wpdb->prepare( " AND $wpdb->users.user_registered <= %s", $_REQUEST['end_date'] );
                       
            return $query;        
    }    



}
endif;
