<?php
session_start();
//session_destroy();
/*

@icd Account URI: https://icd.who.int/icdapi

@Token URL for OAuth2.0
URI: https://icdaccessmanagement.who.int/connect/token

@Header Accept Types
Accept:application/json
Accept:application/ld+json

JSON-LD (JSON for Linked Data), is a syntax defined by W3C for encoding Linked Data using JSON. JSON-LD documents could be 
consumed as simple JSON files.

@client Secrete and Key TO Access the API
ClientId: fdae929f-4ab1-4752-96a3-600ba62302e8_b1bf06be-f059-4579-8607-7cfa2918913a
ClientSecret: tn2SWRlSlnptEFLQap06Hoc/yoV4d0XP9Rm0YEZzrMM=
*/

/*Will need a seperat PHP file to handle ajax calls but for now lets just ry and make calls*/


class ICDCodes{
    const Token_EndPoint = "https://icdaccessmanagement.who.int/connect/token";
    const Client_Secret = "tn2SWRlSlnptEFLQap06Hoc/yoV4d0XP9Rm0YEZzrMM=";
    const Client_ID = "fdae929f-4ab1-4752-96a3-600ba62302e8_b1bf06be-f059-4579-8607-7cfa2918913a";
    const Scope = "icdapi_access";
    const Grant_Type= "client_credentials";

    private $token;
    private $uri;
    private $api_response;

    public function __construct($uri)
    {
        $this->uri = $uri;
        if(isset($_SESSION["token"]))
        {
            $this->token = $_SESSION["token"];
        }
        else{
            //var_dump("no token");exit();
            $this->newToken();
        }
    }

    public function get()
    {
        if($this->makeRequest() ==401)
        {
            //get new token
            $this->newToken();
            $this->makeRequest();
        }
        //return json
        return json_decode($this->api_response);
    }

    /*
    Make http Request 
     return http code
     */
    private function makeRequest()
    {
        $headers= array(
            'Authorization: Bearer '.$this->token,
            'Accept: application/json',
            'Accept-Language: en',
            'API-Version: v2'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // set curl without result echo
        $this->api_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $http_code;
    }

    /**
		 * Request an OAUTH 2.0 token from the server
		 */
		private function newToken() {
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, self::Token_EndPoint);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array(
					'client_id' => self::Client_ID,
					'client_secret' => self::Client_Secret,
					'scope' => self::Scope,
					'grant_type' => self::Grant_Type
			));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // set curl without result echo
			$result = curl_exec($ch);
			curl_close($ch);
			//var_dump($result);exit();
			$json_array = json_decode($result, true);
			$this->token = $json_array['access_token'];
			$_SESSION['token'] = $this->token;
		}
}

?>