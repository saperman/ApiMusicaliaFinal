<?php

namespace Fuel\Migrations;


class Users 
{
    function up()
   {   
            \DBUtil::create_table(
                'Users',
                array(
                    'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                    'userName' => array('type' => 'varchar', 'constraint' => 100),
                    'password' => array('type'=> 'varchar', 'constraint' => 200),
                    'id_role' => array('type' => 'int', 'constraint' => 100),
                    'email' => array('type' => 'varchar', 'constraint' => 100),
                    'id_device' => array('type' => 'varchar', 'constraint'=> 100),
                    'profilePicture' => array('type' => 'varchar', 'constraint' => 100, NULL),
                    'description' => array('type' => 'varchar', 'constraint' => 500, NULL),
                    'birthday' => array('type' => 'varchar', 'constraint' => 200, NULL),
                    'x' => array('type' => 'varchar', 'constraint' => 100, NULL),
                    'y' => array('type' => 'varchar', 'constraint' => 100, NULL)
                ),
                array('id'), false, 'InnoDB', 'utf8_general_ci',
                array(
                    array(
                        'constraint' => 'foreingKeyUsersToRoles',
                        'key' => 'id_role',
                        'reference' => array(
                            'table' => 'Roles',
                            'column' => 'id',
                        ),
                        'on_update' => 'RESTRICT',
                        'on_delete' => 'RESTRICT'
                    )
                )
            );
         
        }
 
              
    function down()
    {
       \DBUtil::drop_table('Users');
    }

}