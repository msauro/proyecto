/* Custom ecloud */
/*
function minimized(){
	$.ajax({
		url: '/admin/minimized',
		type: 'POST',
		data: '',
		dataType: "json",
		async: false,
		success: function(data) {
			console.log(data);
			return callback(data);
		}
	})
}
*/
function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function panelAjax(url,params,callback) {
	try {
		$.ajax({
			url: url,
			type: "POST",
			data: params,
			dataType: "json",
			async: false,
			beforeSend: function(){
				$.mpb('show',{value: [0,100],speed: 10});
				//$('#load').fadeIn(50);
				//$('#load').html('<img src="/resources/img/load-ajax.gif">');
			},
			success: function(data) {
				setTimeout(function() { $.mpb('destroy') }, 3000);
				if(data.error){
					$(".page-content .message-box-admin").remove();
					$(".page-content .x-navigation-panel").after('<div class="message-box-admin"><div class="col-lg-12"><div class="alert alert-danger"><i class="fa fa-info-circle"></i> '+data.message+'</div></div></div>');
					if(data.data == "error")
						alert(data.message);
				} else {
					$(".page-content .message-box-admin").remove();
					$(".page-content .x-navigation-panel").after('<div class="message-box-admin"><div class="col-lg-12"><div class="alert alert-success"><i class="fa fa-info-circle"></i> '+data.message+'</div></div></div>');
				}

				return callback(data.data);
			}
		})
	} catch (e){
		alert(e.message);
	}
}

function getArrayID(){
	var id	= new Array;
	$(".icheckbox_minimal-grey.checked").each(function(){
		var campo = $(this).closest(".element-id").attr("id");
		id.push(campo);
	});
	id	=	id + ',';
	return id
}
function validateID(id){
	if(id==0 || id==','){
		$(".page-content .message-box-admin").remove();
		$(".page-content .x-navigation-panel").after('<div class="message-box-admin"><div class="col-lg-12"><div class="alert alert-danger"><i class="fa fa-info-circle"></i> Ningún registro seleccionado</div></div></div>');
		return true;
	}
}

function messageAjax(url) {
	try {
		$.ajax({
			url: url,
			type: "POST",
			//data: params,
			dataType: "json",
			async: false,
			beforeSend: function(){
				$.mpb('show',{value: [0,100],speed: 10});
			},
			success: function(data,callback) {
				setTimeout(function() { $.mpb('destroy') }, 3000);
				if(data.type != ""){
					$("#message-box-" + data.type).children(".mb-container").children(".mb-middle").children(".mb-title").html(data.title);
					$("#message-box-" + data.type).children(".mb-container").children(".mb-middle").children(".mb-content").html("<p>" + data.message + "</p>");
					$(".btn.btn-" + data.type + ".mb-control").click();
				}

				return callback(data.data);
			}
		})
	} catch (e){
		alert(e.message);
	}
}

function noticeAjax(url) {
	$.noty.closeAll();
	setTimeout(function(){
		try {
			$.ajax({
				url: url,
				type: "POST",
				// data: params,
				dataType: "json",
				async: false,
				beforeSend: function(){
					$.mpb('show',{value: [0,100],speed: 10});
				},
				success: function(data) {
					setTimeout(function() { $.mpb('destroy') }, 3000);
					data.forEach(function(entry) {
						switch (entry.type){
							case "success": 
						    	noty({text: entry.message, layout: 'topRight', type: 'success'})
								break;
							case "information": 
						    	noty({text: entry.message, layout: 'topRight', type: 'information'})
								break;
							case "error": 
						    	noty({text: entry.message, layout: 'topRight', type: 'error'})
								break;
							case "warning": 
						    	noty({text: entry.message, layout: 'topRight', type: 'warning'})
								break;
						   default: 
						       break;
						}
					});

					return callback(data.data);
				}
			})
		} catch (e){
			alert(e.message);
		}
	}, 3000);
}

$().ready(function(){
	$('.delete-selected').click(function() {
		var params = {};
		url = $(this).attr("id");
		params.selected	=  getArrayID();
		if(validateID(params.selected))
			return;
		if(!confirm("Estás seguro?"))
			return;
		panelAjax(url,params, function(data){
			$.each(data, function( index, value ) {
				$("#"+value).remove();
			});
		});
	});

	if($('#modules-menu').children('ul').children('li.active').length){
		$('#modules-menu').addClass('active');
	}

	/*
	$(".x-navigation-minimize").on("click",function(){
		minimized();
    });
	*/
});