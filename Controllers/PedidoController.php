<?php

use Illuminate\Support\Facades\Process;

include_once './Models/Pedido.php';
include_once "./Models/Comanda.php";
include_once "./Models/Producto.php";
include_once "./Models/Usuario.php";
require_once './Interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idComada = $parametros['idComanda'];
        $nombreEmpleado = $parametros['nombreEmpleado'];
        $nombreProducto = $parametros['nombreProducto'];
        $cantidad = $parametros['cantidad'];

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->_comanda = Comanda::obtenerComanda($idComada);
        $pedido->_usuarioAsignado = Usuario::obtenerUsuarioByName($nombreEmpleado);
        $pedido->_producto = Producto::obtenerProductoByName($nombreProducto);
        $pedido->_cantidad = (int)$cantidad;

        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos pedido por nombre
        // $queryParams = $request->getQueryParams();
        // $id = $queryParams['idPedido'];
        $id = $args["pedido"];
        $pedido = Pedido::obtenerPedido($id);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $args['pedido'];
        $nombreProducto = $parametros['nombreProducto'];
        $nombreEmpleado = $parametros['nombreEmpleado'];
        $cantidad = $parametros['cantidad'];
        $estado = $parametros["estado"];

        $producto = Producto::obtenerProductoByName($nombreProducto);
        $empleado = Usuario::obtenerUsuarioByName($nombreEmpleado);
        $pedido = Pedido::obtenerPedido($id);
        
        $pedido->_producto = $producto;
        $pedido->_usuarioAsignado = $empleado;
        $pedido->_cantidad = $cantidad;
        $pedido->_estado = $estado;

        Pedido::modificarPedido($pedido);

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        // $parametros = $request->getParsedBody();
        $id = $args['pedido'];
        $pedido = Pedido::obtenerPedido($id);
        Pedido::borrarPedido($pedido);

        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

?>