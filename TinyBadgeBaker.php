<?php

class TinyBadgeBaker {

	protected $assertionJsonUrl = '';
	protected $outputFilename = '';

	function __construct($assertion_json_url, $output_filename) {
		$this->assertionJsonUrl = $assertion_json_url;
		$this->outputFilename = $output_filename;
	}

	public getAssertionJsonUrl() {
		return ($this->assertionJsonUrl);
	}

	public setAssertionJsonUrl($value) {
		$this->assertionJsonUrl = $value;
	}

	public getOutputFilename() {
		return ($this->outputFilename);
	}

	public setOutputFilename($value) {
		$this->outputFilename = $value;
	}

	protected createImageLink(){
		 try {
		  	// Retrieve the assertion JSON.
		    $assertion_file = file_get_contents($this->assertionJsonUrl);
		    $assertion_array = json_decode($assertion_file, true);

		    // Verify the JSON type of the file.
		    if (json_last_error() == JSON_ERROR_NONE) {
		    	return('The file is not a JSON.')
		    }

		    // Retrieve the imagefile url.
		    $assertion_image_url = $assertion_array['image'];

		    // Generate the image via ImageMagick. If this fails will throw an exception.
		    $im = new Imagick($assertion_image_url);

		    // Verify if the image is an already baked file.
		    $old_assertion = $im->getimageproperty('openbadges');
		    if (!empty($old_assertion)) {
		      return('This image file has been already baked!');
		    }

		    // Set the image properties.
		    $im->setimageformat('png');
		    $im->setimageproperty('openbadges', $assertion_url);
		    $im->setfilename($this->outputFilename . '.png');
		    $im->setimageproperty('filename', $this->outputFilename . '.png');

		    // Return the final baked blob of the imagefile.
		    return base64_encode($im->getimageblob());
		  }
		  catch (ImagickException $e) {
		    var_dump($e);
		  }
	}
}