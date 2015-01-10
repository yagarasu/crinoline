<?php
	
	/**
	 * News Presenter
	 */
	class News extends Presenter
	{

		public function __construct($args=array())
		{
			parent::__construct($args);
		}

		public function listAll()
		{
			$article = new ArticleModel();
			$article->title = "algo";
			echo $article->title;
		}

		public function showSingle()
		{
			$id = $this->getArg("id");
			echo "Single: ".$id;
		}

	}

?>