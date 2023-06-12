<?php

include_once "./Models/Pedido.php";
include_once "./Models/Mesa.php";
include_once "./Models/Usuario.php";


class Comanda{

    public $_idComanda;
    public $_mesa;
    public $_arrayPedidos;
    public $_mozo;
    public $_fechaComanda;

    public function crearComanda()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO comandas (ID_MESA,ID_MOZO,FECHA_COMANDA) VALUES (?,?,?)");
        $fechaString = date_format($this->_fechaComanda, 'Y-m-d H:i:s');

        $consulta->bindParam(1, $this->_mesa->_idMesa);
        $consulta->bindParam(2, $this->_mozo->_idUsuario);
        $consulta->bindParam(3, $fechaString);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comandas");
        $consulta->execute();
        // $consulta->fetchAll(PDO::FETCH_OBJ);
        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Comanda');
        $arrayComandas = array();
        foreach($consulta->fetchAll(PDO::FETCH_OBJ) as $prototipo)
        {
            array_push($arrayComandas,Comanda::transformarPrototipo($prototipo));
        }
        

        return $arrayComandas;
    }

    public static function obtenerComanda($id)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comandas WHERE ID_COMANDA = ?");
        $consulta->bindParam(1, $id);
        $consulta->execute();

        // return $consulta->fetchObject('Comanda');
        // return $consulta->fetch(PDO::FETCH_OBJ);
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        if($prototipeObject != false)
        {
            $rtn = Comanda::transformarPrototipo($prototipeObject);
        }

        return $rtn;
    }

    private static function transformarPrototipo($prototipo)
    {   
        $comanda = new Comanda();
        $comanda->_idComanda = $prototipo->ID_COMANDA;
        $comanda->_mozo = Usuario::obtenerUsuario($prototipo->ID_MOZO);
        $comanda->_arrayPedidos = Pedido::obtenerTodos($prototipo->ID_COMANDA);
        $comanda->_mesa = Mesa::obtenerMesa($prototipo->ID_MESA);
        $comanda->_fechaComanda = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_COMANDA,new DateTimeZone("America/Argentina/Buenos_Aires"));

        return $comanda;

    }

    public static function modificarComanda($comanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE comandas SET ID_MOZO = ?, ID_MESA = ? WHERE ID_COMANDA = ?");

        
        $consulta->bindParam(1, $comanda->_mozo->_idUsuario);
        $consulta->bindParam(2, $comanda->_mesa->_idMesa);
        $consulta->bindParam(3, $comanda->_idComanda);

        $consulta->execute();
    }

    public static function borrarComanda($comanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE comandas WHERE ID_COMANDA = ?");
        $consulta->bindParam(1, $comanda->_idComanda);
        foreach($comanda->_arrayPedidos as $pedido)
        {
            Pedido::borrarPedido($pedido);
        }

        $consulta->execute();
    }


    


}
?>