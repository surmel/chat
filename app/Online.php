<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Online extends Model {

    /**
     * {@inheritDoc}
     */
    public $table = 'sessions';
    
    public static function getOnlineUsers(){
        return DB::table('sessions')->get();
    }
    
    public static function getCurrentUser($id, $registered){
        return DB::table('sessions')->where('user_id', $id)
                                    ->where('registered', $registered)
                                    ->get()->first();
    }
    
    public static function addNewUser($data){
       
        return DB::table('sessions')->insert($data);
    }
    
    public static function updateActivity($id, $data){
        return DB::table('sessions')->where('id', $id)->update($data);
    }
    
    public static function upActiv($id, $data){
        return DB::table('sessions')->where('user_id', $id)->update($data);
    }
    
    public static function checkActivity($activity_time){
        return DB::table('sessions')->where('last_activity' ,'<=', $activity_time)->delete();
    }
    
    public static function updateUserActivity($id, $data){
        return DB::table('sessions')->where('user_id', $id)->update($data);
    }
    
    public static function getRoomOnlineUsers($id,$activity_time){
        $users = DB::table('sessions')
                    ->leftjoin('users', 'users.id', '=', 'sessions.user_id')
                    ->where('room_id', $id)
                    ->where('registered', 1)
                    ->where('last_activity' ,'>=', $activity_time)
                    ->select('users.name as username')
                    ->get();

        return $users;              
    }
    
    public static function getRoomOnlineGuests($id,$activity_time){
        $users = DB::table('sessions')
                    ->leftjoin('guests', 'guests.id', '=', 'sessions.user_id')
                    ->where('room_id', $id)
                    ->where('registered', 0)
                    ->where('last_activity' ,'>=', $activity_time)
                    ->select('guests.name as guestname')
                    ->get();

        return $users;              
    }
    
    

}