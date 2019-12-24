<?php
	
	$post_arr = $_POST;

	/**************** 
	*필수값 0,빈값,NULL 체크
	*@param		: $post_arr(post로 넘어온 배열), $key(post의 키값),$value(키에 해당하는 밸류)
	*			: necessary_check()함수는 인자로 넘겨준 키값이 필수항목인지를 체크
	*****************/
	foreach($post_arr as $key => $value) {
	
		if (!necessary_check($key)) continue;

		if (empty($key)) {
			echo "<script>alert('필수항목이 입력되지 않았습니다.');location.href='join_agree.html';</script>";
			return;
		}
	}	

	#패스워드 값을 암호화
	$original	= $_POST['password'];
	$hash_pwd	= md5($original);

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
	
	mysqli_query("SET AUTOCOMMIT=0");
	mysqli_query("START TRANSACTION");

	//메인 테이블
	$user_insert	= "insert into user('user_id','mall_version','join_root','recommender','cuppon','pwd','created_date','type') values('"
						.$_POST['identity']."','".$_POST['version']."','".$_POST['join_root']."','".$_POST['recommender']."','".$_POST['cuppon']."','"
						.$hash_pwd."',NOW(),'user')";
	$company_insert	= "insert into "."company('user_id','s_code','company_type','business_no','business_img','zip_code','address','address_detail','main_phone','fax','business_type',"
					."'employees','representative','company_name') values("
					.$_POST['identity']."',".$_POST['sectors'].",'".$_POST['business_type']."','".$_POST['business_no']."','img','".$_POST['zondecode']."','"
					.$_POST['company_address']."','".$_POST['company_address_detail']."','".$_POST['main_phone']."','".$_POST['fax']."','".$_POST['buisnessman_type']."','"
					.$_POST['employee_number']."','".$_POST['representative']."','".$_POST['company']."')";
	$manager_insert	= "insert into manager('user_id','name','rank','company_call','email') "
					."values(".$_POST['identity']."','".$_POST['manager']."','".$_POST['manager_rank']."','".$_POST['company_call']."','"
					.$_POST['email']."')";
	

	//메인 테이블에 대한 체크박스 관련 서브 테이블 생성을 위해 배열 생성
	$reception_aggree = Array();
	$current_solutions = Array();
	$prev_solutions = Array();

	createSubItemQuery($_POST,$reception_aggree,$current_solutions,$prev_solutions);

	$user_result	= mysqli_query($conn,$user_insert);
	$company_result	= mysqli_query($conn,$company_insert);
	$manager_result	= mysqli_query($conn,$manager_insert);

	if (!insert_validation($user_result,$company_result,$manager_result)) {
		$success = false;
		echo "<script>alert('회원가입에 실패하였습니다.');</script>";
	} else {

		if (!insert_sub_validation($conn,$reception_aggree,$current_solutions,$prev_solutions)) {
			$success = false;
			echo "<script>alert('회원가입에 실패하였습니다.');</script>";
		}

		echo "<script>alert('회원가입에 성공하였습니다.');</script>";
	}
	
	if(!$success) {
		mysqli_query("ROLLBACK",$conn);
		echo "록백됨";
	} else {
		mysqli_query("COMMIT",$conn);
		echo "커밋됨";
	}
	

	/**************** 
	*체크박스 항목 테이블 쿼리에 대한 배열
	*@param		: post값과 reception_aggree,current_solutions,prev_solutions에 대한 배열 주소
	*****************/
	function createSubItemQuery($post,$reception_aggree,$current_solutions,$prev_solutions) {
		
		//서브 
		if(!empty($post['reception_aggree'])) {

			for($idx=0,$loop=count($post['reception_aggree']);$idx<$lop;$idx++) {
				
				$query = "insert into reception_agree('user_id','agree_type') values('".$post['identity']."','".$post['reception_aggree'][$idx]."')";
				array_push($reception_aggree,);
				
			}
		}

		if(!empty($post['current_solutions'])) {

			for($idx=0,$loop=count($post['current_solutions']);$idx<$lop;$idx++) {
				
				$query = "insert into current_solutions('user_id','s_code') values('".$post['identity']."','".$post['current_solutions'][$idx]."')";
				array_push($current_solutions,);
				
			}
		}

		if(!empty($post['prev_solutions'])) {

			for($idx=0,$loop=count($post['prev_solutions']);$idx<$lop;$idx++) {
				
				$query = "insert into prev_solutions('user_id','agree_type') values('".$post['identity']."','".$post['prev_solutions'][$idx]."')";
				array_push($prev_solutions,);
				
			}
		}
		
	}

	/**************** 
	*DB insert 유효성 검사
	*@param		: user,company,manager insert쿼리 결과값
	*@return	: 3쿼리가 모두 참이어야 true를 리턴
	*****************/
	function insert_sub_validation($conn,$reception_aggree,$current_solutions,$prev_solutions) {
		
		$reception_result	= false;
		$current_result		= false;
		$preve_result		= false;

		//문자,이메일 수신 동의 체크박스 관련 insert
		foreach($reception_aggree as $value) {
			
			if(mysqli_query($conn,$value)) {
			
				$reception_result = true;
			} else {
				$reception_result	= false;
				break;
			}
		}

		//현재 솔루션 사용 유무 체크박스 관련 isnert
		foreach($current_solutions as $value) {
			
			if(mysqli_query($conn,$value)) {
			
				$current_result = true;
			} else {
				$current_result	= false;
				break;
			}
		}

		//이전 솔루션 사용 유무 체크박스 관련 insert
		foreach($prev_solutions as $value) {
			
			if(mysqli_query($conn,$value)) {
			
				$preve_result = true;
			} else {
				$preve_result	= false;
				break;
			}
		}

		return $reception_result && $current_result && $preve_result;
	}

	/**************** 
	*DB insert 유효성 검사
	*@param		: user,company,manager insert쿼리 결과값
	*@return	: 3쿼리가 모두 참이어야 true를 리턴
	*****************/
	function insert_validation($query_1,$query_2,$query_3) {

		return $query_1 && $query_2 && $query_3;
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