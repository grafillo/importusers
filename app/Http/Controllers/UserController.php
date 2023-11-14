<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getUsers(){

        $updatedUser = 0;
        $addedUser = 0;

        $timeStart = microtime(true);
        $response = Http::get('https://randomuser.me/api', [
            'results' => '5000'
        ]);

        if($response->failed()){
            return 'Ошибка запроса к https://randomuser.me/api';
        }

        $response = $response->json();

        foreach($response['results'] as $value) {

            if (Cache::has($value['name']['first'] . $value['name']['last'])) {

                $user = User::where('id',Cache::get($value['name']['first'] . $value['name']['last']))->first();
                $user->update([
                    'email' => $value['email'],
                    'age'   => $value['dob']['age'],
                ]);

                $updatedUser++;


            } else {

               $user = User::create([
                    'first_name' => $value['name']['first'],
                    'last_name'  => $value['name']['last'],
                    'email'     => $value['email'],
                    'age'       => $value['dob']['age'],
                ]);

                Cache::forever($user['first_name'].$user['last_name'], $user['id']);

                $addedUser++;

            }
        }

        $allUser = Redis::command('info',['keyspace']);
        $allUser = $allUser['Keyspace']['db1']['keys'];

        $time = microtime(true) - $timeStart;

        return "Всего=$allUser, Добавлено=$addedUser, Обновлено=$updatedUser ,  Затраты времени=$time";


    }





}
