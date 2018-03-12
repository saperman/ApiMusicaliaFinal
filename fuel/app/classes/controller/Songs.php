<?php 
class Controller_Songs extends Controller_Base
{
	public function post_create()
    {
       $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true); //autentificacion usuario
        
         if($arrayAuthenticated['authenticated']){
            try {
                $decodedToken = self::decodeToken(); //recogida token
                if($decodedToken->id == 1){
                    if ( ! isset($_POST['title']) || ! isset($_POST['url']) || ! isset($_POST['artist'])) //comprobacion de parametros
                    {
                        return $this->respuesta(400, 'parametros incorrectos', '');
                    }
                    if ( empty($_POST['title']) ||  empty($_POST['url'] || empty($_POST['artist']))) //comprobacion que no este los campos vacios
                    {
                        return $this->respuesta(400, 'parametros incorrectos', '');
                    }
                    $input = $_POST;
                    $title = $input['title'];
                    $artist = $input['artist'];
                    $url = $input['url'];
                    $song = new Model_Songs();
                    $song->title = $title;
                    $song->url = $url;
                    $song->artist = $artist;

                    $song->save(); // se crea una cancion con los parametros establecidos
                    return $this->respuesta(200, 'Cancion creada', ''); //respuesta cancion creada
                }else{
                    return $this->respuesta(401, 'Solo los admin pueden crear canciones', '');//respuesta si un usuario no admin intenta crear una cancion
                }
            } 
            catch (Exception $e) 
            {

                $json = $this->response(array(
                    'code' => 500,
                    'message' => 'error interno del servidor', //respuesta error interno del servidor
                ));
                return $json;
            }
        }
        else
        {
        	$json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuario no autenticado', //respuesta si no se consigue autenticar al usuario
                ));
                return $json;
        }
        
    }
    public function get_songs()
    {   
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true); //autentificacion usuario
        
         if($arrayAuthenticated['authenticated']){
            try {
                $songs = Model_Songs::find('all'); //buscamos las canciones
                if(empty($songs)){
                    return $this->respuesta(201, 'No hay ninguna cancion aun', ''); //respuesta si no hay canciones creadas
                }
	            $indexedSongs = Arr::reindex($songs);
	            foreach ($indexedSongs as $key => $song) {
	                $title[] = $song->title;
	                $url[] = $song->url;
                    $artist[] = $song->artist;
	                $id[] = $song->id;
                    $songsArray = ['title' => $title, 'url'=> $url, 'artist' => $artist, 'id' => $id];//se devuelven las canciones en un array con sus parametros
	            }
                $json = $this->response(array(
                    'code' => 200,
                    'message' => 'Canciones en la app', //respuesta con las canciones
                    'data' => $songsArray
                ));
                return $json;
            } 
            catch (Exception $e) 
            {
                return $this->respuesta(500, 'Error del servidor', ''); //respuesta error interno del servidor
            }
        }
        else
        {
        	return $this->respuesta(401, 'Usuario no autenticado', ''); //respuesta si no se autentica al usuario
        }
        
    }
    public function post_deleteSong()
    {
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true); //autentificacion usuario
         if($arrayAuthenticated['authenticated']){
            if(!isset($_POST['id'])){ //parametros definidos
                return $this->respuesta(400, 'parametros incorrectos', '');
            }
            if(empty($_POST['id'])){//campos de los parametros rellenos
                return $this->respuesta(400, 'parametros incorrectos', '');
            }
            $decodedToken = self::decodeToken();
            if($decodedToken->role == 1){
                $idSongToDelete = $_POST['id'];
                $songToDelete = Model_Songs::find($idSongToDelete);
                if(!empty($songToDelete)){
                    $songName = $songToDelete->title;
                    $songToDelete->delete();
                    $json = $this->response(array(
                        'code' => 200,
                        'message' => 'Cancion borrada',
                        'name' => $songName,
                        )); //se busca la cancion con el id proporcionado, y se borra
                    return $json; 
                }else{
                    return $this->respuesta(400, 'Cancion no encontrada', ''); //respuesta si no encuentra la cancion con el id
                }
            }else{
                return $this->respuesta(401, 'Solo los administradores pueden borrar canciones', ''); //respuesta si un usuario no administrado intenta borrar una cancion
            }
        }
    }
}













