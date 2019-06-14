<?php

function createPermission( $data , $pid = 0 )
{
    $list = [];

    foreach( $data as $key => $_value ){
        $_value = (array)$_value;
        $_value['extra'] = json_decode($_value['extra'],true);

        if( $_value['parent_id'] == $pid ) {
            $_value['child'] = createPermission($data,$_value['id']);
            $list[] = $_value;
            unset($data[$key]);
        }
    }

    return $list;
}