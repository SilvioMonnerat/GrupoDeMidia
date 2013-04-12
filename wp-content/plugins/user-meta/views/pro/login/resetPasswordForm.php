<?php
global $userMeta;

$html = null;

$html .= "<form id=\"um_resetpassword_form\" method=\"post\" >";

$html .= "<h2>" . __( 'Reset Password', $userMeta->name ) . "</h2>";
$html .= "<p>" . __( 'Enter your new password below.', $userMeta->name ) . "</p>";

if( is_wp_error( @$error ) )
    $html .= $userMeta->showError( $error->get_error_message(), false );

$html .= $userMeta->createInput( 'pass1', 'password', array(
    'label'         => __( 'New password', $userMeta->name ),
    'id'            => 'pass1',
    'class'         => 'um_input pass_strength validate[required]',
    'label_class'   => 'pf_label',
    'enclose'       => 'p',   
) );

$html .= $userMeta->createInput( 'pass2', 'password', array(
    'label'         => __( 'Confirm new password', $userMeta->name ),
    'id'            => 'pass2',
    'class'         => 'um_input validate[required,equals[pass1]]',
    'label_class'   => 'pf_label',
    'enclose'       => 'p',
) );

$html .= $userMeta->nonceField();

$html .= $userMeta->createInput( 'login', 'submit', array(
    'value'     => __( 'Reset Password', $userMeta->name ),
    'enclose'   => 'p',
) );
$html .= "</form>";
 
?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#um_resetpassword_form").validationEngine();
        jQuery(".pass_strength").password_strength(); 
    });
</script>