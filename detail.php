<?php
libxml_use_internal_errors(true);
header("Access-Control-Allow-Origin:*");
$url = "https://www.legabasket.it";

if(isset($_REQUEST['d'])){
    $url .= $_REQUEST['d'];
}else{
    die('Fatal error');
}

$dom = new DomDocument;
$dom->loadHTMLFile($url);

$xpath = new DomXPath($dom);

//ritorna i titoli
$titles = $xpath->query("//div[@id='team']//th//text()");
//ritorna i dettagli dei giocatori
$link = $xpath->query("//div[@id='team']//td//text()");
//images
$imgs = $xpath->query("//div[@id='team']//td//div//div[@class='img']//@style");

$icons = [];
foreach ($imgs as $key => $value) {
    $a = $value->nodeValue; 
    $a = substr($a, strpos($a,"'")+1);
    $icons[] = substr($a, 0, strpos($a,"'")); 
}

$keys;
$values;
$out = [];
foreach ($xpath->query("//h1[@itemprop='sport']//text()") as $key => $value) {
    $out['Name'] = removeWhiteSpace($value->nodeValue);
    break;
}
foreach ($xpath->query("//div[@class='team-logo']//img//@src") as $key => $value) {
    $out['Logo'] = $value->nodeValue;
    break;
}

foreach ($titles as $i => $node) {
    if(!removeWhiteSpace($node->nodeValue) == '')
        $keys[] = $node->nodeValue;
}

$out[$keys[0]]['Displayed_icon'] = $icons[0];

$j = 0;
$n = 0;
foreach ($link as $i => $node) {
    if($j < 2){
        if(is_numeric(removeWhiteSpace($node->nodeValue)) || !removeWhiteSpace($node->nodeValue) == ''){
            $out[$keys[0]]['Name'] = (!isset($out[$keys[0]]['Name'])) ? removeWhiteSpace($node->nodeValue) : $out[$keys[0]]['Name'].' '. removeWhiteSpace($node->nodeValue);
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
    if(!is_numeric($values[$index])) {
        $index -= $missing+1;
        $missing++;
    }
    $out[$keys[1]][$i] = array(
        'Displayed_icon' => $icons [$i+1],
        $keys[2] => (!is_numeric($values[$index]))? '-' : $values[$index],
        $keys[3] => $values[$index+1] . " " . $values[$index+2],
        $keys[4] => $values[$index+3],
        $keys[5] => $values[$index+4],
        $keys[6] => $values[$index+5],
        $keys[7] => $values[$index+6],
        $keys[8] => $values[$index+7]
    );
}

header("Content-Type: application/json");
echo json_encode($out);
exit();

function removeWhiteSpace($text){
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    return $text;
}

?>