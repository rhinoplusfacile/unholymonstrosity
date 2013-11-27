<?php
namespace dvincisite;

class Dimensions
{
	/** @var int */
	public $x;
	/** @var int */
	public $y;
	
	/**
	 * @param int $x
	 * @param int $y
	 */
	public function __construct($x, $y)
	{
		$this->x = $x;
		$this->y = $y;
	}
}

class ImageFile extends \dvinci\entities\EntityBase
{
	const PORTFOLIO_MAIN = 'ppmain';
	const PORTFOLIO_LARGE = 'pplarge';
	const PORTFOLIO_SMALL = 'ppsmall';
	
	const TARGET_QUALITY = 80;
	/** @var array */
	private static $sizes;
	
	/** @var \dvincisite\Image */
	private $image;
	/** @var string */
	private $path;
	/** @var string */
	private $href_path;
	/** @var string */
	private $base_filename;
	/** @var string */
	private $ext;
	
	/**
	 * @param \dvincisite\Image $image
	 * @param string $path The path to the containing folder, starting in assets/images.  '' translates to $_SERVER['DOCUMENT_ROOT'] . '/assets/images'; 'a/b' translates to DOCUMENT_ROOT . '/assets/images/a/b'.  Leading and trailing slashes not required.
	 */
	public function __construct(Image $image, $path)
	{
		$this->image = $image;
		$this->path = self::fixPath($path);
		$this->href_path = self::fixHREFPath($path);
		$filename = $image->getFilename();
		$this->base_filename = self::calculateBaseFilename($filename);
		$this->ext = self::calculateExtension($filename);
	}
	
	/**
	 * 
	 * @param string $size
	 * @return \dvincisite\Dimensions
	 * @throws \Exception
	 */
	protected static function sizes($size)
	{
		if(!isset(self::$sizes))
		{
			self::$sizes = array(
				self::PORTFOLIO_MAIN => new Dimensions(300, 225),
				self::PORTFOLIO_LARGE => new Dimensions(573, 375),
				self::PORTFOLIO_SMALL => new Dimensions(70, 50)
			);
		}
		switch($size)
		{
		case self::PORTFOLIO_MAIN:
		case self::PORTFOLIO_LARGE:
		case self::PORTFOLIO_SMALL:
			return self::$sizes[$size];
		default:
			throw new \Exception('Incorrect size specified.');
		}
	}
	
	/**
	* Getter for image.
	* @return \dvincisite\Image 
	*/
	public function getImage()
	{
		$this->checkLoaded();
		return $this->image;
	}
	
	/**
	* Setter for image.
	* @param \dvincisite\Image $new_image 
	*/
	public function setImage($new_image)
	{
		$this->checkLoaded();
		$this->setEntityPropertyValue($this->image, $new_image);
		return $this;
	}
	
	/**
	 * 
	 * @param string $size The requested size using the constants.
	 * @return string The path to the containing folder.
	 */
	public function href($size)
	{
		$filename = $this->base_filename . '-' . $size . '.' . $this->ext;
		$this->generateFile($size, $filename);
		return $this->href_path . $filename;
	}
	
	protected function generateFile($size, $filename)
	{
		$original_filename = $this->path . $this->base_filename . '.' . $this->ext;
		if(!file_exists($original_filename))
		{
			throw new \Exception($original_filename . ' is not a valid file.');
		}
		$target_filename = $this->path . $filename;
		if(file_exists($target_filename))
		{
			//It's already created, no need to recreate.
			return;
		}
		
		// Compute the type depending on the extension
		$type = strtolower($this->ext);
		if ($type == 'jpg') $type = 'jpeg';

		// $func is the gd2 function we will use
		$func = 'imagecreatefrom' . $type;
		
		//Create image stream 
		$original_image = $func($original_filename);
		if(empty($original_image))
		{
			throw new Exception('File extension for ' . $original_filename . ' is incorrect or file is corrupted.');
		}
		//Gather and store the width and height
		list($original_width, $original_height) = getimagesize($original_filename);
		
		// Determine the scale ratio, a number between 0 and 1.
		// This is a value by which we will multiply the
		// original width and height to obtain the thumbnail 
		// dimensions.

		$dimensions = self::sizes($size);
		
		$scale = min($dimensions->x/$original_width, $dimensions->y/$original_height);
		if ($scale < 1)
		{
			$target_width = floor($scale * $original_width);
			$target_height = floor($scale * $original_height);

			$target_image = imagecreatetruecolor($dimensions->x, 
				$dimensions->y);
			
			$offset_x = floor(($dimensions->x - $target_width) / 2);
			$offset_y = floor(($dimensions->y - $target_height) / 2);

			// Copy and resample into the thumbnail.
			imagecopyresampled($target_image, $original_image,
				$offset_x, $offset_y, 0, 0,
				$target_width, $target_height, 
				$original_width, $original_height);
		}
		else
		{
			// No need to do anything - the image is already 
			// within the defined thumbnail size constraints!
			$target_image = $original_image;
		}

		// Finally, write the thumbnail to a file.
		$func = 'image' . $type;
		$func($target_image, $target_filename, self::TARGET_QUALITY);
	}
	
	/**
	 * @param string $full_filename
	 * @return string;
	 */
	protected static function calculateExtension($full_filename)
	{
		$matches = array();
		if(preg_match('/\.([A-Za-z]+)$/i', $full_filename, $matches))
		{
			return $matches[1];
		}
		return '';
	}
	
	/**
	 * @param string $full_filename
	 * @return string;
	 */
	protected static function calculateBaseFilename($full_filename)
	{
		$matches = array();
		if(preg_match('/(\S+)\.[A-Za-z]+$/i', $full_filename, $matches))
		{
			return $matches[1];
		}
		return '';
	}
	
	protected static function fixPath($relative_path)
	{
		if(file_exists($relative_path))		//In case we want to get something outside of assets for some reason
		{
			return $relative_path;
		}
		else
		{
			trim($relative_path, '/\\');
			return $_SERVER['DOCUMENT_ROOT'] . '/assets/images/' . $relative_path . '/';
		}
	}
	
	protected static function fixHREFPath($relative_path)
	{
		trim($relative_path, '/\\');
		return '/assets/images/' . $relative_path . '/';
	}
}

?>