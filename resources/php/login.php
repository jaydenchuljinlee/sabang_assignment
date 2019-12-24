<?php
	header('Content-Type: text/html; charset=utf-8');

	if (isset($_POST['data'])) {
		$rtnVal = "fail";
		
		#데이터 베이스 연결
		$conn = mysqli_connect(
			'localhost',
			'root',
			'cheoljin1234',
			'cheoljin'
		);

		$data		= json_decode($_POST['data']);
		$enc_pwd	= md5($data->pwd);

		#로그인 정보 조회
		$login			= "select user_id,type from user where user_id='".$data->id."' and pwd='".$enc_pwd."' and activation = 1;";
		$login_result	= mysqli_query($conn, $login);
		$row			= mysqli_fetch_array($login_result);
		$validation		= mysqli_num_rows($login_result);

		if ($validation > 0) {

			$user_info = array('identity'=>$row['user_id'],'authority'=>$row['type']);

			session_start();
			$_SESSION['user_info'] = $user_info;
			
			$rtnVal = "success";
		} 

		echo $rtnVal;
	}
?>