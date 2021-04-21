<PRE><?php
ini_set('memory_limit','256M');
ini_set('display_errors', 'On');
error_reporting(E_ALL || E_STRICT);

include "phpio.php";

$dir = glob("./rom_dec/*.bin");

$header = array();
$nscr = array();
$map = array();

// for each file
foreach ($dir as $long) {
  $short = basename($long);

  // only stage files
  if (preg_match("~^stage((\d{3})([t|b]))_nsc_LZ\.bin~", $short, $matches)) {

    $both = $matches[1];
    $stage = $matches[2];
    $topbtm = $matches[3];

    // if (intval($stage) > 10) break;

    $files[$both] = $long;
    $stages[$stage] = array();
  }
}

$ds = array('t' => 0, 'b' => 24);

// 000, 001
foreach ($stages as $stage => $junk) {
  $screen = null;
  // b,t
  foreach ($ds as $topbtm => $offset) {
    $filepath = $files["{$stage}{$topbtm}"];

    $index = intval($stage);

    $br = new Reader($filepath);
    $br->Open();

    // Generic header
    $header[id] = $br->ReadString(4);
    $header[endianess] = $br->ReadUInt16();
    $header[constant] = $br->ReadUInt16();
    $header[file_size] = $br->ReadUInt32();
    $header[header_size] = $br->ReadUInt16();
    $header[nSection] = $br->ReadUInt16();
    $header = null;

    // Read section
    $nscr[id] = $br->ReadString(4);
    $nscr[section_size] = $br->ReadUInt32();
    $nscr[width] = $br->ReadUInt16();
    $nscr[height] = $br->ReadUInt16();
    $nscr[padding] = $br->ReadUInt32();
    $nscr[data_size] = $br->ReadUInt32();
    $limit = $nscr[data_size] / 2;
    $nscr = null;

    $zero = $offset * 32;
    for ($row=$zero; $row < $zero+$limit; $row++) {
      $screen[$row][tile] = $br->ReadUInt8();
      $screen[$row][flip] = $br->ReadUInt8();
    }

    $br->Close();
  }

  $map[$stage] = $screen;
}

foreach ($map as $key => $val) {
  echo $key .PHP_EOL;
}

?>
<!-- <script type="text/javascript"> -->
<?php for ($i=0; $i < count($map); $i++): ?>
var mapData[<?php echo $i; ?>] =<?php echo json_encode($map[$i]); ?>;
<?php endfor; ?>
<!-- </script> -->
</PRE>
