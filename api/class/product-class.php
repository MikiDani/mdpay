<?php
class Product extends Authorization {
    public $data;
    public function __construct($queryInstructions, $inData) {
        parent::__construct($queryInstructions, $inData);

        if ($this->actionName=="allproduct") { $this->productListAll(); return; }
        if ($this->actionName=="product") { $this->oneProduct(); return; }
        if ($this->actionName=="producttypelist") { $this->productTypeList(); return; }
        if ($this->actionName=="picturelist") { $this->pictureList(); return; }
        if ($this->actionName=="pictureinsert") { $this->pictureInsert(); return; }
        if ($this->actionName=="picturemod") { $this->pictureMod(); return; }
        if ($this->actionName=="productmod") { $this->productMod(); return; }
        if ($this->actionName=="productinsert") { $this->productInsert(); return; }
        if ($this->actionName=="productdelete") { $this->productDelete(); return; }
        if ($this->actionName=="productfilter") { $this->productFilter(); return; }
        if ($this->actionName=="priceminmax") { $this->priceMinMax(); return; }

        $this->data = ["response_data" => "Hibás kérés!", "status_code" => 400];
    }

    protected function priceMinMax() {

        $stmt = $this->conn->prepare("SELECT typeid, @min_val:=MIN(price) AS min, @max_val:=MAX(price) AS max FROM product GROUP BY typeid");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = $this->conn->prepare("SELECT @min_val:=MIN(price) AS min, @max_val:=MAX(price) AS max FROM product");
        $stmt2->execute();
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        $nullrow = ["typeid" => 0, "min" => $result2["min"], "max" => $result2["max"]];
        array_unshift($result, $nullrow);

        if ($result) {
            $this->data = ["response_data" => $result, "status_code" => 200];
        } else {
            $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
        }
        return;
    }

    protected function productFilter() {
        // filtertype, filtername, filterminprice, filtermaxprice, markdowncheckbox
        $insertWhere = true;

        $parameters = [];
        $query = "SELECT product.id, product_type.typename, name, text, price, markdown, instock FROM product INNER JOIN product_type ON ( product.typeid = product_type.id) ";
                
        if ($this->inData->filtertype != 0) {
            if ($insertWhere) { $query .= " WHERE "; $insertWhere = false; }
            $query .= "typeid = :typeid ";
            $parameters[":typeid"] = $this->inData->filtertype;
        }

        if (!empty($this->inData->filtername)) {
            if ($insertWhere) { $query .= " WHERE "; $insertWhere = false; } else { $query .= " AND "; }
            $insert = "%".$this->inData->filtername."%";
            $query .= " name LIKE :filtername ";
            $parameters[":filtername"] = $insert;
        }

        if (!empty($this->inData->filterminprice)) {
            if ($insertWhere) { $query .= " WHERE "; $insertWhere = false; } else { $query .= " AND "; }
            $minprice = (int)$this->inData->filterminprice;
            $query .= " price >= :minprice ";
            $parameters[":minprice"] = $minprice;
        }

        if (!empty($this->inData->filtermaxprice)) {
            if ($insertWhere) { $query .= " WHERE "; $insertWhere = false; } else { $query .= " AND "; }
            $maxprice = (int)$this->inData->filtermaxprice;
            $query .= " price <= :maxprice ";
            $parameters[":maxprice"] = $maxprice;
        }
        
        if ($this->inData->filtermarkdown) {
            if ($insertWhere) { $query .= " WHERE "; $insertWhere = false; } else { $query .= " AND "; }
            $value = 0;
            $query .= " markdown != :markdownvalue ";
            $parameters[":markdownvalue"] = $value;
        }

        $query .= "ORDER BY price DESC";
        
        //echo $query;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($parameters);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            $this->data = ["response_data" => $result, "status_code" => 200];
        } else {
            $this->data = ["response_data" => "none", "status_code" => 200];
        }
        
        return;
    }

    protected function oneProduct() {

        if (isset($this->inData->productid)) {

            $productid = $this->inData->productid;

            $stmt1 = $this->conn->prepare("SELECT product.id, product_type.typename, name, text, price, markdown, instock FROM product INNER JOIN product_type ON ( product.typeid = product_type.id) WHERE product.id = ?");
            $stmt1->bindParam(1, $productid);
            $stmt1->execute();
            $productData = $stmt1->fetch(PDO::FETCH_ASSOC);

            $stmt2 = $this->conn->prepare("SELECT id, productid, serverfilename, text, primarypic FROM product_pic WHERE productid = ? ORDER BY primarypic DESC");
            $stmt2->bindParam(1, $productid);
            $stmt2->execute();
            $picturesData = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            $soloData = [
                "product" => $productData, "pictures" => $picturesData
            ];

            $this->data = ["response_data" => $soloData, "status_code" => 200];
            return;
        } else {
            $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
            return;
        }


    }

    protected function productListAll () {
        $stmt = $this->conn->prepare("SELECT product.id, product_type.typename, product.name, text, price, markdown, instock FROM product INNER JOIN product_type ON (product.typeid = product_type.id) ORDER BY id DESC");
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($result) {
            $this->data = ["response_data" => $result, "status_code" => 200];
        } else {
            $this->data = ["response_data" => null, "status_code" => 200];
        }
        return;
    }

    protected function productTypeList () {
        $stmt = $this->conn->prepare("SELECT id, typename FROM product_type");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($result) {
            $this->data = ["response_data" => $result, "status_code" => 200];
        } else {
            $this->data = ["response_data" => null, "status_code" => 200];
        }
        return;
    }

    protected function pictureList () {
        if (isset($this->inData->productid)) {

            $stmt = $this->conn->prepare("SELECT id, productid, serverfilename, text, primarypic FROM product_pic WHERE productid = ? ORDER BY primarypic DESC");
            $stmt->bindParam(1, $this->inData->productid);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($result) {
                $this->data = ["response_data" => $result, "status_code" => 200];
                return;
            } else {
                $this->data = ["response_data" => null, "status_code" => 200];
                return;
            }
        } else {
            $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
            return;
        }
    }
    
    protected function pictureInsert () {
        if ($this->userRank == 1) {

            if ((isset($this->inData->productid)) && (isset($this->inData->serverfilename))) {
                
                $stmt = $this->conn->prepare("SELECT COUNT(id) AS db FROM product_pic WHERE primarypic = ? AND productid = ?");
                $primarypic = 1;
                $stmt->bindParam(1, $primarypic);
                $stmt->bindParam(2, $this->inData->productid);
                $stmt->execute();
                $result = $stmt->fetch();
                $primaryResult = ($result["db"]) ? "0" : "1";

                $stmt = $this->conn->prepare("INSERT INTO product_pic (productid, serverfilename, primarypic) VALUES (?, ?, ?)");
                $stmt->bindParam(1, $this->inData->productid);
                $stmt->bindParam(2, $this->inData->serverfilename);
                $stmt->bindParam(3, $primaryResult);
                $stmt->execute();

                if ($stmt->errorCode() == "0000") {
                    $this->data = ["response_data" => "Sikeres feltöltés.", "status_code" => 201];
                    return;
                } else {
                    $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                    return;
                }
                
            } else {
                $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
                return;
            }
        } else {
            $this->data = ["response_data" => "null", "status_code" => 403];
            return;
        }
    }

    protected function pictureMod () {
        if ($this->userRank == 1) {
            
            if (isset($this->inData->pictureid)) {
                // picture delete
                if (isset($this->inData->function) && $this->inData->function == "delete") {
                    
                    if (isset($this->inData->productid)) {

                        $stmt = $this->conn->prepare("SELECT productid, serverfilename, text, primarypic FROM product_pic WHERE id = ?");
                        $stmt->bindParam(1, $this->inData->pictureid);
                        $stmt->execute();
                        $pictureData = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($pictureData) {
                            
                            if (file_exists("../backend/product-pictures/big_".$pictureData["serverfilename"])) {
                                unlink("../backend/product-pictures/big_".$pictureData["serverfilename"]);
                            }

                            if (file_exists("../backend/product-pictures/small_".$pictureData["serverfilename"])) {
                                unlink("../backend/product-pictures/small_".$pictureData["serverfilename"]);
                            }

                            $stmt = $this->conn->prepare("DELETE FROM product_pic WHERE id = ?");
                            $stmt->bindParam(1, $this->inData->pictureid);
                            $stmt->execute();

                            $this->definitelyPrimarypic($this->inData->productid);
            
                            if ($stmt->errorCode() == "0000") {
                                $this->data = ["response_data" => "Sikeres képtörlés.", "status_code" => 202];
                                return;
                            } else {
                                $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                                return;
                            }
                            
                        } else {
                            $this->data = ["response_data" => "Hibás bemenet!", "status_code" => 400];
                            return;
                        }
                    }
                    
                }
                
                // picture text mod
                if (isset($this->inData->function) && $this->inData->function == "textmod") {
                    if (isset($this->inData->picturetext)) {
                        
                        $stmt = $this->conn->prepare("UPDATE product_pic SET text = ? WHERE id = ?");
                        $stmt->bindParam(1, $this->inData->picturetext);
                        $stmt->bindParam(2, $this->inData->pictureid);
                        $stmt->execute();
        
                        if ($stmt->errorCode() == "0000") {
                            $this->data = ["response_data" => "Sikeres képcím módosítás.", "status_code" => 202];
                            return;
                        } else {
                            $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                            return;
                        }
                    }
                }

                if (isset($this->inData->function) && $this->inData->function == "primarymod") {

                    if (isset($this->inData->productid)) {

                        $stmt = $this->conn->prepare("SELECT id, primarypic FROM product_pic WHERE productid = ? ");
                        $stmt->bindParam(1, $this->inData->productid);
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // reset all picture primarypic
                        foreach ($result as $row) {
                            $stmt = $this->conn->prepare("UPDATE product_pic SET primarypic = ? WHERE id = ?");
                            $primaryReset = 0;
                            $stmt->bindParam(1, $primaryReset);
                            $stmt->bindParam(2, $row["id"]);
                            $stmt->execute();
                        }
                        
                        $stmt = $this->conn->prepare("UPDATE product_pic SET primarypic = ? WHERE id = ?");
                        $primarypic = 1;
                        $stmt->bindParam(1, $primarypic);
                        $stmt->bindParam(2, $this->inData->pictureid);
                        $stmt->execute();
                        
                        if ($stmt->errorCode() == "0000") {
                            $this->data = ["response_data" => "Sikeres elsődleges kép módosítás.", "status_code" => 202];
                            return;
                        } else {
                            $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                            return;
                        }
                    }
                }
            }
            
            $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
            return;
            
        } else {
            $this->data = ["response_data" => "null", "status_code" => 403];
            return;
        }
    }

    protected function productMod () {
        if ($this->userRank == 1) {
            
            if (isset($this->inData->productid)) {
               
                // product name mod
                if (isset($this->inData->function) && $this->inData->function == "namemod") {
                    if (isset($this->inData->productname)) {
                        
                        if ($this->inData->productname !== "") {

                            $stmt = $this->conn->prepare("UPDATE product SET name = ? WHERE id = ?");
                            $stmt->bindParam(1, $this->inData->productname);
                            $stmt->bindParam(2, $this->inData->productid);
                            $stmt->execute();
                            
                            if ($stmt->errorCode() == "0000") {
                                $this->data = ["response_data" => "Sikeres terméknév módosítás.", "status_code" => 202];
                                return;
                            } else {
                                $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                                return;
                            }
                        } else {
                            $this->data = ["response_data" => "A terméknév üres!", "status_code" => 400];
                            return;
                        }
                    }
                }

                // product price mod
                if (isset($this->inData->function) && $this->inData->function == "pricemod") {
                    if (isset($this->inData->productprice)) {
                        
                        $stmt = $this->conn->prepare("UPDATE product SET price = ? WHERE id = ?");
                        $stmt->bindParam(1, $this->inData->productprice);
                        $stmt->bindParam(2, $this->inData->productid);
                        $stmt->execute();
        
                        if ($stmt->errorCode() == "0000") {
                            $this->data = ["response_data" => "Sikeres termékár módosítás.", "status_code" => 202];
                            return;
                        } else {
                            $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                            return;
                        }
                    }
                }

                // product text mod
                if (isset($this->inData->function) && $this->inData->function == "textmod") {
                    if (isset($this->inData->producttext)) {

                        if ($this->inData->producttext !== "") {
                        
                            $stmt = $this->conn->prepare("UPDATE product SET text = ? WHERE id = ?");
                            $stmt->bindParam(1, $this->inData->producttext);
                            $stmt->bindParam(2, $this->inData->productid);
                            $stmt->execute();
            
                            if ($stmt->errorCode() == "0000") {
                                $this->data = ["response_data" => "Sikeres termékleírás módosítás.", "status_code" => 202];
                                return;
                            } else {
                                $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                                return;
                            }

                        } else {
                            $this->data = ["response_data" => "A termék leírása üres!", "status_code" => 400];
                            return;
                        }

                    }
                }

                // product type mod
                if (isset($this->inData->function) && $this->inData->function == "typemod") {
                    if (isset($this->inData->typeid)) {
                        
                        $stmt = $this->conn->prepare("UPDATE product SET typeid = ? WHERE id = ?");
                        $stmt->bindParam(1, $this->inData->typeid);
                        $stmt->bindParam(2, $this->inData->productid);
                        $stmt->execute();
        
                        if ($stmt->errorCode() == "0000") {
                            $this->data = ["response_data" => "Sikeres termék típus módosítás.", "status_code" => 202];
                            return;
                        } else {
                            $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                            return;
                        }
                    }
                }

                // product markdown mod
                if (isset($this->inData->function) && $this->inData->function == "markdownmod") {
                    if (isset($this->inData->markdown)) {
                        $intMarkdown = (int)$this->inData->markdown;
                        if ($intMarkdown<101) {
                            if ($intMarkdown < 0 ) { $intMarkdown = 0; }

                            $stmt = $this->conn->prepare("UPDATE product SET markdown = ? WHERE id = ?");
                            $stmt->bindParam(1, $intMarkdown);
                            $stmt->bindParam(2, $this->inData->productid);
                            $stmt->execute();
            
                            if ($stmt->errorCode() == "0000") {
                                $this->data = ["response_data" => "Sikeres engedmény százalék módosítás.", "status_code" => 202];
                                return;
                            } else {
                                $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                                return;
                            }
                        } else {
                            $this->data = ["response_data" => "Az érték csak 0-100 között lehet.", "status_code" => 400];
                            return;
                        }

                    }
                }
                // product instock mod
                if (isset($this->inData->function) && $this->inData->function == "instockmod") {
                    if (isset($this->inData->instock)) {
                        $intInstock = (int)$this->inData->instock;
                        
                        if (($intInstock < 0) || ($intInstock > 100000)) { $intInstock = 0; }

                        $stmt = $this->conn->prepare("UPDATE product SET instock = ? WHERE id = ?");
                        $stmt->bindParam(1, $intInstock);
                        $stmt->bindParam(2, $this->inData->productid);
                        $stmt->execute();
        
                        if ($stmt->errorCode() == "0000") {
                            $this->data = ["response_data" => "Sikeres raktárkészlet módosítás.", "status_code" => 202];
                            return;
                        } else {
                            $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                            return;
                        }

                    }
                }
            }
            
            $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
            return;
            
        } else {
            $this->data = ["response_data" => "null", "status_code" => 403];
            return;
        }
    }

    protected function productInsert () {
        if ($this->userRank == 1) {

            if ((isset($this->inData->newtype)) && (isset($this->inData->newproductname))) {

                $stmt = $this->conn->prepare("INSERT INTO product (typeid, name) VALUES (?, ?)");
                $stmt->bindParam(1, $this->inData->newtype);
                $stmt->bindParam(2, $this->inData->newproductname);
                $stmt->execute();

                if ($stmt->errorCode() == "0000") {
                    $this->data = ["response_data" => "Sikeres új termék feltöltés.", "status_code" => 201];
                    return;
                } else {
                    $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                    return;
                }
            } else {
                $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
                return;
            }
        } else {
            $this->data = ["response_data" => "null", "status_code" => 403];
            return;
        }
    }

    protected function productDelete () {
        if ($this->userRank == 1) {

            if (isset($this->inData->productid)) {

                $stmt = $this->conn->prepare("SELECT id, productid, serverfilename FROM product_pic WHERE productid = ?");
                $stmt->bindParam(1, $this->inData->productid);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $location) {
                    if (file_exists("../backend/product-pictures/".$location["serverfilename"])) {
                        unlink("../backend/product-pictures/".$location["serverfilename"]);
                    }
                }
                $stmt = $this->conn->prepare("DELETE FROM product_pic WHERE productid = ?");
                $stmt->bindParam(1, $this->inData->productid);
                $stmt->execute();
                if ($stmt->errorCode() == "0000") {
                    
                    $stmt = $this->conn->prepare("DELETE FROM product WHERE id = ?");
                    $stmt->bindParam(1, $this->inData->productid);
                    $stmt->execute();
                    if ($stmt->errorCode() == "0000") {
                        $this->data = ["response_data" => "A termék törölve lett az adatbázisból.", "status_code" => 202];
                        return;
                    }

                } else {
                    $this->data = ["response_data" => "Szerver hiba!", "status_code" => 500];
                    return;
                }

            } else {
                $this->data = ["response_data" => "Hiányos bemenet!", "status_code" => 400];
                return;
            }

        } else {
            $this->data = ["response_data" => "null", "status_code" => 403];
            return;
        }
    }

    private function definitelyPrimarypic($productid) {
        $stmt = $this->conn->prepare("SELECT id, primarypic FROM product_pic WHERE productid = ? AND primarypic = 1");
        $stmt->bindParam(1, $productid);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            $stmt = $this->conn->prepare("SELECT id, primarypic FROM product_pic WHERE productid = ? LIMIT 1");
            $stmt->bindParam(1, $productid);
            $stmt->execute();
            $firstRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($firstRow) {
                $stmt = $this->conn->prepare("UPDATE product_pic SET primarypic = 1 WHERE id = ?");
                $stmt->bindParam(1, $firstRow["id"]);
                $stmt->execute();
            }
        }
    }

}
?>