<?php

class Application extends Silex\Application
{

  private $httpsMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];

  const WEB_DIR = 'web';
  const CONTROLLER_DIR = 'Controller';
  const DEFAULT_ACTION = 'index';
  const DEFAULT_CONTROLLER = 'default';

  public function __construct(array $values = array())
  {
    parent::__construct($values);

    // Defaults
    $this['debug'] = TRUE;
    // Usefull params
    $this['dir.src'] = __DIR__;
    $this['dir.web'] = __DIR__ . '/../' . self::WEB_DIR;

    $this->registerControllers();
  }

  protected function registerControllers()
  {
    $actions = [];
    $it = new DirectoryIterator($this['dir.src'] . '/' . self::CONTROLLER_DIR);
    foreach($it as $file) {
      if(!$file->isFile()) {
        continue;
      }
      $fileName = $file->getFilename();
      $controllerName = substr($fileName, 0, -4);
      $controllerFullName = self::CONTROLLER_DIR . '\\' . $controllerName;
      if(substr($fileName, -14) === 'Controller.php') {
        $methods = get_class_methods($controllerFullName);
        if($methods) {
          foreach($methods as $method) {
            if($this->isAction($method)) {
              if(!isset($actions[$controllerFullName])) {
                $actions[$controllerFullName] = [
                    'object' => new $controllerFullName($this),
                    'controllerName' => $controllerName,
                    'actions' => []
                ];
              }
              $actions[$controllerFullName]['actions'][] = $method;
            }
          }
        }
      }
    }
    foreach($actions as $key => $controller) {
      foreach($controller['actions'] as $action) {
        $this->get(
                $this->getRouteForAction($controller['controllerName'], $action), [$controller['object'], $action]
        );
      }
    }
  }

  public function getRouteForAction($controllerName, $methodName)
  {
    $methodPart = explode('_', $methodName, 2)[1];
    $controllerPart = lcfirst(substr($controllerName, 0, -10));
    if($methodPart === self::DEFAULT_ACTION) {
      $methodPart = '';
    }

    $paramsAddition = [];
    foreach(
            $this->getFunctionParams(self::CONTROLLER_DIR.'\\'.$controllerName, $methodName)
              as $param => $val){
      $paramsAddition[] = '{'.$param.'}';
    }
    $paramsAddition = implode('/', $paramsAddition);


    if($controllerPart === self::DEFAULT_CONTROLLER && empty($methodPart)) {
      return '/'.$paramsAddition;
    } else {
      return "/" . $controllerPart . (!empty($methodPart) ? '/' . $methodPart : '').(empty($paramsAddition)?'':'/'.$paramsAddition);
    }
  }

  protected function isAction($methodName)
  {
    $methodData = explode('_', $methodName, 2);
    if(count($methodData) > 1 && in_array($methodData[0], $this->httpsMethods)) {
      return TRUE;
    }
    return FALSE;
  }

  protected function getFunctionParams($controllerName, $methodName)
  {
    $attribute_names = [];
    $fx = new ReflectionMethod($controllerName, $methodName);
    foreach($fx->getParameters() as $param) {
      $attribute_names[$param->name] = NULL;
      if($param->isOptional()) {
        $attribute_names[$param->name] = $param->getDefaultValue();
      }
    }
    return $attribute_names;
  }

}
