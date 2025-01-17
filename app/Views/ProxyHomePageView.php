<?php

interface Image {
    public function display();
}


class RealImage implements Image {
    private $filePath;

    public function __construct($filePath) {
        $this->filePath = $filePath;
        $this->loadImage();
    }

    private function loadImage() {
        // Simulate heavy loading
        sleep(5); // Represents expensive image loading
    }

    public function display() {
        echo "<img src='{$this->filePath}' alt='Real Image'><br>";
    }
}

class ImageProxy implements Image {
    private $realImage; 
    private $filePath;

    public function __construct($filePath) {
        $this->filePath = $filePath;
    }

    public function display() {
        // Output a placeholder for async loading
        echo "<div id='image-placeholder' data-url='{$this->filePath}'>Loading image...</div>";
    }

    public function loadRealImage() {
        // Instantiate the RealImage if it hasn't been created yet
        if ($this->realImage === null) {
            $this->realImage = new RealImage($this->filePath);
        }
        return $this->realImage;
    }
}





?>