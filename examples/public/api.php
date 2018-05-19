<?php

use League\OAuth2\Server\ResourceServer;
use OAuth2ServerExamples\Repositories\AccessTokenRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use OAuth2ServerExamples\Repositories\ClientRepository;
use OAuth2ServerExamples\Repositories\EditorialRepository;
use OAuth2ServerExamples\Entities\TestimonyEntity;

include __DIR__ . '/../vendor/autoload.php';

$app = new App([
    // Add the resource server to the DI container
    ResourceServer::class => function () {
        $server = new ResourceServer(
            new AccessTokenRepository(),            // instance of AccessTokenRepositoryInterface
            'file://' . __DIR__ . '/../public.key'  // the authorization server's public key
        );

        return $server;
    },
]);

// Add the resource server middleware which will intercept and validate requests
$app->add(
    new \League\OAuth2\Server\Middleware\ResourceServerMiddleware(
        $app->getContainer()->get(ResourceServer::class)
    )
);

// An example endpoint secured with OAuth 2.0
$app->post(
    '/user',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

        $response = $response->withHeader("Content-type","application/json");
        $clientRepo = new ClientRepository();

        try{
            if (empty($_POST)){
                $data = json_decode(file_get_contents("php://input"));
                foreach ($data as $key => $value) {
                    $_POST[$key] = $value;
                }
            }
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => 401, "msg" => "Missing parameter"]));
            return $response->withStatus($e->getCode());
        }

        // Getting user by username
        if (!empty($_POST['username'])){

            $username = htmlspecialchars($_POST['username']);

            $client = $clientRepo->getUserByUsername($username);
            if ($client===false) $response->getBody()->write(json_encode(["status" => "No client found", "msg" => "Client with this is username does not exist"]));
            else {
                unset($client["password"]);
                $response->getBody()->write(json_encode($client));
            }
            return $response->withStatus(200);
        }

        // Getting user by id
        if (!empty($_POST['id'])){

            $id = htmlspecialchars($_POST['id']);
            try{
                $id = (int)$id;
                if ($id<=0) throw new \Exception("Invalid parameter 'id'", 1);
                
            } catch (\Exception $e){
                $response->getBody()->write(json_encode(["status" => "error", "msg" => $e->getMessage()]));
                return $response->withStatus(400);
            }

            $client = $clientRepo->getUserById($id);
            if ($client===false) $response->getBody()->write(json_encode(["status" => "No client found", "msg" => "Client with this is ID does not exist"]));
            else {
                unset($client["password"]);
                $response->getBody()->write(json_encode($client));
            }
            return $response->withStatus(200);
        }
    }
);

$app->post(
    '/user/validate',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

        $response = $response->withHeader("Content-type","application/json");
        $clientRepo = new ClientRepository();

        try{
            if (empty($_POST)){
                $data = json_decode(file_get_contents("php://input"));
                foreach ($data as $key => $value) {
                    $_POST[$key] = $value;
                }
            }
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => 401, "msg" => "Missing parameter"]));
            return $response->withStatus($e->getCode());
        }

        // Getting all users from database
        try{
            $id = (int)htmlspecialchars($_POST["id"]);
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => 401, "msg" => "Invalid parameter 'id'"]));
            return $response->withStatus($e->getCode());
        }

        $clientRepo->validateUser($id);

        $toEcho = ["status" => "success", "msg" => "This user is now validated"];

        $response->getBody()->write(json_encode($toEcho));

        return $response->withStatus(200);
    }
);

$app->post(
    '/user/delete',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

        $response = $response->withHeader("Content-type","application/json");
        $clientRepo = new ClientRepository();

        try{
            $_DELETE = [];
            if (empty($_DELETE)){
                $data = json_decode(file_get_contents("php://input"));
                foreach ($data as $key => $value) {
                    $_DELETE[$key] = $value;
                }
            }
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => 401, "msg" => "Missing parameter"]));
            return $response->withStatus($e->getCode());
        }

        // Getting all users from database
        try{
            $id = (int)htmlspecialchars($_DELETE["id"]);
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => 401, "msg" => "Invalid parameter 'id'"]));
            return $response->withStatus($e->getCode());
        }

        $clientRepo->deleteUser($id);

        $toEcho = ["status" => "success", "msg" => "This user is now deleted"];

        $response->getBody()->write(json_encode($toEcho));

        return $response->withStatus(200);
    }
);

$app->post(
    '/testimony/validate',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

        $response = $response->withHeader("Content-type","application/json");
        $editorialRepo = new EditorialRepository();

        try{
            if (empty($_POST)){
                $data = json_decode(file_get_contents("php://input"));
                foreach ($data as $key => $value) {
                    $_POST[$key] = $value;
                }
            }
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => 401, "msg" => "Missing parameter"]));
            return $response->withStatus($e->getCode());
        }

        try{
            $id = (int)htmlspecialchars($_POST["id"]);
            $res = $editorialRepo->validateTestimony($id);
            if ($res === EditorialRepository::VALIDATE_TESTIMONY_FAILED){
                $response->getBody()->write(json_encode(["status" => "error", "msg" => "Invalid parameter 'id'"]));
                return $response->withStatus(200);
            }
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => 401, "msg" => "Invalid parameter 'id'"]));
            return $response->withStatus($e->getCode());
        }

        $toEcho = ["status" => "success", "msg" => "This testimony is now validated"];

        $response->getBody()->write(json_encode($toEcho));

        return $response->withStatus(200);
    }
);

$app->post(
    '/testimony/delete',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

        $response = $response->withHeader("Content-type","application/json");
        $testRepo = new EditorialRepository();

        try{
            $_DELETE = [];
            if (empty($_DELETE)){
                $data = json_decode(file_get_contents("php://input"));
                foreach ($data as $key => $value) {
                    $_DELETE[$key] = $value;
                }
            }
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => 401, "msg" => "Missing parameter"]));
            return $response->withStatus($e->getCode());
        }

        try{
            $id = (int)htmlspecialchars($_DELETE["id"]);
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => 401, "msg" => "Invalid parameter 'id'"]));
            return $response->withStatus($e->getCode());
        }

        $testRepo->deleteTestimony($id);

        $toEcho = ["status" => "success", "msg" => "This testimony is now deleted"];

        $response->getBody()->write(json_encode($toEcho));

        return $response->withStatus(200);
    }
);

$app->get(
    '/testimonies/validate',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

        $response = $response->withHeader("Content-type","application/json");
        $testRepo = new EditorialRepository();

        // Getting all users from database
        try{
            $testimonies = $testRepo->getTestimoniesToValidate();
            if ($testimonies===EditorialRepository::NO_TESTIMONIES_TO_VALIDATE) throw new Exception("No testimonies to validate", 200);
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => "error", "msg" => $e->getMessage()]));
            return $response->withStatus(200);
        }   

        $response->getBody()->write(json_encode($testimonies));
        return $response->withStatus(200);
    }
);

$app->get(
    '/users/validate',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

        $response = $response->withHeader("Content-type","application/json");
        $clientRepo = new ClientRepository();

        // Getting all users from database
        $users = $clientRepo->getUsersToValidate();

        $toEcho = [];

        foreach ($users as $key => $user){
            // If the access token doesn't have the `basic` scope hide users' names
            if (in_array('basic', $request->getAttribute('oauth_scopes')) === false) {
                unset($user['name']);
            }
            // If the access token doesn't have the `email` scope hide users' email addresses
            if (in_array('email', $request->getAttribute('oauth_scopes')) === false) {
                unset($user["email"]);
            }

            unset($user['password']);

            array_push($toEcho, $user);
        }

        $response->getBody()->write(json_encode($toEcho));

        return $response->withStatus(200);
    }
);

$app->get(
    '/users',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

        $response = $response->withHeader("Content-type","application/json");
        $clientRepo = new ClientRepository();

        // Getting user by username
        if (!empty($_GET['username'])){

            $id = htmlspecialchars($_GET['id']);
            try{
                $id = (int)$id;
                if ($id<=0) throw new \Exception("Invalid parameter 'username'", 1);
                
            } catch (\Exception $e){
                $response->getBody()->write(json_encode(["status" => "error", "msg" => $e->getMessage()]));
                return $response->withStatus(400);
            }

            $client = $clientRepo->getUserByUsername($username);
            if ($client===false) $response->getBody()->write(json_encode(["status" => "No client found", "msg" => "Client with this is username does not exist"]));
            else {
                unset($client["password"]);
                $response->getBody()->write(json_encode($client));
            }
            return $response->withStatus(200);
        }

        // Getting user by id
        if (!empty($_GET['id'])){

            $id = htmlspecialchars($_GET['id']);
            try{
                $id = (int)$id;
                if ($id<=0) throw new \Exception("Invalid parameter 'id'", 1);
                
            } catch (\Exception $e){
                $response->getBody()->write(json_encode(["status" => "error", "msg" => $e->getMessage()]));
                return $response->withStatus(400);
            }

            $client = $clientRepo->getUserById($id);
            if ($client===false) $response->getBody()->write(json_encode(["status" => "No client found", "msg" => "Client with this is ID does not exist"]));
            else {
                unset($client["password"]);
                $response->getBody()->write(json_encode($client));
            }
            return $response->withStatus(200);
        }

        // Getting all users from database
        $users = $clientRepo->getClients();

        $totalUsers = count($users);
        $toEcho = [];

        foreach ($users as $key => $user){
            // If the access token doesn't have the `basic` scope hide users' names
            if (in_array('basic', $request->getAttribute('oauth_scopes')) === false) {
                unset($user['name']);
            }
            // If the access token doesn't have the `email` scope hide users' email addresses
            if (in_array('email', $request->getAttribute('oauth_scopes')) === false) {
                unset($user["email"]);
            }

            unset($user['password']);

            array_push($toEcho, $user);
        }

        $response->getBody()->write(json_encode($toEcho));

        return $response->withStatus(200);
    }
);

$app->post(
    '/login',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
        $response = $response->withHeader("Content-type","application/json");
        $response = $response->withHeader("Access-Control-Allow-Origin","*");

        $clientRepo = new ClientRepository();
        
        try{
            if (!empty($_POST['username']) && !empty($_POST['password'])){
                $username = htmlspecialchars($_POST['username']);
                $password = htmlspecialchars($_POST['password']);
                $client = $clientRepo->login($username, $password);
                
                if ($client === ClientRepository::LOGIN_USER_DOES_NOT_EXISTS) throw new Exception("User does not exist", 200);
                if ($client === ClientRepository::LOGIN_USERNAME_PASSWORD_MISMATCH) throw new Exception("Username and password does not match", 200);
                if ($client === ClientRepository::LOGIN_MISSING_PARAMETER) throw new Exception("Missig one parameter 'username' or 'password'", 200);
                
                $toEcho = ["status" => "success", "client" => $client];
                
                $response->getBody()->write(json_encode($toEcho));
            } else throw new Exception("Missig one parameter 'username' or 'password'", 400);
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => $e->getCode(), "msg" => $e->getMessage()]));
            return $response->withStatus($e->getCode());
        }
        return $response->withStatus(200);
    }
);

$app->post(
    '/register',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
        $response = $response->withHeader("Content-type","application/json");

        $clientRepo = new ClientRepository();
        
        try{
            if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['email']) ){
                $username = htmlspecialchars($_POST['username']);
                $password = htmlspecialchars($_POST['password']);
                $email = htmlspecialchars($_POST['email']);

                $client = $clientRepo->register($username, $password, $email);
                
                if ($client === ClientRepository::REGISTER_USER_EXISTS) throw new Exception("User already exists", 200);
                if ($client === ClientRepository::REGISTER_FAILED) throw new Exception("Registration failed", 200);
                if ($client === ClientRepository::REGISTER_MISSING_PARAMETER) throw new Exception("Missig one parameter 'username' or 'password' or 'email'", 400);
                
                $toEcho = ["status" => "success", "msg" => "Client successfuly registred"];
                
                $response->getBody()->write(json_encode($toEcho));
            } else throw new Exception("Missig one parameter 'username' or 'password' or 'email'", 400);
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => $e->getCode(), "msg" => $e->getMessage()]));
            return $response->withStatus($e->getCode());
        }
        return $response->withStatus(200);
    }
);

$app->post(
    '/editorial/add',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
        $response = $response->withHeader("Content-type","application/json");

        $testimonyRepo = new EditorialRepository();
        $clientRepo = new ClientRepository();

        try{
            if (empty($_POST)){
                $data = json_decode(file_get_contents("php://input"));
                foreach ($data as $key => $value) {
                    $_POST[$key] = $value;
                }
            }
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => 401, "msg" => "Missing parameter"]));
            return $response->withStatus($e->getCode());
        }

        try{
            if (!empty($_POST['annee']) && !empty($_POST['username']) && !empty($_POST['description']) && !empty($_POST['url']) && !empty($_POST['title']) && !empty($_POST['longitude']) && !empty($_POST['latitude']) ){

                $username = htmlspecialchars($_POST['username']);
                $client = $clientRepo->getUserByName($username);
                if ($client == false) $client = null;

                $description = htmlspecialchars($_POST['description']);
                $title = htmlspecialchars($_POST['title']);
                $url = htmlspecialchars($_POST['url']);
                $longitude = htmlspecialchars($_POST['longitude']);
                $latitude = htmlspecialchars($_POST['latitude']);
                $annee = htmlspecialchars($_POST['annee']);

                if (!empty($_POST["anonym"])) $client["id"] = null;

                $testimony = new TestimonyEntity($client["id"], $title, $description, $url, $longitude, $latitude, $annee);
                $result = $testimonyRepo->add($testimony);

                if ($result===EditorialRepository::ADD_FAILED) throw new Exception("Testimony was not added", 200);
                if ($result===EditorialRepository::ADD_MISSING_PARAMETER) throw new Exception("Testimony was not added", 200);

                $toEcho = ["status" => "success", "msg" => "Testimony successfuly added", "testimony" => $testimony];
                
                $response->getBody()->write(json_encode($toEcho));
            } else {
                throw new Exception("Missing parameter", 400);
            }
        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => $e->getCode(), "msg" => $e->getMessage()]));
            return $response->withStatus($e->getCode());
        }
        return $response->withStatus(200);
    }
);
/* 
$app->get(
    '/editorial',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
        $response = $response->withHeader("Content-type","application/json");

        $testimonyRepo = new EditorialRepository();
        
        try{
            $testimonies = $testimonyRepo->getTestimonies();

            if ($testimonies===false) throw new \Exception("", EditorialRepository::NO_TESTIMONIES);

        } catch (Exception $e){
            $response->getBody()->write(json_encode(["status" => $e->getCode(), "msg" => $e->getMessage()]));
            return $response->withStatus($e->getCode());
        }
        return $response->withStatus(200);
    }
); */

$app->run();
