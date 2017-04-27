<?php
	class ShowController extends Controller
	{
		public function defaultView()
		{
			$this->action = 'machineView';
			$this->view = new View($this->controller, $this->action);
			$this->machineView();
		}

		public function machineView()
		{
			Core::rightFilter(['0', '2', '6']);
			$this->render();
		}
	}
?>