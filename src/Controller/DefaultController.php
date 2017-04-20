<?php

namespace Controller;

use BaseController;

/**
 * Description of DefaultController
 *
 * @author Alexander Ferenets (aka Istamendil) – http://istamendil.info
 */
class DefaultController extends BaseController
{
  public function GET_index(){
    return "Hello world from Controller";
  }
}
