<?php

namespace Controller;

use BaseController;

/**
 * Description of AnotherController
 *
 * @author Alexander Ferenets (aka Istamendil) – http://istamendil.info
 */
class AnotherController extends BaseController
{
  public function GET_index(){
    return "Hello from Another controller (index)";
  }
  
  public function GET_second($a = 1){
    return "Hello from Another controller (second - ".$a.")";
  }
}
