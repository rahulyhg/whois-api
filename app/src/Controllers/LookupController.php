<?php

namespace App\Controllers;

use App\Abstracts\BaseController;

final class LookupController extends BaseController
{

    public function lookupAction($request, $response, $args)
    {

        $domain = $request->getParam('query'); 
        $deep = $request->getParam('deep'); 

        $this->whois->deepWhois = true;

        if(empty($domain)) {
            return $response->withRedirect('/lookup/');
        }

        if (isset($deep) && (($deep == 'false') || !$deep)) {
            $this->whois->deepWhois = false;
        }

        $result = $this->whois->lookup($domain);

        if($result === false || !is_array($result)) {

           return $response->withStatus(500)
            ->withJson(array('error', 'Not found'));
        }

        unset($result['rawdata']);

        if(isset($result)) {
          $this->model->setInfo($result);
        }

        $model = $this->model->toArray();
        return $response->withStatus(200)
          ->withHeader('Content-Type', 'application/json')
          ->withJson($model['data']);
    }
}