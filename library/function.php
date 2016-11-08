<?php
/**
 * Hash a string
 *
 * @param $string
 * @return bool|string
 */
function bcrypt($string)
{
    return password_hash($string, PASSWORD_DEFAULT);
}


/**
 * Checking if encrypted string is valid
 *
 * @param $plainString
 * @param $hash
 * @return bool
 */
function validBcrypt($plainString, $hash)
{
    return password_verify($plainString, $hash);
}


/**
 * Get first record of db
 *
 * @param $object
 * @return mixed
 */
function firstRecord($object)
{
    return count($object) > 0 ? $object[0] : NULL;
}

/**
 * [array_pluck]
 * @param  [type] $array [description]
 * @param  [type] $key   [description]
 * @return [type]        [description]
 */
function array_pluck($array, $key) {
    return array_map(function($v) use ($key)  {
        return is_object($v) ? $v->$key : $v[$key];
    }, $array);
}

/**
 * Console log
 * @param  [type] $array [description]
 * @return [type]        [description]
 */
function dd($array) {
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

/**
 * [redirect description]
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function redirect($url) {
    header('Location: '.$url);
    exit();
}


function cleanUrlString($string){
    return preg_replace("/[^a-zA-Z0-9-._]+/", "", $string);
}

function jsRedirect($url, $timeout = 5, $nomsg = false){
    if(!$nomsg) $html = '<p style="font-family: Consolas, Courier, monospace; font-size: 12px; text-align: center; padding: 100px 0"><a href="'.$url.'">click here</a> if your browser doesn\'t automatically redirect you in '.$timeout.' second(s)</p>';
    else $html = '';
    $html .= '<script>';
    $html .= 'setTimeout(function() { window.location.href = "'.$url.'"; }, '.($timeout * 1000).');';
    $html .= '</script>';
    return $html;
}

function cleanEmailString($email){
    return preg_replace("/[^a-z0-9+_.@-]/i", "", $email);
}

function cleanData($string){

    $search = array(
        '@<script[^>]*?>.*?</script>@si',
        '@<[\/\!]*?[^<>]*?>@si',
        '@<style[^>]*?>.*?</style>@siU',
        '@<![\s\S]*?--[ \t\n\r]*>@'
    );

    $string = preg_replace($search, '', $string);

    $string = strip_tags(trim($string));
    $string = htmlentities($string, ENT_QUOTES, "UTF-8");

    if (get_magic_quotes_gpc())
        $string = stripslashes($string);

    return $string;
}

function safeString($str){
    return filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
}

function sanitize($data,$type=array(),$exception=array()){
    if(is_array($data)){
        $sant = array();
        foreach($data as $key=>$val){
            if( !array_search($key,$exception) && !in_array($key,$exception) ) {
                if( isset($type[$key]) && $type[$key]=='int' && trim($val)!=NULL) {
                    if( is_numeric($val) )
                        $sant[$key] = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
                    else
                        $sant[$key] = '';
                }
                elseif( isset($type[$key]) && $type[$key]=='email' && trim($val)!=NULL){
                    if( !filter_var($val, FILTER_VALIDATE_EMAIL) )
                        $sant[$key] = '';
                    else {
                        $val = cleanEmailString($val);
                        $sant[$key] = filter_var($val, FILTER_SANITIZE_EMAIL);
                    }
                }
                elseif( isset($type[$key]) && ($type[$key]=='bbcode' || $type[$key]=='low') && trim($val)!=NULL ){
                    $sant[$key] = htmlentities($val, ENT_QUOTES, "UTF-8");

                    $search = array(
                        '@<script[^>]*?>.*?</script>@si',
                        '@<[\/\!]*?[^<>]*?>@si',
                        '@<style[^>]*?>.*?</style>@siU',
                        '@<![\s\S]*?--[ \t\n\r]*>@'
                    );

                    $sant[$key] = preg_replace($search, '', $sant[$key]);
                    $sant[$key] = strip_tags($sant[$key]);
                }
                elseif( trim($val)!=NULL ) {
                    $normal = cleanData($val);
                    $sant[$key] = filter_var($normal, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
                }
                elseif( trim($val)==NULL ) $sant[$key] = '';
                else $sant[$key] = '';
            }
        }

        foreach($exception as $key){
            $sant[$key] = $data[$key];
        }
    }
    return $sant;
}

function bindValue($string, $findArray = [], $valueArray = []) {
    $findPregArray = [];
    $replacePregArray = [];

    foreach ($findArray as $findText) {
        $findPregArray[] = '/{'.$findText.'}/';
        $replacePregArray[] = $valueArray[$findText];
    }

    return preg_replace($findPregArray, $replacePregArray, $string);
}

function stringLength($string) {
    return strlen(trim($string));
}
