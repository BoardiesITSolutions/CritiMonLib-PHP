<?php


define("critimon_url", "https://critimon-engine.boardiesitsolutions.com");

class CritiMonBase
{
    protected $app_id;
    protected $api_key;
    protected $app_version;
    protected $device_id;
    protected $cookies;
    protected $initialised = false;


    public function __construct($app_id, $api_key, $version_number)
    {
        $this->app_id = $app_id;
        $this->api_key = $api_key;
        $this->app_version = $version_number;
    }

    protected function returnCurlClient($postFields, $method)
    {
        $curl = curl_init();

        $url = critimon_url . "/" . $method;

        $headers = array();
        $headers[] = "authorisation-token: " . $this->api_key;
        $headers[] = "content-type: application/x-www-form-urlencoded";
        $headers[] = "user-agent: CritiMon PHP Library";
        if (($this->cookies !== null) && count($this->cookies) > 0)
        {
            $cookieString = "Cookie: ";
            foreach ($this->cookies as $key => $value)
            {
                $cookieString .= "$key=$value; ";
            }
            $cookieString = trim($cookieString);
            $headers[] = $cookieString;
            curl_setopt($curl, CURLOPT_COOKIE, $cookieString);
        }
        else
        {
            //If there are cookies set within PHP itself we'll add that as a header here
            $cookieString = "Cookie: ";
            foreach ($_COOKIE as $key=>$value)
            {
                $cookieString .= "$key=$value; ";
            }
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_HEADER => 1,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->urlEncodePostArray($postFields),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => $headers
        ));

        /*if (isset($this->session_id) && !empty($this->session_id))
        {
            curl_setopt($curl, CURLOPT_COOKIE, "SESSIONID=".$this->session_id);
        }*/

        return $curl;
    }

    private function urlEncodePostArray($postFields)
    {
        $encodedPostFields = "";
        foreach ($postFields as $key => $value)
        {
            if ($key === "CustomProperty")
            {
                $encodedPostFields .= "$key=" . json_encode($value) ."&";
            }
            else
            {
                $encodedPostFields .= "$key=$value&";
            }
        }
        //Remove the end & and return
        return $encodedPostFields.substr(0, strlen($encodedPostFields)-1);
    }

    protected function generateRandomString() {
        $length = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
