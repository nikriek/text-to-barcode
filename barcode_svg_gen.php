<?php
	define ("BINARY_VALUES", serialize (array(
		0b11011001100,
		0b11001101100,
		0b11001100110,
		0b10010011000,
		0b10010001100,
		0b10001001100,
		0b10011001000,
		0b10011000100,
		0b10001100100,
		0b11001001000,
		0b11001000100,
		0b11000100100,
		0b10110011100,
		0b10011011100,
		0b10011001110,
		0b10111001100,
		0b10011101100,
		0b10011100110,
		0b11001110010,
		0b11001011100,
		0b11001001110,
		0b11011100100,
		0b11001110100,
		0b11101101110,
		0b11101001100,
		0b11100101100,
		0b11100100110,
		0b11101100100,
		0b11100110100,
		0b11100110010,
		0b11011011000,
		0b11011000110,
		0b11000110110,
		0b10100011000,
		0b10001011000,
		0b10001000110,
		0b10110001000,
		0b10001101000,
		0b10001100010,
		0b11010001000,
		0b11000101000,
		0b11000100010,
		0b10110111000,
		0b10110001110,
		0b10001101110,
		0b10111011000,
		0b10111000110,
		0b10001110110,
		0b11101110110,
		0b11010001110,
		0b11000101110,
		0b11011101000,
		0b11011100010,
		0b11011101110,
		0b11101011000,
		0b11101000110,
		0b11100010110,
		0b11101101000,
		0b11101100010,
		0b11100011010,
		0b11101111010,
		0b11001000010,
		0b11110001010,
		0b10100110000,
		0b10100001100,
		0b10010110000,
		0b10010000110,
		0b10000101100,
		0b10000100110,
		0b10110010000,
		0b10110000100,
		0b10011010000,
		0b10011000010,
		0b10000110100,
		0b10000110010,
		0b11000010010,
		0b11001010000,
		0b11110111010,
		0b11000010100,
		0b10001111010,
		0b10100111100,
		0b10010111100,
		0b10010011110,
		0b10111100100,
		0b10011110100,
		0b10011110010,
		0b11110100100,
		0b11110010100,
		0b11110010010,
		0b11011011110,
		0b11011110110,
		0b11110110110,
		0b10101111000,
		0b10100011110,
		0b10001011110,
		0b10111101000,
		0b10111100010,
		0b11110101000,
		0b11110100010,
		0b10111011110,
		0b10111101110,
		0b11101011110,
		0b11110101110,
		0b11010000100,
		0b11010010000,
		0b11010011100,
		0b11000111010,
		0b11
	)));
	define("START_B", 104);
	define("STOP", 106);
	define("TERMINATION", 107);

	function generateSVGFromBinary($code_array, $height, $modul_width) {
		//Before outputting the array, a content type header should be integrated
		//header("Content-type:image/svg+xml");

		echo'<svg xmlns="http://www.w3.org/2000/svg"xmlns:xlink="http://www.w3.org/1999/xlink">';

		//Offset
		$rect_x_position = 0;

		//Iterate through the different encoded chars
		foreach ($code_array as $code_key => $code_value) {
			$binary_array = str_split($code_value);
			//Iterate through each bit as char…
			foreach ($binary_array as $binary_key => $binary_value) {
				//Output "1" for black and "0" for white stripe(s) – "Seven Nation Army"
				if ($binary_value) {
					echo '<rect x="'.$rect_x_position.'" y="0" height="'.$height.'" width="'.$modul_width.'" style="fill: #000000"/>';
				} else {
					echo '<rect x="'.$rect_x_position.'" y="0" height="'.$height.'" width="'.$modul_width.'" style="fill: #FFFFFF"/>';
				}
				$rect_x_position = $rect_x_position + $modul_width;
			}
		}
		echo '</svg>';
	}

	function get_string_binary($startCode, $cleartext) {
		//Geht binary values from global constant
		$binaryValues = unserialize(BINARY_VALUES);

		//Array to store barcode encoded as binary
		$binaryArray = array();

		//The checkum begins with the start codon
		$checksum = $startCode;
		array_push($binaryArray, decbin($binaryValues[$startCode]));

		//Iterate through string
		$length = strlen($cleartext);
		for ($i=0; $i<$length; $i++) {
			//Convert ascii into code128 binary
			$asciiCode = ord($cleartext[$i]) - 32;
			$binaryCode = $binaryValues[$asciiCode];
			array_push($binaryArray, decbin($binaryCode));

			//Checksum
			$checksum = $checksum + $asciiCode * ($i + 1);
		}
		//Calculate checksum with modulor
		$checksum = fmod($checksum, 103);

		//Add necessary binary values (e.g. stop codon)
		array_push($binaryArray, decbin($binaryValues[$checksum]));
		array_push($binaryArray, decbin($binaryValues[STOP]));
		array_push($binaryArray, decbin($binaryValues[TERMINATION]));

		return $binaryArray;
	}

	# Prozedur
	if (!empty($_GET['cleartext'])) {
		$binary_array = get_string_binary(START_B,$_GET['cleartext']);
		generateSVGFromBinary($binary_array, 100, 2);
	} else {
		echo '<p class="text-center">You have to enter cleartext!</p>';
	}
?>