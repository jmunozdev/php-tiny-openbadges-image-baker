<?php
/**
 * Creates a link to download the baked .png file.
 * @param string $assertion_json_url The assertion file URL.
 * @param string $output_file_name The name of the PNG file will be downloaded.
 * @return string A link in case of success or an error description message in case of failure.
 */
function module_print_baked_assertion_image($assertion_json_url, $output_file_name) {
  try {
    $assertion_url = url($assertion_json_url, array('absolute' => TRUE));
    $assertion_file = file_get_contents($assertion_url);
    $assertion_array = json_decode($assertion_file, true);
    $assertion_image_url = $assertion_array['image'];

    $im = new Imagick($assertion_image_url);
    $im->setimageformat('png');
    $old_assertion = $im->getimageproperty('openbadges');
    
    if (!empty($old_assertion)) {
      return(t('This image has been already baked!'));
    }

    $im->setimageproperty('openbadges', $assertion_url);
    $im->setfilename($output_file_name . '.png');
    $im->setimageproperty('filename', $output_file_name . '.png');
    return '<a download="' . $output_file_name . '.png" href="data:image/png;base64,' .  base64_encode($im->getimageblob())  . '" >Click here to download the baked image.</a>';
  }
  catch (ImagickException $e) {
    return t('The badge image could not be assembled');
  }
}
