<?php

/**
 * Image placeholder request parser
 */

    class PlaceholderRequest {

        public static $requestPath;
        public static $queryString;

        public static $config;


       /**
        * Get/parse path + query string parameters
        *
        * @return array
        */
            public static function parseParameters() {
                    self::cachingHeaders(); // Verify if the browser has a cached version 
                    
                    self::$requestPath = self::requestPath();
                    self::$queryString = self::queryString();			

                    if(self::$requestPath) {
                            // Merge two arrays
                            // Order is important ! The query string shouldn't erase request path !
                            $config = array_merge(self::$queryString, self::$requestPath); 

                            self::$config = $config;

                            return true;
                    }

                    return false;			
            }
        
            
       /**
        * Stop the script if the browser already has image in its cache
        */
          
            public static function cachingHeaders () {
                    header("Cache-Control: private, max-age=10800, pre-check=10800"); 
                    header("Pragma: private");
                    // Set to expire in 2 days 
                    header("Expires: " . date(DATE_RFC822,strtotime(" 2 day"))); 

                    // If the browser has a cached version of this image, send 304 
                    if( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ) {
                            header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304); 
                            exit();
                    }
            }

            
       /**
        * Extract string from request & parse it (main parameters)
        *
        * @return array
        */
            public static function requestPath() {	
                    $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']) );
                    $configString = substr_replace($_SERVER['REQUEST_URI'], '', 0, strlen($scriptName)+1);

                    // Clear query string from path
                    $configString = preg_replace('/\?.*/', '', $configString);			

                    if($configString!='') {
                            // Numeric indexed URL params (extracted from path)
                            $params = explode('/', $configString);

                            // Parse parameters (and get named configuration)
                            $requestPathConfig = self::parseRequestPath($params);


                            return $requestPathConfig;
                    }

                    return false;
            }


       /**
        * Verify parameters and name them
        *
        * @param array List of numeric indexed URL params (extracted from path)
        *
        * @return array
        */
            public static function parseRequestPath($params) {	
                    $requestPathConfig = array();

                    // First parameter is numeric only (but 'x' allowed) !!
                    $params[0] = preg_replace('/[^0-9x]/', '', $params[0]);
                    $dimensions = explode('x', $params[0]);
                    $dimensions = array_slice($dimensions, 0, 2); // Keep only two first numbers found

                    $requestPathConfig['width'] = $dimensions[0];
                    $requestPathConfig['height'] = !empty($dimensions[1]) ? $dimensions[1] : $dimensions[0]; // Detect square...


                    // Second & third parameters must be an hex color

                    if( !empty($params[1]) && preg_match('/^[a-f0-9]{6}$/i', $params[1]) )
                            $requestPathConfig['bgColor'] = $params[1];

                    if( !empty($params[2]) && preg_match('/^[a-f0-9]{6}$/i', $params[2]) )
                            $requestPathConfig['textColor'] = $params[2];



                    return $requestPathConfig;
            }


       /**
        * Get query string (additional parameters)
        *
        * @return array
        */
            public static function queryString() {				
                    $queryString = array();

                    if ( function_exists('mb_parse_str') )
                            mb_parse_str($_SERVER['QUERY_STRING'], $queryString);
                    else
                            parse_str($_SERVER['QUERY_STRING'], $queryString);

                    return $queryString; 
            }


}
?>