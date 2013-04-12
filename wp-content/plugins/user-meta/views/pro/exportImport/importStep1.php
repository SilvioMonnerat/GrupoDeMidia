<?php
global $userMeta;
// Expected $csvCache, $maxSize

$html = null;
$html .= '<form id="um_user_import_form" method="post" enctype="multipart/form-data" >';   
     
    $html .= "<p>" . __( "Upload the <strong>CSV</strong> file only.", $userMeta->name ) . "</p>";      
    
    $html .= "<div id=\"csv_upload_user_import\" name=\"csv_upload_user_import\" class=\"um_file_uploader_field\" um_field_id=\"csv_upload_user_import\" extension=\"csv\" maxsize=\"$maxSize\"></div>"; 
    $html .= "<div id=\"csv_upload_user_import_result\"></div>";
          
$html .= "</form>";

$html .= "<li>" . __( "First row of CSV file will treat as Field Name.", $userMeta->name ) . "</li>";
$html .= "<li>" . __( "Field will separate with comma (,) and Enclosed with the double quotation (\" \").", $userMeta->name ) . "</li>";

echo $userMeta->metaBox( __( 'User Import', $userMeta->name ), $html );
?>