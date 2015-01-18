<?php

	class TestView extends View
	{
		public function __construct()
		{
			$this->template = "
				<html>
					<body>
						<p>Algo</p>
					</body>
				</html>
			";
		}
	}

?>