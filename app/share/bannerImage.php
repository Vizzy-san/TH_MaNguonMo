<?php
/**
 * Banner Image Component
 * Displays banner images from the public/images directory
 */
class BannerImage {
    private $imagesPath;
    private $bannerImages;
    
    public function __construct() {
        $this->imagesPath = BASE_PATH . '/public/images/';
        $this->bannerImages = $this->getBannerImages();
    }
    
    // Get available banner images
    private function getBannerImages() {
        $images = [];
        // Define the 3 fixed banner images to use
        $fixedBanners = [
            'km1.jpg',
            'topdeal.jpg'
        ];
        
        // Check each fixed banner image exists and is readable
        foreach ($fixedBanners as $banner) {
            $fullPath = $this->imagesPath . $banner;
            if (file_exists($fullPath) && is_readable($fullPath)) {
                $images[] = $banner;
            }
        }
        
        // If no banner images found, return at least one default
        if (empty($images)) {
            // Check if default banner exists
            if (file_exists($this->imagesPath . 'default-banner.jpg')) {
                $images[] = 'default-banner.jpg';
            }
        }
        
        return $images;
    }
    
    // Render the banner component
    public function render() {
        // Skip rendering if no images are available
        if (empty($this->bannerImages)) {
            echo '<div class="alert alert-warning">Banner images not available.</div>';
            return;
        }
        ?>
        <div class="banner-container my-3">
            <div id="bannerCarousel" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <?php foreach ($this->bannerImages as $index => $image): ?>
                    <li data-target="#bannerCarousel" data-slide-to="<?php echo $index; ?>" <?php echo $index === 0 ? 'class="active"' : ''; ?>></li>
                    <?php endforeach; ?>
                </ol>
                <div class="carousel-inner">
                    <?php foreach ($this->bannerImages as $index => $image): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <!-- Fixed URL path construction using URL without /BFYL prefix -->
                        <img src="<?php echo htmlspecialchars('/BFYL/public/images/' . $image, ENT_QUOTES, 'UTF-8'); ?>" 
                             class="d-block w-100" alt="Banner Image"
                             style="height: 300px; object-fit: cover;"
                             onerror="this.onerror=null; this.src='/BFYL/public/images/default-banner.jpg'; this.alt='Default Banner';">
                    </div>
                    <?php endforeach; ?>
                </div>
                <a class="carousel-control-prev" href="#bannerCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#bannerCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
        <?php
    }
    
    // Debug method to check image paths
    public function debugImagePaths() {
        echo '<div class="alert alert-info">';
        echo '<h4>Banner Image Debug Info:</h4>';
        echo '<p>Base Path: ' . htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>Images Path: ' . htmlspecialchars($this->imagesPath, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>Available Images:</p>';
        echo '<ul>';
        foreach ($this->bannerImages as $image) {
            $fullPath = $this->imagesPath . $image;
            echo '<li>' . 
                 htmlspecialchars($image, ENT_QUOTES, 'UTF-8') . ' - ' . 
                 (file_exists($fullPath) ? 'Exists' : 'Missing') . ' - ' . 
                 (is_readable($fullPath) ? 'Readable' : 'Not readable') .
                 '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}
?>
