<?php
// Retrieve the high scores data from the game's

include_once('../Database.php');
$db = new Database();
$db->connectDB();

$highScores = [];

$sql = "SELECT @rank := @rank + 1 AS rank, username, points, data
        FROM rankings, (SELECT @rank := 0) r
        ORDER BY points DESC";

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
    $xml .= '    <title>' . $score['username'] . ' - ' . $score['points'] . '</title>' . PHP_EOL;
    $xml .= '    <description>' . $score['username'] . ' achieved a score of ' . $score['points'] . '</description>' . PHP_EOL;
    $xml .= '  </item>' . PHP_EOL;
}

$xml .= '</channel>' . PHP_EOL;
$xml .= '</rss>' . PHP_EOL;

// Output the generated XML content
//echo $xml;
$file = 'feed.xml';

file_put_contents($file, $xml);

header('Location: /feed/feed.xml');


