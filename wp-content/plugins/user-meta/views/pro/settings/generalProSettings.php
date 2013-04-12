<?php
global $userMeta;

$html = null;

// Start reCAPTCHA Settings
$html .= "<div class='pf_divider'></div>";

$html .= "<h4>" . __( "reCAPTCHA Settings", $userMeta->name ) . "</h4>";

$html .= "<p>" . __( "reCAPTCHA is a free CAPTCHA service that helps to digitize books, newspapers and old time radio shows.", $userMeta->name ) . "<a href='http://www.google.com/recaptcha/' target='_blank'>" . __( "Read More", $userMeta->name ) . "</a>.</p>";

$html .= $userMeta->createInput( "general[recaptcha_public_key]", "text", array(
    "value"         => isset( $general[ 'recaptcha_public_key' ] )? $general[ 'recaptcha_public_key' ] : null,
    "label"         => __( "reCAPTCHA Public Key", $userMeta->name ),
    "label_class"   => "pf_label",
    "class"         => "um_input",
    "style"         => "width: 400px;",
) );    

$html .= $userMeta->createInput( "general[recaptcha_private_key]", "text", array(
    "value"         => isset( $general[ 'recaptcha_private_key' ] )? $general[ 'recaptcha_private_key' ] : null,
    "label"         => __( "reCAPTCHA Private Key", $userMeta->name ),
    "label_class"   => "pf_label",
    "class"         => "um_input",
    "style"         => "width: 400px;",        
) );   

$html .= __( "<p>User Meta plugin use reCAPTCHA as Captcha field. reCAPTCHA Public Key and reCAPTCHA Private Key are required for using Captcha validation. Get those key for free. <a href='http://www.google.com/recaptcha/whyrecaptcha' target='_blank'>Sign up now</a>.</p>", $userMeta->name );   


?>