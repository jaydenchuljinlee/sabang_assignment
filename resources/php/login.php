<?php
	header('Content-Type: text/html; charset=utf-8');

	if (isset($_POST['data'])) {
		$rtnVal = "fail";
		
		#������ ���̽� ����
		$conn = mysqli_connect(
			'localhost',
			'root',
			'cheoljin1234',
			'cheoljin'
		);

		$data = json_decode($_POST['data']);

		#�α��� ���� ��ȸ
		$login			= "select user_id from user where user_id='".$data->id."'";
		$login_result	= mysqli_query($conn, $login);
		$row			= mysqli_fetch_array($login_result);

		if ($row['user_id'] != '') {
			$identity = $row['user_id'];

			session_start();
			$_SESSION['identity'] = $identity;
			
			$rtnVal = "success";
		} 

		echo $rtnVal;
	}
?>