<?php

    include __DIR__ . '/../vendor/autoload.php';
    
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Authorization, Content-Type");

    try{
        if (empty($_POST)){
            $data = json_decode(file_get_contents("php://input"));
            foreach ($data as $key => $value) {
                $_POST[$key] = htmlspecialchars($value);
            }
        }

        $filepath = __DIR__."/medias/".$_POST['filename'];
        file_put_contents($filepath, base64_decode($_POST['value']));

        \Tinify\setKey("oyJSOIuuje0HLcnwbi3PCXHrrFOBHhou");
        $source = \Tinify\fromFile($filepath);
        $source->toFile($filepath);

        $response = json_encode(["status" => "success", "msg" => "Image successfuly uploaded and compressed"]);
        echo $response;
    } catch (Exception $e){
        $response = json_encode(["status" => 401, "msg" => "Error while uploading image"]);
        echo $response;
    }

