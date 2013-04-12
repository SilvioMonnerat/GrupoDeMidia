<?php
global $userMeta;

$html = null;

$html .= $userMeta->createInput( 'value_separator', 'text', array(
	'label'			=> __( 'Value Separator', $userMeta->name ),
	'value'			=> !empty( $misc['value_separator'] ) ? $misc['value_separator'] : ',',
	'label_class'	=> 'pf_label',
) );

if( is_multisite() ){
	
}


?>