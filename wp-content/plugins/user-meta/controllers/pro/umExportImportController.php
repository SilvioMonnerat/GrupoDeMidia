<?php

if( !class_exists( 'umExportImportController' ) ) :
class umExportImportController {
    
    function __construct(){
        add_action( 'admin_menu',               array( $this, 'admin_menu' ) );             
        add_action( 'wp_ajax_um_user_import',   array( $this, 'ajaxUserImport' ) );     
    }        
    
    
    function admin_menu(){
        global $userMeta;
        
   	$page = add_submenu_page( 'usermeta', __( 'Export & Import', $userMeta->name ), __( 'Export & Import', $userMeta->name ), 'manage_options', 'user-meta-import-export', array( $this, 'userImportExportForm' ) );                                                               
    }
    
    
    function userImportExportForm(){
        global $userMeta;     
        
         $userMeta->enqueueScripts( array( 
            'plugin-framework', 
            'user-meta',           
            'jquery-ui-all',
            'fileuploader',
            'jquery-ui-sortable',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
            'jquery-ui-datepicker',
            'jquery-ui-dialog',
            'jquery-ui-progressbar',
        ) );                      
        $userMeta->runLocalization(); 
        
        $cache = $userMeta->getData( 'cache' );            
        $csvCache = @$cache['csv_files'];
                           
        //importPage            
        $userMeta->renderPro( 'importExportPage', array(
            'csvCache'  => $csvCache,
            'maxSize'   => (20 * 1024 * 1024), //20M
        ), 'exportImport' );           
    }
    
    
    function ajaxUserImport(){
        global $userMeta;
        $userMeta->verifyNonce( true );
        
        if( !current_user_can( 'add_users' ) )
            wp_die( __('You do not have sufficient permissions to access this page.', $userMeta->name ) );
        
        if( @$_REQUEST['step'] == 'one' ){
            $key = $this->updateCsvCache();
            $this->importForm( $key );                
        }elseif( @$_REQUEST['step'] == 'import' ){
            echo $this->userImport();
        }

        die();
    }
        
    
    function showCsvFiels(){
        global $userMeta;
        
        $cache = $userMeta->getData( 'cache' ); 
        $csvCache = @$cache['csv_files'];         
        $userMeta->renderPro( 'importExportPage', array(
            'csvCache' => $csvCache,
        ), 'exportImport' );
    }
    
    
    function updateCsvCache(){
        global $userMeta;
        
        $cache = $userMeta->getData( 'cache' ); 
        $csvCache = @$cache[ 'csv_files' ];
        
        if( $csvCache && is_array( $csvCache ) )
            $key = array_search( @$_REQUEST[ 'filepath' ], $csvCache );
        
        if( !@$key ){
            $key = time();
            $csvCache[ $key ] = $_REQUEST[ 'filepath' ];
            $cache[ 'csv_files' ] = $csvCache;
            $userMeta->updateData( 'cache', $cache );                             
        }  
        
        return $key;                         
    }
    
    
    function importForm( $key ){
        global $userMeta, $wp_roles;            
                  
        $uploads = wp_upload_dir();
        $fullpath = $uploads[ 'basedir' ] . @$_REQUEST[ 'filepath' ];
        
        $file = fopen( $fullpath, "r" );          
        $csvHeader = fgetcsv( $file );  
        $csvSample = fgetcsv( $file );
        fclose( $file );             
        
        $fieldList  = $userMeta->defaultUserFieldsArray();
        $fieldAdded = array( '' => '', 'custom_field' => 'Custom Field');
        $fieldList  = array_merge( $fieldAdded, $fieldList );     
        
        $roles = $wp_roles->role_names; 
        $roles = array_merge( array(''=>''), $roles );      
        
        $userMeta->renderPro( "importStep2", array(
            'key'       => $key,
            'fullpath'  => $fullpath,
            'csvHeader' => $csvHeader,
            'csvSample' => $csvSample,
            'fieldList' => $fieldList,
            'roles'     => $roles,
        ), 'exportImport');            
    }
    
    
    function userImport(){
        global $userMeta;
                                
        $csv_header     = @$_POST[ 'csv_header' ];
        $selected_field = @$_POST[ 'selected_field' ];
        $custom_field   = @$_POST[ 'custom_field' ];
        $filepath       = urldecode( @$_POST[ 'filepath' ] );   
        $filesize       = @$_POST[ 'filesize' ];          
        
        if( !$filepath || !file_exists( $filepath ) )
            return $userMeta->showError( __( 'CSV file not found for import!', $userMeta->name ) );
        if( !$selected_field || !is_array($selected_field) )
            return $userMeta->showError( __( 'There is some error while importing.', $userMeta->name ) );
                                            
        if( @$_POST['import_by'] == 'email'){
            if(!in_array('user_email', $selected_field))
                return $userMeta->showError( __( 'Email field should be selected as any field.', $userMeta->name ) );                                                                          
        }elseif( @$_POST['import_by'] == 'username'){
            if(!in_array('user_login', $selected_field))
                return $userMeta->showError( __( 'Username field should be selected as any field.', $userMeta->name ) );                                                    
        }elseif( @$_POST['import_by'] == 'both'){
            if( !in_array('user_email', $selected_field) || !in_array('user_login', $selected_field) )
                return $userMeta->showError( __( 'Email and Username field should be selected as any field.', $userMeta->name ) );                         
        }
        

        // Determine $userFields
        foreach( $selected_field as $key => $val ){
            if( $val == 'custom_field' ){
                $userFields[ $key ] = @$custom_field[ $key ];                   
                if( @$custom_field[ $key ] )
                    $extraFields[]  = $custom_field[ $key ];
            }else                
                $userFields[ $key ] = $val;                
        }             
        
        
        // Show Blank progressbar for init
        if( isset( $_POST['init'] ) ){                
            // Added custom fields to 'Field Editor'
            if( @$_POST['add_fields'] ){
                if( @$extraFields )
                    $userMeta->addCustomFields( $extraFields );
            }
            
            $import_count = array(
                'rows'      => 0,
                'create'    => 0,
                'update'    => 0,
                'skip'      => 0,                
            );
            set_transient( 'user_meta_user_import', $import_count, 36000 );
            
            return $userMeta->renderPro( 'importStep3', array(
                'file_pointer'  => 0,
                'percent'       => 0,
                'is_loop'       => true,
                'import_count'  => $import_count,
            ), 'exportImport' );                  
        }        
                
        set_time_limit(36000);  
        $file           = fopen( $filepath, "r" );  
        $import_count   = get_transient( 'user_meta_user_import' );                               
        
        
        if( @$_POST['file_pointer'] )
            fseek( $file, @$_POST['file_pointer'] );  
        else
            $first_row = fgetcsv( $file );
                          
            
        $limit  = 50;    
        $n      = 0;                    
        while( !feof( $file ) ){                                
            if( $n == $limit )
                break;                
            
            $csvData = fgetcsv( $file );
            if( !$csvData ) continue;                
            
            // Assigned data to $userdata array
            foreach( $userFields as $key => $val ){
                if( $val )
                    $userdata[ $val ] = @$csvData[ $key ];
            }
            
                                       
            $userdata[ 'user_email' ] = @$userdata[ 'user_email' ] ? sanitize_email( $userdata[ 'user_email' ] )      : null;
            $userdata[ 'user_login' ] = @$userdata[ 'user_login' ] ? sanitize_user( $userdata[ 'user_login' ], true ) : null;        
                
            $user_id = null;
            if( $_POST[ 'import_by' ] == 'email' ){
                if( ! empty( $userdata[ 'user_email' ] ) ){
                    $user_id = email_exists( $userdata[ 'user_email' ] );
                    if( !$user_id ){
                        if( username_exists( $userdata[ 'user_login' ] ) ) 
                            unset( $userdata[ 'user_login' ] );
                    }
                }               
                                
                /*if(!$userdata['user_email']) $trigger = 'skip_user';
                $user_id = email_exists($userdata['user_email']);
                if(!$user_id){
                    $loginFound = username_exists($userdata['user_login']); 
                    if($loginFound)
                        $userdata['user_login'] = sanitize_user($userdata['user_email'],true);
                } */                           
            }elseif( $_POST[ 'import_by' ] == 'username' ){
                if( ! empty( $userdata[ 'user_login' ] ) ){
                    $user_id = username_exists( $userdata[ 'user_login' ] );
                    if( !$user_id ){
                        if( email_exists( $userdata[ 'user_email' ] ) )
                            unset( $userdata[ 'user_email' ] );
                    }
                }                
                                
                /*if(!$userdata['user_login']) $trigger = 'skip_user';
                $user_id = username_exists($userdata['user_login']);
                if(!$user_id){
                    $emailFound = email_exists($userdata['user_email']); 
                    if($emailFound)
                        $userdata['user_email'] = sanitize_email( $userdata['user_login'] . '@noreply.com' );
                }*/                                                    
            }elseif( $_POST[ 'import_by' ] == 'both' ){
                /*if(!$userdata['user_email']) $trigger = 'skip_user';
                if(!$userdata['user_login']) $trigger = 'skip_user';*/
                $user_id = email_exists( $userdata[ 'user_email' ] );
                if(!$user_id)
                    $user_id = username_exists( $userdata[ 'user_login' ] );                            
            }                  
                   
            if( @$_POST['user_role'] )
                $userdata['role'] = $_POST['user_role'];
                
            //assign value to trigger, for makaing decession for next action
            $overwrite = isset( $_POST[ 'overwrite' ] ) ? true : false;
            if( ( $overwrite AND $user_id ) )
                $trigger = 'update_user';
            elseif( ! $user_id )
                $trigger = 'add_user';
            else
                $trigger = 'skip_user';
                        

            //Implementation user add/update action
		    if( $trigger == 'add_user' ){
                $response = $userMeta->insertUser( $userdata );   
                if( !is_wp_error( $response ) ){
                    if( isset( $_POST['send_email'] ) )
                        do_action( 'user_meta_after_user_register', (object) $response );
                    $import_count['create']++; 
                }	   	                       
                else
                    $import_count['skip']++;             
		    }                            
            elseif( $trigger == 'update_user' ){
                $response = $userMeta->insertUser( $userdata, $user_id );   
                if( !is_wp_error( $response ) ) 	
                    $import_count['update']++;    
                else
                    $import_count['skip']++;                            
            }else
                $import_count['skip']++;
                
                
            $import_count['rows']++;
            unset($userdata);
            $n++;
        }// End While 
        
               
        set_transient( 'user_meta_user_import', $import_count, 36000 );
         
        $file_pointer = ftell( $file ); 
        fclose( $file );
        
        if( $file_pointer < $filesize ){
            $percent = floor( ( $file_pointer * 100 ) / $filesize );
            $is_loop = true;
        }else{
            $percent = 100;
            $is_loop = false;
        }

        return $userMeta->renderPro( 'importStep3', array(
            'file_pointer'  => $file_pointer,
            'percent'       => $percent,
            'is_loop'       => $is_loop,
            'import_count'  => $import_count,
        ), 'exportImport' );                         
    }
    
              
}
endif;

?>