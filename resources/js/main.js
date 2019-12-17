$(document).ready(function() {
			
	/* 
	   페이지 이동 함수
	   @id : 이동할 페이지
	*/
	$(".next-page").on("click",function() {
		var id = $(this).attr("id");

		location.href = id+".html";
	});
});