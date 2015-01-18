#!/bin/bash

echo 'Cleaning XML... '
sed -e 's/<release id="[0-9]*" status="[a-zA-Z]*">/<release>/' \
    -e 's/<images>.*<\/images>//' \
    -e 's/<labels>.*<\/labels>//' \
    -e 's/<extraartists \/>//' \
    -e 's/<formats>.*<\/formats>//' \
    -e 's/<genres>.*<\/genres>//' \
    -e 's/<styles>.*<\/styles>//' \
    -e 's/<master_id>.*<\/master_id>//' \
    -e 's/<data_quality>.*<\/data_quality>//' \
    -e 's/<position \/>//g' \
    -e 's/<position>[^<]*<\/position>//g' \
    -e 's/<duration \/>//g' \
    -e 's/<duration>[^<]*<\/duration>//g' \
    -e 's/<identifiers>.*<\/identifiers>//' \
    -e 's/<videos>.*<\/videos>//' \
    -e 's/<companies \/>//g' \
    -e 's/<companies>.*<\/companies>//' \
    -e 's/<anv \/>//g' \
    -e 's/<anv>[^<]*<\/anv>//g' \
    -e 's/<join \/>//g' \
    -e 's/<role \/>//g' \
    -e 's/<role>[^<]*<\/role>//g' \
    -e 's/<tracks \/>//g' \
    -e 's/<id>[0-9]*<\/id>//g' \
    -e 's/<extraartists>\(<artist>\(<name>[^<]*<\/name>\)<\/artist>\)*<\/extraartists>//g' \
    -e 's/\(<track><title>[^<]*<\/title>\)<artists>\(<artist>\(<name>[^<]*<\/name>\)\(<join>[^<]*<\/join>\)*<\/artist>\)*<\/artists>/\1/g' \
    -e 's/<\/name><join>\([^<]*\)<\/join>/ \1 <\/name>/g' \
    -e 's/<\/name><\/artist><artist><name>//g' \
    -e 's/<artists><artist><name>\([^<]*\)<\/name><\/artist><\/artists>/<artist>\1<\/artist>/' \
    -e 's/<track><title>\([^<]*\)<\/title><\/track>/<track>\1<\/track>/g' \
    -e 's/<released>\([0-9]*\)[^<]*<\/released>/<released>\1<\/released>/g' \
    <$1 >$1.temp1

echo 'Removing foreign and dateless releases...'
grep '<country>Spain<\/country>' < $1.temp1 | grep '<released>' >$1.temp2