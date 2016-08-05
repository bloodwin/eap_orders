<?php

class Eap_User {
    
    private $user_id;
    private $first_name;
    private $last_name;
    private $otchestvo;
    private $zip_code;
    private $country;
    private $state;
    private $city;
    private $address;
    private $email;
    private $phone;
    
    public function __construct($user_id) {
        $this->user_id = $user_id;
    }

    public static function initUser (array $data) {
        if (isset($data['user_id'])) {
        $user = new Eap_User($data['user_id']);
            unset($data['user_id']);
            foreach ($data as $key => $value) {
                if (property_exists('Eap_User', $key)) {
                    $user[$key] = $value;
                }
            }
        } else {
            return null;
        }
    }
    
    public function getFIO() {
        $fio = "$this->last_name $this->first_name $this->otchestvo";
        return $fio;
    }
    
    public function getFullAddress() {
        $address = "$this->zip_code <br />\n";
        $address .= "$this->country <br />\n";
        $address .= "$this->state <br />\n";
        $address .= "$this->city <br />\n";
        $address .= "$this->address <br />\n";
        return $address;
    } 
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getPhone() {
        return $this->phone;
    }
}