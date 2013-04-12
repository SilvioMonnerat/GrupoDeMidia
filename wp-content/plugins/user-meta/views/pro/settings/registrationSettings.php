<?php
global $userMeta;
// Expected: $registration
// field slug: registration

$html = null;
   
$registrationSettings = array( 
    'auto_active'           => __( 'User auto activation.', $userMeta->name ) . '<br /><em>(' . __( 'User will be activated automatically after registration', $userMeta->name ) . ')</em>', 
    'email_verification'    => __( 'Need email verification.', $userMeta->name ) . '<br /><em>(' . __( 'A verification link will be sent to user email. User must verify the link to activate their account', $userMeta->name ) . ')</em>',  
    'admin_approval'        => __( 'Need admin approval.', $userMeta->name ) . '<br /><em>(' . __( 'Admin needs to approve the new user', $userMeta->name ) . ')</em>', 
    'both_email_admin'      => __( 'Need both email verification and admin approval.', $userMeta->name ) . '<br /><em>(' . __( 'A verification link will be sent to user email. User must verify the link to activate their account and an admin needs to approve the account', $userMeta->name ) . ')</em>', 
);    
    

$html .= $userMeta->createInput( "registration[user_activation]", "radio", array( 
    'label'         => __( 'User Activation', $userMeta->name ),
    'value'         => @$registration[ 'user_activation' ],
    'label_class'   => 'pf_label',
    'option_before' => '<p>',
    'option_after'  => '</p>',
    'by_key'        => true,
 ), $registrationSettings ); 
 
//$html .= "<div class='clear'></div>";



$html .= "<div class='pf_divider'></div>";
$html .= $userMeta->createInput( 'registration[auto_login]', 'checkbox', array(
    'label' => __( 'Auto login after registration', $userMeta->name ),
    'value' => !empty( $registration['auto_login'] ) ? true : false,
) );
$html .= '<p><i>' . __( 'Only supported if "User auto activation" is selected.', $userMeta->name ) . '</i></p>';


if( is_multisite() ){
	$html .= "<div class='pf_divider'></div>";
	$html .= "<h4>" . __( 'Multisite Registration', $userMeta->name ) . "</h4>";
	
	$html .= $userMeta->createInput( "registration[add_user_to_blog]", "checkbox", array( 
	    'value'         => @$registration[ 'add_user_to_blog' ],
		'after'			=> " " . __( "Allow user registration if user already exists in other sites under network." )
	 ) );		
}


?>