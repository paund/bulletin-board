
<?php 

	//【送信データの保存】

	//送られた情報を変数に変換する
	$ip_name = $_POST['nickname'];
	$ip_com = $_POST['comments'];
	$ip_date = date("Y/m/d H:i:s");
	$ip_pass = $_POST['password'];

	//DB接続
	// new PDO（ホスト名・DB名、ユーザー名、パスワード）
	$dsn = DB名;
	$user = ユーザー名;
	$password = パスワード;
	$pdo = new PDO($dsn,$user,$password);

	//テーブル作成
	//(存在してないなら)= CREATE TABLE IF NOT EXISTS 'テーブル名'
	//VARCHER:文字列 INT:数値 TEXT:改行可の文字列 DATETIME:日付 (保存桁数)
	//オートインクリメント（$i++）=INT auto_increment primary key,
	$sql="CREATE TABLE tb_mission4"
		."("
			."id INT AUTO_INCREMENT PRIMARY KEY,"
			."name char(30),"
			."comment TEXT(200),"
			."date DATETIME,"
			."password char(30)"
		.")"
		."DEFAULT CHARSET=utf8;";
	
	$tb_mission4 = $pdo->query($sql);


	// 空の場合は除外してDB保存
	if( !empty($ip_name) ){
	
		$sql = $pdo -> prepare("INSERT INTO tb_mission4 (name,comment,date,password) VALUES (:name,:comment,:date,:password)");
		$sql -> bindParam(':name', $ip_name, PDO::PARAM_STR);
		$sql -> bindParam(':comment', $ip_com, PDO::PARAM_STR);
		$sql -> bindParam(':date', $ip_date, PDO::PARAM_STR);
		$sql -> bindParam(':password', $ip_pass, PDO::PARAM_STR);
		$sql -> execute();

	}

//【削除機能】
	$del_number = $_POST['ex_number'];
	$del_pass = $_POST['del_pass'];

	if(!empty($del_number)){

		//パスワード識別
		$sql = "SELECT password FROM tb_mission4 WHERE id = $del_number";
		$del_pass_check = $pdo -> query($sql);

		//フェッチでDBの値を取り出す
		$sql = $pdo -> prepare ("SELECT * FROM tb_mission4 WHERE id = $del_number");
		$sql -> execute();

		if($del_data = $sql->fetch(PDO::FETCH_BOTH)){

			if( $del_data[4] == $del_pass){

				//削除実行
				$sql = "DELETE FROM tb_mission4 WHERE id= $del_number";
				$delete = $pdo->query($sql);
			
				//更新できたか確認
				if ($pdo->query($sql)){
					  echo '<p>削除完了</p>';                    
				} else {
					echo "削除エラー";
					print_r($sql->errorInfo());
				} 

			} else {
			echo "削除パスワードが違います";
			}
		}
	}


//【編集機能】

	//[編集選択機能]
	$edi_number = $_POST['edi_number'];
	$edi_pass = $_POST['edi_pass'];

	//空じゃなければ
	if(!empty($edi_number)){

		//パスワード識別
		$sql = "SELECT password FROM tb_mission4 WHERE id = $edi_number";
		$edi_pass_check = $pdo -> query($sql);

		//フェッチでDBの値を取り出す
		$sql = $pdo -> prepare ("SELECT * FROM tb_mission4 WHERE id = $edi_number");
		$sql -> execute();

		if($edi_data = $sql->fetch(PDO::FETCH_BOTH)){

			if( $edi_data[4] == $edi_pass){

				$edimode_ok = "編集モード";

				//編集内容を入力フォームに表示
				$edi_name_check = $edi_data[1];
				$edi_com_check = $edi_data[2];	

			} else {
			echo "編集パスワードが違います";
			} 
		}
	}	


	//[編集実行機能]
	$edi_name = $_POST['edi_name'];
	$edi_com = $_POST['edi_comment'];
	$edi_num = $_POST['edimode'];

	//編集の中身があれば
	if(!empty($edi_name)){

		//編集内容更新
		$sql = $pdo -> prepare("update tb_mission4 set name='$edi_name', comment='$edi_com' where id = $edi_num");

		//更新できたか確認
		if ($sql->execute()){
			  echo '<p>更新完了</p>';                    
		} else {
			echo "編集エラー";
			print_r($sql->errorInfo());
		} 

	}

?>


<meta charset = "UTF-8">

<form action = "mission_4.php" method = "post">

	名前<br>
	<input type="text" placeholder="ニックネーム" 		

		//編集モードの時はvalu値,name値を変更
		value="<?php  if (!empty($edimode_ok)){ echo $edi_name_check; } ?>"
		name="<?php  if (!empty($edimode_ok)){ echo 'edi_name'; }else{ echo 'nickname'; } ?>"

 	size="50">

	<br>
	コメント<br>
	<textarea placeholder="コメントを入力してください" 

		//編集モードの時はvalu値,name値を変更
		name="<?php  if (!empty($edimode_ok)){ echo 'edi_comment'; }else{ echo 'comments'; } ?>"
		cols="50" rows="8"><?php  if (!empty($edimode_ok)){ echo $edi_com_check; } ?></textarea><br>

	<input type="text" name="password" placeholder="パスワード" size="10"><br>
	<input type="hidden" name="edimode" value="<?php echo $edi_number; ?>" size="50">

	<button type="submit" value="">投稿</button>
	
	<br>
	●投稿を削除する<br>
	<input type="text" name="ex_number" placeholder="削除対象番号" size="10"><br>
	<input type="text" name="del_pass" placeholder="パスワード" size="10">
	<button type="submit" value="">削除</button>
	
	<br>
	●投稿を編集する<br>
	<input type="text" name="edi_number" placeholder="編集対象番号" size="10"><br>
	<input type="text" name="edi_pass" placeholder="パスワード" size="10">
	<button type="submit" value="">編集</button>

</form>

<?php


	//テーブルの中身確認
	$sql = "SELECT * FROM tb_mission4";
	$show_tb = $pdo -> query($sql);

	$sql = "SET CHARSET utf8";
	$char_tb = $pdo -> query($sql);

	foreach($show_tb as $show_row){
		print "<pre>";
		print_r ($show_row);
		print "<pre>";
	}

	$pdo = null;
?>

