<?php
	class PlannerModel extends Model
	{
		public function getTasks()
		{
			$sql = 'select 	machineId, machineCode, machineName, deptName, restTime from machine_info left join dept_info on machine_info.belongedDept = dept_info.	deptId';
			$machines = $this->query($sql, array());
			$machinesMap = [];
			$machinesVis = [];
			foreach ($machines as $value) {
				$id = $value['machineId'];
				$machinesMap[$id] = [$id, $value['machineCode'], $value['machineName'], $value['deptName'], json_decode($value['restTime'], true)];
				$machinesVis[$id] = false;
			}

			$planPath = DATA_PATH . 'palnnerData.txt';
			$planFile = fopen($planPath, 'r');
			if($planFile == false){
				$res = [];
				foreach ($machinesMap as $key => $value) {
					$res[$key] = [];
					$res[$key][0] = $value;
					$res[$key][1] = [];
				}
				return json_encode($res, JSON_UNESCAPED_UNICODE);
			}
			$fileSize = filesize($planPath);
			if($fileSize == 0){
				$res = [];
			}else{
				$str = fread($planFile, $fileSize);
				$res = json_decode($str, true);
			}
			foreach ($res as $key => $value) {
				$machinesVis[$key] = true;
			}
			foreach ($machinesVis as $key => $value) {
				if(!$value){
					$res[$key] = [];
					$res[$key][0] = $machinesMap[$key];
					$res[$key][1] = [];
				}
			}
			fclose($planFile);
			return json_encode($res, JSON_UNESCAPED_UNICODE);
		}

		public function getActiveParts()
		{
			$res = [];
			$sql = 'select modelId, modelCode from model_info where finishTime is null';
			$activeModels = $this->query($sql, array());
			foreach ($activeModels as $key=>$value) {
				$sql = 'select id, partCode, partName from part_info where belongedModel=?';
				$activeParts = $this->query($sql, array($value['modelId']));
				$res[$key] = [];
				$res[$key][0] = [$value['modelId'], $value['modelCode']];
				$res[$key][1] = [];
				foreach ($activeParts as $k=>$val) {
					$res[$key][1][$k] = [$val['id'], $val['partCode'], $val['partName']];
				}
			}
			return json_encode($res, JSON_UNESCAPED_UNICODE);
		}

		public function getWorkers()
		{
			$res = [];
			$sql = 'select deptId,deptName from dept_info where deptId<>0 and deptId<>1 and deptId<>2';
			$depts = $this->query($sql, array());
			foreach ($depts as $key=>$value) {
				$sql = 'select id,name from user_info where dept = ?';
				$workers = $this->query($sql, array($value['deptId']));
				$res[$key] = [];
				$res[$key][0] = $value['deptName'];
				$res[$key][1] = [];
				foreach ($workers as $k => $val) {
					$res[$key][1][$k] = [$val['id'], $val['name']];
				}
			}
			return json_encode($res, JSON_UNESCAPED_UNICODE);
		}

		public function publish($timeStamp, $workerId, $modelId, $partId, $procedureName)
		{
			$sql = 'select id from pending_task where id=?';
			$res = $this->query($sql, array($timeStamp));
			if(count($res)==0){
				$sql = 'insert into pending_task (id,workerId,modelId,partId,procedureName,state) values(?,?,?,?,?,?)';
				return $this->execute($sql, array($timeStamp, $workerId, $modelId, $partId, $procedureName, 1));
			}else{
				$sql = 'update pending_task set state = ? where id=?';
				return $this->execute($sql, array(1, $timeStamp));
			}
		}

		public function getState($id)
		{
			$sql = 'select state from pending_task where id=?';
			$res = $this->query($sql, array($id));
			if(count($res) == 0){
				return 'null';
			}else{
				return $res[0]['state'];
			}
		}

		public function abjust($res)
		{
			$sql = 'select id, state from pending_task';
			$pendTask = $this->query($sql, array());
			$pendTaskMap = [];
			$pendTaskVis = [];
			foreach ($pendTask as $value) {
				$id = $value['id'];
				$pendTaskMap[$id] = $value['state'];
				$pendTaskVis[$id] = false;
			}
			foreach ($res as $key=>$value) {
				foreach ($value[1] as $k=>$val) {
					$timeStamp = $val[9];
					$state = $val[10];
					if(isset($pendTaskMap[$timeStamp])){
						$pendTaskVis[$timeStamp] = true;
						$res[$key][1][$k][10] = $pendTaskMap[$timeStamp];
					}
				}
			}
			foreach ($pendTaskVis as $key => $value) {
				if(!$value){
					$sql = 'delete from pending_task where id=?';
					$this->execute($sql, array($key));
				}
			}
			return json_encode($res, JSON_UNESCAPED_UNICODE);
		}
	}
?>