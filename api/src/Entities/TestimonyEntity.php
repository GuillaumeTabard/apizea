<?php
/**
 * @author      Guillaume Tabard <gtabard.em@gmail.com>
 */

namespace OAuth2ServerExamples\Entities;

class TestimonyEntity 
{
    public $title;
    public $description;
    public $url;
    public $user;
    public $longitude;
    public $latitude;
    public $annee;

    private $_id;

    public function __construct($id_user, $title, $description, $url, $longitude, $latitude, $annee){
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->user = $id_user;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->annee = $annee;
    }

    public function setId($id){
        $this->_id = $id;
    }

    public function getId(){
        return $this->_id;
    }

    public function getAnnee(){
        return $this->annee;
    }

    public function setAnnee($annee){
        return $this->annee = $annee;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getLongitude(){
        return $this->longitude;
    }

    public function getLatitude(){
        return $this->latitude;
    }

    public function getUser(){
        return $this->user;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getUrl(){
        return $this->url;
    }

    public function isAnonym(){
        return !$this->user;
    }

    public function setDescription($description){
        $this->description = $description;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function setUrl($url){
        $this->url = $url;
    }

    public function setUser($user){
        $this->user = $user;
    }
}
