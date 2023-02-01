<?php
libxml_use_internal_errors(true);
$url = "https://www.legabasket.it/lba/squadre/2022/1533/ea7-emporio-armani-milano";
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
$out = []; 

foreach ($titles as $i => $node) {
    if(!removeWhiteSpace($node->nodeValue) == '')
        $keys[] = $node->nodeValue;
}

$j = 0;
$n = 0;
foreach ($link as $i => $node) {
    if($j < 2){
        if(is_numeric(removeWhiteSpace($node->nodeValue)) || !removeWhiteSpace($node->nodeValue) == ''){
            $out[$keys[0]] = (!isset($out[$keys[0]])) ? removeWhiteSpace($node->nodeValue) : $out[$keys[0]].' '. removeWhiteSpace($node->nodeValue);
            $j++;
        }
    }else{
        if(is_numeric(removeWhiteSpace($node->nodeValue)) || !removeWhiteSpace($node->nodeValue) == ''){
            if($n%8 == 7 && is_numeric($node->nodeValue)){
                $values[] = '-';
                $values[] = removeWhiteSpace($node->nodeValue); 
                $n+=2;
            }else{
                $values[] = removeWhiteSpace($node->nodeValue);
                $n++;
            }
        }
    }
}

$missing = 0;
for($i=0; $i<(sizeof($values)/8); $i++){    
    $index = $i*8;
    echo $values[$index] . " ";
    if(!is_numeric($values[$index])) {
        $index -= $missing+1;
        $missing++;
    }
    echo $values[$index] . "|";
    $out[$keys[1]][$i] = array(
        $keys[2] => (!is_numeric($values[$index]))? '-' : $values[$index],
        $keys[3] => $values[$index+1] . " " . $values[$index+2],
        $keys[4] => $values[$index+3],
        $keys[5] => $values[$index+4],
        $keys[6] => $values[$index+5],
        $keys[7] => $values[$index+6],
        $keys[8] => $values[$index+7]
    );
}

print_r($out);

function removeWhiteSpace($text){
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    return $text;
}

?>