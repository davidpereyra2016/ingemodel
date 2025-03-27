<?php

class ModeloReservas {
    private $conexion;
    
    public function __construct() {
        $this->conexion = BD::crearInstancia();
    }
}