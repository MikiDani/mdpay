    <?php

    class Users extends Authorization {
        public $data;
        public function __construct($queryInstructions, $inData) {
            parent::__construct($queryInstructions, $inData);
            
            if ($this->actionName=="registration") { $this->registration(); return; }
            if ($this->actionName=="login") { $this->login(); return; }
            if ($this->actionName=="userdata") { $this->userDataLoad(); return; }
            if ($this->actionName=="allusers") { $this->allUsers(); return; }
            if ($this->actionName=="usermod") { $this->userDataMod(); return; }
            if ($this->actionName=="userdelete") { $this->userDelete(); return; }
            if ($this->actionName=="favorites") { $this->userFavorite(); return; }

            $this->data = ["response_data" => "Hibás kérés!", "status_code" => 400];   
        }

        public function registration() {            
            if (isset($this->inData->username) && isset($this->inData->email) && isset($this->inData->password)) {

                $errorMsg = "";
                // hosszok
                try {
                    $this->textLong('felhasználónév', $this->inData->username, 6, 20);
                    $this->isUsernameUsed($this->inData->username);
                }
                catch (Exception $error) {
                    $errorMsg .= $error->getMessage();
                }

                try {
                    $this->textLong('email', $this->inData->email, 6, 20);
                    $this->isEmailUsed($this->inData->email);
                }
                catch (Exception $error) {
                    $errorMsg .= $error->getMessage();
                }

                try {
                    $this->textLong('jelszó', $this->inData->password, 6, 20);
                } 
                catch (Exception $error) {
                    $errorMsg .= $error->getMessage();
                }
                
                // ha minden ok
                if ($errorMsg == "") {
                    
                    $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (?,?,?)");
                    
                    $codePassword = hash('sha512', $this->inData->password);
                    
                    $stmt->bindParam(1, $this->inData->username);
                    $stmt->bindParam(2, $this->inData->email);
                    $stmt->bindParam(3, $codePassword);
                    $stmt->execute();
                    
                    if ($stmt->errorCode() == "0000") {
                        $this->data = ["response_data" => "Sikeres regisztráció!", "status_code" => 200];
                    } else {
                        $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                    }
                    
                    $stmt = $this->conn->prepare("INSERT INTO user(username, password, email) VALUES (?,?,?)");
                    
                } else {
                    $errorMsg = "<ul>$errorMsg</ul>";
                    $this->data = ["response_data" => "$errorMsg", "status_code" => 400];
                }
            
            } else {
                $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
            }
            
        }
        
        public function login() {
            if (isset($this->inData->usernameoremail) && isset($this->inData->password)) {
                if ($this->loginInputsCheck($this->inData->usernameoremail, $this->inData->password)) {
                    
                    $stmt = $this->conn->prepare("SELECT id, epochend FROM tokens WHERE userid = ?");
                    $stmt->bindParam(1, $this->userId);
                    $stmt->execute();
                    $tokenId = $stmt->fetch();

                    $userId = $this->userId;
                    $epochStart = time();
                    $epochEnd = strtotime("+6 hours");
                    $rand = ($epochStart/2);
                    $token =  hash('sha512', $epochStart.$rand);
                    
                    if (isset($tokenId["id"])) {
                        $tokenId = $tokenId["id"];
                        $stmt = $this->conn->prepare("UPDATE tokens SET userid=?, token=?, epochstart=?, epochend=? WHERE id=?");
                        $stmt->bindParam(1, $userId);
                        $stmt->bindParam(2, $token);
                        $stmt->bindParam(3, $epochStart);
                        $stmt->bindParam(4, $epochEnd);
                        $stmt->bindParam(5, $tokenId);
                        $stmt->execute();
                    } else {
                        $stmt = $this->conn->prepare("INSERT INTO tokens (userid, token, epochstart, epochend) VALUES (?,?,?,?)");
                        $stmt->execute([$userId, $token, $epochStart, $epochEnd]);
                    }

                    $this->data = ["response_data" => $token, "status_code" => 200];

                } else {
                    $this->data = ["response_data" => "Sikertelen belépés!", "status_code" => 400];
                }
                
            } else {
                $this->data = ["response_data" => "Hiányos kitöltés!", "status_code" => 400];
            }
        }

        protected function userDataLoad() {

            $stmt = $this->conn->prepare("SELECT id AS userid, username, email AS useremail, info AS userinfo, rank AS userrank FROM users WHERE id = ?");
            $stmt->bindParam(1, $this->userId);
            $stmt->execute();
            $response = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->data = ["response_data" => $response, "status_code" => 200];
        }

        protected function allUsers() {

            if ($this->userRank == 1) {
                $stmt = $this->conn->prepare("SELECT id, username, email AS useremail, info AS userinfo, rank AS userrank FROM users");
                $stmt->execute();
                $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                if ($response) {
                    $this->data = ["response_data" => $response, "status_code" => 200];
                } else {
                    $this->data = ["response_data" => "null", "status_code" => 200];
                }
            } else {
                $this->data = ["response_data" => null, "status_code" => 403];
            }

        }

        protected function userDataMod() {
            
            foreach ($this->inData as $inputKey => $inputValue) {
                
                if ($inputKey !== "token") {
                
                    if ($inputKey == "useremail") {
                        
                        $errorMsg="";
                        try {
                            $this->textLong($inputKey, $inputValue, 6, 20);
                            $this->isEmailUsed($inputValue);
                        }
                        catch (Exception $error) {
                            $errorMsg .= $error->getMessage();
                        }

                        if ($errorMsg=="") {
                            $this->modifiedUserEmail($inputValue);
                            return;
                        } else {
                            $this->data = ["response_data" => "<ul>$errorMsg</ul>", "status_code" => 400];
                            return;
                        }
                    }

                    if ($inputKey == "userinfo") {
                        
                        $errorMsg="";
                        try {
                            $this->textLong($inputKey, $inputValue, 0, 255);
                        }
                        catch (Exception $error) {
                            $errorMsg .= $error->getMessage();
                        }

                        if ($errorMsg=="") {
                            $this->modifiedUserInfo($inputValue);
                            return;
                        } else {
                            $this->data = ["response_data" => "<ul>$errorMsg</ul>", "status_code" => 400];
                            return;
                        }
                    }
                    
                    if ($inputKey == "password") {
                        if (isset($this->inData->newpassword)) {
                            $this->modifiedUserPassword($inputValue, $this->inData->newpassword);
                        } else {
                            $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
                        }
                    }

                    if ($inputKey == "userrank") {
                        if ($this->userRank == 1) {
                            if (isset($this->inData->userid)) {

                                $userid = $this->inData->userid;
                                $userrank = $this->inData->userrank;

                                $stmt=$this->conn->prepare("UPDATE users SET rank = ? WHERE id = ?");
                                $stmt->bindParam(1, $userrank);
                                $stmt->bindParam(2, $userid);
                                $stmt->execute();

                                if ($stmt->errorcode() == "0000") {
                                    $this->data = ["response_data" => "Sikeres rang módosítás!", "status_code" => 200];
                                } else {
                                    $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                                }

                            } else {
                                $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
                            }
                        } else {
                            $this->data = ["response_data" => null, "status_code" => 403];
                        }
                    }

                }
            }
        }
        
        protected function userDelete() {   

            // frontenden felhasználó általi törlés jelszóval
            if (isset($this->inData->password)) {

                if ($this->passwordCheck($this->inData->password)) {
                    
                    $stmt = $this->conn->prepare("DELETE FROM tokens WHERE token=?");
                    $stmt->bindParam(1, $this->userToken);
                    $stmt->execute();
                    $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
                    $stmt->bindParam(1, $this->userId);
                    $stmt->execute();
                    if ($stmt->errorCode() == "0000") {
                        $this->data = ["response_data" => "A felhasználót töröltük!", "status_code" => 200];
                        return;
                    } else {
                        $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                        return;
                    }
                } else {
                    $this->data = ["response_data" => "Nem megfelelő jelszó!", "status_code" => 400];
                    return;
                }
            }
            
            // backenden admin általi törlés id-vel
            if (isset($this->inData->userid)) {
                if ($this->userRank == 1) {

                    $stmt= $this->conn->prepare("SELECT COUNT(id) AS db FROM users WHERE id = ?");
                    $stmt->bindParam(1, $this->inData->userid);
                    $stmt->execute();
                    $result = $stmt->fetch();

                    if ($result['db']) {
                        
                        $stmt = $this->conn->prepare("DELETE FROM tokens WHERE userid=?");
                        $stmt->bindParam(1, $this->inData->userid);
                        $stmt->execute();

                        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
                        $stmt->bindParam(1, $this->inData->userid);
                        $stmt->execute();
                        if ($stmt->errorCode() == "0000") {
                            $this->data = ["response_data" => "A felhasználót töröltük!", "status_code" => 200];
                            return;
                        } else {
                            $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                            return;
                        }

                    } else {
                        $this->data = ["response_data" => "Rossz bemenet!", "status_code" => 400];
                        return;
                    }
                } else {
                    $this->data = ["response_data" => "null", "status_code" => 403];
                    return;
                }
            }
            
            $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
            return;
        }
        
        private function modifiedUserEmail($email) {

            $stmt = $this->conn->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->bindParam(1, $email);
            $stmt->bindParam(2, $this->userId);
            $stmt->execute();

            if ($stmt->errorCode() == "0000") {
                $this->data = ["response_data" => "Sikeres email módosítás!", "status_code" => 200];
            } else {
                $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
            }
        }

        private function modifiedUserInfo($info) {

            $stmt = $this->conn->prepare("UPDATE users SET info = ? WHERE id = ?");
            $stmt->bindParam(1, $info);
            $stmt->bindParam(2, $this->userId);
            $stmt->execute();
            
            if ($stmt->errorCode() == "0000") {
                $this->data = ["response_data" => "Sikeres info módosítás!", "status_code" => 200];
            } else {
                $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
            }
        }
        
        private function modifiedUserPassword($pwd, $newPwd) {
            
            if ($this->passwordCheck($pwd)) {
                $codeNewPassword = hash('sha512', $newPwd);
                $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bindParam(1, $codeNewPassword);
                $stmt->bindParam(2, $this->userId);
                $stmt->execute();
    
                if ($stmt->errorCode() == "0000") {
                    $this->data = ["response_data" => "Sikeres jelszó módosítás!", "status_code" => 200];
                } else {
                    $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                }
            } else {
                $this->data = ["response_data" => "Nem megfelelő jelszó!", "status_code" => 400];
            }

        }

        private function passwordCheck($pwd) {
            $codePwd = hash('sha512', $pwd);
            $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bindParam(1, $this->userId);
            $stmt->execute();
            $result = $stmt->fetch();
            if (isset($result['password'])) { 
                if ($result['password'] == $codePwd) { 
                    return (true);
                }
            }
            return (false);
        }

        private function textLong($name, $text, $min, $max) {
            if (!(strlen($text)>=$min && strlen($text)<=$max)) {
               throw new Exception("<li>A $name nem megfelelő hosszúságú!($min-$max karakter)</li>");
            }
        }

        private function isUsernameUsed($username) {
            $stmt = $this->conn->prepare("SELECT COUNT(id) AS db FROM users WHERE username = ?");
            $stmt->bindParam(1, $username);
            $stmt->execute();
            $result = $stmt->fetch();
            
            if (!$result["db"] == 0) {
                throw new Exception("<li>A felhasználónév már foglalt az adatbázisban!</li>");
            }
        }

        private function isEmailUsed($email) {
            $stmt = $this->conn->prepare("SELECT COUNT(id) AS db FROM users WHERE email = ?");
            $stmt->bindParam(1, $email);
            $stmt->execute();
            $result = $stmt->fetch();
            
            if (!$result["db"] == 0) {
                throw new Exception("<li>Az email már foglalt az adatbázisban!</li>");
            }
        }

        private function loginInputsCheck($usernameoremail, $password) {
            $stmt = $this->conn->prepare("SELECT id, COUNT(id) AS db FROM users WHERE (username = ? OR email = ?) AND password = ?");

            $codePassword = hash('sha512', $password);

            $stmt->bindParam(1, $usernameoremail);
            $stmt->bindParam(2, $usernameoremail);
            $stmt->bindParam(3, $codePassword);

            $stmt->execute();
            $result = $stmt->fetch();

            $result["db"] == 1 ? $this->userId=$result["id"] : false;

            return ($result["db"]);
        }

        private function userFavorite() {

            if (isset($this->inData->token) && isset($this->inData->function)) {

                $token = $this->inData->token;
                $function = $this->inData->function;
                
                $stmt = $this->conn->prepare("SELECT userid FROM tokens WHERE token = ?");
                $stmt->bindParam(1, $token);
                $stmt->execute();
                $loadUserid = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($loadUserid) {
                    $userid = $loadUserid['userid'];
                    
                    if ($function == 'switch') {
                        
                        if (isset($this->inData->productid)) { 
                            $productid = $this->inData->productid;

                            $stmt = $this->conn->prepare("SELECT * FROM users_favorites WHERE userid = ? AND productid = ?");
                            $stmt->bindParam(1, $userid);
                            $stmt->bindParam(2, $productid);
                            
                            $stmt->execute();
                            
                            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                                // Létezik törölni kell:
                                $stmt = $this->conn->prepare("DELETE FROM users_favorites WHERE userid=? AND productid=?");
                                $stmt->bindParam(1, $userid);
                                $stmt->bindParam(2, $productid);
                                $stmt->execute();
                
                                if ($stmt->errorCode() == "0000") {
                                    $this->data = ["response_data" => false, "status_code" => 200];
                                    return;
                                } else {
                                    $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                                    return;
                                }
                            } else {
                                // nem létezik beillesztés:
                                $stmt = $this->conn->prepare("INSERT INTO users_favorites (userid, productid, selectepoch) VALUES (?,?,?)");
                                
                                $selectepoch = time();
                                $stmt->bindParam(1, $userid);
                                $stmt->bindParam(2, $productid);
                                $stmt->bindParam(3, $selectepoch);
                                $stmt->execute();
                                
                                if ($stmt->errorCode() == "0000") {
                                    $this->data = ["response_data" => true, "status_code" => 201];
                                    return;
                                } else {
                                    $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                                    return;
                                }   
                            }
                        }
                    }

                    if ($function == 'get') {
                        $stmt = $this->conn->prepare("SELECT product.id, product_type.typename, product.name, product.text, price, markdown, instock, users_favorites.selectepoch AS selectepoch, product_pic.serverfilename FROM product 
                        INNER JOIN product_type ON (product.typeid = product_type.id)
                        LEFT JOIN users_favorites ON (product.id = users_favorites.productid)
                        INNER JOIN product_pic ON (product.id = product_pic.productid)
                        WHERE users_favorites.userid = ? AND product_pic.primarypic = 1
                        ORDER BY selectepoch DESC");
                        $stmt->bindParam(1, $userid);
                        $stmt->execute();

                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if ($products) {
                            $this->data = ["response_data" => $products, "status_code" => 200];
                            return;
                        } else {
                            $this->data = ["response_data" => null, "status_code" => 200];
                            return;
                        }

                    }

                    $this->data = ["response_data" => "Hibás kérés!", "status_code" => 400];
                    return;

                } else {
                    $this->data = ["response_data" => "Token error!", "status_code" => 400];
                    return;
                }
                
            } else {
                $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
            }
        }
    }

?>