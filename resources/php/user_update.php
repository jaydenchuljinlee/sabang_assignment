<?php
	session_start();

	if(!(isset($_POST) && isset($_SESSION['user_info']))) {
		
		echo "<script>alert('해당 페이지로의 접근이 차단되었습니다.');location.href='/';</script>";
		return;
	}

	$post_arr = $_POST;

	/**************** 
	*필수값 0,빈값,NULL 체크
	*@param		: $post_arr(post로 넘어온 배열), $key(post의 키값),$value(키에 해당하는 밸류)
	*			: necessary_check()함수는 인자로 넘겨준 키값이 필수항목인지를 체크
	*****************/
	foreach($post_arr as $key => $value) {
	
		if (!necessary_check($key)) continue;

		if (empty($key)) {
			echo "<script>alert('필수항목이 입력되지 않았습니다.');location.href='/user_list.html';</script>";
			return;
		}
	}	

	#패스워드 값을 암호화
	$original	= $_POST['password'];
	$hash_pwd	= md5($original);

	#유저 ID
	$user_id = $_POST['user_id'];

	#데이터 베이스 연결
	$conn = mysqli_connect(
		'localhost',
		'root',
		'cheoljin1234',
		'cheoljin'
	);

	//트랜잭션 성공 여부
	$success = true;

	//트랜잭션 시작
	
	mysqli_query($conn,"SET AUTOCOMMIT=0");
	mysqli_query($conn,"START TRANSACTION");

	//메인 테이블
	$user_update	= "update user set `pwd`='".$hash_pwd."',`updated_date`=NOW() where user_id='".$user_id."';";

	$company_update	= "update company set `zip_code`='".$_POST['zondecode']."',`address`='".$_POST['company_address']."'," 
						."`address_detail`='".$_POST['company_address_detail']."',`main_phone`='".$_POST['main_phone']."',`fax`='".$_POST['fax']."',"
						."`company_type`='".$_POST['business_type']."',`employees`='".$_POST['employee_number']."',`representative`='".$_POST['representative']."',"
						."`company_name`='".$_POST['company']."'where user_id='".$user_id."';";

	$manager_update	= "update manager set `name`='".$_POST['manager']."',`rank`='".$_POST['manager_rank']."',`company_call`='".$_POST['company_call']."',"
						."`email`='".$_POST['email']."' where user_id='".$user_id."';";
	
	//메인 테이블에 대한 체크박스 관련 서브 테이블 생성을 위해 배열 생성
	$reception_aggree = Array();

	if (!clearCheckbox($conn,$user_id)) $success = false;

	createSubItemQuery($_POST,$reception_aggree);

	if (!insert_validation($conn,$user_update,$company_update,$manager_update)) {
		$success = false;
		echo "<script>alert('메인에 실패하였습니다.');location.href='/user_list.html';</script>";
	} else {

		if (!insert_sub_validation($conn,$reception_aggree)) {
			$success = false;
			echo "<script>alert(' 서브에 실패하였습니다.');location.href='/user_list.html';</script>";
		} else {
			echo "<script>alert('정보 수정에 성공하였습니다.');location.href='/user_list.html';</script>";
		}
	}

	if(!$success) {
		mysqli_query($conn,"ROLLBACK");
	} else {
		mysqli_query($conn,"COMMIT");
	}
	
	/**************** 
	*업데이트를 하기 전 체크박스 항목들을 지워주는 메서드
	*@param		: post값과 user_id
	*****************/
	function clearCheckbox($conn,$user_id) {
		
		//수신동의 테이블 로우 삭제
		$reception_delete	= "DELETE FROM reception_agree where 'user_id'='".$user_id."';";

		$reception_result = mysqli_query($conn,$reception_delete);

		return $reception_result;
	}

	/**************** 
	*체크박스 항목 테이블 쿼리에 대한 배열
	*@param		: post값과 reception_aggree,current_solutions,prev_solutions에 대한 배열 주소
	*****************/
	function createSubItemQuery($post,&$reception_aggree) {
		
		//서브 
		if(!empty($post['reception_aggree'])) {
			
			for($idx=0,$loop=count($post['reception_aggree']);$idx<$loop;$idx++) {
				
				$query = "insert into reception_agree(`user_id`,`agree_type`) values('".$post['user_id']."','".$post['reception_aggree'][$idx]."');";
				
				array_push($reception_aggree,$query);
			}
		}
		
	}

	/**************** 
	*DB insert 유효성 검사
	*@param		: user,company,manager insert쿼리 결과값
	*@return	: 3쿼리가 모두 참이어야 true를 리턴
	*****************/
	function insert_sub_validation($conn,&$reception_aggree) {
		
		$reception_result	= false;
		
		//문자,이메일 수신 동의 체크박스 관련 insert
		for($idx=0,$loop=count($reception_aggree);$idx<$loop;$idx++) {
			
			if(mysqli_query($conn,$reception_aggree[$idx])) {
				
				$reception_result	= true;
			} else {
				
				$reception_result	= false;
				break;
			}
			 
		}

		if (count($reception_aggree) == 0) $reception_result= true;
	

		return $reception_result;
	}

	/**************** 
	*DB update 유효성 검사
	*@param		: user,company,manager insert쿼리 결과값
	*@return	: 3쿼리가 모두 참이어야 true를 리턴
	*****************/
	function insert_validation($conn,&$user_update,&$company_update,&$manager_update) {

		$user_result	= mysqli_query($conn,$user_update);
		$company_result	= mysqli_query($conn,$company_update);
		$manager_result	= mysqli_query($conn,$manager_update);

		return $user_result && $company_result && $manager_result;
	}

	/**************** 
	*필수항목 체크
	*@param		: post로 넘어온 key값
	*@return	: 필수값이 존재하면 1, 존재하지 않으면 0
	*****************/
	function necessary_check($param) {
	
		return $param == 'version' || $param == 'identity' || $param == 'password' || $param == 'manager' || $param == 'company_call' ||
			$param == 'manager_call' || $param == 'email' || $param == 'sectors' || $param == 'company' || $param == 'company' ||
			$param == 'representative' || $param == 'buisnessman_type' || $param == 'business_no' || $param == 'zondecode' ||
			$param == 'company_address' || $param == 'company_address_detail' || $param == 'main_phone' || $param == 'fax' || $param == 'employee_number';
	}
?>