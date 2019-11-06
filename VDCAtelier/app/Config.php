<?php

class Config {
    protected $parameters;

    public function __construct(){
        require __DIR__ . '/Config/parameters.php';
        $this->parameters = $parameters;
    }

    public function getParametersConnect() {
        return $this->parameters['connect'];
    }

    public function getParametersUri() {
        return $this->parameters['uri'];
    }
}