<?php

$html = null;

if( $field_type == 'blogname' ):
	$html .= "$fieldDescription $fieldMaxChar";
	$html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly</div>";
	$html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";

elseif( $field_type == 'datetime' ):  
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";  
    $html .= "$fieldDefaultValue $fieldDateTimeSelection $fieldMaxChar"; 
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";

elseif( $field_type == 'password' ):  
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRetypePassword $fieldPasswordStrength $fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";  
    $html .= "$fieldDefaultValue $fieldMaxChar";    
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
    
elseif( $field_type == 'email' ):  
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRetypeEmail $fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";  
    $html .= "$fieldDefaultValue $fieldMaxChar";    
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
    
elseif( $field_type == 'file' ):  
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldDisableAjax $fieldCropImage</div>";  
    $html .= "$fieldAllowedExtension $fieldImageWidth $fieldImageHeight";   
    $html .= "$fieldDivider $fieldMaxFileSize $fieldCssClass $fieldCssStyle";

elseif( $field_type == 'image_url' ):    
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue $fieldMaxChar";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
    
elseif( $field_type == 'phone' ):    
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue $fieldMaxChar";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";

elseif( $field_type == 'number' ):    
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue $fieldMinNumber $fieldMaxNumber";  
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
    
elseif( $field_type == 'url' ):    
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue $fieldMaxChar";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
    
elseif( $field_type == 'country' ):
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue $fieldCountrySelectionType";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
    
    
    
    
    
elseif( $field_type == 'page_heading' OR $field_type == 'section_heading' ) :
    $html .= "$fieldDescription $fieldCssClass $fieldCssStyle $fieldShowDivider";   
    
elseif( $field_type == 'html' ): 
    $html .= "$fieldDescription $fieldDefaultValue";
    
elseif( $field_type == 'captcha' ):
    $html .= "$fieldDescription";
    $general    = $userMeta->getSettings( 'general' );
    if( ( !@$general['recaptcha_public_key'] ) || ( !@$general['recaptcha_private_key'] ) ){
        $html .= "<div class='pf_warning'>" . sprintf( __( 'Please provide reCaptcha public and private key from User Meta %s page', $userMeta->name ), $userMeta->adminPageUrl('settings') ) . "</div>";
    }        
    $html .= "<div class='um_segment'>$fieldNonAdminOnly $fieldRegistrationOnly</div>";
    //$html .= "$fieldCaptchaPublicKey $fieldCaptchaPrivateKey";
    //$html .= "$fieldSize $fieldCssClass $fieldCssStyle";         
endif;


?>