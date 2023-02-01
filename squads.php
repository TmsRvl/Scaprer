<?php
libxml_use_internal_errors(true);
$url = "https://www.legabasket.it/lba/3/squadre";

$dom = new DomDocument;
$dom->loadHTMLFile($url);

$xpath = new DomXPath($dom);

//ritorna le squadre
$titles = $xpath->query("//div[@class='row']//div//div//div//a//h5//text()");
//ritorna i link di dettaglio
$link = $xpath->query("//a[@rel='bookmark']//@href");

$out;
for($i = 0; $i < sizeof($titles); $i++){
    $out[format($titles[$i]->nodeValue)] = "https://www.legabasket.it".$link[$i]->nodeValue;
}

header("Content-Type: application/json");
echo json_encode($out);
exit();

function format($str){
    $str = preg_replace('/\s\s+/', ' ', $str);
    $str = trim($str, " ");
    return str_replace(array("\n"), "", $str);
}

?>