<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="/css/bootstrap.css">
        <link href="css/style.css" rel="stylesheet">
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>   
    </head>
    <body>          
        <div class="confirm_layer">
            <div class="confirm">
                <p class="question">Are You Sure?</p>
                <span class="yesorno" alt="yes" style="margin-left:0px;">Yes</span>
                <span class="yesorno" alt="no">No</span>
            </div>
        </div>
        <div class="pleasewait">
            <img src="images/wait.gif" class="wait_img">
        </div>
        <div class="link_layer">
            <div id="copyLink">
                <h4>Copy Link</h4>
                <div class="form-group">
                    <input type="text" name="link_input" class="form-control" id="link_input" readonly>
                </div>                        
            </div>
        </div>
        
        <div class="container-fluid nopadding">
            <div class="rooms">
                <p class="room_title">Rooms</p>
                <div class="choose_room">
                    <ul class="room_ul">                        
                        @foreach ($rooms as $room)
                            @if(Auth::user() || Session::has('guest_id'))
                                <li class="room_li" name="{{$room->id}}">
                                
                                    {{$room->room_name}}                                    
                                    @if (!Auth::guest() && Auth::user()->id == $room->creator_id) 
                                        @if($room->link != "")
                                            <img src="/images/link.png" class="link_img" name="{{$room->id}}">
                                        @endif
                                        <img src="/images/delete.png" class="del_img" name="{{$room->id}}">                                        
                                    @endif                                    
                                </li>
                            @else
                            <li class="room_li  disabled_li" name="{{$room->id}}">
                                {{$room->room_name}}
                                @if (!Auth::guest() && Auth::user()->id == $room->creator_id)
                                    <img src="/images/delete.png" class="del_img" name="{{$room->id}}">
                                @endif
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="top_section">
                <div class="room_name">
                    @if(isset($data))
                        Room Name: {{$data['room_info']->room_name}}
                    @endif
                </div>
                @if (Auth::guest())
                    <div class="top-right links">
                        <a href="{{ url('/login') }}">Login</a>
                        <a href="{{ url('/register') }}">Register</a>
                    </div>               
                @else
                    <div class="dropdown position">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">{{ Auth::user()->name }}
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                            <li class="dropdown">
                                <a href="#" data-toggle="modal" data-target="#addRoom">Add Room</a>
                            </li>
                            <li class="divider"></li>
                            <li class="dropdown">
                                <a href="{{ url('/logout') }}"
                                    onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                            </li>
                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </ul>
                    </div>
                @endif
            </div>
            <div class="content">
                @if (Session::has('error'))       
                    <div class="alert alert-danger errors">
                        <strong>Warning!</strong> {!! session('error') !!}
                    </div>        
                @endif
                @if(Auth::guest() && !Session::has('guest_id'))
                @if (Session::has('nickError'))       
                    <div class="alert alert-danger errors">
                        <strong>Warning!</strong> {!! session('nickError') !!}
                    </div>        
                @endif
                <div class="welcome">
                    <p class="welcome_message">Welcome</p>
                    <p class="enter_nickname">Sing in to start chat with your friends or enter you nickname and:</p>
                    <div class="nick_input">
                        <div class="prefix">
                            <span class="enter_nickname">Guest_</span>
                        </div>
                        <div class="input">
                            {{ Form::open(array('url' => 'signAsGuest')) }}
                            <div class="input-group">
                                <input type="text" class="form-control nickname" name="nickname" placeholder="Enter You Nickname">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" name="enter_to_chat" type="submit">Enter</button>
                                </span>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        
                    </div>
                </div>
                @endif
                <div class="room_info">  
                    <div class="room_pic">
                        @if(isset($data))
                            <img src="/images/room_images/{{$data['room_info']->room_img}}" class="thumbnail">                            
                        @endif

                    </div>                    
                    <div class="active_users">
                        @if(isset($data))
                        <button class="active_usersbtn" style="display:block;">{{(count($data['onlineUsers']))+(count($data['onlineGuests']))}} User(s) Online</button>
                        <div class="dropdown-content">                            
                                @foreach($data['onlineUsers'] as $key => $value)                                    
                                    <a href="#">{{$value->username}}</a>
                                @endforeach
                                @foreach($data['onlineGuests'] as $key => $value)                                    
                                    <a href="#">{{$value->guestname}}</a>
                                @endforeach                                                            
                        </div>
                        @else
                        <button class="active_usersbtn" style="display:none;"></button>
                        <div class="dropdown-content">
                            
                        </div>
                        @endif
                    </div>
                </div>
                <div class="conversation">
                    @if(isset($data))
                        @foreach($data['result'] as $value)                       
                            @if($value->username)
                                <div class="message_container">
                                    <p class="sender_name">{{$value->username}}</p>
                                    <p class="send_date">{{$value->time}}</p>
                                    <p class="mess">{{$value->message}}</p>
                                </div>                                
                            @else
                                <div class="message_container">
                                    <p class="sender_name">{{$value->guestname}}</p>
                                    <p class="send_date">{{$value->time}}</p>
                                    <p class="mess">{{$value->message}}</p>
                                </div>
                            @endif
                        @endforeach                        
                    @endif
                </div>
                @if(isset($data))
                    
                <div class="send_messages">
                    <div class="input-group">
                        <input type="text" class="form-control sms" placeholder="Enter Text Message">
                        <span class="input-group-btn">
                            <button class="btn btn-primary send_button" value="{{$data['room_info']->id}}" name="send_button" type="button">Send</button>
                        </span>
                    </div><!-- /input-group -->
                </div>
                @else
                <div class="send_messages" style="display: none;">
                    <div class="input-group">
                        <input type="text" class="form-control sms" placeholder="Enter Text Message">
                        <span class="input-group-btn">
                            <button class="btn btn-primary send_button" name="send_button" type="button">Send</button>
                        </span>
                    </div><!-- /input-group -->
                </div>                
                @endif                
            </div>
        </div>
        <!-- Modal -->
        <div id="addRoom" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    {{ Form::open(array('url' => 'addRoom', 'files'=> 'true')) }}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add Room</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="room_name">Enter Room Name</label>
                            <input type="text" name="room_name" class="form-control" id="room_name" required>
                        </div>
                        <div class="form-group">
                            <label for="sel1">Choose privacy level</label>
                            <select class="form-control" id="sel1" name="privacy_level" required>
                                <option value="" disabled selected>-- Choose level --</option>
                                <option value="0">Public</option>
                                <option value="1">Private</option>                               
                            </select>
                        </div>
                        <div class="form-group">
                            
                            <input type="checkbox" id="link_checkbox">
                            <label for="link_checkbox">Generate Link</label>
                            <input type="text" class="form-control" value="" id="link_generator" name="link_generator" readonly style="display :none;">
                            <input type="hidden" name="short_link" class="short_link">
                        </div>
                        <label class="btn btn-primary btn-file">
                            Choose Room Image <input type="file" name="file" style="display: none;">
                        </label>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary add_room_button" disabled>Add Room</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <script src="/js/app.js"></script>
        <script src="/js/script.js"></script>
    </body>
</html>
