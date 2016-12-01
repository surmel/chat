<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Messages extends Model
{
   
    public static function message($id){
        $users = DB::table('messages')
                    ->leftjoin('users', 'users.id', '=', 'messages.user_id')
                    ->leftjoin('guests', 'guests.id', '=', 'messages.guest_id')
                    ->leftjoin('rooms', 'rooms.id', '=', 'messages.room_id')
                    ->where('room_id', $id)->select('users.name as username', 'messages.*', 'guests.name as guestname', 'rooms.*')->orderBy('time')
                    ->get();
        return $users;
               
//      return DB::table('messages')->belongsToMany('App\Rooms','App\User','App\Guest','room_id','user_id','guest_id');
    }
    
    public static function messageReverse($id){
        $users = DB::table('messages')
                    ->leftjoin('users', 'users.id', '=', 'messages.user_id')
                    ->leftjoin('guests', 'guests.id', '=', 'messages.guest_id')
                    ->leftjoin('rooms', 'rooms.id', '=', 'messages.room_id')
                    ->where('room_id', $id)->select('users.name as username', 'messages.*', 'guests.name as guestname', 'rooms.*')->orderBy('time', 'desc')
                    ->get();
        return $users;
               
//      return DB::table('messages')->belongsToMany('App\Rooms','App\User','App\Guest','room_id','user_id','guest_id');
    }
    
    public static function addMessage($data){
        return DB::table('messages')->insert($data);
    }
    
    public static function deleteConversation($id){
        return DB::table('messages')->where('room_id', $id)->delete();
    }
}
