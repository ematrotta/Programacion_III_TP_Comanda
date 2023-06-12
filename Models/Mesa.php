<?php



class Mesa{

    public $_idMesa;
    public $_mozo;
    public $_comanda;
    public $_importeTotal;
    public $_nombreCliente;
    public $_estado;
    public $_fechaApertura;
    public $_fechaCierre;

    
    const ESTADO_PENDIENTE = "pendiente";
    const ESTADO_PREPARACION = "en preparación";
    const ESTADO_LISTO = "listo para servir";
    const ESTADO_CANCELADO = "cancelado";


    public function __construct($idMesa,$mozo,$comanda,$importeTotal,$nombreCliente,$estado,$fechaApertura,$fechaCierre) {
        $this->_idMesa = $idMesa;
        $this->_mozo = $mozo;
        $this->_comanda = $comanda;
        $this->_importeTotal = $importeTotal;
        $this->_nombreCliente = $nombreCliente;
        $this->_fechaApertura = $fechaApertura;
        $this->_fechaCierre = $fechaCierre;
        $this->_estado = $estado;
    }

}
?>