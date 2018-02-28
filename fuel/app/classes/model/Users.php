<?php 

class Model_Users extends Orm\Model
{
    protected static $_table_name = 'Users';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'userName' => array(
            'data_type' => 'text'   
        ),
        'password' => array(
            'data_type' => 'text'   
        ),
        'id_role' => array(
            'data_type' => 'int'   
        ),
         'email' => array(
            'data_type' => 'varchar'   
        ),
        'id_device' => array(
            'data_type' => 'int'   
        ),
        'x' => array(
            'data_type' => 'varchar' 
        ),
        'y' => array(
            'data_type' => 'varchar' 
        )  
    );
    protected static $_has_many = array(
        'Lists' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Lists',
            'key_to' => 'id_user',
            'cascade_save' => true,
            'cascade_delete' => false,
        ), 
    );
    protected static $_belongs_to = array(
        'Roles' => array(
            'key_from' => 'id_role',
            'model_to' => 'Model_Roles',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        )
    );
    

}