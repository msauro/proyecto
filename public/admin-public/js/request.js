var REQUEST = (function(waitingRequests){

  var startTimer = startTimer,
      stopTimer = stopTimer,
      timersList = []; // Stores an Interval object per 'Waiting' request

  // Initialize timers for 'Waiting' requests
  waitingRequests.forEach(function(idRequest){
    startTimer(idRequest);
  });

  return {
    setRequestStatus: setRequestStatus
  }

  // Set a given status to a given request 
  // and update counters, buttons and labels
  function setRequestStatus(idRequest, status, pushToken){
    var xhttp = new XMLHttpRequest();
    
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var response = JSON.parse(this.responseText);
        
        // If request status was successfully updated
        // No matter if push notification was delivered to user
        if(response.status == "success" 
          || (response.status == "error" && response.code == 202)){
         
          var newStatus = '';
          var count = 0;

          switch (status) {
            case 'Waiting':
              // Set new status
              newStatus = '<span class="label label-info">Waiting</span>';
              // Increase counters
              count = parseInt($('#widget_waiting').text()) + 1;
              $('#widget_waiting').text(count);
              // Hide and show buttons
              $("#"+idRequest+"_btn_waiting").addClass('hidden');
              $("#"+idRequest+"_btn_cancelled").removeClass('hidden');
              $("#"+idRequest+"_btn_successful").removeClass('hidden');
              $("#"+idRequest+"_btn_print").removeClass('hidden');
              // Start timer
              startTimer(idRequest);
              break;
            case 'Cancelled':
              // Set new status 
              newStatus = '<span class="label label-danger">Cancelled</span>';
              // Increase counters
              count = parseInt($('#widget_cancelled').text()) + 1;
              $('#widget_cancelled').text(count);
              // Hide and show buttons
              $("#"+idRequest+"_btn_cancelled").addClass('hidden');
              $("#"+idRequest+"_btn_successful").addClass('hidden');
              $("#"+idRequest+"_btn_waiting").addClass('hidden');
              // Hide timer
              $("#"+idRequest+"_minutes").addClass('hidden');
              $("#"+idRequest+"_seconds").addClass('hidden');
              // Stop timer
              stopTimer(idRequest);
              break;
            case 'Successful':
              // Set new status 
              newStatus = '<span class="label label-success">Successful</span>';
              // Increase counters
              count = parseInt($('#widget_successful').text()) + 1;
              $('#widget_successful').text(count);
              // Hide and show buttons
              $("#"+idRequest+"_btn_cancelled").addClass('hidden');
              $("#"+idRequest+"_btn_successful").addClass('hidden');
              // Hide timer
              $("#"+idRequest+"_minutes").addClass('hidden');
              $("#"+idRequest+"_seconds").addClass('hidden');
              // Stop timer
              stopTimer(idRequest);
              break;
          }
          
          // Dicrease counters 
          switch (($("#"+idRequest+"_status").text()).trim()) {
              case 'Pending':
                  count = parseInt($('#widget_pending').text()) - 1;
                  $('#widget_pending').text(count);
                  break;
              case 'Waiting':
                  count = parseInt($('#widget_waiting').text()) - 1;
                  $('#widget_waiting').text(count);
                  break;
              case 'Cancelled':
                  count = parseInt($('#widget_cancelled').text()) - 1;
                  $('#widget_cancelled').text(count);
                  break;
              case 'Successful':
                  count = parseInt($('#widget_successful').text()) - 1;
                  $('#widget_successful').text(count);
                  break;
          }

          // Update requests status 
          $("#"+idRequest+"_status").html(newStatus);

          // If push notification could not be deliverd to user
          if(response.status == "error" && response.code == 202){
            $("#alertText").text(response.message);
            $("#modalAlert").modal('show');
          }

        // If there were another error
        }else{
          $("#alertText").text(response.message);
          $("#modalAlert").modal('show');
        }
      }
    }

    xhttp.open("POST", "/Api_Requests/update", true);
    xhttp.setRequestHeader("Content-type", "application/json");
    xhttp.send(JSON.stringify( {request: {id: idRequest, status: status, pushToken: pushToken}} ));

  }

  // Start a timer when a request is passed to 
  // 'Waiting' status
  function startTimer(idRequest){

    var minutes = parseInt($("#"+idRequest+"_minutes").html()),
        seconds = parseInt($("#"+idRequest+"_seconds").html()),

        currentSeconds = minutes * 60 + seconds,

        timer = setInterval( function(){
            $("#"+idRequest+"_minutes").html(pad(parseInt(currentSeconds/60,10)+" : "));
            $("#"+idRequest+"_seconds").html(pad(++currentSeconds%60));
        }, 1000);

    // Store interval on array
    timersList.push({ timer: timer, idRequest: idRequest });

    // Support function
    function pad(val){ 
      return val > 9 ? val : "0" + val;
    }
  }

  // Stop an initialized timer when it's passed
  // to 'Cancelled' or 'Successful' status
  function stopTimer(idRequest){
    var timerToStop = timersList.find(function(timer){
                                      return timer.idRequest == idRequest;
                                    });
    // Clear Interval object
    clearInterval(timerToStop.idRequest);
  }

})(waitingRequests);


    
