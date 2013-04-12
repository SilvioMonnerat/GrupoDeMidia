<?php

function userMetaLogin( $formName=null ){
    global $userMeta;       
    
    return $userMeta->userLoginProcess($formName);
}

function userMetaProfileRegister( $actionType, $formName ){
    global $userMeta;       
    
    return $userMeta->userUpdateRegisterProcess( $actionType, $formName );
}

?>
