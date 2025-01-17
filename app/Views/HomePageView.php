<?php

require_once __DIR__ . '/ProxyHomePageView.php';


class HomePageView {
    private $image;

    public function __construct(Image $image) {
        $this->image = $image;
    }

    public function render() {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Home Page</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <script>
                // JavaScript for asynchronous image loading
                document.addEventListener('DOMContentLoaded', function () {
                    const placeholder = document.getElementById('image-placeholder');
                    const imageUrl = placeholder.dataset.url;

                    // Create an image element
                    const img = new Image();
                    img.src = imageUrl;

                    // Replace the placeholder once the image is loaded
                    img.onload = function () {
                        placeholder.innerHTML = ''; 
                        placeholder.appendChild(img);
                    };

                    // Handle errors in case the image fails to load
                    img.onerror = function () {
                        placeholder.innerHTML = '<p>Error loading image.</p>';
                    };
                });
            </script>
        </head>
        <body>
            <div class='container text-center center-box'>
                <h2>Home Page</h2>
                <div class='mt-3'>";
                
                // Display the image through the proxy
                $this->image->display();

        echo "
                </div>
                <div>
                    <a href='core-Reporter.php' class='btn btn-primary btn-lg'>Report</a>
                    <a href='core-Doner.php' class='btn btn-success btn-lg'>Donate</a>
                    <a href='core-AllEvents.php' class='btn btn-warning btn-lg'>Volunteer</a>
                </div>
            </div>
        </body>
        </html>";
    }
}

// Instantiate the Proxy and Render the View
$imageProxy = new ImageProxy('../../feed.jpg');
$view = new HomePageView($imageProxy);
$view->render();

?>