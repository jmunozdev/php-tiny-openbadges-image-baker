<?php

/**
 * Class TinyBadgeBaker
 * @property $assertionJsonUrl string The assertion file URL.
 * @property  $output_filename string The name of the PNG file will be downloaded.
 */
class TinyBadgeBaker {

    protected $assertionJsonUrl = '';
    protected $outputFilename = '';


    /**
     * TinyBadgeBaker constructor.
     * @param $assertion_json_url string Needs to be a valid JSON file.
     * @param $output_filename string Without the .png extension, just the filename before the '.'.
     */
    function __construct($assertion_json_url, $output_filename) {
        $this->assertionJsonUrl = $assertion_json_url;
        $this->outputFilename = $output_filename;
    }

    public function getAssertionJsonUrl() {
        return ($this->assertionJsonUrl);
    }

    public function setAssertionJsonUrl($value) {
        $this->assertionJsonUrl = $value;
    }

    public function getOutputFilename() {
        return ($this->outputFilename);
    }

    public function setOutputFilename($value) {
        $this->outputFilename = $value;
    }

    /**
     * This function will return FALSE in case of failure
     * and a string containing the blob of the png file in case of success.
     * @return bool|string
     */
    protected function createImageBlob(){
        try {
            // Retrieve the assertion JSON.
            $assertion_file = file_get_contents($this->assertionJsonUrl);
            $assertion_array = json_decode($assertion_file, true);

            // Verify the JSON type of the file.
            if (json_last_error() == JSON_ERROR_NONE) {
                echo('The file is not a JSON.');
                return FALSE;
		    }

            // Retrieve the imagefile url.
            $assertion_image_url = $assertion_array['image'];

            // Generate the image via ImageMagick. If this fails will throw an exception.
            $im = new Imagick($assertion_image_url);

            // Verify if the image is an already baked file.
            $old_assertion = $im->getimageproperty('openbadges');
            if (!empty($old_assertion)) {
                echo('This image file has been already baked!');
                return FALSE;
            }

            // Set the image properties.
            $im->setimageformat('png');
            $im->setimageproperty('openbadges', $this->assertionJsonUrl);
            $im->setfilename($this->outputFilename . '.png');
            $im->setimageproperty('filename', $this->outputFilename . '.png');

            // Return the final baked blob of the imagefile.
            return base64_encode($im->getimageblob());
        }
        catch (ImagickException $e) {
            var_dump($e);
            return FALSE;
        }
    }
}