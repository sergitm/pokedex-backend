<?php

namespace App\Http\Controllers;

use App\Pokemon;
use PokePHP\PokeApi;

class PokemonController extends Controller
{
    public function get20Pokemon($pageQuery)
    {
        $limit = 20;
        $page = (intval($pageQuery) > 1) ? intval($pageQuery) : 1;

        $offset = $limit * ($page - 1);
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->get('https://pokeapi.co/api/v2/pokemon?limit=' . $limit . '&offset=' . $offset);
        $data = json_decode($response->getBody());
        $count = $data->count;
        $pages = ceil(intval($count) / 20);
        $pokemon_array = array();
        foreach ($data->results as $poke) {
            $pokemon = new Pokemon($poke->url);
            array_push($pokemon_array, $pokemon);
        }
        $result = array(
            "count" => $count,
            "pages" => $pages,
            "results" => $pokemon_array
        );
        return response()->json($result, 200);
    }

    public function types()
    {
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->get('https://pokeapi.co/api/v2/type');
        $data = json_decode($response->getBody());
        $types_array = array_filter($data->results, function($type){
            return $type->name != "unknown" && $type->name != "shadow";
        });
        return response()->json($types_array, 200);
    }

    public function getPokemonByType($type, $pageQuery)
    {
        $limit = 20;
        $page = (intval($pageQuery) > 1) ? intval($pageQuery) : 1;
        $offset = $limit * ($page - 1);

        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->get('https://pokeapi.co/api/v2/type/' . $type . '/?limit=' . $limit . '&offset=' . $offset);
        $data = json_decode($response->getBody());
        $pokemon = array_slice($data->pokemon, $offset, $limit);
        $array_result = array();
        foreach ($pokemon as $item) {
            $poke = new Pokemon($item->pokemon->url);
            array_push($array_result, $poke);
        }
        return response()->json($array_result, 200);
    }

    public function getPokemon($id){
        $api = new PokeApi();
        $response = json_decode($api->pokemon($id), true);
        $pokemon = new Pokemon(null, $response);
        $result = array(
            "count" => 1,
            "results" => array($pokemon)
        );
        return response()->json($result, 200);
    }
}
