<?php 
use \Model\Users;
use Firebase\JWT\JWT;
class Controller_Lists extends Controller_Base
{
	public function post_create() //funcion para crear una lista
    {
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true);
    
         if($arrayAuthenticated['authenticated']){ //miramos la autorizacion del usuario
            try {
                if ( ! isset($_POST['name'])) //miramos que name no este definido
                {
                    return $this->respuesta(400, 'Algun paramentro esta vacio', '');
                }
                if(empty($_POST['name'])){ //miramos que name no este vacio
                    return $this->respuesta(400, 'Algun paramentro esta vacio', '');
                }
                $input = $_POST;
                $name = $input['name'];
                $decodedToken = self::decodeToken(); //recogemos el token

                $list = new Model_Lists();
                $list->title = $name;
                $idUser = $decodedToken->id;
                $list->id_user = $idUser;
                $list->save();

                return $this->respuesta(201, 'lista creada', $name);   //creamos la lista con los datos proporcionados y devolvemos una respuesta con el nombre de la lista creada
            } 
            catch (Exception $e) 
            {
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
                    'message' => 'Usuarios no autenticado', //respuesta si no se consigue autenticar el usuario
            ));
            return $json;
         }
     }
        
        
   public function post_addSong()
    {
        
        $authenticated = $this->authenticate(); 
        $arrayAuthenticated = json_decode($authenticated, true); //miramos la autorizacion del usuario
    
         if($arrayAuthenticated['authenticated']){ 
                if(!isset($_POST['id_song']) || !isset($_POST['id_list'])) //que los parametros esten definidos
                {
                    return $this->respuesta(400, 'Algun paramentro esta vacio', '');
                }
                $decodedToken = self::decodeToken(); //recogida del token
                $input = $_POST;
                $list = Model_Lists::find('all', array(
                    'where' => array(
                        array('id', $input['id_list']),
                        array('id_user', $decodedToken->id)
                    ),
                ));  //se busca la lista que se necesita
                if(empty($list))
                {
                    return $this->respuesta(400, 'Esa lista no existe', ''); //si no exite la lista devuelve
                }
                $song = Model_Songs::find($input['id_song']);   //se busca la cancion que se necesita
                if(empty($song))
                {
                    return $this->respuesta(400, 'Esa cancion no existe', ''); //si no existe la cancion devuelve
                }
                $addName = Model_ListsSongs::find('all', array(
                    'where' => array(
                        array('id_list', $input['id_list']),
                        array('id_song', $input['id_song'])
                    ),
                ));  //se busca si la lista contiene ya esa cancion
                if(!empty($addName))
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Esa cancion ya existe en esta lista', //si la cancion ya esta en la lista se devuelve
                        'data' => ''
                    ));
                    return $response;
                }
                $list = Model_Lists::find($input['id_list']);
                $list->Songs[] = Model_Songs::find($input['id_song']);  //se procede a meter la cancion en la lista 
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
                  'message' => 'Usuarios no autenticado', //respuesta si no esta autenticado el usuario
            ));
        return $json;
         }
     }       
    

    public function post_delete()
    {   
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true); //miramos que este autenticado
    
         if($arrayAuthenticated['authenticated']){
            if (!isset($_POST['id']))  //paramentros definidos
            {
                return $this->respuesta(400, 'Falta el parametro id', ''); //respuesta si no estan definidos los parametros
            }
            $list = Model_Lists::find($_POST['id']); //se busca la lista
            if(!empty($list))
            {
                $listName = $list->title;
                $list->delete();        //si se encuentra la lista ,se procede a su borrado
            }
            $json = $this->response(array(
                'code' => 200,
                'message' => 'lista borrada',  //devuelve una respuesta con el nombre de la lista borrada
                'name' => $listName,
            ));
            return $json;
        }
        else
        {
            $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuarios no autenticado', // respuesta si no esta autenticado
            ));
            return $json;
        }
    }
    public function post_update()
    {
       $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true);  //miramos que este autenticado
    
         if($arrayAuthenticated['authenticated']){
            if (!isset($_POST['id']) && ! isset($_POST['name']) ) //parametros definidos
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'parametros incorrectos' //respuesta si no estan definidos los parametros
                ));
                return $json;
            }
            $id = $_POST['id'];
            $updateList = Model_Lists::find($id); //se busca la lista deseada
            $title = $_POST['name']; //se recoge el nuevo nombre de la lista

            if(!empty($updateList))  // se mira si esta vacia la lista
            {
                $decodedToken = self::decodeToken(); //recogida de token
                if($decodedToken->id == $updateList->id_user)
                {
                    $updateList->title = $title;
                    $updateList->save(); //se cambia el nombre de la lista y se guarda
                    $json = $this->response(array(
                    'code' => 200,
                    'message' => 'lista actualizada, titulo nuevo: '.$title //respuesta si se cambia el nombre de la lista
                    ));
                }
                else
                {
                    $json = $this->response(array(
                        'code' => 401,
                        'message' => 'No estas autorizado a cambiar esa lista' //respuesta si la lista que quieres cambiar no te pertenece
                    ));
                    return $json;
                }
            }
            else
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'lista no encontrada' //respuesta si no encuentra la lista
                ));
                return $json;
            }
        }
        else
        {
            $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuario no autenticado', //respuesta si no se autentica el usuario
            ));
            return $json;
        }
    }    

    public function get_songsFromList()
    {
        try
        {
            $authenticated = $this->authenticate();
            $arrayAuthenticated = json_decode($authenticated, true); //miramos que este autenticado

            if($arrayAuthenticated['authenticated']){
                if(!isset($_GET['id_list'])) //parametros definidos
                {
                    return $this->respuesta(400, 'Debes rellenar todos los campos', ''); //respuesta si no estan definidos
                }
                if(empty($_GET['id_list'])){
                    return $this->respuesta(202, 'id listas vacio', ''); 
                }  
                $input = $_GET;
                $songs = [];
                foreach($input['id_list'] as $key => $idList)
                {
                    $songsFromList = Model_ListsSongs::find('all', array(
                        'where' => array(
                            array('id_list', $idList)
                        ), //se busca la lista
                    ));
                    if(!empty($songsFromList)){

                        foreach ($songsFromList as $key => $list)
                        {
                            $songsOfList[$list->id_list][] = Model_Songs::find($list->id_song);
                            $songs = $songsOfList;
                            // si encuentra la lista busca las canciones que tiene
                        }
                        // foreach ($songsOfList as $key => $song)
                        // {
                            
                        //     $songs[$song->][] = $song;
                        // }  
                        //return $this->respuesta(200, 'Canciones encontradas', $songs); // devuelve las canciones de la lista
                    }
                    else
                    {
                        $songs[] = '';
                        //return $this->respuesta(400, 'No existen canciones en esa lista', ''); //respuesta si la lista no tiene canciones
                    }
                }
                return $this->respuesta(200, 'Canciones por lista de cada lista', $songs);
            }
            else
            {
                return $this->respuesta(400, 'Error de autenticación', ''); //respuesta si no se puede autenticar
            }
        }
        catch (Exception $e)
        {
            return $this->respuesta(500, 'Error del servidor : '.$e, ''); //respuesta de error del servidor
        }
    }

     public function get_show()
    {
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true); //autenticamos al usuario
         if($arrayAuthenticated['authenticated']){

                $decodedToken = self::decodeToken(); //recogemos el token
                if(isset($_GET['idList'])){ //comprobamos parametros, si no recibe id, muestra todas las listas del usuario
                    $idList = $_GET['idList'];
                    $list = Model_Lists::find('all',
                                                    array('where' => array(
                                                    array('id_user', '=', $decodedToken->id),
                                                    array('id', '=', $idList) 
                                                    )
                                                )
                                            ); //buscamos la lista deseada
                    if(!empty($list)){
                        return $this->respuesta(200, 'mostrando la lista', Arr::reindex($list)); //rrespuesta si encuentra la lista vacia                            
                    }else{
                            $json = $this->response(array(
                                 'code' => 202,
                                 'message' => 'Aun no tienes ninguna lista',
                                    'data' => ''
                                ));
                                return $json; //respuesta si no encuentra la lista
                    }
            
                }else{ //si no se pasa id, muesta todas las listas del usuario
                    $lists = Model_Lists::find('all', 
                                                    array('where' => array(
                                                        array('id_user', '=', $decodedToken->id), 
                                                        )
                                                    )
                                                ); //bucamos todas las listas del usuario
                    if(!empty($lists)){
                        return $this->respuesta(200, 'mostrando listas del usuario', Arr::reindex($lists)); //respuesta con las lustas del usuario                           
                    }else{
                        
                        $json = $this->response(array(
                                     'code' => 202,
                                     'message' => 'Aun no tienes ninguna lista',
                                        'data' => ''
                                    ));
                                    return $json;//respuesta si el usuario aun no ha creado ninguna lista
                        }
                }
            }else{
                
                $json = $this->response(array(
                             'code' => 401,
                             'message' => 'NO AUTORIZACION', //respuesta si no esta autorizado el usuario
                                'data' => ''
                            ));
                            return $json;
            }
    }
}






