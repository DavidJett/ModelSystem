<?php
	class IndexController extends Controller
	{
		public function defaultView()
		{
			$this->action = 'loginView';
			$this->view = new View($this->controller, $this->action);
			$this->loginView();
		}

		public function loginView()
		{
			$this->render();
		}

		public function validate()
		{
			$indexModel = new IndexModel;
			$indexModel->validate($_POST['username'], $_POST['password']);
		}
	}
?>