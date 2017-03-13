<?php
namespace Http\Format;

class ImageFormat implements FormatInterface 
{
	private $format;
	
	public function matches(string $path) : bool 
	{
		$isMatch = preg_match("/\.(jpe?g|gif|png)$/i", $path, $matches);
		
		if ($isMatch) {
			$this->format = $matches[1];
		}
		
		return $isMatch;
	}

	public function mimeType(): string 
	{
		switch ($this->format) {
			case "png":
				return "image/png";
				break;
			case "jpeg":
			case "jpg":
			default:
				return "image/jpeg";
		}
	}

	public function parse($data): string 
	{
		ob_Start();
		$image = imagecreatefromstring($data);
		
		switch ($this->format) {
			case "png":
				imagepng($image);
				break;
			case "jpeg":
			case "jpg":
			default:
				imagejpeg($image);
				break;
		}
		
		imagedestroy($image);
		return ob_get_clean();
	}
}
