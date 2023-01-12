<?php
    class Order extends Authorization {
        public $data;
        public function __construct ($queryInstructions, $idData) {
            parent:: __construct($queryInstructions, $idData);

            if ($this->actionName=="list") { $this->orderList(); return; }
            if ($this->actionName=="add") { $this->addOrder(); return; }
            if ($this->actionName=="delete") { $this->deleteOrder(); return; }
            if ($this->actionName=="verified") { $this->verifiedOrder(); return; }

            $this->data = ["response_data" => "Hibás kérés!", "status_code" => 400];
        }

        protected function orderList() {
            if ($this->userRank == 1) {
                $stmt = $this->conn->prepare("SELECT product_orders.id, userid, rank, username, email, info, productlist, totalprice, orderepoch, postalcode, city, designation, designationtype, designationnumber, verified, code FROM product_orders INNER JOIN users ON (product_orders.userid = users.id) ORDER BY orderepoch ASC");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if ($result) {
                    $this->data = ["response_data" => $result, "status_code" => 200];
                } else {
                    $this->data = ["response_data" => null, "status_code" => 200];
                }
                return;
            } else {
                $this->data = ["response_data" => null, "status_code" => 403];
                return;
            }
        }

        protected function addOrder() {

            $haveAllInputs = true;
            
            (!isset($this->inData->userid)) ? $haveAllInputs = false : false;
            (!isset($this->inData->productlist)) ? $haveAllInputs = false : false;
            (!isset($this->inData->totalprice)) ? $haveAllInputs = false : false;
            (!isset($this->inData->postalcode)) ? $haveAllInputs = false : false;
            (!isset($this->inData->city)) ? $haveAllInputs = false : false;
            (!isset($this->inData->designation)) ? $haveAllInputs = false : false;
            (!isset($this->inData->designationtype)) ? $haveAllInputs = false : false;
            (!isset($this->inData->designationnumber)) ? $haveAllInputs = false : false;
            (!isset($this->inData->code)) ? $haveAllInputs = false : false;

            if ($haveAllInputs) {
                
                $epochDate = time();

                $productList = json_encode($this->inData->productlist);

                $stmt = $this->conn->prepare("INSERT INTO product_orders (userid, productlist, totalprice, orderepoch, postalcode, city, designation, designationtype, designationnumber, code, verified) VALUES (?,?,?,?,?,?,?,?,?,?,?)");

                $verified = false;

                $stmt->bindParam(1, $this->inData->userid);
                $stmt->bindParam(2, $productList);
                $stmt->bindParam(3, $this->inData->totalprice);
                $stmt->bindParam(4, $epochDate);
                $stmt->bindParam(5, $this->inData->postalcode);
                $stmt->bindParam(6, $this->inData->city);
                $stmt->bindParam(7, $this->inData->designation);
                $stmt->bindParam(8, $this->inData->designationtype);
                $stmt->bindParam(9, $this->inData->designationnumber);
                $stmt->bindParam(10, $this->inData->code);
                $stmt->bindParam(11, $verified);

                $stmt->execute();
                
                if ($stmt->errorCode() == "0000") {
                    $this->data = ["response_data" => "Sikeres megrendelés rögzítés.", "status_code" => 201];
                    return;
                } else {
                    $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                    return;
                }

            } else {
                $this->data = ["response_data" => "No have all inputs.", "status_code" => 400];
                return; 
            }
        }

        private function deleteOrder() {

            if ($this->userRank == 1) {

                if (isset($this->inData->orderid)) {
                    
                    $stmt = $this->conn->prepare("DELETE FROM product_orders WHERE id = ?");
                    $stmt->bindParam(1, $this->inData->orderid);
                    $stmt->execute();
                    
                    if ($stmt->errorCode() == "0000") {
                        $this->data = ["response_data" => "A megrendelés törölve lett.", "status_code" => 201];
                        return;
                    } else {
                        $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                        return;
                    }

                } else {
                    $this->data = ["response_data" => "Hiányzó bemenet!", "status_code" => 400];
                    return; 
                }

            } else {
                $this->data = ["response_data" => null, "status_code" => 403];
                return;
            }
        }

        protected function verifiedOrder() {
            
            $inputOk = false;
            
            if ((isset($this->inData->userid)) && (isset($this->inData->code))) {
                $code = $this->inData->code;
                $userid = $this->inData->userid;
                $inputOk = true;
            }

            if ($inputOk) {

                $stmt2 = $this->conn->prepare("SELECT verified FROM product_orders WHERE userid = ? AND code = ?");

                $stmt2->bindParam(1, $userid);
                $stmt2->bindParam(2, $code);

                $stmt2->execute();

                $result = $stmt2->fetch(PDO::FETCH_ASSOC);

                if ($result) {

                    if ($result["verified"] == 1) {
                        $this->data = ["response_data" => "A megrendelés már vissza lett igazolva.", "status_code" => 200];
                        return;
                    }

                    $verifiedTrue = true;

                    $stmt = $this->conn->prepare("UPDATE product_orders SET verified = ? WHERE userid = ? AND code = ?");
                    $stmt->bindParam(1, $verifiedTrue);
                    $stmt->bindParam(2, $userid);
                    $stmt->bindParam(3, $code);

                    $stmt->execute();

                    if ($stmt->errorCode() == "0000") {
                        $this->data = ["response_data" => "Sikeres megrendelés visszaigazolás.", "status_code" => 201];
                        return;
                    } else {
                        $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                        return;
                    }
                } else {
                    $this->data = ["response_data" => "Nemlétező megrendelés!", "status_code" => 400];
                    return;
                }

            } else {
                $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
                return;
            }
        }
        
    }
?>