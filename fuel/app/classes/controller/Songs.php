<?php 
class Controller_Songs extends Controller_Base
{
	public function post_create()
    {
       $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true);
        
         if($arrayAuthenticated['authenticated']){
            try {
                if ( ! isset($_POST['title']) && ! isset($_POST['url']) && ! isset($_POST['artist'])) 
                {
                    return $this->respuesta(400, 'parametros incorrectos', '');
                }
                if ( empty($_POST['title']) ||  empty($_POST['url'] || empty($_POST['artist']))) 
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

                $song->save();
                return $this->respuesta(200, 'Cancion creada', '');
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
        else
        {
        	$json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuario no autenticado',
                ));
                return $json;
        }
        
    }
    public function get_songs()
    {   
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true);
        
         if($arrayAuthenticated['authenticated']){
            try {
                $songs = Model_Songs::find('all');
                if(empty($songs)){
                    return $this->respuesta(201, 'No hay ninguna cancion aun', '');
                }
	            $indexedSongs = Arr::reindex($songs);
	            foreach ($indexedSongs as $key => $song) {
	                $title[] = $song->title;
	                $url[] = $song->url;
                    $artist[] = $song->artist;
	                $id[] = $song->id;
                    $songsArray = ['title' => $title, 'url'=> $url, 'artist' => $artist, 'id' => $id];
	            }
                $json = $this->response(array(
                    'code' => 200,
                    'message' => 'Canciones en la app',
                    'data' => $songsArray
                ));
                return $json;
            } 
            catch (Exception $e) 
            {
                var_dump($e);
                exit;
                return $this->respuesta(500, 'Error del servidor', '');
            }
        }
        else
        {
        	return $this->respuesta(401, 'Usuario no autenticado', '');
        }
        
    }
    public function post_deleteSong()
    {
        $authenticated = $this->authenticate();
        $arrayAuthenticated = json_decode($authenticated, true);
         if($arrayAuthenticated['authenticated']){
            if(!isset($_POST['id'])){
                return $this->respuesta(400, 'parametros incorrectos', '');
            }
            if(empty($_POST['id'])){
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
                        ));
                    return $json; 
                }else{
                    return $this->respuesta(400, 'Cancion no encontrada', '');
                }
            }else{
                return $this->respuesta(401, 'Solo los administradores pueden borrar canciones', '');
            }
        }
    }
}













