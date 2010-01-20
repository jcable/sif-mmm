<?php
if(isset($_REQUEST["kind"]) && isset($_REQUEST["action"]))
{
	require_once("header.php");
	require 'sif.inc';
	$kind = strtoupper($_REQUEST["kind"]);
	$id = $_REQUEST["id"];
	$tab_index = $_REQUEST["tab_index"];
	$dbh=connect();
	switch($_REQUEST["action"])
	{
	case "add":
		header("location: manageedges.php?kind=".$_REQUEST["kind"]);
		$stmt=$dbh->prepare("INSERT INTO edge (id,kind,tab_index) values (?,?,?)");
		$stmt->execute(array($id,$kind,$tab_index));
		break;
	case "delete":
		header("location: manageedges.php?kind=".$_REQUEST["kind"]);
		$stmt=$dbh->prepare("DELETE FROM edge WHERE id=? AND kind=?");
		$stmt->execute(array($id,$kind));
		break;
	case "modify":
		header("location: manageedges.php?kind=".$_REQUEST["kind"]);
		$sql="UPDATE edge SET ";
		$args=array();
		$p = array();
		foreach($_REQUEST as $key => $val)
		{
			switch($key)
			{
			case "action":
				break;
			case "kind":
			case "id":
				break;
			default:
				$p[] = "$key=?";
				$args[] = $val;
				break;
			}
		}
		$sql .= implode(",", $p)." WHERE id=? AND kind=?";
		$args[] = $_REQUEST["id"];
		$args[] = $_REQUEST["kind"];
		print $sql;
		$stmt=$dbh->prepare($sql);
		$stmt->execute($args);
		break;
	case "edit":
		sif_header($page, "main.css");
		$stmt=$dbh->prepare("SELECT * FROM edge WHERE id=? AND kind=?");
		$stmt->execute(array($id,$kind));
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$fields=$stmt->columnCount();
		print "<table><tr><form method=\"post\" action=\"editedge.php\">";
		print "\n<input type=\"hidden\" name=\"action\" value=\"modify\">";
		for($i=0; $i<$fields; $i++)
		{
			$meta = $stmt->getColumnMeta($i);
			$name = $meta["name"];
			$len = int(intval($meta["len"])/10);
			if($len==0) $len==6;
			//print "{$meta["len"]} $len<br>";
			switch($name)
			{
			case "id";
			case "kind";
				print "\n<input type=\"hidden\" name=\"$name\" value=\"{$row[$name]}\">";
				break;
			default:
				print "<tr><td>".ucwords($name).":</td>";
				print "<td><input type=\"text\" size=\"{$len}\" name=\"{$name}\" value=\"{$row[$name]}\"></td></tr>";
			}
		}
		print "</form></table>";
		print "SIF Project - Configure Details for {$_REQUEST["kind"]} '{$row["id"]}'</h3>";
		sif_footer();
		break;
	}
}
else
{
	echo "Error - Missing parameters - please go back and check the data.";
}
?>