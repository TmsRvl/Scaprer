<?php
/* Use internal libxml errors -- turn on in production, off for debugging */
libxml_use_internal_errors(true);

if(isset($_REQUEST['d'])){
    $url = "https://www.legabasket.it/lba/6/calendario?d=".$_REQUEST['d'];    
}else{
    $url = "https://www.legabasket.it/lba/6/calendario?";
}

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

$data = [];
foreach ($nodes as $key => $value) {
    if(!str_contains(format($value->nodeValue), 'd.t.s'))
        $data[] = format($value->nodeValue);
}

for($i = 0; $i < (sizeof($data)/3); $i++){
    $output["Games"][$i] = array(
        $titles[0]->nodeValue => $data[$i*3],
        $titles[1]->nodeValue => $data[($i*3)+1],
        $titles[2]->nodeValue => $data[($i*3)+2],
        $titles[4]->nodeValue => format($info[$i*4]->nodeValue.', '.$info[($i*4)+1]->nodeValue),
        $titles[5]->nodeValue => format($info[($i*4)+2]->nodeValue.', '.$info[($i*4)+3]->nodeValue)
    );
    $output["Games"][$i]["info"] = array(
        $titles[0]->nodeValue => $squads[$data[$i*3]],
        $titles[2]->nodeValue => $squads[$data[($i*3)+2]]
    );
}

header("Access-Control-Allow-Origin: *");
echo json_encode($output);


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