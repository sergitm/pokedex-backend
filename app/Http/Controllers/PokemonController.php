<?php

namespace App\Http\Controllers;

use App\Pokemon;
use Illuminate\Http\Request;

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
}
