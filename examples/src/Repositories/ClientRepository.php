<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2ServerExamples\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use OAuth2ServerExamples\Entities\ClientEntity;

class ClientRepository implements ClientRepositoryInterface
{

    const REGISTER_SUCCESS = 0;
    const REGISTER_FAILED = 1;
    const REGISTER_USER_EXISTS = 2;
    const REGISTER_MISSING_PARAMETER = 3;

    const LOGIN_SUCCESS = 0;
    const LOGIN_FAILED = 1;
    const LOGIN_USER_DOES_NOT_EXISTS = 2;
    const LOGIN_USERNAME_PASSWORD_MISMATCH = 3;
    const LOGIN_MISSING_PARAMETER = 4;

    const VALIDATE_USER_SUCCESS = 0;
    const VALIDATE_USER_FAILED = 1;

    const DELETE_USER_SUCCESS = 0;
    const DELETE_USER_FAILED = 1;

    private $_db;

    public function __construct(){
        try{
            $this->_db = new \PDO('mysql:host=localhost;dbname=oauth_test','root','');
        } catch (\Exception $e) {
            $this->_db = null;
        }
    }

    /**
     * Log a user if 'username' and 'password' matches an instance of user from the database
     */
    public function login($username=null, $password=null){
        if ($username===null || $password === null){
            return self::LOGIN_MISSING_PARAMETER;
        } else {
            // Get users whom username matches 'username'
            $req = $this->_db->prepare("SELECT * FROM users WHERE name = :username");
            $req->bindParam(":username", $username);
            $req->execute();

            $client = $req->fetch(\PDO::FETCH_ASSOC);
            
            if ($client === false) return self::LOGIN_USER_DOES_NOT_EXISTS;

            // Checking if passwords matches
            if (password_verify($password, $client["password"]) === true){
                unset($client["password"]);
                // Returning client in case you need to get those informations in front-end
                return $client;
            }
            return self::LOGIN_USERNAME_PASSWORD_MISMATCH;
        }
    }

    /**
     * Insert user into the database
     */
    public function register($username=null, $password=null, $email=null, $firstname=null, $lastname=null){
        if ($username===null || $password===null || $email===null || $firstname===null || $lastname===null){
            return self::REGISTER_MISSING_PARAMETER;
        } else {
            $req = $this->_db->prepare("SELECT * FROM users WHERE name = :username OR email = :email");
            $req->bindParam(":username", $username);
            $req->bindParam(":email", $email);
            $req->execute();
            $req = $req->fetch(\PDO::FETCH_ASSOC);

            if ($req !== false) return self::REGISTER_USER_EXISTS;

            $password = password_hash($password, PASSWORD_BCRYPT);

            $req = $this->_db->prepare("INSERT INTO users (username, name, password, email, firstname, lastname, role) VALUES (:username, :username, :password, :email, :firstname, :lastname, NULL)");
            $req->bindParam(":username", $username);
            $req->bindParam(":email", $email);
            $req->bindParam(":password", $password);
            $req->bindParam(":firstname", $firstname);
            $req->bindParam(":lastname", $lastname);
            $res = $req->execute();

            return ($res===true) ? self::REGISTER_SUCCESS : self::REGISTER_FAILED;
        }
    }

    /**
     * Validate an user
     */
    public function validateUser($id){
        $req = $this->_db->prepare("UPDATE users SET role = 'USER' WHERE id = :id");
        $req->bindParam(":id", $id);

        $res = $req->execute();

        return ($res===true) ? self::VALIDATE_USER_SUCCESS : self::VALIDATE_USER_FAILED;
    }

    /**
     * Delete an user
     */
    public function deleteUser($id){
        $req = $this->_db->prepare("DELETE FROM users WHERE id = :id");
        $req->bindParam(":id", $id);

        $res = $req->execute();

        return ($res===true) ? self::DELETE_USER_SUCCESS : self::DELETE_USER_FAILED;
    }

    /**
     * Get all users from database
     */
    public function getClients(){
        $req = $this->_db->query("SELECT * FROM users");
        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all users to validate
     */
    public function getUsersToValidate(){
        $req = $this->_db->query("SELECT * FROM users WHERE role IS NULL");
        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get user by username
     */
    public function getUserByUsername($username=null){
        if ($username){
            $req = $this->_db->prepare("SELECT * FROM users WHERE name = :username");
            $req->bindParam(":username", $username);
            $req->execute();
            $req=$req->fetch(\PDO::FETCH_ASSOC);
            return (count($req) > 0) ? $req : null; 
        } else return null;
    }

    /**
     * Get user by identifier 'id'
     */
    public function getUserById($id=null){
        if ($id){
            $req = $this->_db->prepare("SELECT * FROM users WHERE id = :id");
            $req->bindParam(":id", $id);
            $req->execute();
            $req=$req->fetch(\PDO::FETCH_ASSOC);
            return (count($req) > 0) ? $req : null; 
        } else return null;
    }

    /**
     * Get user by username 'username'
     */
    public function getUserByName($username=null){
        if ($username){
            $req = $this->_db->prepare("SELECT * FROM users WHERE username = :username OR name = :username");
            $req->bindParam(":username", $username);
            $req->execute();
            $req=$req->fetch(\PDO::FETCH_ASSOC);
            return (count($req) > 0) ? $req : null; 
        } else return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier, $grantType = null, $clientSecret = null, $mustValidateSecret = true)
    {
        $req = $this->_db->query("SELECT * FROM users WHERE role IS NOT NULL");

        $clients = [
            'myawesomeapp' => [
                'secret'          => password_hash('abc123', PASSWORD_BCRYPT),
                'name'            => 'My Awesome App',
                'redirect_uri'    => 'http://foo/bar',
                'is_confidential' => true,
            ],
            'foo' => [
                'secret' => 'bar',
                'name' => 'Alex',
                'redirect_uri'    => '',
                'is_confidential' => false
            ]
        ];

        while ($user = $req->fetch(\PDO::FETCH_ASSOC)) {
            $client = [$user['name'] => [
                'secret'          => $user['password'],
                'name'            => $user['name'],
                'redirect_uri'    => 'http://localhost/oauth/examples/public/auth_code.php/authorize',
                'is_confidential' => true,
            ]];
            $clients += $client;
        }

        // Check if client is registered
        if (array_key_exists($clientIdentifier, $clients) === false) {
            return;
        }

        if (
            $mustValidateSecret === true
            && $clients[$clientIdentifier]['is_confidential'] === true
            && password_verify($clientSecret, $clients[$clientIdentifier]['secret']) === false
        ) {
            return;
        }

        $client = new ClientEntity();
        $client->setIdentifier($clientIdentifier);
        $client->setName($clients[$clientIdentifier]['name']);
        $client->setRedirectUri($clients[$clientIdentifier]['redirect_uri']);

        return $client;
    }
}
