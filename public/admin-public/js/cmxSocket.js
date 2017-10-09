
$(function(){

	var cmxSocket = {

		socket: null,

		init: function(){

			cmxSocket.socket = io(socket_uri);

			cmxSocket.socket.on('request_update', function(data){
			    var message = 'Request #'+data.id_request+' made by '+data.user+' has been '+data.action+' at '+data.time;
			  	
			    cmxSocket.showNotification(message);
			});
		},
		showNotification: function(message){
			if(!(($("#modalAlert").data('bs.modal') || {}).isShown)){
				$("#notificationText").text(message);
	      $("#modalNotification").modal('show');
			};
		}
	}

	cmxSocket.init();
});