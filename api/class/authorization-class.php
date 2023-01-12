<?php

require "database-class.php";

class Authorization extends Database {
    public $authorizationResult;    // true vagy false | Sikerült-e az azonosítás
    public $method;
    public $dataGroup;
    public $actionName;
    public $inData;
    protected $conn;
    protected $userToken;
    protected $userId;
    protected $userRank;

    public function __construct($queryInstructions, $inData) {
        parent::__construct();

        $this->conn = parent::connect();
        $this->conn->query("SET NAMES utf8");

        $this->method = $queryInstructions[0];
        $this->dataGroup = $queryInstructions[1];
        $this->actionName = $queryInstructions[2];

        $this->inData = $inData;
        
        $searchWithout = $this->withoutIdentification();    // azonosítás nélküli
        $searchToken = $this->tokenIdentification();    // token azonosítás
        
        $this->authorizationResult = ($searchWithout || $searchToken) ? true : false;
    }

    private function withoutIdentification() {

        $openMethodsList = [
            ["POST", "user", "login"],
            ["POST", "user", "registration"],
            ["GET", "product", "allproduct"],
            ["GET", "product", "producttypelist"],
            ["GET", "product", "priceminmax"],
            ["POST", "product", "product"],
            ["POST", "product", "picturelist"],
            ["POST", "product", "productfilter"],
            ["GET", "order", "verified"]
        ];
        
        foreach($openMethodsList as $list) {
            if ($list[0] === $this->method && $list[1] === $this->dataGroup && $list[2] === $this->actionName) {
                return true;
            }
        }

        return false;
    }

    private function tokenIdentification() {
        
        $token = isset($this->inData->token) ? $this->inData->token : null;

        if ($token) {
            $stmt = $this->conn->prepare("SELECT id, userid, epochend FROM tokens WHERE token = ?");
            $stmt->bindParam(1, $token);
            $stmt->execute();
            $response = $stmt->fetch();

            if (isset($response["userid"])) {

                $stmt2 = $this->conn->prepare("SELECT rank FROM users WHERE id = ?");
                $stmt2->bindParam(1, $response["userid"]);
                $stmt2->execute();
                $response2 = $stmt2->fetch();
                
                if (($response) && ($response2)) {
    
                    $epocTimeNow = time();
    
                    if ($response["epochend"] > $epocTimeNow) {
                        $this->userId = $response["userid"];
                        $this->userRank = $response2["rank"];
                        $this->userToken = $token;
                        return true;
                    } else {
                        $stmt = $this->conn->prepare("DELETE FROM tokens WHERE id = ?");
                        $stmt->bindParam(1, $response["id"]);
                        $stmt->execute();
                        return false;
                    }
    
                } else {
                    return false;
                }

            } else {
                return false;
            }

        } else {
            return false;
        }

    }
}

?>