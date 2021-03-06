$(document).ready(function() {

	//사업자유형 div 클릭시 하이라이트 이동
	$(".buisnessman").on("click",function() {
		$(".buisnessman-selected").removeClass("buisnessman-selected");

		$(this).addClass("buisnessman-selected");
		$("#buisnessman_type_input").val($(this).val());
	});

	//이메일 셀렉트박스 변경
	$("#email_select").on("change",function() {
		$("#email_address").val($(this).val());
	});

	/********************** 
	*주소검색시 다음주소api 실행
	* #search_zonecode		: 우편번호 태그
	* #search_address		: 검색후 입력되는 주소 태그
	* #address_details		: 상세주소 입력 태그
	***********************/
	$("#search").on("click",function() {
		
		new daum.Postcode({

			oncomplete:function(data) {
				
				$("#search_zonecode").val(data.zonecode);
				$("#search_address").val(data.address);
				$("#address_details").focus();
			}

		}).open();
	});


	$("#user_delete").on("click",function() {
		
		var user_id = $("#user_id_input").val();

		$(":input").removeAttr("name");
	
		$("#user_id_input").attr("name","user_id");

		$("#update_frm").attr("action","/resources/php/user_delete.php");
		$("#update_frm").submit();
		
	});

	/********************** 
	*업데이트 전송
	*@arr		: 필수항목 클래스를 가진 태그
	*@return	: 0은 비정상 종료,1은 정상 종료
	***********************/
	$("#user_update").on("click",function() {

		if (!necessaryCheck()) return;
		
		if (!accountCheck()) return;

		if (!regexCheck()) return;

		var company_call	= $("#company_call_input1").val() + "-" + $("#company_call_input2").val() + "-" + $("#company_call_input3").val(),
			manager_phone	= $("#company_call_input1").val() + "-" + $("#company_call_input2").val() + "-" + $("#company_call_input3").val(),
			email			= $("#email_id").val() + "@" + $("#email_address").val(),
			main_call		= $("#main_phone_input1").val() + "-" + $("#main_phone_input2").val() + "-" + $("#main_phone_input3").val(),
			fax				= $("#fax_input1").val() + "-" + $("#fax_input1").val() + "-" + $("#fax_input1").val();

			$("#company_call_input").val(company_call);
			$("#company_call_input").val(manager_phone);
			$("#email_input").val(email);
			$("#main_phone_input").val(main_call);
			$("#fax_input").val(fax);

		$("#update_frm").attr("action","/resources/php/user_update.php");
		$("#update_frm").attr("method","post");
		$("#update_frm").submit();
		
	})
});

/********************** 
*정규식 체크
*@arr		: 필수항목 클래스를 가진 태그
*@return	: 0은 비정상 종료,1은 정상 종료 
***********************/
function regexCheck() {

	var rtnVal = 0;
	
	var regexEn			= /[a-zA-Z]/,
		regexNum		= /[0-9]/,
		regexSign		= /[\{\}\[\]\/?.,;:|\)*~`!^\-_+<>@\#$%&\\\=\(\'\"]/,
		regexEmail_1	= /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])/,
		regexEmail_2	= /[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}/;

	var password	= $("#input_pwd").val(),
		email_1		= $("#email_id").val(),
		email_2		= $("#email_address").val();
	
	//비밀번호 정규식 체크
	if (regexEn.test(password) === false || regexNum.test(password) === false || 
	regexSign.test(password) === false || password.length < 8) {

		alert("비밀번호는 숫자,문자,특수문자 조합으로 8자 이상 입력해주세요.");
		$("#input_pwd").focus();
		return 0;
	} 
	
	//이메일 정규식 체크
	if (regexEmail_1.test(email_1) === false || regexEmail_2.test(email_2) === false) {

		alert("이메일 형식을 올바르게 작성해주세요.");
		$("#email_input").focus();
		return 0;
	}
	
	return 1;
}

/********************** 
*필수항목 체크
*@arr		: 필수항목 클래스를 가진 태그
*@return	: 0은 비정상 종료,1은 정상 종료
***********************/
function necessaryCheck() {
	
	var arr = new Array();
	
	arr = $(".necessary");
	
	for (var i=0; i<arr.length ; i++) {
		var tag = arr[i];

		if ($(tag).val() == '' || $(tag).val() == 'default') {

			alert("필수항목을 입력해주세요!");
			$(tag).focus();

			return 0;
		}
	}
	
	return 1;
}

/********************** 
*아이디,비밀번호 확인 체크
*@id_confirm	: 중복확인 버튼
*@id_input		: 아이디 인풋
*@compare_input : 아이디 비교 인풋
*@pwd_input		: 비밀번호 인풋
*@pwd_confirm	: 비밀번호 확인 인풋
*@return		: 0은 비정상 종료, 1은 정상 종료
***********************/
function accountCheck() {
	
	var pwd_input		= $("#input_pwd").val(),
		pwd_confirm		= $("#confirm_pwd").val();
	
	if (pwd_input !== pwd_confirm) {
		alert("비밀번호를 확인해 주세요.");
		$("#confirm_pwd").focus();
		return 0;
	}

	return 1;
}

//input number 길이 체크
function maxLengthCheck(object){
	if (object.value.length > object.maxLength){
		object.value = object.value.slice(0, object.maxLength);
	}    
}