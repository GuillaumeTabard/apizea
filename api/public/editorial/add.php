<?php
    use OAuth2ServerExamples\Repositories\ClientRepository;
    use OAuth2ServerExamples\Repositories\EditorialRepository;
    use OAuth2ServerExamples\Entities\TestimonyEntity;

    header("Access-Control-Allow-Origin: *");

    include __DIR__ . '/../../vendor/autoload.php';

    $testimonyRepo = new EditorialRepository();
    $clientRepo = new ClientRepository();

    try{
        if (!empty($_POST['username']) && !empty($_POST['description']) && !empty($_POST['url']) && !empty($_POST['title']) && !empty($_POST['longitude']) && !empty($_POST['latitude']) ){

            $username = htmlspecialchars($_POST['username']);
            $client = $clientRepo->getUserByName($username);
            if ($client == false) $client = null;

            $description = htmlspecialchars($_POST['description']);
            $title = htmlspecialchars($_POST['title']);
            $url = htmlspecialchars($_POST['url']);
            $longitude = htmlspecialchars($_POST['longitude']);
            $latitude = htmlspecialchars($_POST['latitude']);


            $testimony = new TestimonyEntity($client["id"], $title, $description, $url, $longitude, $latitude);
            $result = $testimonyRepo->add($testimony);

            if ($result===EditorialRepository::ADD_FAILED) throw new Exception("Testimony was not added", 200);
            if ($result===EditorialRepository::ADD_MISSING_PARAMETER) throw new Exception("Testimony was not added", 200);

            $toEcho = ["status" => "success", "msg" => "Testimony successfuly added"];
            
            $response = json_encode($toEcho);
            echo $response;
        } else throw new Exception("Missig one parameter 'description' or 'title' or 'username' or 'longitude' or 'latitude'", 400);
    } catch (Exception $e){
        $response = json_encode(["status" => $e->getCode(), "msg" => $e->getMessage()]);
        echo $response;
    }