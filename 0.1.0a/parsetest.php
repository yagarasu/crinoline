<?php

function matchTags($content) {
	$pattern = ''
		.'~\{\s*'
		.'(?P<cmd>\$?\w+)(?:\:(?P<subcmd>\w+))?'
		.'\s*(?P<params>\w+=\".*\")*\s*'
		.'(\}(?P<cont>.*)\{\1)?'
		.'\s*\}~';
	return preg_replace_callback('/'.$pattern.'/mi', 'execTags', $content);
}

function execTags($matches) {
	echo "<hr />";
	echo "<p>CMD: ".$matches['cmd']."</p>";
	var_dump($matches);
	echo "<hr />";
}

echo "<p>Result:</p>";
echo matchTags('

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Prueba</title>
</head>
<body>
	<h1>Prueba crml</h1>
	<hr />
	<h2>Dyn change</h2>
	~{$article:title}~
	~{barfoo}~
	<hr />
	<h2>Attribs</h2>
	~{foo bar="baz" fu="barbar"}~
	<hr />
	<h2>Blocks</h2>
	~{foo bar="baz"}<p>Data data data</p>{foo}~
</body>
</html>

');

?>