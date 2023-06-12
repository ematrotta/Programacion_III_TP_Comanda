<?php

use Illuminate\Support\Facades\Process;

include_once './Models/Mesa.php';
include_once "./Models/Comanda.php";
include_once "./Models/Usuario.php";
require_once './Interfaces/IApiUsable.php';

class ComandaController extends Comanda implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idMesa = $parametros["idMesa"];
        $nombreEmpleado = $parametros['nombreEmpleado'];


        // Creamos la comanda
        $comanda = new Comanda();
        $comanda->_mesa = Mesa::obtenerMesa($idMesa);
        $comanda->_mozo = Usuario::obtenerUsuarioByName($nombreEmpleado);
        $comanda->_fechaComanda = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        

        $comanda->crearComanda();

        $payload = json_encode(array("mensaje" => "Comanda creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos comanda por nombre
        // $queryParams = $request->getQueryParams();
        // $id = $queryParams['idComanda'];
        $id = $args["comanda"];
        $comanda = Comanda::obtenerComanda($id);
        $payload = json_encode($comanda);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Comanda::obtenerTodos();
        $payload = json_encode(array("listaComanda" => $lista));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $args['comanda'];
        $nombreMozo = $parametros['nombreMozo'];
        $idMesa = $parametros['idMesa'];

        $mesa = Producto::obtenerProductoByName($idMesa);
        $empleado = Usuario::obtenerUsuarioByName($nombreMozo);
        $comanda = Comanda::obtenerComanda($id);
        
        $comanda->_mesa = $mesa;
        $comanda->_mozo = $empleado;

        Comanda::modificarComanda($comanda);

        $payload = json_encode(array("mensaje" => "Comanda modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        // $parametros = $request->getParsedBody();
        $id = $args['comanda'];
        $comanda = Comanda::obtenerComanda($id);
        Comanda::borrarComanda($comanda);

        $payload = json_encode(array("mensaje" => "Comanda borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

?>