<?php	
	class Core
	{
		public function run()
		{
			spl_autoload_register(array($this, 'loadClass'));
			$this->route();
		}

		public function route()
		{
			$controllerName = 'Index';
			$action = 'defaultView';
			$param = array();

			$url = isset($_GET['url']) ? $_GET['url'] : false;
			if ($url) {
				$urlArray = explode('/', $url);
				$urlArray = array_filter($urlArray);
				$controllerName = ucfirst($urlArray[0]);
				array_shift($urlArray);
				$action = isset($urlArray[0]) ? $urlArray[0] : 'defaultView';
				array_shift($urlArray);
				$param = $urlArray ? $urlArray : array();
			}
			session_start();
			$controller = $controllerName . 'Controller';
			$dispatch = new $controller($controllerName, $action);
			if ((int)method_exists($controller, $action)) {
				call_user_func_array(array($dispatch, $action), $param);
			} else {
				Logger::info('找不到 ' . $controller . ' 类中包含的方法：' .  $action);
			}
		}

		public static function loadClass($class)
		{
			$frameworks = FRAME_PATH . $class . '.class.php';
			$controllers = APP_PATH . 'application/controllers/' . $class . '.class.php';
			$models = APP_PATH . 'application/models/' . $class . '.class.php';

			if (file_exists($frameworks)) {
				require_once $frameworks;
			} elseif (file_exists($controllers)) {
				require_once $controllers;
			} elseif (file_exists($models)) {
				require_once $models;
			} else {
				Logger::info('无法加载类：' . $class);
			}
		}

		public static function rightFilter($allow)
		{
			if(!isset($_SESSION['user'])){
				header('Location:' . APP_URL . 'Index/defaultView');
				exit;
			}

			$r = isset($_SESSION['user_right']) ? $_SESSION['user_right'] : false;
			if(!$r){
				header('Location:' . APP_URL . 'Index/defaultView');
				exit;
			}
			
			foreach ($allow as $value){
				if($value == $r){
					return;
				}
			}
			header('Location:' . APP_URL . 'public/error-page/403.html');
			exit;
		}
	}
?>