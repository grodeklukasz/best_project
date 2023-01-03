<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class SessionService{
    private $session;
    private $requestStack;

    public function __construct(RequestStack $requestStack){
        $this->requestStack = $requestStack;
        $this->session = $this->requestStack->getSession();
    }

    public function setSessionValues($name,$value){
        $this->session->set($name,$value);
    }

    public function getSessionValue($name){
        return $this->session->get($name);
    }

    public function remove($name){
        
        $this->session->remove($name);

    }

    public function clear(){
        $this->session->clear();
    }

    public function checkConditions(): bool
    {
        if($this->getSessionValue('isLogged')||$this->getSessionValue('role')!='admin'){
            return True;
        }else{
            return False;
        }
        
    }
   
}