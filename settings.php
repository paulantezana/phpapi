<?php
date_default_timezone_set('America/Lima');

$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$requestUri = parse_url('http://example.com' . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
$virtualPath = '/' . ltrim(substr($requestUri, strlen($scriptName)), '/');

function getEnvValueByKey()
{
    $file = new SplFileObject(__DIR__ . '/.env');
    $data = [];

    while (!$file->eof()) {
        $line = $file->fgets();
        if (strlen($line) > 3) {
            list($key, $value) = explode("=", $line, 2);
            $data[trim($key)] = trim($value ?? '');
        }
    }

    return $data;
}

define('APP_ENV', getEnvValueByKey());
define('URL', $virtualPath);

define('ROOT_DIR', $_SERVER["DOCUMENT_ROOT"] . rtrim($scriptName, '/'));
define('CONTROLLER_PATH', ROOT_DIR . '/Controllers');
define('MODEL_PATH', ROOT_DIR . '/Models');

class Result extends stdClass
{
    public $success;
    public $message;
    public $result;

    function __construct()
    {
        $this->success = false;
        $this->message = '';
        $this->result = null;
    }
}

class Router
{
    private $controller;
    private $method;
    private $param;

    public function __construct()
    {
        $this->matchRoute();
    }

    private function matchRoute()
    {
        $url = explode('/', URL);

        $this->method = !empty($url[2]) ? $url[2] : 'home';
        $this->controller = !empty($url[1]) ? $url[1] : 'Page';

        $this->controller = ucwords($this->controller) . 'Controller';
        if (!is_file(CONTROLLER_PATH . "/{$this->controller}.php")) {
            $this->controller = 'PageController';
            $this->method = 'error404';
        }

        require_once(CONTROLLER_PATH . "/{$this->controller}.php");
        if (!method_exists($this->controller, $this->method)) {
            $this->controller = 'PageController';
            $this->method = 'error404';
            require_once(CONTROLLER_PATH . "/{$this->controller}.php");
        }
    }

    private function requestIsJson()
    {
        if (strtolower($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json' || strtolower($_SERVER['CONTENT_TYPE'] ?? '') === 'application/json') {
            return true;
        }
        return false;
    }

    private function setHeaders(string $contentType = 'json')
    {
        switch ($contentType) {
            case 'json':
                header('Content-Type: application/json; charset=utf-8');
                break;
            default:
                # code...
                break;
        }
    }

    public function run()
    {
        $database = new Database();
        $res = new Result();

        try {

            $controller = new $this->controller($database->getConnection());
            $method = $this->method;
            $response = $controller->$method($this->param);

            if ($response instanceof Result) {
                $this->setHeaders('json');
                echo json_encode($response);
            }
        } catch (ControlledException $e) {
            $res->message = $e->getMessage();
            $res->errorType = 'warning';
            $res->title = 'VALIDACIÓN';

            $this->setHeaders('json');
            echo json_encode($res);
            die();
        } catch (Exception $e) {
            // $id = $this->saveException($database->getConnection(), $e);
            $id = 999999;
            $res->errorType = 'danger';
            $res->title = 'ERROR NO CONTROLADO';
            $res->message = 'Ha ocurrido un problema no controlado, por favor comuniquese con TI y proporcionele éste numero para que lo ayuden : ' . $id;
            $this->setHeaders('json');
            echo json_encode($res);
            die();
        }
    }
}
