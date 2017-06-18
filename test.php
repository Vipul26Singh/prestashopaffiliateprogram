<?php

include("./affiliate/amazonAPI.php");


class test{

function initiate(){
$amazon = new amazonAPI(
	"AKIAJQVUBYVW765CZUPQ", 
	"sVey9u7Mq2sDQP2JpnDcJhZ6fYGAGdVw8IB9QCvJ",
	"vipulsinghtha-20"
	);

	$item = "a";
	$listing = $amazon->searchProducts($item, "All", 10);

	echo $item.": total products - ".count($listing)."<br />";
	
	foreach ($listing as $i)
	{
		print_r($i);
		break;
		echo "\n\n";
	}
}
	
}

$test_i = new test();
$test_i->initiate();
?>
