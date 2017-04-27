<?php
	class PlannerController extends Controller
	{
		public function defaultView()
		{
			$this->action = 'plannerView';
			$this->view = new View($this->controller, $this->action);
			$this->plannerView();
		}

		public function plannerView()
		{
			Core::rightFilter(['0', '2']);
			$plannerModel = new PlannerModel;
			$tasks = $plannerModel->getTasks();
			$activeParts = $plannerModel->getActiveParts();
			$workers = $plannerModel->getWorkers();
			$this->assign('tasks', $tasks);
			$this->assign('activeParts', $activeParts);
			$this->assign('workers', $workers);
			$this->render();
		}

		public function save()
		{
			Core::rightFilter(['0', '2']);
			$plannerModel = new PlannerModel;
			$planPath = DATA_PATH . 'palnnerData.txt';
			$planFile = fopen($planPath, 'w');
			if($planFile == false){
				echo 'fail';
				return;
			}
			$data = $_POST['data'];
			$data = $plannerModel->abjust(json_decode($data, true));
			if(flock($planFile, LOCK_EX)){
				if(fwrite($planFile, $data) == false){
					echo 'fail';
					fclose($planFile);
					return;
				}
				flock($planFile, LOCK_UN);
			}
			fclose($planFile);
			echo 'success';
		}

		public function publish()
		{
			Core::rightFilter(['0', '2']);
			$plannerModel = new PlannerModel;
			if($plannerModel->publish($_POST['timeStamp'], $_POST['workerId'], $_POST['modelId'], $_POST['partId'], $_POST['procedureName'])){
				echo 'success';
			}else{
				echo 'fail';
			}
		}

		public function getState()
		{
			Core::rightFilter(['0', '2']);
			$plannerModel = new PlannerModel;
			echo $plannerModel->getState($_POST['id']);
		}
	}
?>