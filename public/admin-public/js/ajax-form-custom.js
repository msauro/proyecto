$("#avatar_url").change(function(){
    if($("#avatar_url").val() != ""){
        $('#submit_changeavatar').removeAttr('disabled');
    }
});
$("#file_recommendation_id").change(function(){
    if($("#file_recommendation_id").val() != ""){
        $('#submit_changerecommendation').removeAttr('disabled');
    }
});
$("#file_driver_id").change(function(){
    if($("#file_driver_id").val() != ""){
        $('#submit_changedriverid').removeAttr('disabled');
    }
});

$('#changeavatar-modal-form').submit(function() { 
    $(this).ajaxSubmit({
        dataType: 'json',
        success: function(data) {
            if(data.status == 200){
                $('.img-thumbnail').attr("src",data.message+"?timestamp=" + new Date().getTime());
                $('#changeavatar-modal-form').trigger("reset");
                $('.fileinput span').text('Select file');
                $('#submit_changeavatar').attr('disabled',true);
                $('#modal_change_photo').modal('hide');
            }
        }
    });      
    return false; 
});

$('#changerecommendation-modal-form').submit(function() { 
    $(this).ajaxSubmit({
        dataType: 'json',
        success: function(data) {
            if(data.status == 200){
                $('#modal_view_recommendation .text-center img').attr("src",data.message+"?timestamp=" + new Date().getTime());
                $('#changerecommendation-modal-form').trigger("reset");
                $('.fileinput span').text('Select file');
                $('#submit_changerecommendation').attr('disabled',true);
                $('#modal_change_recommendation').modal('hide');
            }
        }
    });      
    return false; 
});

$('#changedriverid-modal-form').submit(function() { 
    $(this).ajaxSubmit({
        dataType: 'json',
        success: function(data) {
            if(data.status == 200){
                $('#modal_view_driverid .text-center img').attr("src",data.message+"?timestamp=" + new Date().getTime());
                $('#changedriverid-modal-form').trigger("reset");
                $('.fileinput span').text('Select file');
                $('#submit_changedriverid').attr('disabled',true);
                $('#modal_change_driverid').modal('hide');
            }
        }
    });      
    return false; 
});

$('#changepassword-modal-form').submit(function() {
    $(this).ajaxSubmit({
        dataType: 'json',
        success: function(data) {
            if(data.status == 200){
                $('#changepassword-modal-form').trigger("reset");
                $('#modal_change_password').modal('hide');
                alert(data.message);
            }else if(data.status == 400){
                alert(data.message);
            }
        }
    });      
    return false; 
});