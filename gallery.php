<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

$settings      = getSettings();
$pageTitle     = 'Photo Gallery | Quattro Homes Bungoma';
$pageDescription = 'Browse real photos of both Quattro Homes units in Bungoma: Floor 1 and Floor 2, fully-furnished 2-bedroom apartments.';
$canonicalPath = 'gallery.php';
$activePage    = 'gallery';

// All images with labels, floor2 first (more presentable)
$floor2 = [
  ['src'=>'images/floor2/IMG_5843.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5844.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5845.jpg','label'=>'Floor 2 · Living & Dining Area'],
  ['src'=>'images/floor2/IMG_5848.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5849.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5851.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5854.jpg','label'=>'Floor 2 · Living Area'],
  ['src'=>'images/floor2/IMG_5855.jpg','label'=>'Floor 2 · Living Area'],
  ['src'=>'images/floor2/IMG_5856.jpg','label'=>'Floor 2 · Dining Area'],
  ['src'=>'images/floor2/IMG_5860.jpg','label'=>'Floor 2 · TV Area'],
  ['src'=>'images/floor2/IMG_5878.jpg','label'=>'Floor 2 · Bedroom with Baby Cot'],
  ['src'=>'images/floor2/IMG_5880.jpg','label'=>'Floor 2 · Bedroom with Cot'],
  ['src'=>'images/floor2/IMG_5883.jpg','label'=>'Floor 2 · Bedroom 2'],
  ['src'=>'images/floor2/IMG_5885.jpg','label'=>'Floor 2 · Bedroom 2'],
  ['src'=>'images/floor2/IMG_5888.jpg','label'=>'Floor 2 · Kitchen'],
  ['src'=>'images/floor2/IMG_5893.jpg','label'=>'Floor 2 · Kitchen'],
  ['src'=>'images/floor2/IMG_5870.jpg','label'=>'Floor 2 · Bathroom & Shower'],
  ['src'=>'images/floor2/IMG_5873.jpg','label'=>'Floor 2 · Bathroom'],
  ['src'=>'images/floor2/IMG_5862.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5864.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5866.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5868.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5895.jpg','label'=>'Floor 2 · Front Balcony'],
  ['src'=>'images/floor2/IMG_5896.jpg','label'=>'Floor 2 · Front Balcony'],
];

$floor1 = [
  ['src'=>'images/floor1/IMG_5923.jpg','label'=>'Floor 1 · Living Room'],
  ['src'=>'images/floor1/IMG_5926.jpg','label'=>'Floor 1 · Living Room'],
  ['src'=>'images/floor1/IMG_5928.jpg','label'=>'Floor 1 · Living Area'],
  ['src'=>'images/floor1/IMG_5957.jpg','label'=>'Floor 1 · Living Room'],
  ['src'=>'images/floor1/IMG_7778.jpg','label'=>'Floor 1 · Living Area'],
  ['src'=>'images/floor1/IMG_5910.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5912.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5918.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5922.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5934.jpg','label'=>'Floor 1 · Dining & TV Area'],
  ['src'=>'images/floor1/IMG_5937.jpg','label'=>'Floor 1 · Dining Area'],
  ['src'=>'images/floor1/IMG_5939.jpg','label'=>'Floor 1 · Dining Area'],
  ['src'=>'images/floor1/IMG_7797.jpg','label'=>'Floor 1 · Kitchen'],
  ['src'=>'images/floor1/IMG_7820.jpg','label'=>'Floor 1 · Bathroom with Bathtub'],
  ['src'=>'images/floor1/IMG_7826.jpg','label'=>'Floor 1 · Bathroom 2'],
  ['src'=>'images/floor1/IMG_5940.jpg','label'=>'Floor 1 · Decor & Garden'],
  ['src'=>'images/floor1/IMG_5941.jpg','label'=>'Floor 1 · Back Balcony'],
  ['src'=>'images/floor1/IMG_5942.jpg','label'=>'Floor 1 · Back Balcony'],
  ['src'=>'images/floor1/IMG_5951.jpg','label'=>'Floor 1 · Balcony & Garden'],
  ['src'=>'images/floor1/IMG_5961.jpg','label'=>'Floor 1 · Front Balcony'],
  ['src'=>'images/floor1/IMG_5964.jpg','label'=>'Floor 1 · Front Balcony'],
];

// Merge for "All" tab
$allImages = [
  ['src'=>'images/floor2/IMG_5843.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5844.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5845.jpg','label'=>'Floor 2 · Living & Dining Area'],
  ['src'=>'images/floor2/IMG_5848.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5849.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5851.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5854.jpg','label'=>'Floor 2 · Living Area'],
  ['src'=>'images/floor2/IMG_5855.jpg','label'=>'Floor 2 · Living Area'],
  ['src'=>'images/floor2/IMG_5856.jpg','label'=>'Floor 2 · Dining Area'],
  ['src'=>'images/floor2/IMG_5860.jpg','label'=>'Floor 2 · TV Area'],
  ['src'=>'images/floor2/IMG_5878.jpg','label'=>'Floor 2 · Bedroom with Baby Cot'],
  ['src'=>'images/floor2/IMG_5880.jpg','label'=>'Floor 2 · Bedroom with Cot'],
  ['src'=>'images/floor2/IMG_5883.jpg','label'=>'Floor 2 · Bedroom 2'],
  ['src'=>'images/floor2/IMG_5885.jpg','label'=>'Floor 2 · Bedroom 2'],
  ['src'=>'images/floor2/IMG_5888.jpg','label'=>'Floor 2 · Kitchen'],
  ['src'=>'images/floor2/IMG_5893.jpg','label'=>'Floor 2 · Kitchen'],
  ['src'=>'images/floor2/IMG_5870.jpg','label'=>'Floor 2 · Bathroom & Shower'],
  ['src'=>'images/floor2/IMG_5873.jpg','label'=>'Floor 2 · Bathroom'],
  ['src'=>'images/floor2/IMG_5862.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5864.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5866.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5868.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5895.jpg','label'=>'Floor 2 · Front Balcony'],
  ['src'=>'images/floor2/IMG_5896.jpg','label'=>'Floor 2 · Front Balcony'],
  ['src'=>'images/floor1/IMG_5923.jpg','label'=>'Floor 1 · Living Room'],
  ['src'=>'images/floor1/IMG_5926.jpg','label'=>'Floor 1 · Living Room'],
  ['src'=>'images/floor1/IMG_5928.jpg','label'=>'Floor 1 · Living Area'],
  ['src'=>'images/floor1/IMG_5957.jpg','label'=>'Floor 1 · Living Room'],
  ['src'=>'images/floor1/IMG_7778.jpg','label'=>'Floor 1 · Living Area'],
  ['src'=>'images/floor1/IMG_5910.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5912.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5918.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5922.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5934.jpg','label'=>'Floor 1 · Dining & TV Area'],
  ['src'=>'images/floor1/IMG_5937.jpg','label'=>'Floor 1 · Dining Area'],
  ['src'=>'images/floor1/IMG_5939.jpg','label'=>'Floor 1 · Dining Area'],
  ['src'=>'images/floor1/IMG_7797.jpg','label'=>'Floor 1 · Kitchen'],
  ['src'=>'images/floor1/IMG_7820.jpg','label'=>'Floor 1 · Bathroom with Bathtub'],
  ['src'=>'images/floor1/IMG_7826.jpg','label'=>'Floor 1 · Bathroom 2'],
  ['src'=>'images/floor1/IMG_5940.jpg','label'=>'Floor 1 · Decor & Garden'],
  ['src'=>'images/floor1/IMG_5941.jpg','label'=>'Floor 1 · Back Balcony'],
  ['src'=>'images/floor1/IMG_5942.jpg','label'=>'Floor 1 · Back Balcony'],
  ['src'=>'images/floor1/IMG_5951.jpg','label'=>'Floor 1 · Balcony & Garden'],
  ['src'=>'images/floor1/IMG_5961.jpg','label'=>'Floor 1 · Front Balcony'],
  ['src'=>'images/floor1/IMG_5964.jpg','label'=>'Floor 1 · Front Balcony'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/includes/head.php'; ?></head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<!-- PAGE HEADER -->
<div class="gallery-page-header">
  <div class="container">
    <p class="eyebrow">Photo Gallery</p>
    <h1>A look inside</h1>
    <p>Two fully-furnished floors. Browse both before you book.</p>
  </div>
</div>

<!-- TABS -->
<div class="gallery-tab-bar">
  <div class="container gallery-tab-inner">
    <div class="gallery-tabs" id="gallery-tabs">
      <button class="gallery-tab active" data-tab="floor2">
        Floor 2 <span class="tab-count"><?php echo count($floor2); ?></span>
      </button>
      <button class="gallery-tab" data-tab="floor1">
        Floor 1 <span class="tab-count"><?php echo count($floor1); ?></span>
      </button>
      <button class="gallery-tab" data-tab="all">
        All Photos <span class="tab-count"><?php echo count($allImages); ?></span>
      </button>
    </div>
    <a href="book.php" class="btn btn-gold gallery-book-btn">Book Now</a>
  </div>
</div>

<!-- GALLERY PANELS -->
<div class="gallery-panel" id="panel-floor2">
  <div class="gallery-room-group">
    <div class="gallery-room-label">Living Room</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="0" data-tab="floor2">
        <img src="images/floor2/IMG_5843.jpg" alt="Floor 2 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="1" data-tab="floor2">
        <img src="images/floor2/IMG_5844.jpg" alt="Floor 2 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="2" data-tab="floor2">
        <img src="images/floor2/IMG_5845.jpg" alt="Floor 2 · Living & Dining Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living & Dining Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="3" data-tab="floor2">
        <img src="images/floor2/IMG_5848.jpg" alt="Floor 2 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="4" data-tab="floor2">
        <img src="images/floor2/IMG_5849.jpg" alt="Floor 2 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="5" data-tab="floor2">
        <img src="images/floor2/IMG_5851.jpg" alt="Floor 2 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="6" data-tab="floor2">
        <img src="images/floor2/IMG_5854.jpg" alt="Floor 2 · Living Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="7" data-tab="floor2">
        <img src="images/floor2/IMG_5855.jpg" alt="Floor 2 · Living Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="8" data-tab="floor2">
        <img src="images/floor2/IMG_5856.jpg" alt="Floor 2 · Dining Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Dining Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="9" data-tab="floor2">
        <img src="images/floor2/IMG_5860.jpg" alt="Floor 2 · TV Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · TV Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Bedroom</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="10" data-tab="floor2">
        <img src="images/floor2/IMG_5878.jpg" alt="Floor 2 · Bedroom with Baby Cot" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bedroom with Baby Cot</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="11" data-tab="floor2">
        <img src="images/floor2/IMG_5880.jpg" alt="Floor 2 · Bedroom with Cot" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bedroom with Cot</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="12" data-tab="floor2">
        <img src="images/floor2/IMG_5883.jpg" alt="Floor 2 · Bedroom 2" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bedroom 2</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="13" data-tab="floor2">
        <img src="images/floor2/IMG_5885.jpg" alt="Floor 2 · Bedroom 2" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bedroom 2</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Kitchen</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="14" data-tab="floor2">
        <img src="images/floor2/IMG_5888.jpg" alt="Floor 2 · Kitchen" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Kitchen</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="15" data-tab="floor2">
        <img src="images/floor2/IMG_5893.jpg" alt="Floor 2 · Kitchen" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Kitchen</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Bathroom</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="16" data-tab="floor2">
        <img src="images/floor2/IMG_5870.jpg" alt="Floor 2 · Bathroom & Shower" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bathroom & Shower</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="17" data-tab="floor2">
        <img src="images/floor2/IMG_5873.jpg" alt="Floor 2 · Bathroom" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bathroom</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Balcony</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="18" data-tab="floor2">
        <img src="images/floor2/IMG_5862.jpg" alt="Floor 2 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="19" data-tab="floor2">
        <img src="images/floor2/IMG_5864.jpg" alt="Floor 2 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="20" data-tab="floor2">
        <img src="images/floor2/IMG_5866.jpg" alt="Floor 2 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="21" data-tab="floor2">
        <img src="images/floor2/IMG_5868.jpg" alt="Floor 2 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="22" data-tab="floor2">
        <img src="images/floor2/IMG_5895.jpg" alt="Floor 2 · Front Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Front Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="23" data-tab="floor2">
        <img src="images/floor2/IMG_5896.jpg" alt="Floor 2 · Front Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Front Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
</div>

<div class="gallery-panel" id="panel-floor1" style="display:none;">
  <div class="gallery-room-group">
    <div class="gallery-room-label">Living Room</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="0" data-tab="floor1">
        <img src="images/floor1/IMG_5923.jpg" alt="Floor 1 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="1" data-tab="floor1">
        <img src="images/floor1/IMG_5926.jpg" alt="Floor 1 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="2" data-tab="floor1">
        <img src="images/floor1/IMG_5928.jpg" alt="Floor 1 · Living Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Living Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="3" data-tab="floor1">
        <img src="images/floor1/IMG_5957.jpg" alt="Floor 1 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="4" data-tab="floor1">
        <img src="images/floor1/IMG_7778.jpg" alt="Floor 1 · Living Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Living Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Bedroom</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="5" data-tab="floor1">
        <img src="images/floor1/IMG_5910.jpg" alt="Floor 1 · Bedroom" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bedroom</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="6" data-tab="floor1">
        <img src="images/floor1/IMG_5912.jpg" alt="Floor 1 · Bedroom" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bedroom</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="7" data-tab="floor1">
        <img src="images/floor1/IMG_5918.jpg" alt="Floor 1 · Bedroom" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bedroom</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="8" data-tab="floor1">
        <img src="images/floor1/IMG_5922.jpg" alt="Floor 1 · Bedroom" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bedroom</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Dining & TV</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="9" data-tab="floor1">
        <img src="images/floor1/IMG_5934.jpg" alt="Floor 1 · Dining & TV Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Dining & TV Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="10" data-tab="floor1">
        <img src="images/floor1/IMG_5937.jpg" alt="Floor 1 · Dining Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Dining Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="11" data-tab="floor1">
        <img src="images/floor1/IMG_5939.jpg" alt="Floor 1 · Dining Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Dining Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Kitchen</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="12" data-tab="floor1">
        <img src="images/floor1/IMG_7797.jpg" alt="Floor 1 · Kitchen" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Kitchen</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Bathroom</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="13" data-tab="floor1">
        <img src="images/floor1/IMG_7820.jpg" alt="Floor 1 · Bathroom with Bathtub" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bathroom with Bathtub</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="14" data-tab="floor1">
        <img src="images/floor1/IMG_7826.jpg" alt="Floor 1 · Bathroom 2" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bathroom 2</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Balcony</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="15" data-tab="floor1">
        <img src="images/floor1/IMG_5940.jpg" alt="Floor 1 · Decor & Garden" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Decor & Garden</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="16" data-tab="floor1">
        <img src="images/floor1/IMG_5941.jpg" alt="Floor 1 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="17" data-tab="floor1">
        <img src="images/floor1/IMG_5942.jpg" alt="Floor 1 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="18" data-tab="floor1">
        <img src="images/floor1/IMG_5951.jpg" alt="Floor 1 · Balcony & Garden" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Balcony & Garden</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="19" data-tab="floor1">
        <img src="images/floor1/IMG_5961.jpg" alt="Floor 1 · Front Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Front Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="20" data-tab="floor1">
        <img src="images/floor1/IMG_5964.jpg" alt="Floor 1 · Front Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Front Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
</div>

<div class="gallery-panel" id="panel-all" style="display:none;">
  <div class="gallery-room-group">
    <div class="gallery-room-label">Living Room</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="0" data-tab="all">
        <img src="images/floor2/IMG_5843.jpg" alt="Floor 2 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="1" data-tab="all">
        <img src="images/floor2/IMG_5844.jpg" alt="Floor 2 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="2" data-tab="all">
        <img src="images/floor2/IMG_5845.jpg" alt="Floor 2 · Living & Dining Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living & Dining Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="3" data-tab="all">
        <img src="images/floor2/IMG_5848.jpg" alt="Floor 2 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="4" data-tab="all">
        <img src="images/floor2/IMG_5849.jpg" alt="Floor 2 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="5" data-tab="all">
        <img src="images/floor2/IMG_5851.jpg" alt="Floor 2 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="6" data-tab="all">
        <img src="images/floor2/IMG_5854.jpg" alt="Floor 2 · Living Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="7" data-tab="all">
        <img src="images/floor2/IMG_5855.jpg" alt="Floor 2 · Living Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Living Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="8" data-tab="all">
        <img src="images/floor2/IMG_5856.jpg" alt="Floor 2 · Dining Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Dining Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="9" data-tab="all">
        <img src="images/floor2/IMG_5860.jpg" alt="Floor 2 · TV Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · TV Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="24" data-tab="all">
        <img src="images/floor1/IMG_5923.jpg" alt="Floor 1 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="25" data-tab="all">
        <img src="images/floor1/IMG_5926.jpg" alt="Floor 1 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="26" data-tab="all">
        <img src="images/floor1/IMG_5928.jpg" alt="Floor 1 · Living Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Living Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="27" data-tab="all">
        <img src="images/floor1/IMG_5957.jpg" alt="Floor 1 · Living Room" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Living Room</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="28" data-tab="all">
        <img src="images/floor1/IMG_7778.jpg" alt="Floor 1 · Living Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Living Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Bedroom</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="10" data-tab="all">
        <img src="images/floor2/IMG_5878.jpg" alt="Floor 2 · Bedroom with Baby Cot" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bedroom with Baby Cot</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="11" data-tab="all">
        <img src="images/floor2/IMG_5880.jpg" alt="Floor 2 · Bedroom with Cot" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bedroom with Cot</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="12" data-tab="all">
        <img src="images/floor2/IMG_5883.jpg" alt="Floor 2 · Bedroom 2" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bedroom 2</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="13" data-tab="all">
        <img src="images/floor2/IMG_5885.jpg" alt="Floor 2 · Bedroom 2" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bedroom 2</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="29" data-tab="all">
        <img src="images/floor1/IMG_5910.jpg" alt="Floor 1 · Bedroom" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bedroom</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="30" data-tab="all">
        <img src="images/floor1/IMG_5912.jpg" alt="Floor 1 · Bedroom" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bedroom</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="31" data-tab="all">
        <img src="images/floor1/IMG_5918.jpg" alt="Floor 1 · Bedroom" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bedroom</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="32" data-tab="all">
        <img src="images/floor1/IMG_5922.jpg" alt="Floor 1 · Bedroom" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bedroom</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Kitchen</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="14" data-tab="all">
        <img src="images/floor2/IMG_5888.jpg" alt="Floor 2 · Kitchen" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Kitchen</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="15" data-tab="all">
        <img src="images/floor2/IMG_5893.jpg" alt="Floor 2 · Kitchen" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Kitchen</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="36" data-tab="all">
        <img src="images/floor1/IMG_7797.jpg" alt="Floor 1 · Kitchen" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Kitchen</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Bathroom</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="16" data-tab="all">
        <img src="images/floor2/IMG_5870.jpg" alt="Floor 2 · Bathroom & Shower" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bathroom & Shower</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="17" data-tab="all">
        <img src="images/floor2/IMG_5873.jpg" alt="Floor 2 · Bathroom" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Bathroom</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="37" data-tab="all">
        <img src="images/floor1/IMG_7820.jpg" alt="Floor 1 · Bathroom with Bathtub" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bathroom with Bathtub</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="38" data-tab="all">
        <img src="images/floor1/IMG_7826.jpg" alt="Floor 1 · Bathroom 2" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Bathroom 2</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Balcony</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="18" data-tab="all">
        <img src="images/floor2/IMG_5862.jpg" alt="Floor 2 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="19" data-tab="all">
        <img src="images/floor2/IMG_5864.jpg" alt="Floor 2 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="20" data-tab="all">
        <img src="images/floor2/IMG_5866.jpg" alt="Floor 2 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="21" data-tab="all">
        <img src="images/floor2/IMG_5868.jpg" alt="Floor 2 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="22" data-tab="all">
        <img src="images/floor2/IMG_5895.jpg" alt="Floor 2 · Front Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Front Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="23" data-tab="all">
        <img src="images/floor2/IMG_5896.jpg" alt="Floor 2 · Front Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 2 · Front Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="39" data-tab="all">
        <img src="images/floor1/IMG_5940.jpg" alt="Floor 1 · Decor & Garden" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Decor & Garden</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="40" data-tab="all">
        <img src="images/floor1/IMG_5941.jpg" alt="Floor 1 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="41" data-tab="all">
        <img src="images/floor1/IMG_5942.jpg" alt="Floor 1 · Back Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Back Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="42" data-tab="all">
        <img src="images/floor1/IMG_5951.jpg" alt="Floor 1 · Balcony & Garden" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Balcony & Garden</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="43" data-tab="all">
        <img src="images/floor1/IMG_5961.jpg" alt="Floor 1 · Front Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Front Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="44" data-tab="all">
        <img src="images/floor1/IMG_5964.jpg" alt="Floor 1 · Front Balcony" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Front Balcony</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
  <div class="gallery-room-group">
    <div class="gallery-room-label">Dining & TV</div>
    <div class="gallery-grid">
      <div class="gallery-item" data-index="33" data-tab="all">
        <img src="images/floor1/IMG_5934.jpg" alt="Floor 1 · Dining & TV Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Dining & TV Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="34" data-tab="all">
        <img src="images/floor1/IMG_5937.jpg" alt="Floor 1 · Dining Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Dining Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
      <div class="gallery-item" data-index="35" data-tab="all">
        <img src="images/floor1/IMG_5939.jpg" alt="Floor 1 · Dining Area" loading="lazy">
        <div class="gallery-overlay"><span class="gallery-label">Floor 1 · Dining Area</span><span class="gallery-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span></div>
      </div>
    </div>
  </div>
</div>

<!-- CTA STRIP -->
<div class="gallery-cta-strip">
  <div class="container gallery-cta-inner">
    <div>
      <h3>Like what you see?</h3>
      <p>Both floors are available to book independently or together.</p>
    </div>
    <div class="gallery-cta-btns">
      <a href="book.php" class="btn btn-gold">Book Now</a>
    </div>
  </div>
</div>

<!-- LIGHTBOX -->
<div class="lightbox-overlay" id="lightbox" role="dialog" aria-modal="true" aria-label="Photo lightbox">
  <button class="lightbox-close" id="lb-close" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
  <button class="lightbox-nav prev" id="lb-prev" aria-label="Previous photo"><i class="fa-solid fa-chevron-left"></i></button>
  <div class="lb-img-wrap">
    <img src="" alt="" id="lb-img">
    <div class="lb-caption" id="lb-caption"></div>
  </div>
  <button class="lightbox-nav next" id="lb-next" aria-label="Next photo"><i class="fa-solid fa-chevron-right"></i></button>
  <div class="lightbox-counter" id="lb-counter"></div>
</div>

<!-- Pass image data to JS -->
<script>
window.GALLERY_DATA = {"floor2": [{"src": "images/floor2/IMG_5843.jpg", "label": "Floor 2 \u00b7 Living Room"}, {"src": "images/floor2/IMG_5844.jpg", "label": "Floor 2 \u00b7 Living Room"}, {"src": "images/floor2/IMG_5845.jpg", "label": "Floor 2 \u00b7 Living & Dining Area"}, {"src": "images/floor2/IMG_5848.jpg", "label": "Floor 2 \u00b7 Living Room"}, {"src": "images/floor2/IMG_5849.jpg", "label": "Floor 2 \u00b7 Living Room"}, {"src": "images/floor2/IMG_5851.jpg", "label": "Floor 2 \u00b7 Living Room"}, {"src": "images/floor2/IMG_5854.jpg", "label": "Floor 2 \u00b7 Living Area"}, {"src": "images/floor2/IMG_5855.jpg", "label": "Floor 2 \u00b7 Living Area"}, {"src": "images/floor2/IMG_5856.jpg", "label": "Floor 2 \u00b7 Dining Area"}, {"src": "images/floor2/IMG_5860.jpg", "label": "Floor 2 \u00b7 TV Area"}, {"src": "images/floor2/IMG_5878.jpg", "label": "Floor 2 \u00b7 Bedroom with Baby Cot"}, {"src": "images/floor2/IMG_5880.jpg", "label": "Floor 2 \u00b7 Bedroom with Cot"}, {"src": "images/floor2/IMG_5883.jpg", "label": "Floor 2 \u00b7 Bedroom 2"}, {"src": "images/floor2/IMG_5885.jpg", "label": "Floor 2 \u00b7 Bedroom 2"}, {"src": "images/floor2/IMG_5888.jpg", "label": "Floor 2 \u00b7 Kitchen"}, {"src": "images/floor2/IMG_5893.jpg", "label": "Floor 2 \u00b7 Kitchen"}, {"src": "images/floor2/IMG_5870.jpg", "label": "Floor 2 \u00b7 Bathroom & Shower"}, {"src": "images/floor2/IMG_5873.jpg", "label": "Floor 2 \u00b7 Bathroom"}, {"src": "images/floor2/IMG_5862.jpg", "label": "Floor 2 \u00b7 Back Balcony"}, {"src": "images/floor2/IMG_5864.jpg", "label": "Floor 2 \u00b7 Back Balcony"}, {"src": "images/floor2/IMG_5866.jpg", "label": "Floor 2 \u00b7 Back Balcony"}, {"src": "images/floor2/IMG_5868.jpg", "label": "Floor 2 \u00b7 Back Balcony"}, {"src": "images/floor2/IMG_5895.jpg", "label": "Floor 2 \u00b7 Front Balcony"}, {"src": "images/floor2/IMG_5896.jpg", "label": "Floor 2 \u00b7 Front Balcony"}], "floor1": [{"src": "images/floor1/IMG_5923.jpg", "label": "Floor 1 \u00b7 Living Room"}, {"src": "images/floor1/IMG_5926.jpg", "label": "Floor 1 \u00b7 Living Room"}, {"src": "images/floor1/IMG_5928.jpg", "label": "Floor 1 \u00b7 Living Area"}, {"src": "images/floor1/IMG_5957.jpg", "label": "Floor 1 \u00b7 Living Room"}, {"src": "images/floor1/IMG_7778.jpg", "label": "Floor 1 \u00b7 Living Area"}, {"src": "images/floor1/IMG_5910.jpg", "label": "Floor 1 \u00b7 Bedroom"}, {"src": "images/floor1/IMG_5912.jpg", "label": "Floor 1 \u00b7 Bedroom"}, {"src": "images/floor1/IMG_5918.jpg", "label": "Floor 1 \u00b7 Bedroom"}, {"src": "images/floor1/IMG_5922.jpg", "label": "Floor 1 \u00b7 Bedroom"}, {"src": "images/floor1/IMG_5934.jpg", "label": "Floor 1 \u00b7 Dining & TV Area"}, {"src": "images/floor1/IMG_5937.jpg", "label": "Floor 1 \u00b7 Dining Area"}, {"src": "images/floor1/IMG_5939.jpg", "label": "Floor 1 \u00b7 Dining Area"}, {"src": "images/floor1/IMG_7797.jpg", "label": "Floor 1 \u00b7 Kitchen"}, {"src": "images/floor1/IMG_7820.jpg", "label": "Floor 1 \u00b7 Bathroom with Bathtub"}, {"src": "images/floor1/IMG_7826.jpg", "label": "Floor 1 \u00b7 Bathroom 2"}, {"src": "images/floor1/IMG_5940.jpg", "label": "Floor 1 \u00b7 Decor & Garden"}, {"src": "images/floor1/IMG_5941.jpg", "label": "Floor 1 \u00b7 Back Balcony"}, {"src": "images/floor1/IMG_5942.jpg", "label": "Floor 1 \u00b7 Back Balcony"}, {"src": "images/floor1/IMG_5951.jpg", "label": "Floor 1 \u00b7 Balcony & Garden"}, {"src": "images/floor1/IMG_5961.jpg", "label": "Floor 1 \u00b7 Front Balcony"}, {"src": "images/floor1/IMG_5964.jpg", "label": "Floor 1 \u00b7 Front Balcony"}], "all": [{"src": "images/floor2/IMG_5843.jpg", "label": "Floor 2 \u00b7 Living Room"}, {"src": "images/floor2/IMG_5844.jpg", "label": "Floor 2 \u00b7 Living Room"}, {"src": "images/floor2/IMG_5845.jpg", "label": "Floor 2 \u00b7 Living & Dining Area"}, {"src": "images/floor2/IMG_5848.jpg", "label": "Floor 2 \u00b7 Living Room"}, {"src": "images/floor2/IMG_5849.jpg", "label": "Floor 2 \u00b7 Living Room"}, {"src": "images/floor2/IMG_5851.jpg", "label": "Floor 2 \u00b7 Living Room"}, {"src": "images/floor2/IMG_5854.jpg", "label": "Floor 2 \u00b7 Living Area"}, {"src": "images/floor2/IMG_5855.jpg", "label": "Floor 2 \u00b7 Living Area"}, {"src": "images/floor2/IMG_5856.jpg", "label": "Floor 2 \u00b7 Dining Area"}, {"src": "images/floor2/IMG_5860.jpg", "label": "Floor 2 \u00b7 TV Area"}, {"src": "images/floor2/IMG_5878.jpg", "label": "Floor 2 \u00b7 Bedroom with Baby Cot"}, {"src": "images/floor2/IMG_5880.jpg", "label": "Floor 2 \u00b7 Bedroom with Cot"}, {"src": "images/floor2/IMG_5883.jpg", "label": "Floor 2 \u00b7 Bedroom 2"}, {"src": "images/floor2/IMG_5885.jpg", "label": "Floor 2 \u00b7 Bedroom 2"}, {"src": "images/floor2/IMG_5888.jpg", "label": "Floor 2 \u00b7 Kitchen"}, {"src": "images/floor2/IMG_5893.jpg", "label": "Floor 2 \u00b7 Kitchen"}, {"src": "images/floor2/IMG_5870.jpg", "label": "Floor 2 \u00b7 Bathroom & Shower"}, {"src": "images/floor2/IMG_5873.jpg", "label": "Floor 2 \u00b7 Bathroom"}, {"src": "images/floor2/IMG_5862.jpg", "label": "Floor 2 \u00b7 Back Balcony"}, {"src": "images/floor2/IMG_5864.jpg", "label": "Floor 2 \u00b7 Back Balcony"}, {"src": "images/floor2/IMG_5866.jpg", "label": "Floor 2 \u00b7 Back Balcony"}, {"src": "images/floor2/IMG_5868.jpg", "label": "Floor 2 \u00b7 Back Balcony"}, {"src": "images/floor2/IMG_5895.jpg", "label": "Floor 2 \u00b7 Front Balcony"}, {"src": "images/floor2/IMG_5896.jpg", "label": "Floor 2 \u00b7 Front Balcony"}, {"src": "images/floor1/IMG_5923.jpg", "label": "Floor 1 \u00b7 Living Room"}, {"src": "images/floor1/IMG_5926.jpg", "label": "Floor 1 \u00b7 Living Room"}, {"src": "images/floor1/IMG_5928.jpg", "label": "Floor 1 \u00b7 Living Area"}, {"src": "images/floor1/IMG_5957.jpg", "label": "Floor 1 \u00b7 Living Room"}, {"src": "images/floor1/IMG_7778.jpg", "label": "Floor 1 \u00b7 Living Area"}, {"src": "images/floor1/IMG_5910.jpg", "label": "Floor 1 \u00b7 Bedroom"}, {"src": "images/floor1/IMG_5912.jpg", "label": "Floor 1 \u00b7 Bedroom"}, {"src": "images/floor1/IMG_5918.jpg", "label": "Floor 1 \u00b7 Bedroom"}, {"src": "images/floor1/IMG_5922.jpg", "label": "Floor 1 \u00b7 Bedroom"}, {"src": "images/floor1/IMG_5934.jpg", "label": "Floor 1 \u00b7 Dining & TV Area"}, {"src": "images/floor1/IMG_5937.jpg", "label": "Floor 1 \u00b7 Dining Area"}, {"src": "images/floor1/IMG_5939.jpg", "label": "Floor 1 \u00b7 Dining Area"}, {"src": "images/floor1/IMG_7797.jpg", "label": "Floor 1 \u00b7 Kitchen"}, {"src": "images/floor1/IMG_7820.jpg", "label": "Floor 1 \u00b7 Bathroom with Bathtub"}, {"src": "images/floor1/IMG_7826.jpg", "label": "Floor 1 \u00b7 Bathroom 2"}, {"src": "images/floor1/IMG_5940.jpg", "label": "Floor 1 \u00b7 Decor & Garden"}, {"src": "images/floor1/IMG_5941.jpg", "label": "Floor 1 \u00b7 Back Balcony"}, {"src": "images/floor1/IMG_5942.jpg", "label": "Floor 1 \u00b7 Back Balcony"}, {"src": "images/floor1/IMG_5951.jpg", "label": "Floor 1 \u00b7 Balcony & Garden"}, {"src": "images/floor1/IMG_5961.jpg", "label": "Floor 1 \u00b7 Front Balcony"}, {"src": "images/floor1/IMG_5964.jpg", "label": "Floor 1 \u00b7 Front Balcony"}]};
</script>

<?php include __DIR__ . '/includes/foot.php'; ?>
