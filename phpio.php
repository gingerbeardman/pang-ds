<?php

// PHPIO: [PHP In/Out Classes]
// Version: 1.0.6.0
// Created by: JizzaBeez (jizzabeez@hotmail.com)
// Released: 07/13/2011
// Updated: 05/10/2012

class Reader

{
	private $filepath = "";
	private $openfile = "";
	private $filemode = "";
	private $position = 0;
	private $length = 0;
	public

	function __construct($filepath, $filemode = "r", $position = 0)
	{
		if (file_exists($filepath)) {
			$this->filepath = $filepath;
			$this->filemode = $filemode;
			$this->position = $position;
			$this->length = filesize($this->filepath);
		}
		else {
			throw new Exception("file does not exist");
		}
	}

	function FileName()
	{
		return basename($this->filepath);
	}

	function FileMode($filemode)
	{
		if ($filemode != null) {
			$this->filemode = $filemode;
		}

		return $this->filemode;
	}

	function Length()
	{
		clearstatcache();
		$this->length = filesize($this->filepath);
		return $this->length;
	}

	function Position($position)
	{
		if ($position != null) {
			$this->position = $position;
		}

		return $this->position;
	}

	function Open()
	{
		$this->openfile = fopen($this->filepath, $this->filemode) or exit("could not open file");
		return $this->openfile;
	}

	function Close()
	{
		return fclose($this->openfile);
	}

	function ReadByte()
	{
		fseek($this->openfile, $this->position);
		$data = strtoupper(bin2hex(fread($this->openfile, 1)));
		$this->position = ftell($this->openfile);
		return hexdec($data);
	}

	function ReadBytes($length)
	{
		fseek($this->openfile, $this->position);
		$data = strtoupper(bin2hex(fread($this->openfile, $length)));
		$bytes = array();
		$x = 0;
		for ($i = 0; $i < strlen($data); $i+= 2) {
			$bytes[$x] = hexdec(substr($data, $i, 1) . substr($data, $i + 1, 1));
			$x+= 1;
		}

		$this->position = ftell($this->openfile);
		return $bytes;
	}

	function ReadHexString($length)
	{
		fseek($this->openfile, $this->position);
		$data = strtoupper(bin2hex(fread($this->openfile, $length)));
		$this->position = ftell($this->openfile);
		return $data;
	}

	function ReadChar()
	{
		fseek($this->openfile, $this->position);
		$data = fread($this->openfile, 1);
		$this->position = ftell($this->openfile);
		return $data;
	}

	function ReadString($length)
	{
		fseek($this->openfile, $this->position);
		$data = fread($this->openfile, $length);
		$this->position = ftell($this->openfile);
		return $data;
	}

	function ReadUnicodeString($length)
	{
		fseek($this->openfile, $this->position);
		$buffer = fread($this->openfile, $length * 2);
		$data = str_replace(chr(0x0) , "", $buffer);
		$this->position = ftell($this->openfile);
		return $data;
	}

	function ReadInt8()
	{
		$conv = new Conversions;
		$bytes = $this->ReadByte();
		return $conv->ByteToInt8($bytes);
	}

	function ReadInt16($endian = 1)
	{
		$conv = new Conversions;
		$bytes = $this->ReadBytes(2);
		if ($endian == 1) {
			$bytes = array_reverse($bytes);
		}

		return $conv->BytesToInt16($bytes);
	}

	function ReadInt32($endian = 1)
	{
		$conv = new Conversions;
		$bytes = $this->ReadBytes(4);
		if ($endian == 1) {
			$bytes = array_reverse($bytes);
		}

		return $conv->BytesToInt32($bytes);
	}

	function ReadUInt8()
	{
		$conv = new Conversions;
		$bytes = $this->ReadByte();
		return $conv->ByteToUInt8($bytes);
	}

	function ReadUInt16($endian = 1)
	{
		$conv = new Conversions;
		$bytes = $this->ReadBytes(2);
		if ($endian == 1) {
			$bytes = array_reverse($bytes);
		}

		return $conv->BytesToUInt16($bytes);
	}

	function ReadUInt32($endian = 1)
	{
		$conv = new Conversions;
		$bytes = $this->ReadBytes(4);
		if ($endian == 1) {
			$bytes = array_reverse($bytes);
		}

		return $conv->BytesToUInt32($bytes);
	}
}

class Writer

{
	private $filepath = "";
	private $openfile = "";
	private $filemode = "";
	private $position = 0;
	private $length = 0;
	public

	function __construct($filepath, $filemode = "w", $position = 0)
	{
		$this->filepath = $filepath;
		$this->filemode = $filemode;
		$this->position = $position;
		if (file_exists($this->filepath)) {
			$this->length = filesize($this->filepath);
		}
		else {
			$this->length = 0;
		}
	}

	function FileName()
	{
		return basename($this->filepath);
	}

	function FileMode($filemode)
	{
		if ($filemode != null) {
			$this->filemode = $filemode;
		}

		return $this->filemode;
	}

	function Length()
	{
		clearstatcache();
		if (file_exists($this->filepath)) {
			$this->length = filesize($this->filepath);
		}
		else {
			$this->length = 0;
		}

		return $this->length;
	}

	function Position($position)
	{
		if ($position != null) {
			$this->position = $position;
		}

		return $this->position;
	}

	function Open()
	{
		$this->openfile = fopen($this->filepath, $this->filemode) or exit("could not open file");
		return $this->openfile;
	}

	function Close()
	{
		return fclose($this->openfile);
	}

	function WriteByte($byte)
	{
		fseek($this->openfile, $this->position);
		$data = fwrite($this->openfile, chr($byte));
		$this->position = ftell($this->openfile);
		return $data;
	}

	function WriteBytes($bytes)
	{
		fseek($this->openfile, $this->position);
		$data = 0;
		for ($i = 0; $i < count($bytes); $i++) {
			$data+= fwrite($this->openfile, chr($bytes[$i]));
		}

		$this->position = ftell($this->openfile);
		return $data;
	}

	function WriteHexString($str)
	{
		fseek($this->openfile, $this->position);
		$bytes = array();
		$x = 0;
		for ($i = 0; $i < strlen($str); $i+= 2) {
			$bytes[$x] = hexdec(substr($str, $i, 1) . substr($str, $i + 1, 1));
			$x+= 1;
		}

		$data = 0;
		for ($i = 0; $i < count($bytes); $i++) {
			$data+= fwrite($this->openfile, chr($bytes[$i]));
		}

		$this->position = ftell($this->openfile);
		return $data;
	}

	function WriteChar($char)
	{
		fseek($this->openfile, $this->position);
		$data = fwrite($this->openfile, $char);
		$this->position = ftell($this->openfile);
		return $data;
	}

	function WriteString($string)
	{
		fseek($this->openfile, $this->position);
		$data = fwrite($this->openfile, $string);
		$this->position = ftell($this->openfile);
		return $data;
	}

	function WriteUnicodeString($str, $endian = 1)
	{
		fseek($this->openfile, $this->position);
		$data = 0;
		for ($i = 0; $i < strlen($str); $i++) {
			if ($endian == 1) {
				$data+= fwrite($this->openfile, substr($str, $i, 1) . chr(0x0));
			}
			else {
				$data+= fwrite($this->openfile, chr(0x0) . substr($str, $i, 1));
			}
		}

		$this->position = ftell($this->openfile);
		return $data;
	}

	function WriteInt8($dec)
	{
		$conv = new Conversions;
		$bytes = $conv->Int8ToByte($dec);
		return $this->WriteByte($bytes);
	}

	function WriteInt16($dec, $endian = 0)
	{
		$conv = new Conversions;
		$bytes = $conv->Int16ToBytes($dec);
		if ($endian == 1) {
			$bytes = array_reverse($bytes);
		}

		return $this->WriteBytes($bytes);
	}

	function WriteInt32($dec, $endian = 0)
	{
		$conv = new Conversions;
		$bytes = $conv->Int32ToBytes($dec);
		if ($endian == 1) {
			$bytes = array_reverse($bytes);
		}

		return $this->WriteBytes($bytes);
	}

	function WriteUInt8($dec)
	{
		$conv = new Conversions;
		$bytes = $conv->UInt8ToByte($dec);
		return $this->WriteByte($bytes);
	}

	function WriteUInt16($dec, $endian = 0)
	{
		$conv = new Conversions;
		$bytes = $conv->UInt16ToBytes($dec);
		if ($endian == 1) {
			$bytes = array_reverse($bytes);
		}

		return $this->WriteBytes($bytes);
	}

	function WriteUInt32($dec, $endian = 0)
	{
		$conv = new Conversions;
		$bytes = $conv->UInt32ToBytes($dec);
		if ($endian == 1) {
			$bytes = array_reverse($bytes);
		}

		return $this->WriteBytes($bytes);
	}
}

class EndianType

{
	const BIG = 0;
	const LITTLE = 1;
	function GetValue($name)
	{
		$name = strtoupper($name);
		return constant("EndianType::" . $name);
	}

	function GetName($value)
	{
		switch ($value) {
		case 0:
			return "BIG";
			break;

		case 1:
			return "LITTLE";
			break;

		default:
			return "NOTHING";
		}
	}
}

class Conversions

{
	function UInt8ToInt8($dec)
	{
		if ($dec > 127) {
			$dec = ($dec - 256);
		}

		return $dec;
	}

	function UInt16ToInt16($dec)
	{
		if ($dec > 32767) {
			$dec = ($dec - 65536);
		}

		return $dec;
	}

	function UInt32ToInt32($dec)
	{
		if ($dec > 2147483647) {
			$dec = ($dec - 4294967296);
		}

		return $dec;
	}

	function Int8ToUInt8($dec)
	{
		if ($dec < 0) {
			$dec = ($dec + 256);
		}

		return $dec;
	}

	function Int16ToUInt16($dec)
	{
		if ($dec < 0) {
			$dec = ($dec + 65536);
		}

		return $dec;
	}

	function Int32ToUInt32($dec)
	{
		if ($dec < 0) {
			$dec = ($dec + 4294967296);
		}

		return $dec;
	}

	function BytesToAscii($bytes)
	{
		$str = "";
		for ($i = 0; $i < count($bytes); $i++) {
			$str.= chr($bytes[$i]);
		}

		return $str;
	}

	function AsciiToBytes($str)
	{
		$bytes = array();
		for ($i = 0; $i < strlen($str); $i++) {
			$bytes[$i] = ord(substr($str, $i, 1));
		}

		return $bytes;
	}

	function AsciiToHex($str)
	{
		return bin2hex($str);
	}

	function HexToAscii($str)
	{
		$data = "";
		$byte = "";
		for ($i = 0; $i < strlen($str); $i+= 2) {
			$byte = hexdec($str[$i] . $str[$i + 1]);
			$data.= chr($byte);
		}

		return $data;
	}

	function BytesToUnicode($bytes)
	{
		$str = "";
		for ($i = 0; $i < count($bytes); $i++) {
			$str.= chr($bytes[$i]);
		}

		return str_replace(chr(0x0) , "", $str);
	}

	function UnicodeToBytes($str, $endian = 1)
	{
		$bytes = array();
		for ($i = 0; $i < strlen($str); $i++) {
			if ($endian == 1) {
				array_push($bytes, ord(substr($str, $i, 1)));
				array_push($bytes, 0x0);
			}
			else {
				array_push($bytes, 0x0);
				array_push($bytes, ord(substr($str, $i, 1)));
			}
		}

		return $bytes;
	}

	function UnicodeToHex($str, $endian = 1)
	{
		$data = "";
		for ($i = 0; $i < strlen($str); $i++) {
			if ($endian == 1) {
				$data.= substr($str, $i, 1);
				$data.= chr(0x0);
			}
			else {
				$data.= chr(0x0);
				$data.= substr($str, $i, 1);
			}
		}

		return strtoupper(bin2hex($data));
	}

	function HexToUnicode($str)
	{
		$data = "";
		$byte = "";
		for ($i = 0; $i < strlen($str); $i+= 2) {
			$byte = hexdec($str[$i] . $str[$i + 1]);
			if (chr($byte) != chr(0x0)) {
				$data.= chr($byte);
			}
		}

		return $data;
	}

	function HexToBytes($str)
	{
		if (strlen($str) == 0) {
			return 0;
		}

		$bytes = array();
		$x = 0;
		for ($i = 0; $i < strlen($str); $i+= 2) {
			$bytes[$x] = hexdec(substr($str, $i, 1) . substr($str, $i + 1, 1));
			$x+= 1;
		}

		return $bytes;
	}

	function BytesToHex($bytes)
	{
		if (!is_array($bytes)) {
			return strtoupper(dechex($bytes));
		}

		$hex = '';
		for ($i = 0; $i < count($bytes); $i++) {
			$hex.= dechex($bytes[$i]);
		}

		return strtoupper($hex);
	}

	function ByteToInt8($byte)
	{
		if (is_array($byte)) {
			throw new Exception("invalid input");
		}

		$conv = new Conversions;
		return $conv->UInt8ToInt8($byte);
	}

	function BytesToInt16($bytes)
	{
		if (count($bytes) != 2) {
			throw new Exception("invalid input");
		}

		$dec = (($bytes[0] << 8) + ($bytes[1]));
		$conv = new Conversions;
		return $conv->UInt16ToInt16($dec);
	}

	function BytesToInt32($bytes)
	{
		if (count($bytes) != 4) {
			throw new Exception("invalid input");
		}

		$dec = (($bytes[0] << 24) + ($bytes[1] << 16) + ($bytes[2] << 8) + ($bytes[3]));
		$conv = new Conversions;
		return $conv->UInt32ToInt32($dec);
	}

	function ByteToUInt8($byte)
	{
		if (is_array($byte)) {
			throw new Exception("invalid input");
		}

		$conv = new Conversions;
		return $conv->Int8ToUInt8($byte);
	}

	function BytesToUInt16($bytes)
	{
		if (count($bytes) != 2) {
			throw new Exception("invalid input");
		}

		$dec = (($bytes[0] << 8) + ($bytes[1]));
		$conv = new Conversions;
		return $conv->Int16ToUInt16($dec);
	}

	function BytesToUInt32($bytes)
	{
		if (count($bytes) != 4) {
			throw new Exception("invalid input");
		}

		$dec = (($bytes[0] << 24) + ($bytes[1] << 16) + ($bytes[2] << 8) + ($bytes[3]));
		$conv = new Conversions;
		return $conv->Int32ToUInt32($dec);
	}

	function Int8ToByte($dec)
	{
		if ($dec > 127 || $dec < - 128) {
			throw new Exception("invalid input");
		}

		$byte = ($dec & 0xFF);
		return $byte;
	}

	function Int16ToBytes($dec)
	{
		if ($dec > 32767 || $dec < - 32768) {
			throw new Exception("invalid input");
		}

		$bytes = array();
		$bytes[0] = ($dec >> 8) & 0xFF;
		$bytes[1] = ($dec) & 0xFF;
		return $bytes;
	}

	function Int32ToBytes($dec)
	{
		if ($dec > 2147483647 || $dec < - 2147483648) {
			throw new Exception("invalid input");
		}

		$bytes = array();
		$bytes[0] = ($dec >> 24) & 0xFF;
		$bytes[1] = ($dec >> 16) & 0xFF;
		$bytes[2] = ($dec >> 8) & 0xFF;
		$bytes[3] = ($dec >> 0) & 0xFF;
		return $bytes;
	}

	function UInt8ToByte($dec)
	{
		if ($dec > 255 || $dec < 0) {
			throw new Exception("invalid input");
		}

		$byte = ($dec & 0xFF);
		return $byte;
	}

	function UInt16ToBytes($dec)
	{
		if ($dec > 65535 || $dec < 0) {
			throw new Exception("invalid input");
		}

		$bytes = array();
		$bytes[0] = ($dec >> 8) & 0xFF;
		$bytes[1] = ($dec) & 0xFF;
		return $bytes;
	}

	function UInt32ToBytes($dec)
	{
		if ($dec > 4294967295 || $dec < 0) {
			throw new Exception("invalid input");
		}

		$bytes = array();
		$bytes[0] = ($dec >> 24) & 0xFF;
		$bytes[1] = ($dec >> 16) & 0xFF;
		$bytes[2] = ($dec >> 8) & 0xFF;
		$bytes[3] = ($dec) & 0xFF;
		return $bytes;
	}

	function ArrayReverse($array)
	{
		return array_reverse($array);
	}
}

?>
