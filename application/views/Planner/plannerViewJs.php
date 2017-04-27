<script>
	window.onload = function(){
		var initialTasks = JSON.parse('<?php echo $this->variables['tasks'] ?>');
		var modelParts = JSON.parse('<?php echo $this->variables['activeParts'] ?>');
		var workers = JSON.parse('<?php echo $this->variables['workers'] ?>');
		modelSystemPlanner(initialTasks, modelParts, workers);
	}
</script>