<?php
class App
{
    protected $baseUrl;
    protected $route;
    public $componentFolder = 'components';
    protected $sessionVar;
    protected $protectedRoute;
    protected $loginPage;

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    public function setRoute($array = [])
    {
        $this->route = $array;

        return $this;
    }

    public function setAuthSessionVar($string)
    {
        $this->sessionVar = $string;

        return $this;
    }

    public function setProtectedRoute($array = [])
    {
        $this->protectedRoute = $array;

        return $this;
    }

    public function setLoginPage($string)
    {
        $this->loginPage = $string;

        return $this;
    }

    public function run()
    {
        $urlString = $this->getAllRoutes();

        if(!$urlString) {
            $path = APPROOT.'/'.$this->componentFolder.'/'.$this->route['default'];
            $this->loadRoute($path);
        }
        else {
            $url = implode('/', $urlString);

            if( $this->userNotAuthorized($url) ) {
                $path = APPROOT.'/'.$this->componentFolder.'/'.$this->route[$this->loginPage];
            }
            else {
                if( !isset($this->route[$url]) ) {
                    $path = APPROOT.'/'.$this->componentFolder.'/'.$url;
                }
                else {
                    $path = APPROOT.'/'.$this->componentFolder.'/'.$this->route[$url];
                }
            }

            $this->loadRoute($path);
        }
    }

    protected function userNotAuthorized($url)
    {
        if( isset($this->protectedRoute[$url]) ) {
            if( isset($_SESSION[$this->sessionVar]) && $_SESSION[$this->sessionVar] != 'true' ) {
                return false;
            }
        }

        return true;
    }

    public function loadRoute($path)
    {
        $path .= '.php';

        if( file_exists($path) ) {
            include $path;
        }
        else {
            $path = APPROOT.'/'.$this->componentFolder.'/error/404.php';
            include $path;
        }
    }

    public function getAllRoutes()
    {
        $actualUrl = rtrim(str_replace($this->baseUrl, '', $this->currentUrl()), '/');

        if( strstr($actualUrl, '/') ) {
            return $this->sanitizeUrlArray(explode('/', $actualUrl));
        }
        else if( strlen(trim($actualUrl)) > 0 ) {
            return $this->sanitizeUrlArray([$actualUrl]);
        }

        return NULL;
    }

    protected function sanitizeUrlArray($array = [])
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[] = $this->sanitizeUrlString($value);
        }

        return $newArray;
    }


    public function currentUrl()
    {
        return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    private function sanitizeUrlString($string){
        return preg_replace("/[^a-zA-Z0-9-._]+/", "", $string);
    }
}

class AppFactory
{
    public static function load($path)
    {
        $app = new App();
        $app->loadRoute(APPROOT.'/'.$app->componentFolder.'/'.$path);
    }

    public static function CurrentUrl()
    {
        $app = new App();
        $app->currentUrl();
    }
}
