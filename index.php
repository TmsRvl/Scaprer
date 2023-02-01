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

foreach ($xpath->query("//li[@class='page-item active']//a//text()") as $key => $value) {
    $output['Championship_day'] = format($value->nodeValue);
    break;
}

for($i = 0; $i < (sizeof($nodes)/3); $i++){
    $output["Games"][$i] = array(
        $titles[0]->nodeValue => format($nodes[$i*3]->nodeValue),
        $titles[1]->nodeValue => format($nodes[($i*3)+1]->nodeValue),
        $titles[2]->nodeValue => format($nodes[($i*3)+2]->nodeValue),
        $titles[4]->nodeValue => format($info[$i*4]->nodeValue.', '.$info[($i*4)+1]->nodeValue),
        $titles[5]->nodeValue => format($info[($i*4)+2]->nodeValue.', '.$info[($i*4)+3]->nodeValue)
    );
    $output["Games"][$i]["info"] = array(
        $titles[0]->nodeValue => $squads[format($nodes[$i*3]->nodeValue)],
        $titles[2]->nodeValue => $squads[format($nodes[($i*3)+2]->nodeValue)]
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
        $out[format($titles[$i]->nodeValue)] = $link[$i]->nodeValue;
    }
    return $out;
}

?>