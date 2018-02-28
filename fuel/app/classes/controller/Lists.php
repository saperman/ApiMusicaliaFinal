<?php 
use \Model\Users;
use Firebase\JWT\JWT;
class Controller_Lists extends Controller_Base
{
	public function post_create()
    {
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true);
    
         if($arrayAuthenticated['authenticated']){
            try {
                if ( ! isset($_POST['name'])) 
                {
                    return $this->respuesta(400, 'Algun paramentro esta vacio', '');
                }
                if(empty($_POST['name'])){
                    return $this->respuesta(400, 'Algun paramentro esta vacio', '');
                }
                $input = $_POST;
                $name = $input['name'];
                $decodedToken = self::decodeToken();

                $list = new Model_Lists();
                $list->title = $name;
                $idUser = $decodedToken->id;
                $list->id_user = $idUser;
                $list->save();
                $json = $this->response(array(
                    'code' => 201,
                    'message' => 'lista creada',
                    'name' => $name,
                ));
                return $json;
            } 
            catch (Exception $e) 
            {
                var_dump($e);
                exit;
                $json = $this->response(array(
                    'code' => 500,
                    'message' => 'error interno del servidor',
                ));
                return $json;
            }
        }
        else{
            $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuarios no autenticado',
            ));
            return $json;
         }
     }
        
        
   public function post_addSong()
    {
        
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true);
    
         if($arrayAuthenticated['authenticated']){ 
                if(!isset($_POST['id_song']) || !isset($_POST['id_list']))
                {
                    return $this->respuesta(400, 'Algun paramentro esta vacio', '');
                }
                $decodedToken = self::decodeToken();
                $input = $_POST;
                $list = Model_Lists::find('all', array(
                    'where' => array(
                        array('id', $input['id_list']),
                        array('id_user', $decodedToken->id)
                    ),
                ));
                if(empty($list))
                {
                    return $this->respuesta(400, 'Esa lista no existe', '');
                }
                $song = Model_Songs::find($input['id_song']);
                if(empty($song))
                {
                    return $this->respuesta(400, 'Esa cancion no existe', '');
                }
                $addName = Model_ListsSongs::find('all', array(
                    'where' => array(
                        array('id_list', $input['id_list']),
                        array('id_song', $input['id_song'])
                    ),
                ));
                if(!empty($addName))
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Esa cancion ya existe en esta lista',
                        'data' => ''
                    ));
                    return $response;
                }
                $list = Model_Lists::find($input['id_list']);
                $list->Songs[] = Model_Songs::find($input['id_song']);  //nombre array many to many
                $list->save();
                $response = $this->response(array(
                    'code' => 200,
                    'message' => 'Cancion agregada',
                    'data' => ''
                ));
                return $response;
            }
         else
         {
            $json = $this->response(array(
                  'code' => 401,
                  'message' => 'Usuarios no autenticado',
            ));
        return $json;
         }
     }       
    

    public function post_delete()
    {   
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true);
    
         if($arrayAuthenticated['authenticated']){
            if (!isset($_POST['id'])) 
            {
                return $this->respuesta(400, 'Falta el parametro id', '');
            }
            $list = Model_Lists::find($_POST['id']);
            if(!empty($list))
            {
                $listName = $list->title;
                $list->delete();
            }
            $json = $this->response(array(
                'code' => 200,
                'message' => 'lista borrada',
                'name' => $listName,
            ));
            return $json;
        }
        else
        {
            $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuarios no autenticado',
            ));
            return $json;
        }
    }
    public function post_update()
    {
       $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true);
    
         if($arrayAuthenticated['authenticated']){
            if (!isset($_POST['id']) && ! isset($_POST['name']) ) 
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'parametros incorrectos'
                ));
                return $json;
            }
            $id = $_POST['id'];
            $updateList = Model_Lists::find($id);
            $title = $_POST['name'];

            if(!empty($updateList))
            {
                $decodedToken = self::decodeToken();
                if($decodedToken->id == $updateList->id_user)
                {
                    $updateList->title = $title;
                    $updateList->save();
                    $json = $this->response(array(
                    'code' => 200,
                    'message' => 'lista actualizada, titulo nuevo: '.$title
                    ));
                }
                else
                {
                    $json = $this->response(array(
                        'code' => 401,
                        'message' => 'No estas autorizado a cambiar esa lista'
                    ));
                    return $json;
                }
            }
            else
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'lista no encontrada'
                ));
                return $json;
            }
        }
        else
        {
            $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuario no autenticado',
            ));
            return $json;
        }
    }    

    public function get_songsFromList()
    {
        try
        {
            $authenticated = $this->authenticate();
            $arrayAuthenticated = json_decode($authenticated, true);

            if($arrayAuthenticated['authenticated']){
                if(!isset($_GET['id_list']))
                {
                    return $this->respuesta(400, 'Debes rellenar todos los campos', '');
                }
                $input = $_GET;
                $songsFromList = Model_ListsSongs::find('all', array(
                    'where' => array(
                        array('id_list', $input['id_list'])
                    ),
                ));
                if(!empty($songsFromList)){
                    foreach ($songsFromList as $key => $list)
                    {
                        $songsOfList[] = Model_Songs::find($list->id_song);
                    }
                    foreach ($songsOfList as $key => $song)
                    {
                        $songs[] = $song;
                    }  
                    return $this->respuesta(200, 'Canciones encontradas', $songs);
                }
                else
                {
                    return $this->respuesta(400, 'No existen canciones en esa lista', '');
                }
            }
            else
            {
                return $this->respuesta(400, 'Error de autenticación', '');
            }
        }
        catch (Exception $e)
        {
            return $this->respuesta(500, 'Error del servidor : $e', '');
        }
    }

     public function get_show()
    {
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true);
         if($arrayAuthenticated['authenticated']){

                $decodedToken = self::decodeToken();
                if(isset($_GET['idList'])){
                    $idList = $_GET['idList'];
                    $list = Model_Lists::find('all',
                                                    array('where' => array(
                                                    array('id_user', '=', $decodedToken->id),
                                                    array('id', '=', $idList) 
                                                    )
                                                )
                                            );
                    if(!empty($list)){
                        return $this->respuesta(200, 'mostrando la lista', Arr::reindex($list));                            
                    }else{
                            $json = $this->response(array(
                                 'code' => 202,
                                 'message' => 'Aun no tienes ninguna lista',
                                    'data' => ''
                                ));
                                return $json;
                    }
            
                }else{
                    $lists = Model_Lists::find('all', 
                                                    array('where' => array(
                                                        array('id_user', '=', $decodedToken->id), 
                                                        )
                                                    )
                                                );
                    if(!empty($lists)){
                        return $this->respuesta(200, 'mostrando listas del usuario', Arr::reindex($lists));                           
                    }else{
                        
                        $json = $this->response(array(
                                     'code' => 202,
                                     'message' => 'Aun no tienes ninguna lista',
                                        'data' => ''
                                    ));
                                    return $json;
                        }
                }
            }else{
                
                $json = $this->response(array(
                             'code' => 401,
                             'message' => 'NO AUTORIZACION',
                                'data' => ''
                            ));
                            return $json;
            }
    }
}






