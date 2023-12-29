<?php

namespace App;

class Pokemon {
    public $name;
    public $types;
    public $weight;
    public $height;
    public $pkdex_number;
    public $img;

    public function __construct($url = null, $data = null) {
        if($data === null){
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);
        }

        $this->name = $data['name'];
        $this->types = $data['types'];
        $this->weight = $data['weight'];
        $this->height = $data['height'];
        $this->pkdex_number = $data['id'];
        $this->img = $data['sprites']['other']['official-artwork']['front_default'];
    }
}