<?php

if (!class_exists('umShortcodesController')) :
class umShortcodesController {
    
    function __construct(){
        global $userMeta;
        
        add_shortcode( 'user-meta', array( $this, 'init' ) );
                                     
        //add_action( 'wp_ajax_um_insert_user',                   array( $userMeta, 'ajaxInsertUser' ) );
        //add_action( 'wp_ajax_nopriv_um_insert_user',            array( $userMeta, 'ajaxInsertUser' ) );
        //add_action( 'wp_ajax_um_login',                         array( $userMeta, 'ajaxLogin' ) );
        //add_action( 'wp_ajax_nopriv_um_login',                  array( $userMeta, 'ajaxLogin' ) );          
        
        add_action( 'wp_ajax_um_show_uploaded_file',            array( $userMeta, 'ajaxShowUploadedFile' ) );
        add_action( 'wp_ajax_nopriv_um_show_uploaded_file',     array( $userMeta, 'ajaxShowUploadedFile' ) );
          
        add_action( 'wp_ajax_um_validate_unique_field',         array( $userMeta, 'ajaxValidateUniqueField' ) );
        add_action( 'wp_ajax_nopriv_um_validate_unique_field',  array( $userMeta, 'ajaxValidateUniqueField' ) );                                                  
    }
    
   
    function init( $atts ){     
        global $userMeta;
        
        extract( shortcode_atts( array(
                'type'  => 'profile', // profile,registration,both,none
    		'form'  => null,   		
                'diff'  => null,
    	), $atts ) );
                
        
        $actionType = strtolower( $type );
        if( in_array( $actionType, array( 'registration', 'profile', 'both', 'none' ) ) )
            return $userMeta->userUpdateRegisterProcess( $actionType, $form, $diff ); 
        elseif( $actionType == 'login' )
            return $userMeta->userLoginProcess( $form );
        else
            return $userMeta->showError( sprintf( __( 'type="%s" is invalid.', $userMeta->name ), $actionType ), false );             
    }
    
    
        
}
endif;
?>