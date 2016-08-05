<?php

class Eap_User {
    
    private $user_id;
    public $first_name;
    public $last_name;
    public $otchestvo;
    public $zip_code;
    public $country;
    public $state;
    public $city;
    public $address;
    public $email;
    public $phone;
    
    public function __construct($user_id) {
        $this->user_id = $user_id;
    }

    public static function initUser ($data) {
        if (isset($data->user_id)) {
            $user = new Eap_User($data->user_id);
            unset($data->user_id);
            foreach ($data as $key => $value) {
                if (property_exists('Eap_User', $key)) {
                    $user->$key = $value;
                }   
            }
            return $user;
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
