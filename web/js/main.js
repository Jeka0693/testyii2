
$(document).ready(function(){
	var intervalId;	
	$('.selSel').bind('change',function(){ $('.vitrina').html('');
		if(intervalId){
			clearTimeout(intervalId);
		}
		intervalId =  setTimeout(getBooksFromLists,5000);
		couter();
	});

});

var intervalaIdCounter;
function couter(){
	if(intervalaIdCounter){
			clearInterval(intervalaIdCounter);
	}
	var sec = 5;
	$('.counter').show().text(sec);
	intervalaIdCounter = setInterval(function(){
		sec-= 1;
		$('.counter').text(sec);
		if(sec==0){
			$('.counter').hide();
			clearInterval(intervalaIdCounter);
		}
	},1000);
}

function getBooksFromLists(){
	var datas = $('#w0').serialize();

		$.post('',datas,function(data){
			console.log(data);
			var html='';
			$.each(data,function(k,v){
				html+='<div class="col-md-2">';
				html+='<div>Информация о книге:</div>';
				html+='<div>Название '+v.name+'</div>';
				if(v.author_c){html+='<div>Автор '+v.author_c+'</div>';}
				if(v.release){html+='<div>Год выпуска '+v.release+'</div>';}
				if(v.shop_c){html+='<div>Магазин '+v.shop_c+'</div>';}
				html+='</div>';
			});
			$('.vitrina').html(html);
		},'json');
}