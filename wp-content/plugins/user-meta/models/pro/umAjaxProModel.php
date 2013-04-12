<?php

if( !class_exists( 'umAjaxProModel' ) ) :
class umAjaxProModel {
    
    function postLostpassword(){
        global $userMeta;
        $login  = $userMeta->getSettings( 'login' );

        $resetPassLink = isset( $login[ 'login_page' ] ) ? get_permalink( $login[ 'login_page' ] ) : null;                
        $response = $userMeta->retrieve_password( $resetPassLink );
        
        if( $response === true )
            $output = $userMeta->showMessage( $userMeta->getMsg( 'check_email_for_link' ) , 'success', false );
        elseif( is_wp_error( $response ) )
            $output = $userMeta->showError( $response->get_error_message(), false );   
            
        if( isset( $_REQUEST['is_ajax'] ) )
            echo @$output;
        else
           return @$output;               
    }
    
    
    /**
     * ajaxLogin function will call with um_login action
     */
    function postLogin(){
        global $userMeta;        
        $userMeta->verifyNonce();
        
        $output = null;
        $user = $userMeta->doLogin();  
        if( $user ){
            if( !is_wp_error( $user ) ){
                
                if( empty( $_REQUEST['is_ajax'] ) ){
                    wp_redirect( $user->redirect_to );
                    exit();
                }
                
                $redirect   = "redirect_to=\"$user->redirect_to\"";
                $html       = $userMeta->showMessage( $userMeta->getMsg( 'login_success' ), 'success', false );
                $html      .= $userMeta->loginResponse( $user );               
                $output     = "<div status=\"success\" $redirect >$html</div>";                 
            }else
                $output     = $userMeta->showError( $user->get_error_message(), false );
        }   
        
        return $userMeta->printAjaxOutput( $output );
          
    }
    
    function ajaxSaveEmailTemplate(){
        global $userMeta;
        if( ! isset( $_REQUEST ) )
            $userMeta->showError( __( 'There is some problem while updating', $userMeta->name ) );
        
        $data = $userMeta->arrayRemoveEmptyValue( $_REQUEST );  
        $data = $userMeta->removeNonArray( $data );
        //$userMeta->dump($data);
              
        $userMeta->updateData( 'emails', stripslashes_deep( $data ) );
        echo $userMeta->showMessage( __( 'Successfully Saved.', $userMeta->name ) );
    }
    
    /**
     * Export UMP fields,forms,settings etc to txt file.
     */
    function ajaxExportUmp(){
        global $userMeta;
               
        $result = array();      
        $result[ 'fields' ] = $userMeta->getData( 'fields' );
        
        if( is_array(@$_REQUEST['includes']) ){
            foreach( $_REQUEST['includes'] as $key ){
                $data = $userMeta->getData( $key );
                if( $data )
                    $result[ $key ] = $data;
            }
        }       
        
        $result = base64_encode( serialize($result) );     
        
        $fileName = 'User Meta Pro (' . get_bloginfo('name') . ') ' . date('Y-m-d_H-i') . '.txt';      
        $userMeta->generateTextFile( $fileName, $result);
        exit();
    }
    
    /**
     * Import UMP fields,forms,settings etc exported by UMP export tools.
     * Give user choice to replace existing data or add new data.
     */
    function ajaxImportUmp(){
        global $userMeta;
        
        /**
         * Reading uploaded file and asssign file content to $data 
         */
        if( empty( $_REQUEST['filepath'] ) )
            return $userMeta->showError( __( 'Something went wrong. File not uploaded', $userMeta->name ) );
        
        $uploads = wp_upload_dir();
        $fullpath = $uploads[ 'basedir' ] . @$_REQUEST[ 'filepath' ];
        
        $data = file_get_contents( $fullpath );
        $data = unserialize( base64_decode( $data ) );
              
        /**
         * Run Import 
         */
        if( isset( $_REQUEST[ 'do_import' ] ) ){
            if( empty( $_REQUEST[ 'includes' ] ) || !is_array( $_REQUEST[ 'includes' ] ) )
                return $userMeta->showError( __( 'Nothing to import!', $userMeta->name ) );
            
            foreach( $_REQUEST[ 'includes' ] as $key => $action ){
                if( empty( $data[ $key ] ) ) continue;
                
                if( $action == 'replace' ){
                    $userMeta->updateData( $key, $data[ $key ] );
                    $imported = true;
                }elseif( $action == 'add' ){
                    if( is_array( $data[ $key ] ) ){
                        $existingData = $userMeta->getData( $key );
                        if( is_array( $existingData ) )
                            $data[ $key ] = $existingData + $data[ $key ];
                        $userMeta->updateData( $key, $data[ $key ] );
                        $imported = true;
                    }
                }
            }

            if( !empty( $imported ) )
                echo $userMeta->showMessage( __( 'Imported completed.', $userMeta->name ) );
            else
                echo $userMeta->showError( __( 'Nothing to import!', $userMeta->name ) );
            
            die();
        }
        
        /**
         * Attempt for import
         */
        elseif( @$_REQUEST[ 'field_id' ] == 'txt_upload_ump_import' ){               
            echo $userMeta->renderPro( 'importUMStep2', array(
                'data' => $data
            ), 'exportImport' );           
        }
    }
    
    /**
     * Perform user exports by ajax call also save user export template.
     */
    function ajaxUserExport(){
        global $userMeta;
        $userMeta->verifyNonce( true );        
        
        $fieldsSelected = array();
        if( is_array( @$_REQUEST[ 'fields' ] ) )
            $fieldsSelected = array_slice( $_REQUEST['fields'], 0, $_REQUEST['field_count'], true );
        
        /**
         * Saving Data 
         */
        if( $_REQUEST['action_type'] == 'save' || $_REQUEST['action_type'] == 'save_export' ){           
            $data = array();          
            $data['fields']         = $fieldsSelected;
            $data['exclude_roles']  = @$_REQUEST['exclude_roles'];
            $data['start_date']     = @$_REQUEST['start_date'];
            $data['end_date']       = @$_REQUEST['end_date'];
            $data['orderby']        = @$_REQUEST['orderby'];
            $data['order']          = @$_REQUEST['order'];
            
            $export = $userMeta->getData( 'export' );
            
            $export['user'][ @$_REQUEST['form_id'] ] = $data;
            
            $userMeta->updateData( 'export', $export );           
        }
        
        /**
         * Export to csv 
         */
        if( $_REQUEST['action_type'] == 'export' || $_REQUEST['action_type'] == 'save_export' ){
            $meta_query = array();
            if( is_array( @$_REQUEST['exclude_roles'] ) ){
                foreach( @$_REQUEST['exclude_roles'] as $role ){
                    $meta_query[] = array(
                        'key' => "wp_capabilities",
                        'value' => "\"$role\"",
                        'compare' => "not like",                       
                    );
                }                   
            }
            
            $args = array(
                'fields'        => 'all_with_meta',
                'meta_query'    => $meta_query,
                'orderby'       => @$_REQUEST['orderby'],
                'order'         => @$_REQUEST['order']
            );
            
            add_action( 'pre_user_query', array( $userMeta, 'filterRegistrationDate' ) );
            $users = get_users( $args );
            remove_action( 'pre_user_query', array( $userMeta, 'filterRegistrationDate' ) );
            
            /// Add header row for csv
            $fileData = array();
            $fileData[] = $fieldsSelected;
     
            /// Add user data for csv
            foreach( $users as $user ){
                $userData = array();
                foreach( $fieldsSelected as $key => $val ){
                    $fieldValue     = !empty( $user->$key ) ? $user->$key : null;
                    if( is_array( $fieldValue ) || is_object( $fieldValue ) )
                        $userData[$key] = implode( ',', (array) $fieldValue ); 
                    else
                        $userData[$key]   = $fieldValue;
                }                    
                $fileData[] = $userData;
            } 
            
            $fileName = 'User Export (' . get_bloginfo('name') . ') ' . date('Y-m-d_H-i') . '.csv';
            $userMeta->generateCsvFile( $fileName, $fileData );
        }
    }
    
    /**
     * Build user export forms in admin section and generate new form by ajax call.
     * verifyNonce is calling inside.
     */    
    function ajaxUserExportForm( $populateAll=false ){
        global $userMeta;
                
        $fieldsDefault  = $userMeta->defaultUserFieldsArray();        
        $fieldsMeta     = array();       
        $extraFields    = $userMeta->getData('fields');
        if( is_array( $extraFields ) ){
            foreach($extraFields as $data){
                if( !empty( $data['meta_key'] ) ){
                    $fieldTitle = ! empty( $data['field_title'] ) ? $data['field_title'] : $data['meta_key'] ;
                    $fieldsMeta[ $data['meta_key'] ] = $fieldTitle;
                }
            } 
        }
        $fieldsAll = array_merge( $fieldsDefault, $fieldsMeta );
        
        $roles = $userMeta->getRoleList();
        
        if( $populateAll ){
            $export      = $userMeta->getData('export');
            $formsSaved = @$export[ 'user' ];
            if( is_array( $formsSaved ) && !empty( $formsSaved ) ){
                foreach( $formsSaved as $formID => $formData ){
                    $fieldsSelected = $formData[ 'fields' ];
                    $fieldsAvailable = $fieldsAll;
                    if( is_array( $fieldsSelected ) ){
                        foreach( $fieldsSelected as $key => $val )
                            unset( $fieldsAvailable[$key] );
                    } 
                    
                    echo $userMeta->renderPro( 'exportForm', array(
                        'formID'            => $formID,
                        'fieldsSelected'    => $fieldsSelected,
                        'fieldsAvailable'   => $fieldsAvailable,       
                        'roles'             => $roles,
                        'formData'          => $formData,
                    ), 'exportImport' );                    
                }
                
                $break = true;
            }
            
            $newUserExportFormID = (int) $userMeta->maxKey( $formsSaved ) + 1;
            echo "<input type=\"hidden\" id=\"new_user_export_form_id\" value=\"$newUserExportFormID\" />";            
        }
        
        /// For default or new form
        if( !@$break ){            
            $formID = !empty($_REQUEST['form_id']) ? $_REQUEST['form_id'] : 'default';           
            if( $formID <> 'default' )
                $userMeta->verifyNonce( true );
            
             echo $userMeta->renderPro( 'exportForm', array(
                'formID'            => $formID,
                'fieldsSelected'    => array(),
                'fieldsAvailable'   => $fieldsAll,       
                'roles'             => $roles,
            ), 'exportImport' );               
        }     

    }
    
    /**
     * Remove User Export Template by ajax call
     */
    function ajaxRemoveExportForm(){
        global $userMeta;
        $userMeta->verifyNonce( true );
        
        $export     = $userMeta->getData('export');
        
        if( !empty( $export[ 'user' ][ $_REQUEST['form_id'] ] ) ){
            unset( $export[ 'user' ][ $_REQUEST['form_id'] ] );
            $userMeta->updateData( 'export', $export );
        }
    }
    
}
endif;