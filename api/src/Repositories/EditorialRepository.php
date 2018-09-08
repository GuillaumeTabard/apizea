<?php
/**
 * @author      Guillaume Tabard <gtabard.em@gmail.com>
 */

namespace OAuth2ServerExamples\Repositories;

use OAuth2ServerExamples\Entities\TestimonyEntity;

class EditorialRepository
{
    const ADD_SUCCESS = 0;
    const ADD_FAILED = 1;
    const ADD_MISSING_PARAMETER = 2;

    const VALIDATE_TESTIMONY_SUCCESS = 0;
    const VALIDATE_TESTIMONY_FAILED = 1;

    const NO_TESTIMONIES_TO_VALIDATE = 0;

    const DELETE_TESTIMONY_SUCCESS = 0;
    const DELETE_TESTIMONY_FAILED = 1;
    
    private $_db;

    public function __construct(){
        try{
            $this->_db = new \PDO('mysql:host=localhost;dbname=oauth_test','root','');
        } catch (\Exception $e) {
            $this->_db = null;
        }
    }

    public function getTestimonies(){
        try{
            $req = $this->_db->query("SELECT title, description, username, annee, testimonies.id AS id_testimony, longitude, latitude FROM testimonies LEFT OUTER JOIN users ON (users.id = testimonies.id_user) WHERE validated = 1");
            return $req->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e){
            return $e->getCode();
        }
    }

    public function validateTestimony($id){
        $req = $this->_db->prepare("UPDATE testimonies SET validated = 1 WHERE id = :id");
        $req->bindParam(":id", $id);
        $res = $req->execute();

        return ($res === true) ? self::VALIDATE_TESTIMONY_SUCCESS : self::VALIDATE_TESTIMONY_FAILED; 
    }

    public function deleteTestimony($id){
        $req = $this->_db->prepare("DELETE FROM testimonies WHERE id = :id");
        $req->bindParam(":id", $id);
        $res = $req->execute();

        return ($res === true) ? self::DELETE_TESTIMONY_SUCCESS : self::DELETE_TESTIMONY_FAILED; 
    }
    
    public function getTestimoniesToValidate(){
        try{
            $req = $this->_db->query("SELECT title, description, username, annee, testimonies.id AS id_testimony FROM testimonies LEFT OUTER JOIN users ON (users.id = testimonies.id_user) WHERE validated = 0");
            return $req->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e){
            return self::NO_TESTIMONIES_TO_VALIDATE;
        }
    }

    public function add(TestimonyEntity $testimony=null){
        try{
            if ($testimony === null) throw new Exception("Missing parameter 'testimony", self::ADD_MISSING_PARAMETER);

            $description = $testimony->getDescription();
            $url = $testimony->getUrl();
            $title = $testimony->getTitle();
            $id_user = $testimony->getUser();
            $long = $testimony->getLongitude();
            $lat = $testimony->getLatitude();
            $annee = $testimony->getAnnee();

            $req = $this->_db->prepare("INSERT INTO testimonies (id_user, title, description, url, longitude, latitude, annee) VALUES (:id_user, :title, :description, :url, :long, :lat, :annee)");
            $req->bindParam(":description", $description);
            $req->bindParam(":title", $title);
            $req->bindParam(":id_user", $id_user);
            $req->bindParam(":url", $url);
            $req->bindParam(":long", $long);
            $req->bindParam(":lat", $lat);
            $req->bindParam(":annee", $annee);
            $res = $req->execute();

            return ($res===true) ? self::ADD_SUCCESS : self::ADD_FAILED;

        } catch (\Exception $e){
            return $e->getCode();
        }
    }
}