<div class="container login-page-container">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="card login-card">
                <div class="card-body">
                    
                    <div class="text-center mb-4">
                        <h2 class="login-title">Welcome Back</h2>
                        <p class="text-muted">Please enter your details to sign in.</p>
                    </div>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger text-center border-0 p-2 small">
                            <?= $error_message ?>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?controller=User&action=authenticate" method="POST">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label login-form-label">Email Address</label>
                            <input type="email" class="form-control login-input" id="email" name="email" required 
                                   placeholder="name@example.com">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label login-form-label">Password</label>
                            <input type="password" class="form-control login-input" id="password" name="password" required 
                                   placeholder="••••••••">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn login-btn">
                                Log In
                            </button>
                        </div>

                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="small text-muted">
                            Don't have an account? 
                            <a href="index.php?controller=User&action=register" class="login-register-link">
                                Create one
                            </a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>