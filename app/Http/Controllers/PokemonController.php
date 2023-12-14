<?php

namespace App\Http\Controllers;

use App\Pokemon;

class PokemonController extends Controller
{
    public function all()
    {
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->get('https://pokeapi.co/api/v2/pokemon');
        $data = json_decode($response->getBody());
        $pokemon_array = array();
        foreach ($data->results as $poke) {
            $pokemon = new Pokemon($poke->url);
            array_push($pokemon_array, $pokemon);
        }
        return response()->json($pokemon_array, 200);
    }

    public function types(){
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->get('https://pokeapi.co/api/v2/type');
        $data = json_decode($response->getBody());
        $types_array = array_filter($data->results, function($type){
            return $type->name != "unknown" && $type->name != "shadow";
        });
        return response()->json($types_array, 200);
    }
}
