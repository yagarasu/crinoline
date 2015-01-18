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
			$article = new ArticleMap();
			$article->title = "algo";
			echo $article->title;
		}

		public function showSingle()
		{
			$id = $this->getArg("id");
			echo "<p>Single: ".$id."</p>";
			$article = new ArticleMap();
			$article->bindEvent("ALL", function($evtArgs) {
				echo "<hr>";
				var_dump($evtArgs['event']);
				echo "<hr>";
			});
			echo "<p>load res: </p>";
			var_dump($article->load($id));
		}

		public function newArticle()
		{
			$article = new ArticleMap();
			$article->bindEvent("ALL", function($evtArgs) {
				echo "<hr>";
				var_dump($evtArgs['event']);
				echo "<hr>";
			});

			$article->id = "2";
			$article->title = "Edit article 2";
			$article->date = "now()";
			$article->author = "Carmen Crinoline";
			$article->excerpt = "Lorem ipsum...";
			$article->content = "Lorem ipsum dolor sit amet, consasd asd asdas dasd asdectetur adipisicing elit. Nobis, magni, vel? Magni sit nulla harum consectetur consequatur, eum corrupti omnis aspernatur similique ipsam eaque voluptatem. Qui expedita omnis aliquam, id.";

			$article->save();
		}

		public function test()
		{
			error_reporting(E_ALL ^ E_STRICT);
			//echo "<h1>test!</h1>";
			$mArticle = new ArticleMap();
			$mArticle->load(1);
			$cArticles = new ArticlesCollection();
			$cArticles->load();
			$vTest = new TestView();
			$vTest->registerModel("article", $mArticle);
			$vTest->registerModel("articles", $cArticles);
			$vTest->loadTemplate("templates/newtest.crml");
			$vTest->render();
		}

	}

?>