<?php
class TestApi
{

    public $action = "";
    public $message = "";
    public $detailUsers;

    private $listUsers;
    private $apiUrl = "";

    /** Inicializace testovacího prostředí pro REST API
     *  Nastavuje cestu k api/api.php
     *
     */
    public function __construct()
    {
        $this->apiUrl = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"])."/api/users/";

        $this->getAction();
        $this->actionHandler($this->action);
    }

    /** Rozděluje akce podle požadavků uživatele - vkládání, mazání, výpis apd.
     * @param string $action // požadovaná akce
     */
    public function actionHandler($action = "list")
    {
        switch($action)
        {
            case "list":
                $this->getAllUsers();
                break;

            case "insert":
                $this->insertUsers();
                break;

            case "delete":
                $this->deleteUsers();
                break;

            case "detail":
                $this->detailUsers();
                break;

            case "update":
                $this->updateUsers();
                break;

            default:
                break;
        }
    }

    /** Provede dotaz na vložení nového záznamu uživatele
     *
     */
    private function insertUsers()
    {
        $this->message = "někde nastala chyba";
        if(empty($_POST["name"]) || empty($_POST["surname"]) || empty($_POST["email"]))
        {
            $this->message = "některé údaje nebyly zadány";
        }
        else
        {
            if($this->callApi("POST", $this->apiUrl, array("name"=>$_POST["name"], "surname"=>$_POST["surname"], "email"=>$_POST["email"])))
            {
                $this->message = "uživatel byl uložen";
            }
        }
    }

    /** Provede dotaz na update záznamu uživatele.
     *  Přebírá POST proměnné z formuláře.
     */
    private function updateUsers()
    {
        $this->message = "někde nastala chyba";
        if(empty($_POST["usersId"]) || empty($_POST["name"]) || empty($_POST["surname"]) || empty($_POST["email"]))
        {
            $this->message = "některé údaje nebyly zadány";
        }
        else
        {
            if($this->callApi("PUT", $this->apiUrl, array("users_id"=>$_POST["usersId"], "name"=>$_POST["name"], "surname"=>$_POST["surname"], "email"=>$_POST["email"])))
            {
                $this->message = "uživatel byl updatován";
            }
        }
    }

    /** Provede dotaz na detailní záznam uživatele a uloží do detailUser (object)
     *  Přebírá POST proměnné z formuláře.
     */
    private function detailUsers()
    {
        if(!empty($_POST["usersAction"]) && !empty($_POST["usersId"]) && $_POST["usersId"] > 0)
        {
            $result = $this->callApi("GET", $this->apiUrl, array("users_id"=>$_POST["usersId"]));
            $this->detailUsers = json_decode($result);
        }
    }

    /** Provede dotaz na výmaz záznamu uživatele.
     *  Přebírá POST proměnné z formuláře.
     */
    private function deleteUsers()
    {
        if(!empty($_POST["usersAction"]) && !empty($_POST["usersId"]) && $_POST["usersId"] > 0)
        {
            if($this->callApi("DELETE", $this->apiUrl, array("users_id"=>$_POST["usersId"])))
            {
                $this->message = "uživatel byl smazán";
            }
        }
    }

    /** Provede dotaz na získání všech uživatelů v databázi.
     *  Výsledek se dekóduje a uloží do listUsers (object)
     */
    private function getAllUsers()
    {
        $result = $this->callApi("GET", $this->apiUrl);
        $this->listUsers = json_decode($result);
    }

    /** Provede tisk všech uživatelů jako řádků tabulky i s buttony na editaci a výmaz
     *
     */
    public function printListUsers()
    {
        $output = "";
        if(count($this->listUsers) > 0)
        {
            foreach($this->listUsers as $v)
            {
                $output .= "<tr>\n";
                $output .= "    <td>".$v->users_id."</td>\n";
                $output .= "    <td>".$v->name."</td>\n";
                $output .= "    <td>".$v->surname."</td>\n";
                $output .= "    <td>".$v->email."</td>\n";
                $output .= '    <td><button onclick="setAction(\'detail\','.$v->users_id.')">editovat</button>'."</td>\n";
                $output .= '    <td><button onclick="setAction(\'delete\','.$v->users_id.')">smazat</button>'."</td>\n";
                $output .= "</tr>\n";
            }
        }
        else
        {
            $output = "<tr><td>v databázi není žádný uživatel</td></tr>";
        }
        echo $output;
    }

    /** Požadovanou akci uloží do action
     *
     */
    private function getAction()
    {
        $this->action = (!empty($_POST["usersAction"]))? $_POST["usersAction"] : "";
    }

    /** Provádí dotazi na REST API
     * @param $method // metoda jaká se má provést
     * @param $url // url na API
     * @param bool|false $data // předávaná data
     * @return mixed // vrací JSON data podle typu dotazu
     */
    private function callApi($method, $url, $data = false)
    {
        $curl = curl_init();
        switch($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, true);
                if(!empty($data))
                {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;

            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if(!empty($data))
                {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                }

                break;

            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

            default:
                if(!empty($data))
                {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

}