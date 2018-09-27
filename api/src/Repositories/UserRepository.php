<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2ServerExamples\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use OAuth2ServerExamples\Entities\UserEntity;
use OAuth2ServerExamples\Properties\Configuration;

class UserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $db = new \PDO('mysql:host='.Configuration::DATABASE_HOST.';dbname='.Configuration::DATABASE_NAME, Configuration::DATABASE_USER, Configuration::DATABASE_PASSWORD);

        $req = $db->query("SELECT * FROM users");
        while ($user = $req->fetch()) {
            if ($username == $user["name"] && password_verify($password, $user["password"])){
                return new UserEntity();
            }
        }
        return;
    }
}
