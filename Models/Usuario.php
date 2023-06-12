<?php

class Usuario{

    public $_idUsuario;
    public $_nombre;
    public $_fechaCreacion;
    public $_fechaFinalizacion;
    public $_user;
    public $_password;
    public $_sector;
    public $_tipo;

    const TIPO_BARTENDER = "bartender";
    const TIPO_CERVEZERO = "cervecero";
    const TIPO_MOZO = "mozo";
    const TIPO_COCINERO = "cocinero";
    const TIPO_SOCIO = "socio";

    const SECTOR_COCINA = "cocina";
    const SECTOR_CERVECERIA = "cerveza";
    const SECTOR_MESAS = "mesas";
    const SECTOR_ADMINISTRACION = "adminisracion";
    const SECTOR_BAR = "bar";

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (NOMBRE, FECHA_CREACION,USER,PASSWORD,SECTOR,TIPO) VALUES (?,?,?,?,?,?)");
        $fecha = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $claveHash = password_hash($this->_password, PASSWORD_DEFAULT);
        $consulta->bindParam(1, $this->_nombre);
        $consulta->bindParam(2, date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->bindParam(3, $this->_user);
        $consulta->bindParam(4, $claveHash);
        $consulta->bindParam(5, $this->_sector);
        $consulta->bindParam(6, $this->_tipo);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
        $consulta->execute();
        // $consulta->fetchAll(PDO::FETCH_OBJ);
        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        $arrayUsuarios = array();
        foreach($consulta->fetchAll(PDO::FETCH_OBJ) as $prototipo)
        {
            array_push($arrayUsuarios,Usuario::transformarPrototipo($prototipo));
        }
        

        return $arrayUsuarios;
    }

    public static function obtenerUsuario($id)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE ID_USUARIO = ?");
        $consulta->bindParam(1, $id);
        $consulta->execute();

        // return $consulta->fetchObject('Usuario');
        // return $consulta->fetch(PDO::FETCH_OBJ);
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        if($prototipeObject != false)
        {
            $rtn = Usuario::transformarPrototipo($prototipeObject);
        }

        return $rtn;
    }

    public static function obtenerUsuarioByName($nombreUsuario)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE NOMBRE = ?");
        $consulta->bindParam(1, $nombreUsuario);
        $consulta->execute();

        // return $consulta->fetchObject('Usuario');
        // return $consulta->fetch(PDO::FETCH_OBJ);
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        if($prototipeObject != false)
        {
            $rtn = Usuario::transformarPrototipo($prototipeObject);
        }

        return $rtn;
    }

    private static function transformarPrototipo($prototipo)
    {   
        $usuario = new Usuario();
        $usuario->_idUsuario = $prototipo->ID_USUARIO;
        $usuario->_nombre = $prototipo->NOMBRE;
        $usuario->_fechaCreacion = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_CREACION,new DateTimeZone("America/Argentina/Buenos_Aires"));
        if($prototipo->FECHA_BAJA != NULL)
        {
            $usuario->_fechaFinalizacion = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_BAJA,new DateTimeZone("America/Argentina/Buenos_Aires"));

        }
        else{
            $usuario->_fechaFinalizacion = $prototipo->FECHA_BAJA;
        }
        $usuario->_user = $prototipo->USER;
        $usuario->_password = $prototipo->PASSWORD;
        $usuario->_sector = $prototipo->SECTOR;
        $usuario->_tipo = $prototipo->TIPO;
        return $usuario;

    }

    public static function modificarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET NOMBRE = ?, USER = ?, PASSWORD = ?, SECTOR = ?, TIPO = ?  WHERE ID_USUARIO = ?");
        $claveHash = password_hash($usuario->_password, PASSWORD_DEFAULT);
        $consulta->bindParam(1, $usuario->_nombre);
        $consulta->bindParam(2, $usuario->_user);
        $consulta->bindParam(3, $claveHash);
        $consulta->bindParam(4, $usuario->_sector);
        $consulta->bindParam(5, $usuario->_tipo);
        $consulta->bindParam(6, $usuario->_idUsuario);
        $consulta->execute();
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET FECHA_BAJA = ? WHERE ID_USUARIO = ?");
        $fecha = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $fechaString = date_format($fecha, 'Y-m-d H:i:s');
        $consulta->bindParam(1, $fechaString);
        $consulta->bindParam(2, $usuario->_idUsuario);
        $consulta->execute();
    }




}

?>