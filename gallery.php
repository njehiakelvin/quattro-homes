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
  ['src'=>'images/floor2/IMG_5851.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5845.jpg','label'=>'Floor 2 · Living & Dining Area'],
  ['src'=>'images/floor2/IMG_5856.jpg','label'=>'Floor 2 · Dining Area'],
  ['src'=>'images/floor2/IMG_5860.jpg','label'=>'Floor 2 · TV Area'],
  ['src'=>'images/floor2/IMG_5878.jpg','label'=>'Floor 2 · Bedroom with Baby Cot'],
  ['src'=>'images/floor2/IMG_5883.jpg','label'=>'Floor 2 · Bedroom 2'],
  ['src'=>'images/floor2/IMG_5885.jpg','label'=>'Floor 2 · Bedroom 2'],
  ['src'=>'images/floor2/IMG_5893.jpg','label'=>'Floor 2 · Kitchen'],
  ['src'=>'images/floor2/IMG_5862.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5844.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5848.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5849.jpg','label'=>'Floor 2 · Living Room'],
  ['src'=>'images/floor2/IMG_5854.jpg','label'=>'Floor 2 · Living Area'],
  ['src'=>'images/floor2/IMG_5855.jpg','label'=>'Floor 2 · Living Area'],
  ['src'=>'images/floor2/IMG_5864.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5866.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5868.jpg','label'=>'Floor 2 · Back Balcony'],
  ['src'=>'images/floor2/IMG_5870.jpg','label'=>'Floor 2 · Bathroom & Shower'],
  ['src'=>'images/floor2/IMG_5873.jpg','label'=>'Floor 2 · Bathroom'],
  ['src'=>'images/floor2/IMG_5880.jpg','label'=>'Floor 2 · Bedroom with Cot'],
  ['src'=>'images/floor2/IMG_5888.jpg','label'=>'Floor 2 · Kitchen'],
  ['src'=>'images/floor2/IMG_5895.jpg','label'=>'Floor 2 · Front Balcony'],
  ['src'=>'images/floor2/IMG_5896.jpg','label'=>'Floor 2 · Front Balcony'],
];

$floor1 = [
  ['src'=>'images/floor1/IMG_5923.jpg','label'=>'Floor 1 · Living Room'],
  ['src'=>'images/floor1/IMG_5926.jpg','label'=>'Floor 1 · Living Room'],
  ['src'=>'images/floor1/IMG_5912.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5918.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5934.jpg','label'=>'Floor 1 · Dining & TV Area'],
  ['src'=>'images/floor1/IMG_5939.jpg','label'=>'Floor 1 · Dining Area'],
  ['src'=>'images/floor1/IMG_5951.jpg','label'=>'Floor 1 · Balcony & Garden'],
  ['src'=>'images/floor1/IMG_5940.jpg','label'=>'Floor 1 · Decor & Flower Pots'],
  ['src'=>'images/floor1/IMG_5961.jpg','label'=>'Floor 1 · Front Balcony'],
  ['src'=>'images/floor1/IMG_5964.jpg','label'=>'Floor 1 · Front Balcony'],
  ['src'=>'images/floor1/IMG_5910.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5922.jpg','label'=>'Floor 1 · Bedroom'],
  ['src'=>'images/floor1/IMG_5928.jpg','label'=>'Floor 1 · Living Area'],
  ['src'=>'images/floor1/IMG_5937.jpg','label'=>'Floor 1 · Dining Area'],
  ['src'=>'images/floor1/IMG_5941.jpg','label'=>'Floor 1 · Back Balcony'],
  ['src'=>'images/floor1/IMG_5942.jpg','label'=>'Floor 1 · Back Balcony'],
  ['src'=>'images/floor1/IMG_5957.jpg','label'=>'Floor 1 · Living Room'],
  ['src'=>'images/floor1/IMG_7778.jpg','label'=>'Floor 1 · Living Area'],
  ['src'=>'images/floor1/IMG_7797.jpg','label'=>'Floor 1 · Kitchen'],
  ['src'=>'images/floor1/IMG_7820.jpg','label'=>'Floor 1 · Bathroom with Bathtub'],
  ['src'=>'images/floor1/IMG_7826.jpg','label'=>'Floor 1 · Bathroom 2'],
];

// Merge for "All" tab
$allImages = array_merge($floor2, $floor1);
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

<!-- MASONRY PANELS -->
<?php
$panels = [
  'floor2' => $floor2,
  'floor1' => $floor1,
  'all'    => $allImages,
];
foreach ($panels as $tabKey => $images):
?>
<div class="masonry-panel" id="panel-<?php echo $tabKey; ?>"
     style="<?php echo $tabKey !== 'floor2' ? 'display:none;' : ''; ?>">
  <div class="masonry-grid" id="masonry-<?php echo $tabKey; ?>">
    <?php foreach ($images as $i => $img): ?>
    <div class="masonry-item" data-index="<?php echo $i; ?>" data-tab="<?php echo $tabKey; ?>">
      <img src="<?php echo htmlspecialchars($img['src']); ?>"
           alt="<?php echo htmlspecialchars($img['label']); ?>"
           loading="<?php echo $i < 6 ? 'eager' : 'lazy'; ?>">
      <div class="masonry-overlay">
        <span class="masonry-label"><?php echo htmlspecialchars($img['label']); ?></span>
        <span class="masonry-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endforeach; ?>

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
window.GALLERY_DATA = <?php echo json_encode($panels, JSON_HEX_TAG); ?>;
</script>

<?php include __DIR__ . '/includes/foot.php'; ?>
