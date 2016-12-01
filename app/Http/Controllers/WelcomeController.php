<?php

namespace App\Http\Controllers;
use App\Rooms;
use Auth;
use Validator;
use Carbon;
use App\Online;
use App\Guest;
use App\Messages;
use Session;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    protected $activity_time ;
    public function __construct()
    {
        $this->activity_time= Carbon\Carbon::now()->addSeconds(-300)->toDateTimeString();
    }
 
    public function index($link = null)
    {   
        if(Auth::user() && Session::has('guest_id')){
            Session::forget('guest_id');
        }
        if(Auth::guest() && Session::has('guest_id')){
            $id = Session::get('guest_id');
            $registered = 0;
            $online = Online::getCurrentUser($id, $registered);

            if(!$online){
                $data = ['user_id' => $id,  
                         'room_id' => 0,
                         'registered' => 0,
                         'last_activity' => Carbon\Carbon::now(),
                       ];
                $addUser = Online::addNewUser($data);
            }
            else {
                $data = ['last_activity' => Carbon\Carbon::now()];
                $updateActivity = Online::updateActivity($id, $data);
            }
            $rooms = Rooms::getOpenRooms();
        }
        else if(Auth::guest() == true){
            $rooms = Rooms::getOpenRooms();
        }
        else {
            $id = Auth::user()->id;
            $registered = 1;
            $online = Online::getCurrentUser($id, $registered);
            
            if(!$online){
                $data = ['user_id' => $id,  
                         'room_id' => 0,
                         'registered' => 1,
                         'last_activity' => Carbon\Carbon::now(),
                       ];
                $addUser = Online::addNewUser($data);
            }
            else {
                $data = ['last_activity' => Carbon\Carbon::now()];
                $updateActivity = Online::updateActivity($id, $data);
            }
            $rooms = Rooms::getAllRooms();
        }
        
        if($link){
            if(!Auth::user() && !Session::has('guest_id')){
                Session::flash('error', 'You should sign in or enter Your nickname');
                return redirect('/');
            }
            
            $room_id = Rooms::getRoomId($link);
            $id = $room_id->id;            
            $result = Messages::messageReverse($id);
            $room_info = Rooms::getRoomInfo($id);
            $getRoomOnlineUsers = Online::getRoomOnlineUsers($id,$this->activity_time);
            $getRoomOnlineGuests = Online::getRoomOnlineGuests($id,$this->activity_time);
            $data['onlineGuests'] = $getRoomOnlineGuests;
            $data['onlineUsers'] = $getRoomOnlineUsers;
            $data['room_info'] = $room_info;
            $data['result'] = $result;
 
            return view('welcome')->with('rooms', $rooms)->with('data', $data);
        }
        return view('welcome')->with('rooms', $rooms);
    }
    
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'room_name' => 'required',
            'privacy_level' => 'required',           
            'file' => 'required|mimes:jpg,jpeg,png,gif',
        ]);
    }
    
    public function addRoom(Request $request){
        $this->validator($request->all())->validate();
        if($request->file('file')){
            $file_extension = $request->file('file')->getClientOriginalExtension();
            $file_name = $request->file('file')->getClientOriginalName();
            $current_date = Carbon\Carbon::now();
            $file_name = md5($current_date->toDateTimeString()).'.'.$file_extension;
            $request->file('file')->move(base_path().'/public/images/room_images/', $file_name);
        }
        
        $creator_id = Auth::user()->id;
        $room_name = $request->input('room_name');
        $privacy_level = $request->input('privacy_level');
        $link = $request->input('short_link');
        $data = ['room_name' => $room_name,
                 'creator_id' => $creator_id,
                 'privacy' => $privacy_level,
                 'room_img'=> $file_name,
                 'link' => $link,
                 'created_at' => Carbon\Carbon::now(),
                 'updated_at' => Carbon\Carbon::now(),
                ];
        $rooms = Rooms::addRoom($data);
        if($rooms){
            return redirect('/');
        }
    }
    
    public function deleteRoom(Request $request){
        if($request->input('id')){
            $id = $request->input('id');
            $room_pic = Rooms::getRoomPic($id);
            unlink('../public/images/room_images/'.$room_pic->room_img);
            Messages::deleteConversation($id);
            $rooms = Rooms::deleteRoom($id);
            if($rooms){
                echo 'done';
                exit;
            }            
        }
    }
    
    public function messages(Request $request){        
        if($request->input('id')){
            
            $id = $request->input('id');            
            $data = ['room_id' => $id,'last_activity' => Carbon\Carbon::now()];
            if(Auth::user()){
                $user_id = Auth::user()->id;
            }
            else if(Session::has('guest_id')){
                $user_id = Session::get('guest_id');
            }
            Online::updateUserActivity($user_id, $data);
            $result = Messages::message($id);
            $room_info = Rooms::getRoomInfo($id);
            $getRoomOnlineUsers = Online::getRoomOnlineUsers($id,$this->activity_time);
            $getRoomOnlineGuests = Online::getRoomOnlineGuests($id,$this->activity_time);
            $data['onlineGuests'] = $getRoomOnlineGuests;
            $data['onlineUsers'] = $getRoomOnlineUsers;
            $data['room_info'] = $room_info;
            $data['result'] = $result;
            echo json_encode($data);
            exit;
        }        
    }
    
    public function updateMessages(Request $request){
        if($request->input('id')){
            $id = $request->input('id');            
            $result = Messages::message($id);
            $room_info = Rooms::getRoomInfo($id);
            $getRoomOnlineUsers = Online::getRoomOnlineUsers($id,$this->activity_time);
            $getRoomOnlineGuests = Online::getRoomOnlineGuests($id,$this->activity_time);
            $data['onlineGuests'] = $getRoomOnlineGuests;
            $data['onlineUsers'] = $getRoomOnlineUsers;
            $data['room_info'] = $room_info;
            $data['result'] = $result;
            echo json_encode($data);
            exit;
        }
    }

    public function addMessage(Request $request){
        if($request->input('room')){
            $room_id = $request->input('room');
            $sms = $request->input('sms');
            $data = ['room_id' => $room_id,'last_activity' => Carbon\Carbon::now()];
            if(Auth::user()){                
                $user_id = Auth::user()->id;                
                $message_data = ['message' => $sms,
                                 'time' => Carbon\Carbon::now(),
                                 'room_id' => $room_id,
                                 'user_id' => $user_id,
                                 'guest_id'=> 0,
                                 'created_at' => Carbon\Carbon::now(),
                                 'updated_at' => Carbon\Carbon::now(),
                                ];
                $result = Messages::addMessage($message_data);
                $data +=['registered' => 1,'user_id' => $user_id,];                
            }
            else if(Auth::guest() && Session::has('guest_id')){                
                $user_id = Session::get('guest_id');
                $message_data = ['message' => $sms,
                                 'time' => Carbon\Carbon::now(),
                                 'room_id' => $room_id,
                                 'user_id' => 0,
                                 'guest_id'=> $user_id,
                                 'created_at' => Carbon\Carbon::now(),
                                 'updated_at' => Carbon\Carbon::now(),
                                ];
                $result = Messages::addMessage($message_data);
                $data +=['registered' => 0,'user_id' => $user_id,];                
            }
            if($result){
                Online::updateUserActivity($user_id, $data);
                echo $room_id;
                exit;
            }
        }
    }
    
    public function signAsGuest(Request $request){        
        if($request->input('nickname')){
            $nickname = "Guest_".$request->input('nickname');
            $check_name = Guest::checkNickname($nickname);
            if(!$check_name){
                $guest_data = [
                    'name' => $nickname,
                    'created_at' => Carbon\Carbon::now(),
                    'updated_at' => Carbon\Carbon::now(),
                ];
                $guest_id = Guest::addGuest($guest_data);
                if($guest_id){
                    Session::put('guest_id', $guest_id);
                    return redirect('/');
                }
            }
            else {
                Session::flash('nickError', 'Choose Another Nickname');
                return redirect('/');
            }
        }
        else {
            Session::flash('nickError', 'Enter Your Nickname');
            return redirect('/');
        }
    }
    
    public function generateLink(Request $request){
        if($request->input('link')){
            $current_time = Carbon\Carbon::now()->toDateTimeString();
            $data['link'] = base64_encode($current_time);
            $data['fullLink'] = url('/'.base64_encode($current_time));
            echo json_encode($data);
            exit;
        }
    }
    
    public function getLink(Request $request){
        if($request->input('id')){
            $room_id = $request->input('id');
            $result = Rooms::getLink($room_id);
            $link = $result->link;
            echo $link;
            exit;
        }
    }
}
