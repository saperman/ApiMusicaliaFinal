<?php 

class Model_Songs extends Orm\Model
{
    protected static $_table_name = 'Songs';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'title' => array(
            'data_type' => 'text'   
        ),
        'url' => array(
            'data_type' => 'text'   
        ),
        'artist' => array(
            'data_type' => 'text')
    );
    protected static $_many_many = array(
        'Lists' => array(
            'key_from' => 'id',
            'key_through_from' => 'id_list', // column 1 from the table in between, should match a posts.id
            'table_through' => 'ListsSongs', // both models plural without prefix in alphabetical order
            'key_through_to' => 'id_song', // column 2 from the table in between, should match a users.id
            'model_to' => 'Model_Lists',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => true,
        )
    );
}