<script>
  // This creates a global variable that scripts.js can read
  window.isUserLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>
<nav class="navbar hearth-navbar">
  <div class="container-fluid px-4 position-relative d-flex justify-content-between align-items-center">

    <div class="dropdown">
      <a class="d-flex align-items-center text-decoration-none dropdown-toggle hide-arrow nav-left-trigger"
        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-list fs-3 me-2"></i> <span class="d-none d-md-inline">EXPERIENCE</span> </a>

      <ul class="dropdown-menu">

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
          <li><a class="dropdown-item fw-bold" href="index.php?controller=Admin&action=dashboard">DASHBOARD</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
        <?php endif; ?>

        <li><a class="dropdown-item" href="index.php?controller=Home">Home Page</a></li>
        <li><a class="dropdown-item" href="index.php?controller=Product">Our Menu</a></li>
        <li><a class="dropdown-item" href="/location">Our Location</a></li>
        <li><a class="dropdown-item" href="/contact">Contact</a></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <a class="dropdown-item" href="index.php?controller=Cart" onclick="return checkAuthAndRedirect();">
            Your Cart
          </a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li><a class="dropdown-item" href="index.php?controller=User&action=profile">My Profile</a></li>
          <li><a class="dropdown-item text-danger" href="index.php?controller=User&action=logout">Log Out</a></li>
        <?php else: ?>
          <li><a class="dropdown-item" href="index.php?controller=User&action=login">Log In</a></li>
        <?php endif; ?>
      </ul>
    </div>


    <a href="index.php?controller=Home" class="logo-link">
      <img src="/DAW2/thehearth/public/img/logo.svg" alt="The Hearth Logo" class="logo">
    </a>


    <div class="d-flex align-items-center gap-4 right-icons">

      <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <a href="index.php?controller=Admin&action=dashboard" class="nav-dashboard-link d-none d-md-block">
          Dashboard
        </a>
      <?php endif; ?>

      <div class="dropdown currency-selector">
        <button class="btn btn-sm btn-outline-dark dropdown-toggle border-0 fw-bold currency-btn" type="button" id="currencyBtn" data-bs-toggle="dropdown">
          USD $
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
          <li><a class="dropdown-item small" href="#" onclick="changeCurrency('USD')">USD ($)</a></li>
          <li><a class="dropdown-item small" href="#" onclick="changeCurrency('EUR')">EUR (€)</a></li>
        </ul>
      </div>

      <a href="/search" class="nav-icon-link">
        <svg class="custom-icon" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
          <g fill="currentColor">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M22 15C22 18.866 18.866 22 15 22C11.134 22 8 18.866 8 15C8 11.134 11.134 8 15 8C18.866 8 22 11.134 22 15ZM20 15C20 17.7614 17.7614 20 15 20C12.2386 20 10 17.7614 10 15C10 12.2386 12.2386 10 15 10C17.7614 10 20 12.2386 20 15Z"></path>
            <path d="M24.9641 22.5956C23.8769 21.9679 22.8868 21.1928 21.9684 20.2877L20.5646 21.7123C21.5891 22.7219 22.7127 23.6052 23.9641 24.3277L24.9641 22.5956Z"></path>
          </g>
        </svg>
      </a>

      <a href="index.php?controller=Cart" class="nav-icon-link position-relative" onclick="return checkAuthAndRedirect();">
        <svg class="custom-icon" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
          <path fill-rule="evenodd" d="M12 11v1H8v13h16V12h-4v-1a4 4 0 0 0-8 0Zm4-2a2 2 0 0 0-2 2v5h-2v-2h-2v9h12v-9h-2v2h-2v-2h-2v-2h2v-1a2 2 0 0 0-2-2Z" clip-rule="evenodd"></path>
        </svg>

        <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
          <?= isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0' ?>
        </span>
      </a>

      <?php 
        $userLink = isset($_SESSION['user_id']) 
                    ? 'index.php?controller=User&action=profile' 
                    : 'index.php?controller=User&action=login';
      ?>
      <a href="<?= $userLink ?>" class="nav-icon-link">
        <svg class="custom-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="currentColor">
          <path fill-rule="evenodd" d="M19.274 16.78A5 5 0 1 0 16 18c3.192 0 6 3.004 6 7h2c0-3.585-1.898-6.796-4.726-8.22ZM19 13a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" clip-rule="evenodd"></path>
          <path d="M10 25c0-2.375 1.013-4.441 2.516-5.696l-1.282-1.535C9.25 19.424 8 22.064 8 25h2Z"></path>
        </svg>
      </a>

    </div>

  </div>
</nav>