<?php

class Controller
{
    public $layout = 'main';

    public function __construct() {

    }

    protected function _view($view, $data = array()) {

        $controllerName = get_class($this);
        $controllerPathName = str_replace("controller","",strtolower($controllerName));

        $viewPath = 'application/views/' . $controllerPathName . '/' . $view . '.php';

        if(!empty($data) && is_array($data)) {
            foreach($data as $key => $item) {
                ${$key} = $item;
            }
        }
        
        ob_start();
        include $viewPath;
        $viewResult = ob_get_clean();
        echo $viewResult;
    }

    protected function _model($modelClass = false) {

        if ($modelClass) {
            $modelPath = APPLICATION_PATH . 'models' . DIRECTORY_SEPARATOR . $modelClass . '.php';
            require_once $modelPath;
            $model = strtolower($modelClass);
            $this->$model = new $modelClass;
        }
    }
}


