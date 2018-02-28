<?php 

class Model_Lists extends Orm\Model
{
    protected static $_table_name = 'Lists';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'title' => array(
            'data_type' => 'text'   
        ),
        'id_user' => array(
            'data_type' => 'int'   
        )
    );
    protected static $_belongs_to = array(
	    'Users' => array(
	        'key_from' => 'id_user',
	        'model_to' => 'Model_Users',
	        'key_to' => 'id',
	        'cascade_save' => true,
	        'cascade_delete' => true,
	    )
	);
    protected static $_many_many = array(
        'Songs' => array(
            'key_from' => 'id',
            'key_through_from' => 'id_list', // column 1 from the table in between, should match a posts.id
            'table_through' => 'ListsSongs', // both models plural without prefix in alphabetical order
            'key_through_to' => 'id_song', // column 2 from the table in between, should match a users.id
            'model_to' => 'Model_Songs',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => true,
        )
    );
}