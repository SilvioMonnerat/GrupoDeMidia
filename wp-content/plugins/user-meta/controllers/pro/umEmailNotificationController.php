<?php

if( !class_exists( 'umEmailNotificationController' ) ) :
class umEmailNotificationController {
    
    function __construct() {
        add_action( 'admin_menu',                   array( $this, 'admin_menu' ) ); 
        add_action( 'user_meta_after_user_register',array( $this, 'registrationEmail' )); 
        add_action( 'user_meta_after_user_update',  array( $this, 'profileUpdateEmail' ));
        add_action( 'user_meta_user_activate',      array( $this, 'userActivate' ) ); 
        add_action( 'user_meta_user_deactivate',    array( $this, 'userDeactivate' ) );
        add_action( 'user_meta_email_verified',     array( $this, 'emailVerified' ) );
        add_action( 'pf_lostpassword_email',        array( $this, 'lostPasswordEmail' ), 10, 3 );        
        
        add_filter( 'user_meta_raw_email',          array( $this, 'addPlaceholder' ) );
    }

    function admin_menu(){
        global $userMeta;
        
        $page = add_submenu_page( 'usermeta', __( 'E-mail Notification', $userMeta->name ), __( 'E-mail Notification', $userMeta->name ), 'manage_options', 'user-meta-email', array( $this, 'init' ));            
        
        $userMeta->addScript( 'jquery-ui-tabs', 'admin', $page );                   
        $userMeta->addScript( 'jquery.ui.all.css', 'admin', $page, 'jqueryui' );   
        
        $userMeta->addScript( 'plugin-framework.js',  'admin', $page );
        $userMeta->addScript( 'plugin-framework.css', 'admin', $page );
        $userMeta->addScript( 'user-meta.js',         'admin', $page ); 
        $userMeta->addScript( 'user-meta.css',        'admin', $page );               
    }  
    
    function init(){
        global $userMeta;
                
        $data = array(
            'registration'          => $userMeta->getEmailsData( 'registration' ), 
            'profile_update'        => $userMeta->getEmailsData( 'profile_update' ),
            'activation'            => $userMeta->getEmailsData( 'activation' ),
            'deactivation'          => $userMeta->getEmailsData( 'deactivation' ),
            'email_verification'    => $userMeta->getEmailsData( 'email_verification' ),
            'lostpassword'          => $userMeta->getEmailsData( 'lostpassword' ),
        );
        
        $userMeta->renderPro( 'emailNotificationPage', array(
            'data'      => $data,
            'roles'     => $userMeta->getRoleList(),
        ), 'email' );            
               
    }
        
    function registrationEmail( $userdata ){                
        $user = new WP_User( $userdata->ID );
        $user->password = $userdata->user_pass;
        $this->_sendEmail( 'registration', $user );
    }
    
    function profileUpdateEmail( $userdata ){                
        $user = new WP_User( $userdata->ID );
        if( !empty( $userdata->user_pass ) )
            $user->password = $userdata->user_pass;
        $this->_sendEmail( 'profile_update', $user );
    }    
    
    function userActivate( $userID ){
        $user = new WP_User( $userID );
        $this->_sendEmail( 'activation', $user );     
    }
    
    function userDeactivate( $userID ){
        $user = new WP_User( $userID );
        $this->_sendEmail( 'deactivation', $user );                 
    }
    
    function emailVerified( $userID ){
        $user = new WP_User( $userID );
        $this->_sendEmail( 'email_verification', $user );                  
    }
    
    function lostPasswordEmail( $passwordResetLink, $resetKey, $userID ){
        global $userMeta;        
        $user = new WP_User( $userID ); 
        
        $data = $userMeta->getEmailsData( 'lostpassword' );        
        $role = $userMeta->getUserRole( $user->ID );
                
        if( ! @$data[ 'user_email' ][ 'um_disable' ] ){
            $mailData = @$data[ 'user_email' ][ $role ];
            $mailData[ 'email' ]        = $user->user_email;
            $mailData[ 'email_type' ]   = 'lostpassword';  
            
            if( strpos( @$mailData[ 'body' ], '%reset_password_link%' ) === false )
                $mailData[ 'body' ] .= sprintf( __('To reset your password, please visit the following address: \r\n\r\n %s', $userMeta->name), "%reset_password_link%");
                                             
            $mailData[ 'body' ] = str_replace( '%reset_password_link%', $passwordResetLink, @$mailData[ 'body' ] );            
            $userMeta->sendEmail( $this->_prepareEmail( $mailData, $user ) );      
        }                
    }
    
    
    function _sendEmail( $key, $user ){
        global $userMeta;        
        $data = $userMeta->getEmailsData( $key );        
        $role = $userMeta->getUserRole( $user->ID );
        
        if( ! @$data[ 'admin_email' ][ 'um_disable' ] ){
            $mailData = @$data[ 'admin_email' ][ $role ];
            $mailData[ 'email' ]        = get_bloginfo( 'admin_email' );
            $mailData[ 'email_type' ]   = $key;
            $userMeta->sendEmail( self::_prepareEmail( $mailData, $user ) ); 
        }
        
        if( ! @$data[ 'user_email' ][ 'um_disable' ] ){
            $mailData = @$data[ 'user_email' ][ $role ];
            $mailData[ 'email' ]        = $user->user_email;
            $mailData[ 'email_type' ]   = $key;
            $userMeta->sendEmail( self::_prepareEmail( $mailData, $user ) );      
        }  
       
    }    
    
    function _prepareEmail( $mailData, $user ){
        global $userMeta;
        
        $mailData = apply_filters( 'user_meta_raw_email', $mailData );
        
        $mailData[ 'subject' ]  = $userMeta->convertUserContent( $user, @$mailData[ 'subject' ] );
        $mailData[ 'body' ]     = $userMeta->convertUserContent( $user, @$mailData[ 'body' ] );        
        
        return $mailData;
    }
    
    function addPlaceholder( $mailData ){
        global $userMeta;
        
        if( $mailData[ 'email_type' ] <> 'registration' )
            return $mailData;
        
        $registration       = $userMeta->getSettings( 'registration' );
        $user_activation    = @$registration[ 'user_activation' ];   
             
        $mailBody = @$mailData[ 'body' ]; 
        
        /**
         * Adding Password to user email if not added
         */                      
        if( ( strpos( $mailBody, '%password%' ) === false ) && ( get_bloginfo( 'admin_email' ) <> @$mailData[ 'email' ] ) )
            $mailBody .= "\n" . sprintf( __( 'Password: %s', $userMeta->name ), '%password%' ) . "\n";

        /**
         * Add/Remove proper placeholder for email_verification, admin_approval and both_email_admin.
         */       
        if( $user_activation == 'email_verification' ){
            if( ( strpos( $mailBody, '%email_verification_url%' ) === false ) && ( get_bloginfo( 'admin_email' ) <> @$mailData[ 'email' ] ) )
                $mailBody .= "\r\n" . sprintf( __( 'Email verification url: %s', $userMeta->name ), '%email_verification_url%' ) . "\n";                                  
        }elseif( $user_activation == 'admin_approval' ){
            if( ( strpos( $mailBody, '%activation_url%' ) === false ) && ( get_bloginfo( 'admin_email' ) == @$mailData[ 'email' ] ) )
                $mailBody .= "\r\n" . sprintf( __( 'Activation url: %s', $userMeta->name ), '%activation_url%' ) . "\n";            
        }elseif( $user_activation == 'both_email_admin' ){
            if( ( strpos( $mailBody, '%email_verification_url%' ) === false ) && ( get_bloginfo( 'admin_email' ) <> @$mailData[ 'email' ] ) )
                $mailBody .= "\r\n" . sprintf( __( 'Email verification url: %s', $userMeta->name ), '%email_verification_url%' ) . "\n";                                  
            if( ( strpos( $mailBody, '%activation_url%' ) === false ) && ( get_bloginfo( 'admin_email' ) == @$mailData[ 'email' ] ) )
                $mailBody .= "\r\n" . sprintf( __( 'Activation url: %s', $userMeta->name ), '%activation_url%' ) . "\n";                       
        }
        $mailData[ 'body' ] = $mailBody;          
        
        return $mailData;              
    }    
    
}
endif;
?>