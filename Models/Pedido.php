<?php

include_once "./Models/Comanda.php";
include_once "./Models/Producto.php";
include_once "./Models/Usuario.php";

class Pedido{

    public $_idPedido;
    public $_comanda;
    public $_usuarioAsignado;
    public $_producto;
    public $_cantidad;
    public $_fechaEstimadaDeFinalizacion;
    public $_fechaFinalizacion;
    public $_sector;
    public $_estado;
    
    const ESTADO_PENDIENTE = "pendiente";
    const ESTADO_PREPARACION = "en preparación";
    const ESTADO_LISTO = "listo para servir";
    const ESTADO_CANCELADO = "cancelado";

    const SECTOR_CERVEZA = "cerveza";
    const SECTOR_BARTENDER = "bartender";
    const SECTOR_COCINA = "cocina";

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (ID_COMANDA,ID_USUARIO,ID_PRODUCTO,CANTIDAD,FECHA_ESTIMADA_FINALIZACION,SECTOR,ESTADO) VALUES (?,?,?,?,?,?,?)");
        
        // Calulo la fecha de finalizacion estimada del pedido
        $tiempoEnMinutosDeProducto = $this->_cantidad*$this->_producto->_tiempoPreparacion;
        $interval = DateInterval::createFromDateString($tiempoEnMinutosDeProducto.'minutes');
        $fechaEstimadaFinalizacion = $this->_comanda->_fechaComanda->add($interval);

        // Se cargan los datos faltantes en el pedido
        $this->_fechaEstimadaDeFinalizacion = $fechaEstimadaFinalizacion;
        $this->_sector = $this->_producto->_sector;
        $this->_estado = self::ESTADO_PENDIENTE;

        $fechaEstimadaFinalizacionString = date_format($this->_fechaEstimadaDeFinalizacion, 'Y-m-d H:i:s');

        
        $consulta->bindParam(1, $this->_comanda->_idComanda);
        $consulta->bindParam(2, $this->_usuarioAsignado->_idUsuario);
        $consulta->bindParam(3, $this->_producto->_idProducto);
        $consulta->bindParam(4, $this->_cantidad);
        $consulta->bindParam(5, $fechaEstimadaFinalizacionString);
        $consulta->bindParam(6, $this->_sector);
        $consulta->bindParam(7, $this->_estado);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos($idComanda = NULL)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        if($idComanda == null)
        {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        }
        else{

            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE ID_COMANDA = ?");
            $consulta->bindParam(1, $idComanda);
        }
        
        $consulta->execute();
        // $consulta->fetchAll(PDO::FETCH_OBJ);
        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        $arrayPedidos = array();
        foreach($consulta->fetchAll(PDO::FETCH_OBJ) as $prototipo)
        {
            array_push($arrayPedidos,Pedido::transformarPrototipo($prototipo));
        }
        

        return $arrayPedidos;
    }

    public static function obtenerPedido($id)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE ID_PEDIDO = ?");
        $consulta->bindParam(1, $id);
        $consulta->execute();

        // return $consulta->fetchObject('Pedido');
        // return $consulta->fetch(PDO::FETCH_OBJ);
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        if($prototipeObject != false)
        {
            $rtn = Pedido::transformarPrototipo($prototipeObject);
        }

        return $rtn;
    }

    private static function transformarPrototipo($prototipo)
    {   
        $pedido = new Pedido();
        $pedido->_idPedido = $prototipo->ID_PEDIDO;
        $pedido->_comanda = Comanda::obtenerComanda($prototipo->ID_COMANDA);
        $pedido->_usuarioAsignado = Usuario::obtenerUsuario($prototipo->ID_USUARIO);
        $pedido->_producto = Producto::obtenerProducto($prototipo->ID_PRODUCTO);
        $pedido->_cantidad = $prototipo->CANTIDAD;
        $pedido->_fechaEstimadaDeFinalizacion = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_ESTIMADA_FINALIZACION,new DateTimeZone("America/Argentina/Buenos_Aires"));
        if($prototipo->FECHA_FINALIZACION != NULL)
        {
            $pedido->_fechaFinalizacion = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_FINALIZACION,new DateTimeZone("America/Argentina/Buenos_Aires"));

        }
        else{
            $pedido->_fechaFinalizacion = $prototipo->FECHA_BAJA;
        }
        $pedido->_sector = $prototipo->SECTOR;
        $pedido->_estado = $prototipo->ESTADO;
        return $pedido;

    }

    public static function modificarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET ID_USUARIO = ?, ID_RPDUCTO = ?, CANTIDAD = ?, FECHA_ESTIMADA_FINALIZACION = ?, SECTOR = ?, ESTADO = ? WHERE ID_PEDIDO = ?");
        // Calulo la fecha de finalizacion estimada del pedido
        $tiempoEnMinutosDeProducto = $pedido->_cantidad*$pedido->_producto->_tiempoPreparacion;
        $interval = DateInterval::createFromDateString($tiempoEnMinutosDeProducto.'minutes');
        $fechaEstimadaFinalizacion = $pedido->_comanda->_fechaComanda->add($interval);
        $fechaEstimadaFinalizacionString = date_format($fechaEstimadaFinalizacion, 'Y-m-d H:i:s');
        
        $consulta->bindParam(1, $pedido->_usuarioAsignado->_idUsuario);
        $consulta->bindParam(2, $pedido->_producto->_idProducto);
        $consulta->bindParam(3, $pedido->_cantidad);
        $consulta->bindParam(4, $fechaEstimadaFinalizacionString);
        $consulta->bindParam(5, $pedido->_producto->_sector);
        $consulta->bindParam(6, $pedido->_estado);

        $consulta->execute();
    }

    public static function borrarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET ESTADO = ? WHERE ID_PEDIDO = ?");
        $consulta->bindParam(1, self::ESTADO_CANCELADO);
        $consulta->bindParam(2, $pedido->_idPedido);
        $consulta->execute();
    }




}

?>