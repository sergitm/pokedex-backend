<?php

namespace App\Http\Controllers;

use App\Pokemon;
use Illuminate\Support\Facades\Cache;
use PokePHP\PokeApi;

class PokemonController extends Controller
{
    public function get20Pokemon($pageQuery)
    {
        $page = (intval($pageQuery) > 1) ? intval($pageQuery) : 1;
        
        $limit = 20;
        $offset = $limit * ($page - 1);
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->get('https://pokeapi.co/api/v2/pokemon?limit=' . $limit . '&offset=' . $offset);
        $data = json_decode($response->getBody());
        $count = $data->count;
        $pages = ceil(intval($count) / 20);
        $pokemon_array = array();
        foreach ($data->results as $poke) {
            $pokemon = Cache::remember($poke->name, now()->addDay(), function() use ($poke) {
                return new Pokemon($poke->url);
            });
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
        $types_array = Cache::remember('types', now()->addDay(), function() {
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $response = $client->get('https://pokeapi.co/api/v2/type');
            $data = json_decode($response->getBody());
            $types_array = array_filter($data->results, function($type){
                return $type->name != "unknown" && $type->name != "shadow";
            });
            return $types_array;
        });
        return response()->json($types_array, 200);
    }

    public function getPokemonByType($type1, $type2, $pageQuery)
    {
        $limit = 20;
        $page = (intval($pageQuery) > 1) ? intval($pageQuery) : 1;
        $offset = $limit * ($page - 1);

        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->get('https://pokeapi.co/api/v2/type/' . $type1 . '/?limit=' . $limit . '&offset=' . $offset);
        $data = json_decode($response->getBody());
        
        if($type2 != 'none'){
            $data->pokemon = array_filter($data->pokemon, function($pokemon) use ($type2) {
                $poke = Cache::remember($pokemon->pokemon->name, now()->addDay(), function() use ($pokemon) {
                    return new Pokemon($pokemon->pokemon->url);
                });
                return count($poke->types) == 2 && ($poke->types[1]['type']['name'] == $type2 || $poke->types[0]['type']['name'] == $type2);
            });
        }
        $count = count($data->pokemon);
        $pages = ceil(intval($count) / 20);
        $pokemon = array_slice($data->pokemon, $offset, $limit);
        $list = array();
        foreach ($pokemon as $item) {
            $newPoke = Cache::remember($item->pokemon->name, now()->addDay(), function() use ($item) {
                return new Pokemon($item->pokemon->url);
            });
            array_push($list, $newPoke);
        }
        $result = array(
            "count" => $count,
            "pages" => $pages,
            "results" => $list
        );
        return response()->json($result, 200);
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
