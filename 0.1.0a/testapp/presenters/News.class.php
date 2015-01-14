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
			echo "<h1>test!</h1>";
			$cArticles = new ArticlesCollection();
			$cArticles->create(array(
				"title"		=> "Articulo 1",
				"author"	=> "Carmen Crinoline",
				"excerpt"	=> "Lorem",
				"content"	=> "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam ipsum animi corporis, odio minus, ducimus commodi nostrum perspiciatis, error soluta repellendus nesciunt. Cumque iure iste dolor animi, recusandae asperiores delectus?"
			));
			$auth = new AuthorMap(array(
				"name"	=> "Carmen Crinoline",
				"age"	=> 40
			));
			echo "<p>Author: ".$auth->name."</p>";
			$last = $cArticles->append(new ArticleMap(array(
				"title"		=> "Articulo 2",
				"author"	=> "Carmen Crinoline"
			)));
			echo "<p>Last article author: ".$cArticles->at($last)->author."</p>";
			echo "<hr>";
			echo "<p>Filter: </p>";
			$f = $cArticles->searchFor(array(
				"title"	=> "Articulo 1"
			));
			foreach ($f as $ar) {
				echo "<p>".$ar->title."</p>";
			}
		}

	}

?>