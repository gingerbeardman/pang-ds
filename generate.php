<PRE><?php
ini_set('memory_limit','256M');
ini_set('display_errors', 'On');
error_reporting(E_ALL || E_STRICT);

require 'vendor/autoload.php';

$dir = glob("./rom_dec/*.bin");

$header = array();
$nscr = array();
$map = array();
$stages = array();

$time_start = microtime(true);

// for each file
foreach ($dir as $long) {
  $short = basename($long);
  $s = null;

  // only stage files
  if (preg_match("~^stage((\d{3})([t|b]))_nsc_LZ\.bin~", $short, $matches)) {

    $sw = $matches[1];
    $s = $matches[2];
    $w = $matches[3];

    // if (intval($s) >= 10) break;

    $files[$sw] = $long;
    $stages[$s] = null;
  }
}

$time_end = microtime(true);
// echo "glob: ". ($time_end-$time_start) .PHP_EOL;

$ds = array('t' => 0, 'b' => 24);

$time_start = microtime(true);

// 000, 001
foreach ($stages as $stage => $junk) {
  $screen = null;
  // b,t
  foreach ($ds as $topbtm => $offset) {
    $filepath = $files["{$stage}{$topbtm}"];

    $i = intval($stage);

    // echo $filepath .PHP_EOL;

    $fileData = file_get_contents($filepath);
    $br = new PhpBinaryReader\BinaryReader($fileData, PhpBinaryReader\Endian::ENDIAN_LITTLE);

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
      $screen[$row][0] = $br->ReadUInt8();
      $screen[$row][1] = $br->ReadUInt8();
    }

  }

  $map[intval($i)] = $screen;
}
$time_end = microtime(true);
// echo "binary: ". ($time_end-$time_start) .PHP_EOL;

$handle = fopen("js/stages.js", "w");
fwrite($handle, "var stageData = [];\n");
foreach ($map as $i => $data) {
  $json = json_encode($map[$i]);
  fwrite($handle, "stageData[$i] = $json;\n");
}
fclose($handle);

echo "wrote $i lines" .PHP_EOL;

?>
