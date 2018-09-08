<?php
    use OAuth2ServerExamples\Repositories\ClientRepository;
    use OAuth2ServerExamples\Repositories\EditorialRepository;
    use OAuth2ServerExamples\Entities\TestimonyEntity;

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Authorization, Content-Type");

    include __DIR__ . '/../vendor/autoload.php';

    header("Content-type: application/json");

    $clientRepo = new ClientRepository();
    $error = false;

    try{
        if (empty($_POST)){
            $data = json_decode(file_get_contents("php://input"));
            foreach ($data as $key => $value) {
                $_POST[$key] = $value;
            }
        }
    } catch (Exception $e){
        $error = true;
        $response = json_encode(["status" => 401, "msg" => "Missing parameter"]);
        echo $response;
    }
    
    if (!$error){
        try{
            if (!empty($_POST['lastname']) && !empty($_POST['firstname']) && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['email']) ){
                $username = htmlspecialchars($_POST['username']);
                $password = htmlspecialchars($_POST['password']);
                $email = htmlspecialchars($_POST['email']);
                $firstname = htmlspecialchars($_POST['firstname']);
                $lastname = htmlspecialchars($_POST['lastname']);

                $client = $clientRepo->register($username, $password, $email, $firstname, $lastname);
                
                if ($client === ClientRepository::REGISTER_USER_EXISTS) throw new Exception("L'utilisateur existe déjà", 200);
                if ($client === ClientRepository::REGISTER_FAILED) throw new Exception("Erreur lros de l'inscription", 200);
                if ($client === ClientRepository::REGISTER_MISSING_PARAMETER) throw new Exception("Missig parameter", 400);
                
                $toEcho = ["status" => "success", "msg" => "Client successfuly registred"];
                
                $response = json_encode($toEcho);
                echo $response;
            } else throw new Exception("Missig one parameter 'username' or 'password' or 'email'", 400);
        } catch (Exception $e){
            $response = json_encode(["status" => $e->getCode(), "msg" => $e->getMessage()]);
            echo $response;
        }
    }