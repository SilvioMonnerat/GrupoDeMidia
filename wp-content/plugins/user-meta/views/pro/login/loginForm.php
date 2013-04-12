<?php
global $userMeta;
// Expected: $loginBy, $loginTitle, $disableAjax, $methodName

$uniqueID = rand(0,99);

$onSubmit = @$disableAjax ? null : "onsubmit=\"umLogin(this); return false;\"";
$html = "<form id=\"um_login_form$uniqueID\" class=\"um_login_form\" method=\"post\" $onSubmit >";

$html .= $userMeta->createInput( 'user_login', 'text', array(
    'label'         => 'Nome de Usuário',
    'id'            => 'user_login' . $uniqueID,
    'class'         => 'um_login_field um_input validate[required]',
    'label_class'   => 'pf_label',
    'enclose'       => 'p',
) );
$html .= $userMeta->createInput( 'user_pass', 'password', array(
    'label'     => __( 'Senha', $userMeta->name ), 
    'id'        => 'user_pass' . $uniqueID,
    'class'     => 'um_pass_field um_input validate[required]',
    'label_class'   => 'pf_label',
    'enclose'   => 'p',
) );            

$html .= $userMeta->createInput( 'remember', 'checkbox', array(    
    'label'     => __( 'Lembrar-me', $userMeta->name ),
    'id'        => 'remember' . $uniqueID,
    'class'     => 'um_remember_field',
    'enclose'   => 'p',
) );    

//$html .= $userMeta->methodName( 'Login' );

$html .= "<input type='hidden' name='action' value='um_login' />";
$html .= "<input type='hidden' name='action_type' value='login' />";
//$html .= "<input type='hidden' name='login_by' value='$loginBy' />";
//$html .= $userMeta->nonceField();
//$html .= wp_original_referer_field( false, 'previous' );

/*$html .= $userMeta->nonceField();
$html .= $userMeta->methodName( $methodName );
$html .= wp_nonce_field( $methodName, 'um_post_method_nonce', false, false ); */

$html .= $userMeta->methodPack( $methodName );

if( !empty( $_REQUEST['redirect_to'] ) ){
    $html .= $userMeta->createInput( 'redirect_to', 'hidden', array(    
        'value'     => $_REQUEST['redirect_to']
    ) ); 
}    
    
$html .= $userMeta->createInput( 'login', 'submit', array(
    'value'     => __( 'Login', $userMeta->name ),
    'id'        => 'um_login_button' . $uniqueID,
    'class'     => 'um_login_button',
    'enclose'   => 'p',
) );


$html .= "</form>"; 

/*$html .= "
<script type='text/javascript'>
    jQuery('#um_login_form_{$initBy}').validationEngine();  
</script>
";*/

?>