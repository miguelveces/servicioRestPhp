<?php

/**
 * La clase: PeopleAPI 
 * Desarrollada por admin el 18-jun-2016
 * Cualquier consulta, bug, o sugrencias enviar un correo a mvece8@gmail.com
 * @author admin
 */
class PeopleAPI {

    public function API() {
        header('Content-Type: application/JSON');
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET'://consulta
                $this->getPeoples();
                break;
            case 'POST'://inserta
                $this->savePeople();
                break;
            case 'PUT'://actualiza
                $this->updatePeople();
                break;
            case 'DELETE'://elimina
                $this->deletePeople();
                break;
            default://metodo NO soportado
                $this->response(405);
                break;
        }
    }

    /**
     * Respuesta al cliente
     * @param int $code Codigo de respuesta HTTP
     * @param String $status indica el estado de la respuesta puede ser "success" o "error"
     * @param String $message Descripcion de lo ocurrido
     */
    function response($code = 200, $status = "", $message = "") {
        http_response_code($code);
        if (!empty($status) && !empty($message)) {
            $response = array("status" => $status, "message" => $message);
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }

    function getPeoples() {
        require_once "../database/PeopleDB.php";
        if ($_GET['action'] == 'peoples') {
            $db = new PeopleDB();
            if (isset($_GET['id'])) {//muestra 1 solo registro si es que existiera ID                 
                $response = $db->getPeople($_GET['id']);
                echo json_encode($response, JSON_PRETTY_PRINT);
            } else { //muestra todos los registros                   
                $response = $db->getPeoples();
                echo json_encode($response, JSON_PRETTY_PRINT);
            }
        } else {
            $this->response(400);
        }
    }

    function savePeople() {
        require_once "../database/PeopleDB.php";
        if ($_GET['action'] == 'peoples') {
            //Decodifica un string de JSON
            $obj = json_decode(file_get_contents('php://input'));
            $objArr = (array) $obj;
            if (empty($objArr)) {
                $this->response(422, "error", "Nothing to add. Check json");
            } else if (isset($obj->name)) {
                $people = new PeopleDB();
                $people->insert($obj->name);
                $this->response(200, "success", "new record added");
            } else {
                $this->response(422, "error", "The property is not defined");
            }
        } else {
            $this->response(400);
        }
    }

    /**
     * Actualiza un recurso
     */
    function updatePeople() {
        require_once "../database/PeopleDB.php";
        if (isset($_GET['action']) && isset($_GET['id'])) {
            if ($_GET['action'] == 'peoples') {
                $obj = json_decode(file_get_contents('php://input'));
                $objArr = (array) $obj;
                if (empty($objArr)) {
                    $this->response(422, "error", "Nothing to add. Check json");
                } else if (isset($obj->name)) {
                    $db = new PeopleDB();
                    $db->update($_GET['id'], $obj->name);
                    $this->response(200, "success", "Record updated");
                } else {
                    $this->response(422, "error", "The property is not defined");
                }
                exit;
            }
        }
        $this->response(400);
    }

    /* elimina persona
     */

    function deletePeople() {
        require_once "../database/PeopleDB.php";
        if (isset($_GET['action']) && isset($_GET['id'])) {
            if ($_GET['action'] == 'peoples') {
                $db = new PeopleDB();
                $db->delete($_GET['id']);
                $this->response(204);
                exit;
            }
        }
        $this->response(400);
    }

}

//end class
