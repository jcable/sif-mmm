<?php

$dbh = new PDO( 'mysql:host=localhost;dbname=sif', 'sif', 'sif',
		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
); 

$sql = <<<EOT
SELECT output,if(event_type='OFF','OFF',ifnull(input,'OFF')) as input 
FROM (select id AS output FROM edge) AS a
LEFT JOIN
(
SELECT output,input,event_type
FROM
(SELECT max( event_time ) AS event_time, output FROM as_run GROUP BY output) AS m
JOIN as_run AS r
USING ( event_time, output )
) AS b USING (output)
EOT;

$stmt = $dbh->prepare($sql);
$stmt->execute();

print json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
