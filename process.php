<?php

$DB['server'] = 'localhost';                                                //Server
$DB['user'] = 'root';                                                       //username
$DB['password'] = '';                                                       //password
$DB['db'] = 'technical_test';                                               //database name

try
{

    // connect to database
    $conn = new PDO("mysql:host=".$DB['server'].";dbname=".$DB['db'],
                    $DB['user'],
                    $DB['password']);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // have my fetch data returned as an associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e)                                                          //thows an exception if anything goes wrong with database
{
    echo "Connection failed: " . $e->getMessage();
}

$result = array("error", false);                                                //result array 
$action = "";                                                                   //action from front-end

//checks if action is provided
if(isset($_GET["action"])){
    $action = $_GET["action"];
}

//To handle a GET request to get the documents
if($action == "read"){
    $category_id = (int)$_GET["id"];                                    
    try{
        $stmt = $conn->prepare("select * from documents where category_id = ?");
        $stmt->execute([$category_id]);
        $docs = $stmt->fetchAll();
        $result["docs"] = $docs;
    }catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }
    
}

//To handle POST request to add new document
if($action == "create"){
    $category_id = $_POST["cat_id"];
    $document_name = $_POST["name"];
    try{
        $stmt = $conn->prepare("insert into documents (category_id, name) values(?, ?)");
        $stmt->execute([$category_id, $document_name]);
        if($stmt){
            $result["message"] = "Document Added Successfully!";
        }else{
            $result["error"] = true;
            $result["message"] = "Failed to Add Document!";
        }
    }catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }

}

//To handle POST request to update document
if($action == "update"){
    $document_id = (int)$_POST["id"];
    $document_name = $_POST["name"];
    try{
        $stmt = $conn->prepare("update documents set name=?, updated_at=default where id=?");
        $stmt->execute([$document_name, $document_id]);
        if($stmt){
            $result["message"] = "Document Updated Successfully!";
        }else{
            $result["error"] = true;
            $result["message"] = "Failed to Update Document!";
        }
    }catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }
    
}

//To handle POST request to delete document
if($action == "delete"){
    $document_id = (int)$_POST["id"];
    try{
        $stmt = $conn->prepare("delete from documents where id=?");
        $stmt->execute([$document_id]);
        if($stmt){
            $result["message"] = "Document Deleted Successfully!";
        }else{
            $result["error"] = true;
            $result["message"] = "Failed to Delete Document!";
        }
    }catch(Exception $e){
        echo "Error: " . $e->getMessage();
    } 
}

//To handle POST request to get categories
if($action == "cats"){
    try{
        $stmt = $conn->prepare("select * from categories");
        $stmt->execute();
        $cats = $stmt->fetchAll();
        $result["cats"] = $cats;
    }catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }
    
}

echo json_encode($result);

