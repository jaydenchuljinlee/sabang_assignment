$(document).ready(function() {
			
	/**************************************
	가입완료 버튼을 눌렀을 때, 이전 페이지의 값들에 대한 input을 그려주는 이벤트
	@completeBtn : 가입완료 버튼
	**************************************/
	var completeBtn = document.getElementById('join_complete');

	completeBtn.addEventListener('click',function() {
		<?php foreach($_POST as $key => $value) {
			
				#체크박스 배열 제어
				if ($key == 'reception_aggree') {

					for($idx = 0,$loop=count($value); $idx<$loop; $idx++) {

						echo	"var inputTag = document.createElement('input');";
						echo	"inputTag.setAttribute('type','hidden');";
						echo	"inputTag.setAttribute('name','".$key."[]');";
						echo	"inputTag.setAttribute('value','".$value[$idx]."');";
						echo	"$('#join_frm').prepend(inputTag);";
					}

					continue;
				}

				echo	"var inputTag = document.createElement('input');";
				echo	"inputTag.setAttribute('type','hidden');";
				echo	"inputTag.setAttribute('name','".$key."');";
				echo	"inputTag.setAttribute('value','".$value."');";
				echo	"$('#join_frm').prepend(inputTag);";
			}
		?>

		var len = $("input");
		console.log(len);
		for (var i=0; ; )
		{
		}
		
	});

});