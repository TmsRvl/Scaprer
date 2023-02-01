<?php
libxml_use_internal_errors(true);
$url = "https://www.legabasket.it/lba/squadre/2022/1538/banco-di-sardegna-sassari";
$dom = new DomDocument;
$dom->loadHTMLFile($url);

$xpath = new DomXPath($dom);

//ritorna i titoli
$titles = $xpath->query("//div[@id='team']//th//text()");
//ritorna i dettagli dei giocatori
$link = $xpath->query("//div[@id='team']//td//text()");
header("Content-type: text/plain");

$keys;
$values;
$out; 

foreach ($titles as $i => $node) {
    if(!removeWhiteSpace($node->nodeValue) == '')
        $keys[] = $node->nodeValue;
}

$j = 0;
foreach ($link as $i => $node) {
    if($j < 2){
        if(is_numeric(removeWhiteSpace($node->nodeValue)) || !removeWhiteSpace($node->nodeValue) == ''){
            $out[$keys[0]] .= removeWhiteSpace($node->nodeValue) . " ";
            $j++;
        }
    }else{
        if(is_numeric(removeWhiteSpace($node->nodeValue)) || !removeWhiteSpace($node->nodeValue) == '')
            $values[] = removeWhiteSpace($node->nodeValue);
    }
}

for($i=0; $i<(sizeof($values)/8); $i++){
    $out[$keys[1]][$i] = array(
        $keys[2] => $values[($i*8)],
        $keys[3] => $values[($i*8)+1] . " " . $values[($i*8)+2],
        $keys[4] => $values[($i*8)+3],
        $keys[5] => $values[($i*8)+4],
        $keys[6] => $values[($i*8)+5],
        $keys[7] => $values[($i*8)+6],
        $keys[8] => $values[($i*8)+7]
    );
}


function removeWhiteSpace($text){
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    return $text;
}

?>