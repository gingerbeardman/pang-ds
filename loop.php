<?php

// for ($i=0; $i < 768; $i++) {
//   $r = intval($i / 32);
//   $c = intval($i % 32);
//   echo "$i $r $c<br>";
// }

$tw = 8;
$th = 8;
$imgw = 128;

for ($data=0; $data < 240; $data++) {
  $sx = ($data * $tw) % $imgw;
  $sy = intval(($data * $tw) / $imgw) * $th;
  echo "$data=$sx,$sy<br>";
}
