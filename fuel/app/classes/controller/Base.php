<?php 
use \Firebase\JWT\JWT;

class Controller_Base extends Controller_Rest
{
	private static $secret_key = 'AppMusic';
    private static $encrypt = ['HS256'];
    private static $aud = null;
    public $key = 'ETHBSBSAOOIBb134742166128278gutVYTAS76215GFGASUDBG665GBYV656STG2383782BY7Y8R23RY3787QWWQIJ';

    protected function respuesta($code, $message, $data = [])
    {
        $json = $this->response(array(
                    'code' => $code,
                    'message' => $message,
                    'data' => $data
                ));
            return $json;
    }
    protected function encode($data)
    {
        return  JWT::encode($data, $this->key);
        
    }
    protected function decode($data)
    {
        return  JWT::decode($data, $this->key, array('HS256'));
        
    }

	protected function encodeToken($userName, $password, $id, $email, $id_role)
    {
        $token = array(
        		"id" => $id,
                "userName" => $userName,
                "password" => $password,
                "email" => $email,
                "role" => $id_role,
        );
        $encodedToken = JWT::encode($token, $this->key);
        return $encodedToken;
    }
    protected function decodeToken()
    {
        $header = apache_request_headers();
        $token = $header['Authorization'];
        if(!empty($token))
        {
            $decodedToken = JWT::decode($token, $this->key, array('HS256'));
            return $decodedToken;
        }      
    }

    protected function authenticate(){
        try {
               
            $header = apache_request_headers();
            $token = $header['Authorization'];
            if(!empty($token))
            {
                $decodedToken = JWT::decode($token, $this->key, array('HS256'));
                $query = Model_Users::find('all', 
                    ['where' => ['userName' => $decodedToken->userName, 
                                 'password' => $decodedToken->password, 
                                 'id_role' => $decodedToken->role,
                                 'email' => $decodedToken->email,
                                 'id' => $decodedToken->id
                                ]]);
                if($query != null)
                {
                    $json = array(
                    'code' => 200,
                    'message' => 'Usuario autenticado',
                    'authenticated' => true,
                    'data' => $token
                    );
                    return json_encode($json);

                }else{
                    $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuario no autenticado(query vacia)',
                    'authenticated' => false,
                    'data' => null
                    ));
                    return $json;
                
                }
            }else{
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuario no autenticado(token vacio)',
                    'authenticated' => false,
                    'data' => null
                    ));
                    return $json;
            }
        } 
        catch (Exception $UnexpectedValueException)
        {
            $json = $this->response(array(
                    'code' => 401,
                    'message' => $UnexpectedValueException,
                    'authenticated' => false,
                    'data' => null
                    ));
                    return $json;
        }
    }
}