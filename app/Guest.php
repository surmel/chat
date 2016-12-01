<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    public static function checkNickname($nickname){
        return DB::table('guests')->where('name', $nickname)->first();
    } 
    
    public static function addGuest($data){
        return DB::table('guests')->insertGetId($data);
    }
}
