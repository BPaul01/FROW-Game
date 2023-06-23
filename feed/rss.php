<?php
// Retrieve the high scores data from the game's

include_once('../Database.php');
$db = new Database();
$db->connectDB();

$highScores = [];

$sql = "SELECT u.name, MAX(g.score) as score, g.data FROM `games` g
inner join users u on u.id=g.user_id
group BY g.user_id
order by g.score desc;";

if($result = $db->getDb()->query($sql))
    $highScores = $result->fetch_all(MYSQLI_ASSOC);

// Set the appropriate headers to indicate that it is an XML content
header('Content-Type: application/rss+xml; charset=UTF-8');

// Generate the XML content for the RSS feed
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<rss version="2.0">' . PHP_EOL;
$xml .= '<channel>' . PHP_EOL;
$xml .= '  <title>Game High Scores</title>' . PHP_EOL;
$xml .= '  <link>http://localhost/rank</link>' . PHP_EOL;
$xml .= '  <description>Latest high scores for the game</description>' . PHP_EOL;

// Iterate over each high score and generate the corresponding XML
foreach ($highScores as $score) {
    $xml .= '  <item>' . PHP_EOL;
    $xml .= '    <title>' . $score['name'] . ' - ' . $score['score'] . '</title>' . PHP_EOL;
    $xml .= '    <description>' . $score['name'] . ' achieved a score of ' . $score['score'] . '</description>' . PHP_EOL;
    $xml .= '  </item>' . PHP_EOL;
}

$xml .= '</channel>' . PHP_EOL;
$xml .= '</rss>' . PHP_EOL;

// Output the generated XML content
//echo $xml;
$file = 'feed.xml';

file_put_contents($file, $xml);

header('Location: /feed/feed.xml');


