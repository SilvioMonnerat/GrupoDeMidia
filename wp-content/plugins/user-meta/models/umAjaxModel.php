<?php

if( !class_exists( 'umAjaxModel' ) ) :
class umAjaxModel {
        
    function postInsertUser(){
        global $userMeta, $user_ID;
        $userMeta->verifyNonce();
        $errors = new WP_Error();     
        
        /// Determine $userID        
        $userID = $user_ID;
        if( isset( $_REQUEST['user_id']) ){
            if( $userMeta->isAdmin() && $_REQUEST['user_id'] )
                $userID = $_REQUEST['user_id'];
        }
        
        /// $_REQUEST Validation
        $actionType = @$_REQUEST['action_type'];
        if( empty( $actionType ) )
            $errors->add( 'empty_action_type', __( 'Action type not set', $userMeta->name ) );
        if( !isset( $_REQUEST['form_key'] ) )
            $errors->add( 'empty_form_name', __( 'Form name not set', $userMeta->name ) );

        /// Determine $actionType  
        $actionType = strtolower( $actionType );
        if( $actionType == 'both' ){
            if( $user_ID )
                $actionType = 'profile';
            else
                $actionType = 'registration';
        }                                        
        
        /// filter valid key for update
        $validFields = $userMeta->formValidInputField( @$_REQUEST['form_key'] );
        if( !$validFields )
            $errors->add( 'empty_field', __( 'No field to Update', $userMeta->name ) );

        /// Showing error
        if( $errors->get_error_code() )
            return $userMeta->ShowError( $errors );  
        
        // Free version limitation
        //if( ( $actionType <> 'profile' ) && ! ( $userMeta->isPro ) ) 
            //return $userMeta->showError( sprintf( __( 'type="%s" is not supported in free version', $userMeta->name ), $actionType ) );  
        
        /// Assign $fieldName,$fieldData to $userData. Also validating required and unique
        foreach( $validFields as $fieldName => $fieldData ){

            /// user_login is read-only for profile update, so remove it to being $userData
            if( $fieldName == 'user_login' && $actionType == 'profile' )
                continue;

            if( $fieldName == 'user_pass' ){
                if( !$_REQUEST[$fieldName] )
                    continue;
            }        
            
            /// Assigning data to $userData       
            $userData[ $fieldName ] = @$_REQUEST[ $fieldName ];
            
            /// Handle non-ajax file upload
            if( in_array( $fieldData[ 'field_type' ], array( 'user_avatar', 'file' ) ) ){
                if( isset( $_FILES[ $fieldName ] ) ){
                    $extensions = @$fieldData[ 'allowed_extension' ] ? $fieldData[ 'allowed_extension' ] : "jpg, png, gif";
                    $maxSize    = @$fieldData[ 'max_file_size' ] ? $fieldData[ 'max_file_size' ] * 1024 : 1024 * 1024;
                    $file = $userMeta->fileUpload( $fieldName, $extensions, $maxSize );
                    if( is_wp_error( $file ) ){
                        if( $file->get_error_code() <> 'no_file' )                       
                            $errors->add( $file->get_error_code(), $file->get_error_message() );
                    }else{
                        if( is_string( $file ) )
                            $userData[ $fieldName ] = $file;
                    }                       
                }
            }
            
            // For removing value for cache
            //if( $fieldName == 'user_avatar' OR $fieldName == 'file' )
                //$imageCache[] = $userData[$fieldName];
            
            if( $fieldName == 'user_login' || $fieldName == 'user_email' ){
                $fieldData[ 'required' ] = true;
                $fieldData[ 'unique' ]   = true;
            }                    
            if( $fieldData[ 'required' ] ){
                if( !$userData[ $fieldName ] ){
                    $errors->add( 'required', sprintf( __( '%s field is required', $userMeta->name ), $fieldData['field_title'] ) );
                    continue;
                }                        
            }
            if( $fieldData[ 'unique' ] ){
                $available = $userMeta->isUserFieldAvailable( $fieldName, $userData[ $fieldName ], $userID );
                if( ! $available ){
                    $errors->add( 'existing_' . $fieldName, sprintf( __( '%1$s: "%2$s" already taken', $userMeta->name ), $fieldData[ 'field_title' ], $userData[ $fieldName ] ) );					
                }								
            }
        }       

		// If add_user_to_blog set true in UserMeta settings panel
		if( is_multisite() && ($actionType == 'registration') ){
			$registrationSettings = $userMeta->getSettings('registration');
			if( !empty( $registrationSettings['add_user_to_blog'] ) ){
				if( in_array( 'existing_user_login', $errors->get_error_codes() )  )
					unset( $errors->errors['existing_user_login'] );
				if( in_array( 'existing_user_email', $errors->get_error_codes() )  )
					unset( $errors->errors['existing_user_email'] );				
			}				
		}
			
        if( empty( $userData ) )
            return $userMeta->ShowError( __( 'No data to update', $userMeta->name ) );         
        
        // Showing error
        if( $errors->get_error_code() )
            return $userMeta->ShowError( $errors ); 
        
        /// Run Captcha validation after completed all other validation     
        $captchaValidation = $userMeta->isInvalidateCaptcha();
        if( $captchaValidation ){
            $errors->add( 'invalid_captcha', $captchaValidation );  
            return $userMeta->ShowError( $errors );
        }
        
        
        /**
         * Check allowed role for security purpose
         */
        if( isset( $userData['role'] ) ){
            $ignoreRole = true;

            $fieldData = $userMeta->getFieldData( @$_REQUEST['role_field_id'] );
            if( is_array( @$fieldData['allowed_roles'] ) ){
                if( in_array( $userData['role'], $fieldData['allowed_roles'] ) )
                        $ignoreRole = false;
            }
           
            if( $ignoreRole )
                unset( $userData['role'] );
        }

        
        if( $actionType == 'registration' )
            return $userMeta->registerUser( $userData, @$imageCache );
         
        $html = null;
        if( $actionType == 'profile' ){
            if( !$user_ID )
                return $userMeta->showError( __( 'User must be logged in to update profile', $userMeta->name ) );           

            $userData = apply_filters( 'user_meta_pre_user_update', $userData );
            if( is_wp_error( $userData ) )
                return $userMeta->showError( $userData );            
            
            $response = $userMeta->insertUser( $userData, $userID );
            if( is_wp_error( $response ) )
                return $userMeta->showError( $response );  
            
            /// Allow to populate form data based on DB instead of $_REQUEST
            $userMeta->showDataFromDB = true;            
                
            // Removing Cache
            if( isset( $imageCache ) )
                $userMeta->removeCache( 'image_cache', $imageCache, false );  
                              
            do_action( 'user_meta_after_user_update', (object) $response );
              
            $message    = $userMeta->getMsg( 'profile_updated' );
            $html = "<div action_type='$actionType'>" . $userMeta->showMessage( $message ) . "</div>";  
        }
        
        return $userMeta->printAjaxOutput( $html );
    }  
    
    function ajaxValidateUniqueField(){
        global $userMeta;
        $userMeta->verifyNonce( false );
        
        $status = false;               
        if( !isset($_REQUEST['fieldId']) OR !$_REQUEST['fieldValue'] ) return;
        
        $id     = ltrim( $_REQUEST['fieldId'], 'um_field_' );
        $fields = $userMeta->getData( 'fields' );
        
        if( isset( $fields[$id] ) ){
            $fieldData = $userMeta->getFieldData( $id, $fields[$id] );
            $status    = $userMeta->isUserFieldAvailable( $fieldData['field_name'], $_REQUEST['fieldValue'] );
            
            if( !$status ){
                $msg = sprintf( __( '%s already taken', $userMeta->name ), $_REQUEST[ 'fieldValue' ] );
                if( isset($_REQUEST['customCheck']) ){
                     echo "error";
                     die();
                }                        
            }
                                    
            $response[] = $_REQUEST['fieldId'];
            $response[] = isset($status) ? $status: true;
            $response[] = isset( $msg ) ? $msg : null;
                            
            echo json_encode($response);                                        
        }

        die();
    }   
    
    function ajaxShowUploadedFile(){
        global $userMeta;     
        $userMeta->verifyNonce();     
        
        if( isset($_REQUEST['showimage']) ){
            if( isset($_REQUEST['imageurl']) )
                echo "<img src='{$_REQUEST['imageurl']}' />";
            die();
        }
        
        // Update Cache
        if( isset( $_REQUEST['filepath'] ) ){
            if( $_REQUEST['filepath'] ){
                $cache   = $userMeta->getData( 'cache' );
                if( isset( $cache['image_cache'] ) ){
                    if( !in_array( $_REQUEST['filepath'], $cache['image_cache'] ) )
                        $cache['image_cache'][] = $_REQUEST['filepath'];
                }else
                    $cache['image_cache'][] = $_REQUEST['filepath'];
                $userMeta->updateData( 'cache', $cache );
            }
        }
        
        // Showing Image
        $fieldID    = trim( str_replace( 'um_field_', '', @$_REQUEST['field_id'] ) );
        $fields     = $userMeta->getData( 'fields' );
        $field      = @$fields[@$fieldID];          
        if( @$field['field_type'] == 'user_avatar' ){
            if( ! empty( $field['image_size'] ) ){
                $field['image_width']   = $field['image_size'];
                $field['image_height']  = $field['image_size'];
            }else{
                $field['image_width']   = 96;
                $field['image_height']  = 96;
            }          
        }  
        
        if( ! empty( $field ) ){
            echo $userMeta->renderPro( 'showFile', array(
                'filepath'      => @$_REQUEST['filepath'],
                'field_name'    => @$_REQUEST['field_name'],
                'width'         => @$field['image_width'],
                'height'        => @$field['image_height'],
                'crop'          => !empty( $field['crop_image'] ) ? true : false,
                //'readonly'  => @$fieldReadOnly,   // implementation of read-only is not needed.
            ) );                 
        }
                
        die();
    }     
      
    
}
endif;
?>