<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\Game;
use App\Enums\GameDataType;

class DataController extends Controller
{
    public function getData(Game $game, GameDataType $type)
    {
        // $type = $request['type'];
        // $game = $request['game'];
        // dd($game, $type);

        $model = $game->getModel($type);
        $data = $model->latest()->paginate(100);
        // dd($data);
        return view('data', [
            'game' => $game->value,
            'type' => $type->value,
            'data' => $data
        ]);
    }
}
