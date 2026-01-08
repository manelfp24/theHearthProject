<?php 
// Cargamos la bbdd
require_once __DIR__ . '/../../config/Database.php';

// conectamos a bbdd
$conn = Database::connect();

// cogemos los signature cuts de la bbdd
$sql_meats = "SELECT * FROM product WHERE product_type = 'Meats' AND is_featured = 1";
$result_meats = $conn->query($sql_meats);

// cogemos bebidas de la bbdd
$sql_drinks = "SELECT * FROM product WHERE product_type IN ('Spirits', 'Wines') AND is_featured = 1";
$result_drinks = $conn->query($sql_drinks);
// include __DIR__ . '/../layouts/header.php'; 
// include __DIR__ . '/../layouts/navbar.php'; 
?>
<!-- BANNER SECTION -->
<section class="hero-banner" style="background-image: url('img/banner1.png');">

    <div class="overlay"></div>

    <div class="banner-content">
        <span class="subheading">DISCOVER</span>
        <h1>Where Fire Meets Flavour</h1>
        <a href="/order" class="cta-button">START ORDER</a>
    </div>

    <div class="carousel-indicators-custom">
        <span class="dot active">1</span>
        <span class="dot">2</span>
        <span class="dot">3</span>
    </div>
    <!-- OUR SIGNATURE CUTS SECTION -->
</section>
<section class="signature-section">
    <div class="container">

        <span class="section-subheading">DISCOVER</span>
        <h2 class="section-title">Our Signature Cuts</h2>

        <div class="hearth-divider"></div>

        <p class="section-text">
            From our <span class="text-underline">dry-aged heritage cuts</span> expertly sourced from local ranches, to our premium global selections, each dish tells a story of the land and the fire. Every plate reveals our unrivaled commitment to the mastery of meat and flame, honoring the rustic elegance of The Hearth.
        </p>

        <a href="index.php?controller=Product" class="btn-outline-black">VIEW THE FULL MENU</a>

    </div>
    <div class="cuts-carousel-container">
    
    <div class="cuts-track" id="cutsTrack">
        
        <?php if ($result_meats && $result_meats->num_rows > 0): ?>
            <?php while($row = $result_meats->fetch_assoc()): ?>
                
                <div class="cut-card">
                    <div class="img-wrapper">
                        <img src="/DAW2/thehearth/public/<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
                    </div>
                    <h3><?= $row['name'] ?></h3>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <p>No signature cuts available at the moment.</p>
        <?php endif; ?>

    </div>

    <div class="carousel-nav-container">
        <div class="carousel-nav-pill">
            <button class="nav-arrow-btn" onclick="scrollCuts('left', 'cutsTrack')">
                <svg width="12" height="20" viewBox="0 0 12 20" fill="none" stroke="black" stroke-width="2"><path d="M10 2L2 10L10 18"/></svg>
            </button>
            <button class="nav-arrow-btn" onclick="scrollCuts('right', 'cutsTrack')">
                <svg width="12" height="20" viewBox="0 0 12 20" fill="none" stroke="black" stroke-width="2"><path d="M2 2L10 10L2 18"/></svg>
            </button>
        </div>
    </div>

</div>
</section>
<!-- BOOK YOUR TABLE SECTION -->
<section class="experience-banner" style="background-image: url('img/booktable.png');">
    
    <div class="overlay"></div>

    <div class="container position-relative" style="z-index: 2; height: 100%; display: flex; align-items: flex-end;">
        <div class="experience-content">
            <span class="subheading">EXPERIENCE</span>
            <h2>Crafting Moments, One Dish at a Time</h2>
            <a href="/reservations" class="cta-button">BOOK YOUR TABLE</a>
        </div>
    </div>

</section>
 <!-- OUR CRAFTED SPIRITS -->
</section>
<section class="signature-section">
    <div class="container">

        <span class="section-subheading">DISCOVER</span>
        <h2 class="section-title">Our Crafted Spirits</h2>

        <div class="hearth-divider"></div>

        <p class="section-text">
            From the robust character of our <span class="text-underline">single barrel whiskey</span>,
            aged in charred oak, to the elegant notes of our estate <span class="text-underline">reserve wines</span>,
            each bottle is a testament to our passion. Discover the spirit of The Hearth, meticulously distilled and 
            aged to perfection. Explore our full collection and find your perfect complement.
        </p>

        <a href="index.php?controller=Product" class="btn-outline-black">EXPLORE OUR FULL SELECTION</a>

    </div>
    <div class="cuts-carousel-container">
    
    <div class="cuts-track" id="drinksTrack">
        
        <?php if ($result_drinks && $result_drinks->num_rows > 0): ?>
            <?php while($drink = $result_drinks->fetch_assoc()): ?>
                
                <div class="cut-card">
                    <div class="img-wrapper">
                        <img src="/DAW2/thehearth/public/<?= $drink['image'] ?>" alt="<?= $drink['name'] ?>">
                    </div>
                    <h3><?= $drink['name'] ?></h3>
                </div>

            <?php endwhile; ?>
        <?php endif; ?>

    </div>

    <div class="carousel-nav-container">
        <div class="carousel-nav-pill">
            <button class="nav-arrow-btn" onclick="scrollCuts('left', 'drinksTrack')">
                <svg width="12" height="20" viewBox="0 0 12 20" fill="none" stroke="black" stroke-width="2"><path d="M10 2L2 10L10 18"/></svg>
            </button>
            <button class="nav-arrow-btn" onclick="scrollCuts('right', 'drinksTrack')">
                <svg width="12" height="20" viewBox="0 0 12 20" fill="none" stroke="black" stroke-width="2"><path d="M2 2L10 10L2 18"/></svg>
            </button>
        </div>
    </div>

</div>
</section>

<?php
// 3. Close the page
// include __DIR__ . '/../layouts/footer.php';
?>