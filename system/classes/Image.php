<?php

    /**
     * Class that handles Images
     * 
     * @author Joshua Kissoon
     * @date 20121212
     */
    class Image
    {

        private $image, $width, $height, $resized_image;

        /**
         * If image filename is passed, Load the image file then get the width and height of the image
         * 
         * @param $filename The image to be loaded
         */
        function __construct($filename = null)
        {
            if ($filename)
            {
                if (!$this->image = $this->loadImage($filename))
                {
                    return false;
                }
                $this->width = imagesx($this->image);
                $this->height = imagesy($this->image);
            }
        }

        /**
         * Checks what type of image file it is and load the image file
         * 
         * @param $file The image file to be loaded
         */
        public function loadImage($file)
        {
            /* Get the extension */
            $extension = strtolower(strrchr($file, '.'));
            switch ($extension)
            {
                case '.jpg':
                case '.jpeg':
                    $img = imagecreatefromjpeg($file);
                    break;
                case '.gif':
                    $img = imagecreatefromgif($file);
                    break;
                case '.png':
                    $img = imagecreatefrompng($file);
                    break;
                default:
                    $img = false;
                    break;
            }
            return $img;
        }

        /**
         * This is where we call the necessary resize Image function to resize the image
         * 
         * @param $option This parameter contains which resize option we want, options include:
         *          - exact - If we want the exact image the exact size specified
         *          - portrait - Scale the image to suit the vertical size specified
         *          - landscape - Scale the image to suit the horizontal size specified
         *          - crop Scale the image to suit the smaller axis(horiz or vertical), then crop out the edges of the larger axis to get the specified size
         */
        public function resizeImage($new_img_data = array(), $option = "crop")
        {
            $new_width = $new_img_data['width'];
            $new_height = $new_img_data['height'];

            switch ($option)
            {
                case 'exact':
                    $optimal_width = $new_width;
                    $optimal_height = $new_height;
                    break;
                case 'portrait':
                    $optimal_width = $this->getWidthByFixedHeight($new_height);
                    $optimal_height = $new_height;
                    break;
                case 'landscape':
                    $optimal_width = $new_width;
                    $optimal_height = $this->getHeightByFixedWidth($new_width);
                    break;
                case 'crop':
                    $optionArray = $this->getOptimalCrop($new_width, $new_height);
                    $optimal_width = $optionArray['optimal_width'];
                    $optimal_height = $optionArray['optimal_height'];
                    break;
            }

            /* Create a canvas to put the image on then copy our resampled image onto this canvas */
            $this->resized_image = imagecreatetruecolor($optimal_width, $optimal_height);
            imagecopyresampled($this->resized_image, $this->image, 0, 0, 0, 0, $optimal_width, $optimal_height, $this->width, $this->height);

            /* After resampling the image, crop it if specified */
            if ($option == 'crop')
            {
                $this->crop($optimal_width, $optimal_height, $new_width, $new_height);
            }
        }

        /**
         * Auto resizes an image without skewing the image
         * 
         * @param $new_img_data Contain the height and width of the new image
         */
        public function autoResizeImage($new_img_data = array())
        {
            if ($this->width > $this->height)
            {
                /* If the image width > the height, we take a landscape image */
                $this->resizeImage($new_img_data, "landscape");
                $this->resizeImage($new_img_data, "crop");
            }
            else if ($this->width < $this->height)
            {
                /* If the image height > the width, we take a portrait image */
                $this->resizeImage($new_img_data, "portrait");
                $this->resizeImage($new_img_data, "crop");
            }
            else
            {
                /* if Width = height, we resize image to be exact */
                $this->resizeImage($new_img_data, "exact");
                $this->resizeImage($new_img_data, "crop");
            }
        }

        /**
         * Computes the new width of an image given a fixed height
         * 
         * @param $new_height The required new height of the image
         * @return The width the image will be given a height
         */
        private function getWidthByFixedHeight($new_height)
        {
            $ratio = $this->width / $this->height;
            $new_width = $new_height * $ratio;
            return $new_width;
        }

        /**
         * Computes the new height of an image given a fixed width
         * 
         * @param $new_width The required new width of the image
         * @return The height the image will be given a width
         */
        private function getHeightByFixedWidth($new_width)
        {
            $ratio = $this->height / $this->width;
            $new_height = $new_width * $ratio;
            return $new_height;
        }

        /**
         * Computes the optimal size to crop an image to
         * 
         * @param $new_width The preferred width
         * @param $new_height The preferred height
         */
        private function getOptimalCrop($new_width, $new_height)
        {
            $height_ratio = $this->height / $new_height;
            $width_ratio = $this->width / $new_width;

            /*
             * Calculate which axis is smaller and resize to suit the smaller axis,
             * since we would later on cut out the edges of the larger axis
             */
            if ($height_ratio < $width_ratio)
            {
                $optimal_ratio = $height_ratio;
            }
            else
            {
                $optimal_ratio = $width_ratio;
            }

            $optimal_height = $this->height / $optimal_ratio;
            $optimal_width = $this->width / $optimal_ratio;

            return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
        }

        /**
         * Crops the image
         * 
         * @param $optimal_width
         * @param $optimal_height
         * @param $new_width
         * @param $new_height         * 
         */
        private function crop($optimal_width, $optimal_height, $new_width, $new_height)
        {
            /*
             * Here we find the center height and center of width to crop out the
             * edges of the longer side so we can have a square image
             */
            $crop_start_x = ( $optimal_width / 2) - ( $new_width / 2 );
            $crop_start_y = ( $optimal_height / 2) - ( $new_height / 2 );

            $crop = $this->resized_image;
            /* Here we start cropping to get the exact requested size */
            $this->resized_image = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($this->resized_image, $crop, 0, 0, $crop_start_x, $crop_start_y, $new_width, $new_height, $new_width, $new_height);
        }

        /**
         * Saves the image to a file
         * 
         * @param $save_path The path and file name where to save the image to
         * @param $image_quality The quality of the image
         */
        public function saveImage($save_path, $image_quality = "100")
        {
            /* Get extension */
            $extension = strtolower(strrchr($save_path, '.'));
            switch ($extension)
            {
                case '.jpg':
                case '.jpeg':
                    if (imagetypes() & IMG_JPG)
                    {
                        imagejpeg($this->resized_image, $save_path, $image_quality);
                    }
                    break;
                case '.gif':
                    if (imagetypes() & IMG_GIF)
                    {
                        imagegif($this->resized_image, $save_path);
                    }
                    break;
                case '.png':
                    /* Invert the quality since 0 is best for png, then compute the PNG scale quality  which ranges from 0-9 */
                    $scale_quality = round(((100 - $image_quality) / 100) * 9);
                    if (imagetypes() & IMG_PNG)
                    {
                        imagepng($this->resized_image, $save_path, $scale_quality);
                    }
                    break;
            }
            /* Remove the image from memory after it has been saved */
            imagedestroy($this->resized_image);
        }
    }
    