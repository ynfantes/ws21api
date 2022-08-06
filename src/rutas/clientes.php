<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

// GET todos los clientes
$app->get('/clientes', function(Request $request, Response $response){
    
    $db = new db();
    $r = $db->select("*", 'clientes');
    
    // echo '<pre>';
    // echo print_r($r);
    // echo '</pre>';
    
    if ($r['suceed'] && count($r['data'])>0) {
        echo json_encode($r['data']);
    } else {
        echo 'No hay clientes registrados';
    }
    $db = null;
});

// PUT cliente nuevo
$app->post('/clientes/add', function(Request $request, Response $response){
    
    $db = new db();
    
    $datos = Array();
    $datos['token']             = password_hash($request->getParam('token'),PASSWORD_BCRYPT);
    $datos['nombre']            = $request->getParam('nombre');
    $datos['tel_principal']     = $request->getParam('tel_principal');
    $datos['tel_alternativo']   = $request->getParam('tel_alternativo');
    $datos['email_principal']   = $request->getParam('email_principal');
    $datos['email_alternativo'] = $request->getParam('email_alternativo');
    $datos['ult_fecha_pago']    = Misc::format_mysql_date($request->getParam('ult_fecha_pago'));
    $datos['fecha_registro']    = date('Y-m-d');
    $datos['activo']            = 1;
    
    $r = $db->insert('clientes', $datos);
    
    if ($r['suceed']) {
        echo 'Cliente registrado con éxito: ('.$r['insert_id'].'-'.$datos['token'].')';
    } else {
        echo 'No hay clientes registrados';
    }
    $r  = null;
    $db = null;
});

// GET ver cliente por id
$app->get('/clientes/{id}', function(Request $request, Response $response){
    $id_cliente = $request->getAttribute('id');
    $db = new db();
    
    $r = $db->select("*", 'clientes',array("id" => $id_cliente));
    
    if ($r['suceed'] && count($r['data'])>0) {
        $db->insert('bitacora', array(
            'id_cliente'    => $id_cliente,
            'descripcion'   => 'Inicio de sesion',
            'fecha'         => date("Y-m-d H:i:00 ", time())
            ));
        $cliente = $r['data'][0];
        $fecha1 = new DateTime(date('Y-m-d'));
        $fecha2 = new DateTime($cliente['ult_fecha_pago']);
        //$cliente['dias_servicio_'] = $fecha2->add('30 Days');
        $diff = $fecha1->diff($fecha2);
        $cliente['dias_servicio'] = $diff->days;
        echo json_encode($cliente);
    } else {
        echo 'No hay clientes registrados';
    }
    $db = null;
});

// PUT editar cliente
$app->PUT('/clientes/edit/{id}', function(Request $request, Response $response){
    
    $id_cliente = $request->getAttribute('id');
    
    $db = new db();
    
    $datos = Array();
    $datos['token']             = password_hash($request->getParam('token'),PASSWORD_BCRYPT);
    $datos['nombre']            = $request->getParam('nombre');
    $datos['tel_principal']     = $request->getParam('tel_principal');
    $datos['tel_alternativo']   = $request->getParam('tel_alternativo');
    $datos['email_principal']   = $request->getParam('email_principal');
    $datos['email_alternativo'] = $request->getParam('email_alternativo');
    $datos['ult_fecha_pago']    = $request->getParam('ult_fecha_pago');
    $datos['fecha_registro']    = date('Y-m-d');
    $datos['activo']            = 1;
    
    $r = $db->update('clientes', $datos,Array('id'=>$id_cliente));
    
    if ($r['suceed']) {
        echo 'Cliente actualizado con éxito: ('.$id_cliente.')';
    } else {
        echo $r['stats']['error'];
    }
    $r  = null;
    $db = null;
    
});

// GET mostrar estatus por id
$app->get('/clientes/estatus/{id}/{estatus}', function(Request $request, Response $response){
    $id_cliente = $request->getAttribute('id');
    $estatus    = $request->getAttribute('estatus');
    $mensaje = '';
    $db = new db();
    
    $r = $db->select("*", 'clientes',array("id" => $id_cliente));
    
    if ($r['suceed'] && count($r['data'])>0) {
        $cliente = $r['data'][0];
        switch ($estatus) {
            case '1':   // token invalido
                $mensaje = 'Ha intentado usar un token inválido';
                break;
            case '2':
                $mensaje = 'Servicio vencido';
                break;
            case '3':
                $mensaje = 'Ha intentado iniciar el servicio en forma indebida.<br>Esta operación quedará registrada en la bitácora del servicio';
                break;
            
            default:
                $mensaje = 'Debe ponerse en contacto con el proveedor del servicio';
                break;
        }
        echo $cliente['nombre'].'<br>'.$mensaje;
    } else {
        echo 'Cliente no registrado';
    }
    $r = null;
    $db = null;
});