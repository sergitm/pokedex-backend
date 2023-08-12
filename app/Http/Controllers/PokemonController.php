<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function all()
    {
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->get('https://pokeapi.co/api/v2/pokemon');
        $data = json_decode($response->getBody());
        return response()->json($data->results);
    }
}
