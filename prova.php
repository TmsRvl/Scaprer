<?php
libxml_use_internal_errors(true);
$url = "https://www.legabasket.it/lba/squadre/2022/1538/banco-di-sardegna-sassari";
$dom = new DomDocument;
$dom->loadHTMLFile($url);

$xpath = new DomXPath($dom);

//ritorna le squadre
$titles = $xpath->query("//div[@id='team']//th//text()");
//ritorna i link di dettaglio
$link = $xpath->query("//div[@id='team']//td//text()");
header("Content-type: text/plain");

$out;

foreach ($titles as $i => $node) {
    echo "Node($i): ", $node->nodeValue, "\n";
}

foreach ($link as $i => $node) {
    echo "Node($i): ", $node->nodeValue, "\n";
}

?>