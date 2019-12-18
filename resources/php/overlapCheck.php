<?php
	
	if (isset($_POST['identity'])) {
		
		$identity = $_POST['identity'];

		#데이터 베이스 연결
		$conn = mysqli_connect(
			'localhost',
			'root',
			'cheoljin1234',
			'cheoljin'
		);

		$chk_query	= "select user_id from user where user_id=".$identity;
		
		$result		= mysqli_query($conn,$chk_query);
		echo $result;
	}
?>