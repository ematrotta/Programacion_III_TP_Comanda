<?php
require_once './Models/Usuario.php';
require_once './Interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $user = $parametros['user'];
        $sector = $parametros['sector'];
        $tipo = $parametros['tipo'];
        $password = $parametros['password'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->_nombre = $nombre;
        $usr->_user = $user;
        $usr->_sector = $sector;
        $usr->_tipo = $tipo;
        $usr->_password = $password;

        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        // $queryParams = $request->getQueryParams();
        // $id = $queryParams['idUsuario'];
        $id = $args["usuario"];
        $usuario = Usuario::obtenerUsuario($id);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $args['usuario'];
        $nombre = $parametros['nombre'];
        $user = $parametros['user'];
        $sector = $parametros['sector'];
        $tipo = $parametros['tipo'];
        $password = $parametros['password'];

        $usuario = Usuario::obtenerUsuario($id);
        
        $usuario->_nombre = $nombre;
        $usuario->_user = $user;
        $usuario->_password = $password;
        $usuario->_sector = $sector;
        $usuario->_tipo = $tipo;

        Usuario::modificarUsuario($usuario);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        // $parametros = $request->getParsedBody();
        $id = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($id);
        Usuario::borrarUsuario($usuario);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

?>