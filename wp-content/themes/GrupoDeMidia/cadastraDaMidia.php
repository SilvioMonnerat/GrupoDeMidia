<?php 
	include_once('m_toolbox/inc/generic/m_super_dump.php');

	d($_POST);
	//d($_FILE);
	d(get_defined_vars());

	//create user 
	
	$user_data = array(
        'ID' => '',
        'user_pass' => wp_generate_password(),
        'user_login' => 'dummy',
        'user_nicename' => 'Dummy',
        'user_url' => '',
        'user_email' => 'dummy@example.com',
        'display_name' => 'Dummy',
        'nickname' => 'dummy',
        'first_name' => 'Dummy',
        'user_registered' => '2010-05-15 05:55:55',
        'role' => get_option('default_role') // Use default role or another role, e.g. 'editor'
    );
    
    $user_id = wp_insert_user( $user_data );