<?php
require_once("template.php");
require_once("database.php");
date_default_timezone_set('America/New_York');

$data = array();
$dbh = connect_database();

if (isset($_GET['q'])) {
	$k = 0;

	// Find etds with particular word in the title
	$q = "%".$_GET['q']."%";
	$stmt = $dbh->prepare("select * from etd where title like ?;");
	// $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$stmt->bindParam(1, $q);
	$stmt->execute();
	$data['term'] = $_GET['q'];
	$pattern = "/(".$data['term'].")/i";
	$replace = "<span class='label label-info'>$1</span>";
	foreach ($stmt as $r) {
		$r['title'] = preg_replace($pattern, $replace, $r['title']);
		$data['results'][] = $r;
		$k++;
	}
	$data['count'] = $k;

	$t = preg_replace('/\.php$/', '.tmpl', __FILE__);
	echo gen_template($t, $data);
}
else {
	// http://norm.al/2009/04/14/list-of-english-stop-words/
	$stopwords = array("a", "about", "above", "above", "across", 
		"after", "afterwards", "again", "against", "all", 
		"almost", "alone", "along", "already", "also","although",
		"always","am","among", "amongst", "amoungst", "amount",  
		"an", "and", "another", "any","anyhow","anyone","anything",
		"anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "using", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
	if (isset($_GET['count']))
		$count = $_GET['count'];
	else
		$count = 75;
	// get all titles, accumulate the words and show the top 100
	$tags = array();
	$stmt = $dbh->query("SELECT title, urn from etd;");
	// $stmt->setFetchMode(PDO::FETCH_ASSOC);
	foreach ($stmt as $record) {
		$tok = strtok($record['title'], " \n\t");
		while ($tok !== false) {
			$tok = strtolower($tok);
			if ((strlen($tok) > 3) && (!in_array($tok, $stopwords, true))) {		// keep words of more than 3 characters
				if (isset($tags[$tok])) {
					$tags[$tok]++;
				}
				else {
					$tags[$tok] = 1;
				}
			}
			$tok = strtok(" \n\t");		// get the next one
		}
	}

	asort($tags);
	$s = count($tags);
	$data = array();
	$data['count'] = $count;
	$keys = array_keys($tags);
	$cutoff = $tags[$keys[$s - $data['count']]];
	ksort($tags);
	foreach($tags as $term => $count) {
		if ($count >= $cutoff)
			$data['tags'][] = array('tag'=>$term, 'count'=>$count);
	}
	// for ($i = $s - $data['count']; $i < $s; $i++)
	// 	$data['tags'][] = array('tag'=>$keys[$i], 'count'=>$tags[$keys[$i]]);

	// sort keys alphabetically so the cloud looks random

	$t = preg_replace('/\.php$/', '.tmpl', __FILE__);
	echo gen_template($t, $data);
}

?>
