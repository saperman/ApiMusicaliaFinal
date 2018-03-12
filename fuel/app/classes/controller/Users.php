<?php
use \Model\Users;
use Firebase\JWT\JWT;
class Controller_Users extends Controller_Base
{
    private  $idAdmin = 1;
    private  $idUser = 2;

    public function get_configAdmin(){ //funcion para configurar al administrador la primera vez que se tira
    	$adminName = 'Admin';
    	$adminPassword = 'admin1234';
    	$adminEmail = 'admin@cev.com';
    	$adminRole = $this->idAdmin;
    	$adminIdDevice = 1;
    	$adminX = 1;
    	$adminY = 1;
    	$userAdmin =Model_Users::find('all', 
    								array('where' => array(
    													array('email', '=', $adminEmail),
    														)
    									)
    							);
    	if(empty($userAdmin)){
	    	$admin = new Model_Users();
	    	$admin->userName = 'Admin';
	    	$admin->password = $this->encode($adminPassword);
	    	$admin->email = 'admin@cev.com';
	    	$admin->id_role = $adminRole;
	    	$admin->id_device = 1;
	    	$admin->x = 1;
	    	$admin->y = 1;
	    	$admin->save();
	    	return $this->respuesta(200,"Usuario administrador creado", ''.$admin->email.' '.self::decode($admin->password));
	    }else{
	    	return $this->respuesta(201, "usuario Admin ya creado",'');
	    }
    }

    public function post_register()
    {
        try {
            if ( !isset($_POST['userName']) || !isset($_POST['password']) || !isset($_POST['email'])) //parametros definidos
            {	
            	$array[]= [$_POST['userName'],$_POST['email']];

            	return $this->respuesta(400, 'Algun paramentro esta vacio', $array);
            }if(isset($_POST['x']) || isset($_POST['y'])){
            		if(empty($_POST['x']) || empty($_POST['y'])){
	            		return $this->respuesta(400, 'Coordenadas vacias', ''); 
	            	}
            	}else{
            		return $this->respuesta(400, 'Coordenadas no definidas', '');
            	}
            if(!empty($_POST['userName']) && !empty($_POST['password']) && !empty($_POST['email']) && !empty($_POST["confirmPassword"])){ //campos de los parametros no vacios

            	if((($_POST["password"] !== $_POST["confirmPassword"]))){
            		return $this->respuesta(400, 'Las contraseñas no coinciden', '');
            	}
            	if(strlen($_POST['password']) < 5){
            			return $this->respuesta(400, 'La contraseña debe tener al menos 5 caracteres', '');
            	}
				$input = $_POST;
	            $newUser = $this->newUser($input); //se crea un usuario nuevo
	           	$json = $this->saveUser($newUser); //se guarda el usuario y manda la respuesta
	        }else{
	        	return $this->respuesta(400, 'Algun campo vacio ', '');
	        }
        }catch (Exception $e){
        	return $this->respuesta(500, $e->getMessage(), ''); //error del servidor
        }     
    }
    private function newUser($input)
    {
    		$user = new Model_Users();
            $user->userName = $input['userName'];
            $user->password = $this->encode($input['password']);
            $user->email = $input['email'];
            $user->id_device = $input['id_device'];
            $user->id_role = $this->idUser;
            $user->x = $input['x'];
            $user->y = $input['y'];
            return $user;
    }

    private function saveUser($user)
    {
    	$userExists = Model_Users::find('all', 
    								array('where' => array(
    													array('email', '=', $user->email),
    														)
    									)
    							);
    	if(empty($userExists)){
    		$userToSave = $user;
    		$userToSave->save();
    		$arrayData = array();
    		$arrayData['userName'] = $user->userName;
    		return $this->respuesta(201, 'Usuario creado', $arrayData);
    	}else{
    		return $this->respuesta(204, 'Usuario ya registrado', '');
    	}
    }

    public function post_login()
    {	try{
	        if ( !isset($_POST['email']) || !isset($_POST['password']) ) { //parametros correctos
	        	return $this->respuesta(400, 'alguno de los datos esta vacio', '');
	        }else if( !empty($_POST['email']) && !empty($_POST['password'])){ //parametros no vacios
	            $input = $_POST;
	            $user = Model_Users::find('all', 
		            						array('where' => array(
		            							array('email', '=', $input['email']), 
		            							array('password', '=', $this->encode($input['password']))
		            							)
		            						)
		            					);
	            if(!empty($user))
	            {
	            	$user = reset($user);
	            	$userName = $user->userName;
	            	$password = $user->password;
	            	$id = $user->id;
	            	$email = $user->email;
	            	$id_role = $user->id_role;
	                $token = $this->encodeToken($userName, $password, $id, $email, $id_role);
	                $arrayData = array();
	               	$arrayData['token'] = $token;
	               	return $this->respuesta(200, 'Log In correcto', $arrayData); //si se encuentra al usuario, se devuelve un token con su informacion
	        	}else{
	        		return $this->respuesta(400, 'algun dato erroneo ', '');//respuesta si los datos no coinciden con la bbdd
	       		 }
	     
	        }else{
	        	return $this->respuesta(400, 'No se permiten cadenas de texto vacias', ''); //si los parametros estan vacios
	        }
	        	
	    }catch(Exception $e){
	    	return $this->respuesta(500, $e->getMessage(), '');
	    }
	}
	
	public function post_forgotPassword()
	{
		try{
			$input = $_POST;
			if ( !isset($_POST['email']) ) {
				return $this->respuesta(400, 'alguno de los datos esta vacio', '');
	        }else if( !empty($_POST['email'])){
		    	$user = Model_Users::find('all', 
		           					array('where' => array( 
		           							array('email', '=', $input['email'])
		           							)
		           						)
		           					);
			    if($user != null){
			   		   	$user = reset($user);
		            	$userName = $user->userName;
		            	$password = $user->password;
		            	$id = $user->id;
		            	$email = $user->email;
		            	$id_role = $user->id_role;
		                $token = $this->encodeToken($userName, $password, $id, $email, $id_role);
		                $arrayData = array();
		               	$arrayData['token'] = $token;
		               	return $this->respuesta(200, 'forgot correcto', $arrayData);
			    }else{
			    	return $this->respuesta(400, 'Usuario no encontrado.', '');
			    }
			}
		}catch(Exception $e){
			return $this->respuesta(500, $e->getMessage(), '');
		}
	}

	public function post_changePassword()
	{
		$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    	
    	 if($arrayAuthenticated['authenticated']){

			if(!isset($_POST['newPassword']) || !isset($_POST['confirmPassword'])) {
				return $this->respuesta(400, 'parametro no definido', "");
			}
			if(empty($_POST['newPassword'] || empty($_POST['confirmPassword']))){
				return $this->respuesta(400, 'campos vacios', "");
			}

			$newPassword = $_POST['newPassword'];
			$confirmPassword = $_POST['confirmPassword'];
			if(($_POST["newPassword"] == $_POST["confirmPassword"])){
					$decodedToken = $this->decodeToken();
					$user = Model_Users::find('all', 
					            					array('where' => array(
				            							array('email', '=', $decodedToken->email), 
				            							array('password', '=', $decodedToken->password)
				            							)
				            						)
				            					);
					if(strlen($newPassword) >= 5){
						$userTochange = Model_Users::find($decodedToken->id);
						$userTochange ->password = $this->encode($newPassword);
						$userTochange -> save();

						$userName = $userTochange->userName;
				    	$password = $userTochange->password;
				    	$id = $userTochange->id;
				    	$email = $userTochange->email;
				    	$id_role = $userTochange->id_role;

						$token = $this->encodeToken($userName, $password, $id, $email, $id_role);
						$arrayData = array();
			       		$arrayData['token'] = $token;
			       		return $this->respuesta(200, 'Contraseña modificada correctamente', $arrayData);
				  }else{
					   		return $this->respuesta(204, 'Contraseña demasiado corta', "");
					   	}
			}else{
					return $this->respuesta(400, 'las contraseñas no coinciden', "");
				}
		}else{
			return $this->respuesta(400, 'NO AUTORIZADO', "");
		}

	}
	public function get_show()
	{
		$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    
    	 if($arrayAuthenticated['authenticated']){
        	$decodedToken = self::decodeToken();

	    			$arrayData = array();
	    			$arrayData['userName'] = $decodedToken->userName;
	    			$arrayData['userEmail'] = $decodedToken->email;

	    			return $this->respuesta(200, 'info User', $arrayData);
    	}else{
    			return $this->respuesta(401, 'NO AUTORIZACION','');
    		}
    }
}


