<?php

/**
 * VIEW: Menu Index
 * Updated to include Cart & Info Tooltips
 */

// 1. CONFIGURATION ARRAYS
$display_titles = [
    'Meats'     => 'MEATS',
    'Seafood'   => 'SEAFOOD',
    'Sides'     => 'SIDES',
    'Beverages' => 'BEVERAGES',
    'Wines'     => 'WINES',
    'Spirits'   => 'SPIRITS',
    'Desserts'  => 'DESSERTS'
];

$category_images = [
    'Meats'     => 'img/meats-section-logo.png',
    'Seafood'   => 'img/seafood-section-logo.png',
    'Sides'     => 'img/sides-section-logo.png',
    'Beverages' => 'img/beverages-section-logo.png',
    'Wines'     => 'img/wines-section-logo.png',
    'Spirits'   => 'img/spirits-section-logo.png',
    'Desserts'  => 'img/desserts-section-logo.png'
];

$section_descriptions = [
    'Meats'     => 'Our commitment to the mastery of meat and flame, expertly sourced and prepared.',
    'Seafood'   => 'Fresh from the ocean, featuring sustainable catches and pristine shellfish.',
    'Sides'     => 'The perfect accompaniments, crafted to elevate your main course.',
    'Beverages' => 'Refreshing non-alcoholic options, from artisanal sodas to fresh juices.',
    'Wines'     => 'A curated cellar selection featuring bold reds and crisp whites.',
    'Spirits'   => 'Small-batch bourbons, aged gins, and premium whiskies.',
    'Desserts'  => 'Decadent finales to complete your dining experience.'
];

// 2. ORGANIZE DATA
$menu_items = [];
if (isset($allProducts)) {
    foreach ($allProducts as $product) {
        $type = $product->getProductType();
        $menu_items[$type][] = $product;
    }
}
?>

<div class="container mt-4 mb-4">
    <span class="breadcrumbs-text text-uppercase">
        <a href="/" class="text-decoration-none text-muted">HOME</a> <span class="mx-2">›</span> OUR FULL MENU
    </span>
</div>

<section class="menu-cat-nav-bar py-5 mb-5" style="background-color: #EFEBE6;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-start gap-4 overflow-auto pb-3" style="scrollbar-width: none;">
            <?php foreach ($display_titles as $db_type => $title):
                if (isset($category_images[$db_type])):
                    $img_path = '/DAW2/thehearth/public/' . $category_images[$db_type];
                    $custom_style = ($db_type === 'Seafood') ? 'padding: 15px;' : '';
            ?>
                    <a href="#section-<?= $db_type ?>" class="cat-nav-item text-decoration-none text-center">
                        <div class="cat-thumb mb-3">
                            <img src="<?= $img_path ?>" alt="<?= $title ?>" style="<?= $custom_style ?>">
                        </div>
                        <span class="cat-title"><?= $title ?></span>
                    </a>
            <?php endif;
            endforeach; ?>
        </div>
    </div>
</section>

<?php
$active_sections = [];
foreach ($display_titles as $type => $title) {
    if (!empty($menu_items[$type])) {
        $active_sections[] = $type;
    }
}

foreach ($active_sections as $index => $type):
    $title = $display_titles[$type];
    $items = $menu_items[$type];
?>

    <div class="container mb-5" id="section-<?= $type ?>">

        <div class="row align-items-end mb-5">
            <div class="col-md-6">
                <h1 class="section-main-title">
                    <?= ($type === 'Meats') ? 'Our Meats' : $title ?>
                </h1>
            </div>
            <div class="col-md-6 text-md-end text-start">
                <p class="section-description text-muted">
                    <?= $section_descriptions[$type] ?? '' ?>
                </p>
            </div>
        </div>

        <div class="row">
            <?php foreach ($items as $item): ?>
                <div class="col-lg-4 col-md-6 mb-5">
                    <div class="menu-product-card h-100 position-relative">

                        <div class="menu-img-wrapper mb-3 product-image-wrapper">
                            <img src="/DAW2/thehearth/public/<?= $item->getImage() ?>" alt="<?= $item->getName() ?>">

                            <button type="button" class="info-icon-btn"
                                data-bs-toggle="tooltip"
                                data-bs-placement="left"
                                data-bs-html="true"
                                title="<strong>Description:</strong><br><?= htmlspecialchars($item->getDescription()) ?>">
                                i
                            </button>
                        </div>

                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h3 class="product-name mb-0 lh-1"><?= $item->getName() ?></h3>
                            <span class="fw-bold fs-5 text-nowrap ms-2 lh-1 product-price" data-usd="<?= $item->getBasePrice() ?>">
                                $<?= number_format($item->getBasePrice(), 2) ?>
                            </span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-3">

                            <div class="quantity-pill">
                                <button class="qty-btn" onclick="updateQty(<?= $item->getProductId() ?>, -1)">-</button>
                                <span class="qty-display" id="qty-<?= $item->getProductId() ?>">0</span>
                                <button class="qty-btn" onclick="updateQty(<?= $item->getProductId() ?>, 1)">+</button>
                            </div>

                            <button class="btn btn-sm btn-hearth-add"
                                onclick="if(checkAuthAndRedirect()) { addToCart(<?= $item->getProductId() ?>); }">
                                ADD
                            </button>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <?php if ($index < count($active_sections) - 1): ?>
        <div class="hearth-section-divider"></div>
    <?php endif; ?>

<?php endforeach; ?>

<div style="height: 50px;"></div>