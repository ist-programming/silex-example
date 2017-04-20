<?php

/**
 * Description of BaseController
 *
 * @author Alexander Ferenets (aka Istamendil) – http://istamendil.info
 */
class BaseController
{
  private $app;
  
  public function __construct(Application $app){
    $this->app = $app;
  }
  
  protected function getApp(){
    return $this->app;
  }
  protected function get($component){
    return $this->getApp()[$component];
  }
  protected function getRequest(){
    return $this->get('request_stack')->getCurrentRequest();
  }
}
