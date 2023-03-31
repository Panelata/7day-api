<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Pokemon;

class PokemonController extends Controller
{
    public function getAllPokemon(Request $request){
        $pokemonList = Pokemon::get();

        //Loop through the list and attempt to get pokemon image based on name
        foreach($pokemonList as $pokemon){
            try{
                $pokeAPIResponse = json_decode(file_get_contents('https://pokeapi.co/api/v2/pokemon/' . strtolower($pokemon->name)), true);
                $pokemon->image = $pokeAPIResponse['sprites']['front_default'];
            } catch(\Exception $e){
                $pokemon->image = '';
            }
        }

        $response['pokemonList'] = $pokemonList;
        $response['success'] = true;
        $response['status'] = 200;
        return response()->json($response, 200);
    }

    public function createUpdatePokemon(Request $request){
        $bodyContent = json_decode($request->getContent(), true);

        //If PokemonID is null then create new pokemon, else update
        try{
            if($bodyContent['pokemonID'] == null){
                Pokemon::insert([
                    'name' => $bodyContent['name']
                ]);
            } else {
                Pokemon::where('pokemonID', $bodyContent['pokemonID'])
                ->update([
                    'name' => $bodyContent['name']
                ]);
            }
        } catch(\Exception $e){
            Log::error('Unable to create/update pokemon');
            $response['message'] = 'Unable to create/update pokemon';
            $response['success'] = false;
            $response['status'] = 500;
            return response()->json($response, 500);
        }

        $response['success'] = true;
        $response['status'] = 200;
        return response()->json($response, 200);
    }

    public function deletePokemon(Request $request, $id){
        try{
            Pokemon::where('pokemonID', $id)
            ->delete();
        } catch(\Exception $e){
            Log::error('Unable to delete pokemon');
            $response['message'] = 'Unable to delete pokemon';
            $response['status'] = 500;
            $response['success'] = false;
        }

        $response['success'] = true;
        $response['status'] = 200;
        return response()->json($response, 200);
    }

    public function searchPokemon(Request $request){
        try{
            $result = Pokemon::where('name', 'LIKE', '%' . $request->input('name') . '%');
            
            switch($request->input('sort')){
                case 'asc':
                    $result->orderBy('name');
                    break;
                case 'dsc':
                    $result->orderByDesc('name');
                    break;
            }

            $pokemonList = $result->get();
        } catch(\Exception $e){
            Log::error('Unable to search pokemon');
            $response['message'] = 'Unable to search pokemon';
            $response['success'] = false;
            $response['status'] = 500;
        }

        foreach($pokemonList as $pokemon){
            try{
                $pokeAPIResponse = json_decode(file_get_contents('https://pokeapi.co/api/v2/pokemon/' . strtolower($pokemon->name)), true);
                $pokemon->image = $pokeAPIResponse['sprites']['front_default'];
            } catch(\Exception $e){
                $pokemon->image = 'https://cdn-icons-png.flaticon.com/512/3106/3106703.png';
            }
        }

        $response['results'] = $pokemonList;
        $response['success'] = true;
        $response['status'] = 200;
        return response()->json($response, 200);
    }
}
