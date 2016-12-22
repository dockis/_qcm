<?php
class RestApi
{

    public $requestData = array();

    private $method = "";

    /** Volá methodHandler při incializaci => při přístupu k API
     *
     */
    public function __construct()
    {
        $this->methodHandler();
    }

    /** Načítá HTTP Verbs z dotazu na API, vrací a ukládá do $method
     * @return mixed // GET, POST, PUT, DELETE atd.
     */
    public function getMethod()
    {
        return $this->method = $_SERVER["REQUEST_METHOD"];
    }

    /** Rozděluje akce podle příslušné metody, akceptované jsou jen GET, POST, PUT a DELETE.
     *  Ostatní HTTP Verbs skončí stavovým kódem 405 - Method not allowed
     */
    public function methodHandler()
    {
        switch($this->getMethod())
        {
            case "GET":
                $this->getUsers();
                break;

            case "POST":
                $this->insertUsers();
                break;

            case "PUT":
                $this->updateUsers();
                break;
            case "DELETE":
                $this->deleteUsers();
                break;
            default:
                $this->response(array(),405);
                break;
        }
    }

    /** Načtítá uživatele z databáze. Jestli existuje ID načte jednoho konkrétního, v opačném případě všechny.
     *  Získaná data se předávají jako object do návratové fce API response().
     *  Přebírá data z metody GET.
     */
    private function getUsers()
    {
        $sql = "SELECT * FROM `users`";
        if(!empty($_GET["users_id"]))
        {
            $result = DB::query($sql." WHERE `users_id` = ? LIMIT 1", array((int)$_GET["users_id"]));
            $userData = $result->fetch();
        }
        else
        {
            $result =  DB::query($sql);
            $userData = $result->fetchAll();
        }
        $this->response($userData);
    }

    /** Ukládá nového uživatele do databáze. Předává stavové kódy podle úspěchu.
     *  Přebírá data z metody POST.
     */
    private function insertUsers()
    {
        if(empty($_POST["name"]) || empty($_POST["surname"]) || empty($_POST["email"]))
        {
            $this->response(array(), 400);
        }
        $sql = "INSERT INTO `users` (`name`, `surname`, `email`) VALUES (?, ? ,?)";
        if(DB::query($sql, array($_POST["name"], $_POST["surname"], $_POST["email"])))
        {
            $this->response(array('status'=>1),201);
        }
        else
        {
            $this->response(array('status'=>0),412);
        }
    }

    /** Update záznamu uživatele v databázi. Předává stavové kódy podle úspěchu.
     *  Přebírá data z metody PUT.
     */
    private function updateUsers()
    {
        $updateData = array();
        parse_str(file_get_contents("php://input"), $updateData);
        if(empty($updateData["users_id"]) || empty($updateData["name"]) || empty($updateData["surname"]) || empty($updateData["email"]))
        {
            $this->response(array(), 400);
        }
        $sql = "UPDATE `users` SET `name`=?, `surname`=?, `email`=? WHERE `users_id`=?";
        if(DB::query($sql, array($updateData["name"], $updateData["surname"], $updateData["email"], $updateData["users_id"])))
        {
            $this->response(array('status'=>1),202);
        }
        else
        {
            $this->response(array('status'=>0),304);
        }
    }

    /** Maže záznam uživatele v databázi. Předává stavové kódy podle úspěchu.
     *
     */
    private function deleteUsers()
    {
        if(empty($_GET["users_id"]))
        {
            $this->response(array(), 400);
        }
        $sql = "DELETE FROM `users` WHERE `users_id`=?";
        if(DB::query($sql,array((int)$_GET["users_id"])))
        {
            $this->response(array('status'=>1),202);
        }
        else
        {
            $this->response(array('status'=>0),412);
        }
    }

    /** Návratová fce API, přenáší data ve ve formátu JSON a v hlavičce příslušný stavový kód
     * @param $data // data k odeslání ke klientovi
     * @param int $code // stavový kód podle úspěchu operace
     */
    private function response($data, $code = 200)
    {
        header("HTTP/1.1 ".$code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}