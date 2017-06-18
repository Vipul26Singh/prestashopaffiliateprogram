<?php

include("./amazonAPI.php");

$amazon = new amazonAPI(
	"AKIAJQVUBYVW765CZUPQ", 
	"sVey9u7Mq2sDQP2JpnDcJhZ6fYGAGdVw8IB9QCvJ",
	"vipulsinghtha-20"
	);

	$item = "";
	$listing = $amazon->searchProducts($item, "allascascas", 1000);

	echo $item.": total products - ".count($listing)."<br />";
	
	foreach ($listing as $i)
	{
		print_r($i);
		break;
		echo "\n\n";
	}
	

?>
