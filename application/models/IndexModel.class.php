<?php
	class IndexModel extends Model
	{
		public function validate($username, $password)
		{
			$sql = 'select password, isActive, rightId from user_info where id = ?';
			$res = $this->query($sql, array($username));
			if(count($res)==0 || $res[0]['isActive']=='0' || $password!=$res[0]['password']){
				echo 'fail';
			}else{
				$_SESSION['user'] = $username;
				$rightId = $res[0]['rightId'];
				$_SESSION['user_right'] = $rightId;
				$sql = 'select pageEntrance from right_info where rightId = ?';
				$res = $this->query($sql, array($rightId));
				if(count($res)>0){
					echo 'success:' . APP_URL . $res[0]['pageEntrance'];
				}else{
					echo 'fail';
				}
			}
		}
	}
?>