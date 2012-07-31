<?php

/**
 * Create image FPO (For Position Only)
 */

    class PlaceholderImage {	

        public $width = 250;
        public $height = 250;
        public $bgColor = 'CCCCCC';
        public $textColor = '';
        public $text = '';
        public $fontFile = '';
        public $filename = '';

        public $image;


       /**
        * Constructor
        */
            public function __construct($config) { 
                    $this->width = (!empty($config['width'])) ? $config['width'] : $this->width;
                    $this->height = (!empty($config['height'])) ? $config['height'] : $this->height;

                    $this->bgColor = (!empty($config['bgColor'])) ? $config['bgColor'] : $this->bgColor;
                    $this->textColor = (!empty($config['textColor'])) ? $config['textColor'] : $this->textColor;

                    $this->text = (!empty($config['text'])) ? $config['text'] : $this->text;
                    
                    $this->createPlaceholder(); 
            }

            
       /**
        * Create image placeholder
        */
            public function createPlaceholder() {		
                    // Configuration
                    $w = $this->width;
                    $h = $this->height;

                    $bgColorRgb = $this->hex2rgb($this->bgColor);
                    $textColorRgb = !empty($this->textColor) ? $this->hex2rgb($this->textColor) : false;

                    $text = ($this->text!='')  ?  $this->text  :  ($w . "x". $h);
                    $bgPadding = 5;

                    $fontFile = ($this->fontFile!='')  ?  $this->fontFile  :  'Arvo-Regular.ttf';
                    $fontSize = 20;

                    $lineThickness = 1;


                    // Create image			
                    $this->image = imagecreatetruecolor($w, $h); 
                    imageantialias($this->image, true );


                    // Colors
                    $bgColor = imagecolorallocate($this->image, $bgColorRgb[0], $bgColorRgb[1], $bgColorRgb[2]);
                    $lineColor = imagecolorallocatealpha($this->image, 30, 30, 30, 100);
                    $textColor = ($textColorRgb==false) ? imagecolorallocatealpha($this->image, 30, 30, 30, 60) : imagecolorallocate($this->image, $textColorRgb[0], $textColorRgb[1], $textColorRgb[2]);


                    // Draw background
                    imagefill($this->image, 0, 0, $bgColor); 


                    // Add cross                                
                    $this->imagelinethick($this->image, 0, 0, $w, $h, $lineColor, $lineThickness);
                    $this->imagelinethick($this->image, $w, 0, 0, $h, $lineColor, $lineThickness);


                    // Write text                           
                    list($x, $y, $textWidth, $textHeight) = $this->imageTTFCenter($this->image, $text, $fontFile, $fontSize);

                    $bgx1 = $x - $bgPadding;
                    $bgx2 = $bgx1 + $textWidth + ($bgPadding*2);

                    $bgy1 = $y - $textHeight - $bgPadding;
                    $bgy2 = $bgy1 + $textHeight + ($bgPadding*2);

                    imagefilledrectangle($this->image, $bgx1, $bgy1, $bgx2, $bgy2, $bgColor); // Draw text background
                    imagettftext($this->image, $fontSize, 0, $x, $y, $textColor, $fontFile, $text);
                    
                    // Generate filename
                    $this->filename = ($w . "x". $h) . '_' . substr( md5(uniqid(rand(), true)), 0, 5) . '.png';
            }

            
       /**
        * Output image in browser
        */
            public function output() {	
                // Output in browser
                header("Content-Type: image/png"); 
                header("Content-disposition: inline; filename=" . $this->filename); // RFC2183: http://www.ietf.org/rfc/rfc2183.txt
                
                imagepng($this->image);

                // Free memory
                imagedestroy($this->image);

                exit;
            }   

            
       /**
        * Force download of image placeholder
        */
            public function forceDownload() {
                // Download it
                header("Content-Type: image/png");
                header("Content-disposition: attachment; filename=" . $this->filename); // RFC2183: http://www.ietf.org/rfc/rfc2183.txt
                
                imagepng($this->image);

                // Free memory
                imagedestroy($this->image);
                           
                exit;
            }  

            
       /**
        * Return text block position (both horizontally/vertically centered)
        * 
        * @param resource $image
        * @param string $text
        * @param string $font
        * @param float $size
        * 
        * @return array 
        */
            public function imageTTFCenter($image, $text, $font, $size) {
                    // Find the size of the image
                    $imageWidth = imagesx($image);
                    $imageHeight = imagesy($image);

                    // Get the bounding box of the text
                    $box = imagettfbbox($size, 0, $font, $text);

                    // Calculate its dimensions
                    $textWidth = abs($box[6]) + abs($box[4]);
                    $textHeight = abs($box[7]) + abs($box[1]) ;

                    // Compute centering
                    $x = ($imageWidth - $textWidth) / 2;
                    $y = ($imageHeight + $textHeight) / 2;
                    //$y -= $textHeight; // Y-ordinate sets the position of the font baseline 

                    return array($x, $y, $textWidth, $textHeight);
            }

            
       /**
        * Drawing a thick line
        *  
        * @param resource $image
        * @param int $x1
        * @param int $y1
        * @param int $x2
        * @param int $y2
        * @param string $color
        * @param int $thick
        * 
        * @return resource
        */
            public function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1) {
                    $t = $thick / 2 - 0.5;
                    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
                    $a = $t / sqrt(1 + pow($k, 2));
                    $points = array(
                        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
                        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
                        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
                        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
                    );

                    return  imagefilledpolygon($image, $points, 4, $color);
            } 


       /**
        * Convert hexadecimal color '#abc123' or 'abc123' to RGB values
        * support 3-chars hex colors '#aaa' or 'aaa'
        *
        * @param string Color in hexadecimal format
        *
        * @return array
        */
            public function hex2rgb($color) {
                    $color = str_replace("#", "", $color);


                    if(strlen($color) == 3) {
                            $r = hexdec( substr($color,0,1).substr($color,0,1) );
                            $g = hexdec( substr($color,1,1).substr($color,1,1) );
                            $b = hexdec( substr($color,2,1).substr($color,2,1) );
                    } else {
                            $r = hexdec( substr($color,0,2) );
                            $g = hexdec( substr($color,2,2) );
                            $b = hexdec( substr($color,4,2) );
                    }

                    $rgb = array($r, $g, $b);

                    return $rgb;
            }


    }
?>