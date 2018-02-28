<?php
namespace Fuel\Migrations;

class Songs
{
		
    function up()
    {	
    	try
    	{
	        \DBUtil::create_table(
	    		'Songs',
	    		array(
	    		    'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
	    		    'title' => array('type' => 'text'),
	    		    'url' => array('type' => 'text'),
	    		    'artist' => array('type' => 'text')
	    		),
	    		array('id'), false, 'InnoDB', 'utf8_general_ci'
			);
    	}
    	catch(\Database_Exception $e)
		{
		   echo 'songs ya creada'; 
		}
    }

    function down()
    {
       \DBUtil::drop_table('Songs');
    }

}