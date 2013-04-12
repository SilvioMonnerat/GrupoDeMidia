<?php
global $userMeta;
// Expected: $disableAjax, $visibility

$uniqueID = rand(0,99);
$methodName = "Lostpassword";

$html = null;    

$html .= "<p><a href=\"javascript:void(0);\" class=\"lostpassword_link lostpassword_link_$uniqueID\">" . __( 'Perdeu a senha?', $userMeta->name ) . "</a></p>";

$displayNone = "display:none;";
if( isset( $userMeta->um_post_method_status->$methodName ) ){
    $html .= $userMeta->um_post_method_status->$methodName;
    $displayNone = null;
}

$onSubmit   = @$disableAjax ? null : "onsubmit=\"pfAjaxRequest(this); return false;\"";
//$display    = $visibility == 'hide' ? "style=\"display:none\"" : null;
$html .= "<form id=\"um_lostpass_form_$uniqueID\" class=\"um_lostpass_form\" method=\"post\" $onSubmit >";
$html .= "<div class=\"lostpassword_form_div_$uniqueID\" style=\"$displayNone\" >";
$html .= "<p>" . __('Please enter your username or email address. You will receive a link to create a new password via email.') . "</p>";

/*if( !@$_REQUEST['is_ajax'] && @$_REQUEST['method_name'] == 'lostpassword' )
    $html .= $userMeta->ajaxLostpassword();   */

$html .= $userMeta->createInput( 'user_login', 'text', array(
    'label'         => __( 'Nome de Usuário ou E-mail', $userMeta->name ),
    'id'            => 'user_login' . $uniqueID,
    'class'         => 'um_lostpass_field um_input validate[required]',
    'label_class'   => 'pf_label',
    'enclose'       => 'p',
) );



$html .= $userMeta->nonceField();
$html .= $userMeta->methodName( $methodName );
$html .= wp_nonce_field( $methodName, 'um_post_method_nonce', false, false ); 

$html .= $userMeta->createInput( 'login', 'submit', array(
    'value'     => __( 'Obter nova senha', $userMeta->name ),
    'id'        => 'um_login_button' . $uniqueID,
    'class'     => 'um_lostpass_button',
    'enclose'   => 'p',
) );
$html .= "</div></form>";

$html .= "
<script type='text/javascript'>
    jQuery('.lostpassword_link_{$uniqueID}').click(function(){
        jQuery('.lostpassword_form_div_{$uniqueID}').toggle('slow');
    });
</script>
";

?>
