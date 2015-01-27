<?php

$wordList=array(
    array("id"=>1,"name"=>"álava","alias"=>"vitoria"),
    array("id"=>2,"name"=>"albacete"),
    array("id"=>3,"name"=>"alicante"),
    array("id"=>4,"name"=>"almería"),
    array("id"=>33,"name"=>"asturias","alias"=>"oviedo"),
    array("id"=>5,"name"=>"ávila"),
    array("id"=>6,"name"=>"badajoz"),
    array("id"=>7,"name"=>"baleares","alias"=>"palma"),
    array("id"=>8,"name"=>"barcelona"),
    array("id"=>9,"name"=>"burgos"),
    array("id"=>10,"name"=>"cáceres"),
    array("id"=>11,"name"=>"cádiz"),
    array("id"=>39,"name"=>"cantabria","alias"=>"santander"),
    array("id"=>12,"name"=>"castellón"),
    array("id"=>51,"name"=>"ceuta"),
    array("id"=>13,"name"=>"ciudad real"),
    array("id"=>14,"name"=>"córdoba"),
    array("id"=>15,"name"=>"coruña"),
    array("id"=>16,"name"=>"cuenca"),
    array("id"=>17,"name"=>"gerona"),
    array("id"=>18,"name"=>"granada"),
    array("id"=>19,"name"=>"guadalajara"),
    array("id"=>20,"name"=>"guipúzcoa","alias"=>"san sebastián"),
    array("id"=>21,"name"=>"huelva"),
    array("id"=>22,"name"=>"huesca"),
    array("id"=>23,"name"=>"jaén"),
    array("id"=>24,"name"=>"león"),
    array("id"=>25,"name"=>"lérida"),
    array("id"=>27,"name"=>"lugo"),
    array("id"=>28,"name"=>"madrid"),
    array("id"=>29,"name"=>"málaga"),
    array("id"=>52,"name"=>"melilla"),
    array("id"=>30,"name"=>"murcia"),
    array("id"=>31,"name"=>"navarra","alias"=>"pamplona"),
    array("id"=>32,"name"=>"orense"),
    array("id"=>34,"name"=>"palencia"),
    array("id"=>35,"name"=>"palmas"),
    array("id"=>36,"name"=>"pontevedra"),
    array("id"=>26,"name"=>"rioja","alias"=>"logroño"),
    array("id"=>37,"name"=>"salamanca"),
    array("id"=>40,"name"=>"segovia"),
    array("id"=>41,"name"=>"sevilla"),
    array("id"=>42,"name"=>"soria"),
    array("id"=>43,"name"=>"tarragona"),
    array("id"=>38,"name"=>"tenerife"),
    array("id"=>44,"name"=>"teruel"),
    array("id"=>45,"name"=>"toledo"),
    array("id"=>46,"name"=>"valencia"),
    array("id"=>47,"name"=>"valladolid"),
    array("id"=>48,"name"=>"vizcaya","alias"=>"bilbao"),
    array("id"=>49,"name"=>"zamora"),
    array("id"=>50,"name"=>"zaragoza")
);

//$wordList="enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre";
//$wordList="lunes,martes,miércoles,jueves,viernes,sábado,domingo";
//$wordList="primavera,verano,otoño,invierno";


$totalReleasesCounter=0;
$spanishReleasesCounter=0;
$totalTrackCounter = 0;
$validTrackCounter=0;

$dataArray = array();
foreach ($wordList as $wordData) {
  $dataArray["words"][$wordData["name"]]=0;
}

$x = new XMLReader;
$x->open($argv[1]);

while ($x->read() && $x->name !== 'release');

while ($x->name === 'release') {

  $xml = simplexml_load_string($x->readOuterXML());

  $year = $xml->released->__toString();

  $validYear = false;

  if (isset($dataArray[$year]["totalReleases"])) {

    $dataArray[$year]["totalReleases"]++;
    $validYear = true;

  } else {

    if(is_numeric($year) && $year > 1900 && $year < 2100){
      $dataArray[$year]["totalReleases"]=1;
      $dataArray[$year]["totalTracks"]=0;
      $dataArray[$year]["validTracks"]=0;
      foreach ($wordList as $wordData) {
        $dataArray[$year]["data"][$wordData["name"]]=0;
      }
      $validYear = true;
    }

  }

  if ($validYear) {
    
    $tracks = $xml->tracklist->children();
    foreach ($tracks as $trackTitle) {

      $dataArray[$year]["totalTracks"]++;

      foreach ($wordList as $wordData) {

        if (preg_match("/\b".$wordData["name"]."\b/i", $trackTitle) ||
            (isset($wordData["alias"]) && preg_match("/\b".$wordData["alias"]."\b/i", $trackTitle))){
          $dataArray[$year]["data"][$wordData["name"]]++;
          $dataArray["words"][$wordData["name"]]++;
          $dataArray[$year]["validTracks"]++;
          #echo $trackTitle . " (" . $xml->artist . ", $year)\n";
          $validTrackCounter++;
        }

      }

      $totalTrackCounter++;

    }

    $spanishReleasesCounter++;

  }

  $totalReleasesCounter++;
  $x->next('release');

}

$x->close();
ksort($dataArray);

//echo "$spanishReleasesCounter releases from a total of $totalReleasesCounter\n";
//echo "$validTrackCounter coincidences found from a total of $totalTrackCounter tracks\n\n\n";
//print_r($dataArray);

echo "\"cod_prov\",\"year\",\"tracks\"\n";
foreach ($dataArray as $key => $value) {
  if ($key != "words") {
    foreach ($value["data"] as $provincia => $counter) {
      foreach ($wordList as $wordData) {
        if($wordData["name"]==$provincia) {
          echo $wordData["id"].",$key,$counter\n";
          break;
        }
      }
    }
  }
}

