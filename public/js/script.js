$(document).ready(function(){    
    $("input:file").change(function (){
       var fileName = $(this).val();
       if(fileName != ''){
           $('.add_room_button').prop("disabled", false);
       }
    });
    $('.link_img').click(function(){
        $('.link_layer').fadeIn('fast');
        var id = $(this).attr('name');
        $.ajaxSetup(
        {
            headers:
            {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'post',
            url:'getLink',
            data:{id:id},
            success:function(result){                
                $('#link_input').val(window.location.protocol+"//"+window.location.host+"/"+result);                
            }
        });
    })
    $('.link_layer').click(function(){
        $(this).fadeOut('fast');
    })
    $("#copyLink").click(function(e) {
        e.stopPropagation(); 
        return false;      
    });
    $('.del_img').click(function(){       
        id = $(this).attr('name');
        $('.confirm_layer').fadeIn(500);
        $('.yesorno').click(function(){
            if($(this).attr('alt') == "no"){             
                $('.confirm_layer').fadeOut(500);
            }
            else {         
                $('.confirm_layer').css('display','none');
                $('.pleasewait').css('display','block');
                $.ajaxSetup(
                {
                    headers:
                    {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type:'post',
                    url:'deleteRoom',
                    data:{id:id},
                    success:function(result){
                        if(result == 'done'){
                            location.reload();
                        }

                    }
                });
            }
        });
    });
    $('#link_checkbox').click(function(){
        if($(this).prop('checked')){
            $.ajaxSetup(
            {
                headers:
                {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'post',
                dataType:'json',
                url:'generateLink',               
                data:{link:true},
                success:function(result){
                    if(result){
                        $('#link_generator').val(result.fullLink);
                        $('#link_generator').css('display', 'block');
                        $('.short_link').val(result.link);
                    }
                }
            });   
        }
        else {
            $('#link_generator').val('');
            $('#link_generator').css('display', 'none');
            $('.short_link').val('');
        }
    });
    $('.room_li').click(function(){
        if($(this).hasClass('active_room') || $(this).hasClass('disabled_li')){
           return false;
        }
        else {            
            $('.room_li').removeClass('active_room');
            $('.conversation').empty();
            $(this).addClass('active_room');
            id = $(this).attr('name');
            if(window.location.pathname.length > 5){
                getSms(id);
            }
            $.ajaxSetup(
            {
                headers:
                {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'post',
                url:'messages',
                dataType:'json',
                data:{id:id},
                success:function(result){
                    if(result){
                        $('.send_messages').css('display', 'block');
                        $('.send_button').val(id);
                        getSms(id);                 
                    }
                }
            });           
        }        
    });
    $(document).keypress(function(e) {
        if(e.which == 13) {
            $(".send_button").click();
        }
    });
    if(window.location.pathname.length > 5){
        setInterval(function(){
            id = $(".send_button").val();
            getSms(id);

        },3000);
    }
    $('.send_button').click(function(){
        if($('.sms').val() == ''){
            return false;
        }
        
        console.log(window.location.pathname.length);
        room = $('.send_button').val();
        sms = $('.sms').val();
        $('.sms').val('');
        $.ajaxSetup(
        {
            headers:
            {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'post',
            url:'addMessage',
            data:{room:room, sms:sms},
            success:function(result){
                if(result){
                    getSms(result);
                }
            }
        });
   });
   setInterval(function(){
       if(typeof(id) != "undefined" && id !== null){
            getSms(id);
       }
       
   },3000);
});

function getSms(id){
    
    $.ajaxSetup(
    {
        headers:
        {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type:'post',
        url:'updateMessages',
        dataType:'json',
        data:{id:id},
        success:function(result){
            if(result){
                $('.conversation').empty();
                $('.dropdown-content').empty();
                $('.room_pic').empty();
                $('.room_name').empty();
                $('.room_pic').append('<img src="/images/room_images/'+result.room_info.room_img+'" class="thumbnail">');
                $('.room_name').html('Room Name: '+result.room_info.room_name);
                $('.active_usersbtn').css('display', 'block').html((result.onlineUsers.length+result.onlineGuests.length)+' User(s) Online');
                $.each(result.onlineUsers, function(key, value){
                    $('.dropdown-content').append('<a href="#">'+value.username+'</a>'); 
                });
                $.each(result.onlineGuests, function(key, value){
                    $('.dropdown-content').append('<a href="#">'+value.guestname+'</a>'); 
                });
                $.each(result.result, function(key, value){
                    
                    room_id = value.room_id;
                    if(value.user_id != 0){
                        var users = '<div class="message_container">\
                                        <p class="sender_name">'+value.username+'</p>\
                                        <p class="send_date">'+value.time+'</p>\
                                        <p class="mess">'+value.message+'</p>\
                                    </div>';                    
                        $('.conversation').prepend(users);
                    }
                    else {
                        var users = '<div class="message_container">\
                                        <p class="sender_name">'+value.guestname+'</p>\
                                        <p class="send_date">'+value.time+'</p>\
                                        <p class="mess">'+value.message+'</p>\
                                    </div>';                    
                        $('.conversation').prepend(users);                      
                    }
                    
                      
                });
                if(typeof(room_id) != "undefined" && room_id !== null){
                    $('.send_button').val(room_id);                    
                }                     
            }
        }
    });
}