<?php
	
	if (isset($_POST['business_no'])) {
		
		$business_no = $_POST['business_no'];

		#데이터 베이스 연결
		$conn = mysqli_connect(
			'localhost',
			'root',
			'cheoljin1234',
			'cheoljin'
		);

		$chk_query	= "select business_no from company where business_no =".$business_no;
		
		$result		= mysqli_query($conn,$chk_query);
		echo $row = mysqli_fetch_array($result);
	}
?>