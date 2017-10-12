
$(function(){

	var gabinandoSocket = {

		socket: null,

		init: function(){

			gabinandoSocket.socket = io(socket_uri);

			gabinandoSocket.socket.on('request_update', function(data){
			    var message = 'Request #'+data.id_request+' made by '+data.user+' has been '+data.action+' at '+data.time;
			  	
			    gabinandoSocket.showNotification(message);
			});
		},
		showNotification: function(message){
			if(!(($("#modalAlert").data('bs.modal') || {}).isShown)){
				$("#notificationText").text(message);
	      $("#modalNotification").modal('show');
			};
		}
	}

	gabinandoSocket.init();
});