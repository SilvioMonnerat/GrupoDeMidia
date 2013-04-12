<?php

if( !class_exists( 'umMethods' ) ) :
class umMethods {    
        
    function userUpdateRegisterProcess( $actionType, $formName, $rolesForms=null ){
        global $userMeta, $user_ID;

        $userMeta->enqueueScripts( array( 
            'plugin-framework', 
            'user-meta',           
            'jquery-ui-all',
            'fileuploader',
            'wysiwyg',
            'jquery-ui-datepicker',
            'jquery-ui-slider',
            'timepicker',
            'validationEngine',
            'password_strength',
        ) );                      
        $userMeta->runLocalization();    
        
        $actionType = strtolower( $actionType );
        if( $actionType == 'both' )
            $actionType = $user_ID ? 'profile' : 'registration';          
               
        $userID  = $user_ID;
        $isAdmin = $userMeta->isAdmin();  
        
        // Loading $userID as admin request
        if( $isAdmin ){
            if( isset($_REQUEST['user_id']) )
                $userID = $_REQUEST['user_id'];
        }        
        
        
        if( !empty( $rolesForms ) ){
            $rolesForms = $userMeta->toArray( $rolesForms );            
            if( $userID && in_array( $actionType, array('profile','none') )  ){
                $role   = $userMeta->getUserRole( $userID );
                if( isset( $rolesForms[ $role ] ) ){
                    $formName   = $rolesForms[ $role ];
                }
            }          
        }
                     
                            
        if( empty( $actionType ) )
            return $userMeta->showError( __( 'Please provide a name of action type.', $userMeta->name ), false );
        
        if( empty( $formName ) )
            return $userMeta->showError( __( 'Please provide a form name.', $userMeta->name ), false );        
        
        if( !$userMeta->isValidFormType( $actionType ) )
            return $userMeta->showError( sprintf( __( 'Sorry. type="%s" is not supported.', $userMeta->name ), $actionType ), false );              

        if( ! (  $userMeta->isPro() && $userMeta->isPro ) ){
            if( !($actionType == 'profile' || $actionType == 'none') )
                return $userMeta->showError( "type='$actionType' is only supported, in pro version. Get " . $userMeta->getProLink( 'User Meta Pro' ), "info", false );                                    
        }
                             
                        
        // Checking Permission
        if( $actionType == 'profile' OR $actionType == 'none' ){
            if( !$user_ID )
                return $userMeta->showMessage( __( 'You do not have permission to access this page.', $userMeta->name ), 'info', false );
        }elseif( $actionType == 'registration' ) {
            if( $user_ID AND !$isAdmin )
                return $userMeta->showMessage( sprintf( __( 'You are already registered. See your <a href="%s">profile</a>', $userMeta->name ), $userMeta->getProfileLink() ) , 'info' );
            elseif( !get_option( 'users_can_register' ) )
                return $userMeta->showError( __( 'User registration is currently not allowed.', $userMeta->name ), false);            
        }
                     
        // Process submited request for non-ajax call
        $output = null;
        if( in_array( @$_REQUEST['action_type'], array( 'profile', 'registration' ) ) ) {
            //if( @$_REQUEST['form_key'] == $formName && @$_REQUEST['action_type'] == $actionType ){
                //if( isset( $userMeta->um_post_method_status->InsertUser ) )
                    //$output .= $userMeta->um_post_method_status->InsertUser;
                //$output = $userMeta->ajaxInsertUser();  
            //}
        }
                   
        
        /*$fields     = $userMeta->getData( 'fields' );
        $forms      = $userMeta->getData( 'forms' );                     
        $form       = isset( $forms[$formName] ) ? $forms[$formName] : null;
        
        $form   = $userMeta->getFormData( $formName );
        if( $form === false )
            return $userMeta->ShowError( __( 'Form not found.', $userMeta->name ) );
        
        $form['form_class'] = 'um_user_form ' . !empty( $form['form_class'] ) ? $form['form_class'] : null;
        if( empty( $form['disable_ajax'] ) )
            $form['onsubmit']   = "umInsertUser(this);";
                          
        $extraFields = $userMeta->methodName( 'InsertUser' );
        $extraFields .= wp_nonce_field( 'InsertUser', 'um_post_method_nonce', false );
                   
        $output .= $userMeta->renderPro( 'generateForm', array( 
            'form'          => $form,            
            'fieldValues'   => ( $actionType == 'profile' || $actionType == 'none' ) ? get_userdata( $userID ) : null,
            'actionType'    => $actionType,
            'userID'        => $userID,
            'extraFields'   => $extraFields,
        ) );*/
        
        
        $form   = $userMeta->getFormData( $formName );
        if( is_wp_error( $form ) )
            return $userMeta->ShowError( $form );

        $form['form_class'] = 'um_user_form ' . !empty( $form['form_class'] ) ? $form['form_class'] : null;
        if( empty( $form['disable_ajax'] ) )
            $form['onsubmit']   = "umInsertUser(this);";

        $output .= $userMeta->renderPro( 'generateForm', array( 
            'form'          => $form,     
            'fieldValues'   => ( $actionType == 'profile' || $actionType == 'none' ) ? get_userdata( $userID ) : null,
            'actionType'    => $actionType,
            'userID'        => $userID,
            'methodName'    => 'InsertUser',
        ) );         
       
        
        return $output;        
    }
    
    function userLoginProcess( $formName=null ){
        global $userMeta;
        
        if( ! empty( $formName ) ){
           $userMeta->enqueueScripts( array( 
                'plugin-framework', 
                'user-meta',           
                'jquery-ui-all',
                'fileuploader',
                'wysiwyg',
                'jquery-ui-datepicker',
                'jquery-ui-slider',
                'timepicker',
                'validationEngine',
                'password_strength',
            ) );           
        }else{
            $userMeta->enqueueScripts( array( 'plugin-framework', 'user-meta' ) );
        }                     
        $userMeta->runLocalization();         

        return $userMeta->generateLoginForm( $formName );        
    }
             
}
endif;
