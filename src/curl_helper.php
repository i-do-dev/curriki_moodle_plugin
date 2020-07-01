<?php

class curl_helper {

    protected static $url;
    protected static $headers;
    protected static $query;
    protected static $params_count;
    protected static $responses;

    /**
     * @param $url
     * @param $headers
     * @param $query
     */
    public static function prepare($url, $query, $headers = array()) {
        self::$url = $url;
        self::$headers = $headers;        
        self::$params_count = count($query);
        self::$query = $query;
    }

    /**
     *  Execute post method curl request
     */
    public static function exec_post() {

        $fields_string = "";
        foreach(self::$query as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,self::$url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, self::$headers);
        curl_setopt($curl, CURLOPT_POST, self::$params_count);
        curl_setopt($curl, CURLOPT_POSTFIELDS, self::$query);
        self::$responses = curl_exec($curl);
        curl_close($curl);
    }

    /**
     *  Execute get method curl request
     */
    public static function exec_get() {

        self::$query = http_build_query(self::$query);
        $full_url = self::$url.'?'.self::$query;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$full_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, self::$headers);
        self::$responses = curl_exec($curl);
        curl_close($curl);

    }

    /**
     * @return mixed
     */
    public static function get_response() {
        return self::$responses;
    }

    /**
     * @return mixed
     */
    public static function get_response_assoc() {
        return json_decode(self::$responses, true);
    }


}