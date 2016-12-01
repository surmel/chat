<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    public static function getAllRooms(){
        return DB::table('rooms')->get();
    }
    
    public static function getOpenRooms(){
        return DB::table('rooms')->where('privacy', 0)->get();
    }
    
    public static function addRoom($data){
        return DB::table('rooms')->insert($data);
    }
    
    public static function deleteRoom($id){
        return DB::table('rooms')->where('id', $id)->delete();
    }
    
    public static function getRoomPic($id){
        return DB::table('rooms')->where('id', $id)->select('room_img')->first();
    }
    public static function getRoomInfo($id){
        return DB::table('rooms')->where('id', $id)->select('room_img','room_name', 'id')->first();
    }
    
    public static function getRoomId($link){
        return DB::table('rooms')->where('link', $link)->select('id')->first();
    }
    
    public static function getLink($room_id){
        return DB::table('rooms')->where('id', $room_id)->select('link')->first();
    }
}
