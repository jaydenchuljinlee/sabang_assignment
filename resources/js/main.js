$(document).ready(function() {
			
	/* 
	   ������ �̵� �Լ�
	   @id : �̵��� ������
	*/
	$(".next-page").on("click",function() {
		var id = $(this).attr("id");

		location.href = id+".html";
	});
});