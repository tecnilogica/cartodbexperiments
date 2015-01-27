<?php

$totalCounter = 0;
$spanishCounter = 0;

$x = new XMLReader;
$x->open($argv[1]);

$fh = fopen ( $argv[1] . '.clean' , 'w');

fwrite($fh, "<releases>\n");

while ($x->read() && $x->name !== 'release');

while ($x->name === 'release') {

  $totalCounter++;

  $xml = simplexml_load_string($x->readOuterXML());

  if ($xml->country == 'Spain' &&
      isset($xml->released) ){

    $spanishCounter++;

    unset($xml->images);
    unset($xml->labels);
    unset($xml->master_id);
    unset($xml->formats);
    unset($xml->genres);
    unset($xml->styles);
    unset($xml->data_quality);
    unset($xml->identifiers);
    unset($xml->notes);
    unset($xml->companies);
    unset($xml->extraartists);
    unset($xml->videos);

    $xmlString = $xml->asXML();

    $xmlString = preg_replace ( array(
                                  '/<position\/>/', 
                                  '/<duration\/>/', 
                                  '/<anv\/>/', 
                                  '/<join\/>/', 
                                  '/<role\/>/',
                                  '/<tracks\/>/',
                                  '/<extraartists\/>/',
                                  '/<id>[0-9]*<\/id>/',
                                  '/<role>[^<]*<\/role>/',
                                  '/<position>[^<]*<\/position>/',
                                  '/<duration>[^<]*<\/duration>/',
                                  '/<anv>[^<]*<\/anv>/',
                                  '/<extraartists>(<artist>(<name>[^<]*<\/name>(<tracks>[^<]*<\/tracks>)*)<\/artist>)*<\/extraartists>/',
                                  '/<country>.*<\/country>/',
                                  '/<\?.*>/'
                                ),
                                '' , 
                                $xmlString);

    $xmlString = preg_replace( array(
                                  '/(<track><title>[^<]*<\/title>)<artists>(<artist>(<name>[^<]*<\/name>)(<join>[^<]*<\/join>)*<\/artist>)*<\/artists>/',
                                  '/<\/name><join>([^<]*)<\/join>/',
                                  '/<\/name><\/artist><artist><name>/',
                                  '/<artists><artist><name>([^<]*)<\/name><\/artist><\/artists>/',
                                  '/<track><title>([^<]*)<\/title><\/track>/',
                                  '/<release (id="[0-9]*") status="[a-zA-Z]*">/',
                                  '/<released>([0-9]*)[^<]*<\/released>/'
                               ), 
                               array(
                                  '$1',
                                  ' $1 </name>',
                                  '',
                                  '<artist>$1</artist>',
                                  '<track>$1</track>',
                                  '<release $1>',
                                  '<released>$1</released>'
                               ), $xmlString);

    $xmlString = trim($xmlString);
    if (strstr($xmlString, '&')!==false) {
      xml_entity_decode($xmlString);
    }
    $xmlString .= "\n";

    fwrite($fh, $xmlString);

    if ($spanishCounter % 1000 == 0) {
      echo "$totalCounter / $spanishCounter\n";
      //exit;
    }

  }

  $x->next('release');

}

fwrite($fh, "</releases>\n");
fclose($fh);

$x->close();

//From
//http://stackoverflow.com/questions/18039765/php-not-have-a-function-for-xml-safe-entity-decode-not-have-some-xml-entity-dec
function xml_entity_decode($s) {
  static $XENTITIES = array('&amp;','&gt;','&lt;');
  static $XSAFENTITIES = array('#_x_amp#;','#_x_gt#;','#_x_lt#;');
  $s = str_replace($XENTITIES,$XSAFENTITIES,$s); 
  $s = html_entity_decode($s, ENT_HTML5|ENT_NOQUOTES, 'UTF-8'); // PHP 5.3+
  $s = str_replace($XSAFENTITIES,$XENTITIES,$s);
  return $s;
}  