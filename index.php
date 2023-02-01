<?php
$url = "https://www.legabasket.it/lba/6/calendario?";
/* Use internal libxml errors -- turn on in production, off for debugging */
libxml_use_internal_errors(true);
$dom = new DomDocument;
$dom->loadHTMLFile($url);

$xpath = new DomXPath($dom);

//ritorna i titoli
$titles = $xpath->query("//th//text()");
//ritorna squadre e punteggi
$nodes = $xpath->query("//td//a//text()");
//ritorna altre info
$info = $xpath->query("//td[@class='small']//text()");

$output;
$squads = getSquadInfo();

for($i = 0; $i < (sizeof($nodes)/3); $i++){
    $output["games"][$i] = array(
        $titles[0]->nodeValue => format($nodes[$i*3]->nodeValue),
        $titles[1]->nodeValue => format($nodes[($i*3)+1]->nodeValue),
        $titles[2]->nodeValue => format($nodes[($i*3)+2]->nodeValue),
        $titles[4]->nodeValue => format($info[$i*4]->nodeValue.', '.$info[($i*4)+1]->nodeValue),
        $titles[5]->nodeValue => format($info[($i*4)+2]->nodeValue.', '.$info[($i*4)+3]->nodeValue)
    );
    $output["games"][$i]["info"] = array(
        $titles[0]->nodeValue => getSquadRoster($squads[format($nodes[$i*3]->nodeValue)]),
        $titles[2]->nodeValue => getSquadRoster($squads[format($nodes[($i*3)+2]->nodeValue)])
    );
}

header("Content-Type: application/json");
echo json_encode($output);
exit();

function format($str){
    $str = preg_replace('/\s\s+/', ' ', $str);
    $str = trim($str, " ");
    return str_replace(array("\n"), "", $str);
}

function getSquadInfo(){
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
        $out[format($titles[$i]->nodeValue)] = "https://www.legabasket.it" . $link[$i]->nodeValue;
    }
    return $out;
}

function getSquadRoster($url){
    $dom = new DomDocument;
    $dom->loadHTMLFile($url);

    $xpath = new DomXPath($dom);

    //ritorna i titoli
    $titles = $xpath->query("//div[@id='team']//th//text()");
    //ritorna i dettagli dei giocatori
    $link = $xpath->query("//div[@id='team']//td//text()");

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
       if(!is_numeric($values[$index])) {
           $index-=$missing+1;
           $missing++;
       }
       $out[$keys[1]][$i] = array(
           $keys[2] => (!is_numeric($values[($i*8)]))? '-' : $values[($i*8)],
           $keys[3] => $values[$index+1] . " " . $values[$index+2],
           $keys[4] => $values[$index+3],
           $keys[5] => $values[$index+4],
          $keys[6] => $values[$index+5],
          $keys[7] => $values[$index+6],
          $keys[8] => $values[$index+7]
      );
    }

    return $out;
}

function removeWhiteSpace($text){
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    return $text;
}
?>