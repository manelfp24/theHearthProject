<div class="container login-page-container">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="card login-card">
                <div class="card-body">
                    
                    <div class="text-center mb-4">
                        <h2 class="login-title">Create Account</h2>
                        <p class="text-muted">Join The Hearth for an exclusive experience.</p>
                    </div>

                    <form action="index.php?controller=User&action=store" method="POST">
                        
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label login-form-label">First Name</label>
                                <input type="text" class="form-control login-input" name="name" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label login-form-label">Last Name</label>
                                <input type="text" class="form-control login-input" name="last_name" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label login-form-label">Email Address</label>
                            <input type="email" class="form-control login-input" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label login-form-label">Phone Number</label>
                            <input type="tel" class="form-control login-input" name="phone" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label login-form-label">Password</label>
                            <input type="password" class="form-control login-input" name="password" required>
                            <div class="form-text small">Must be at least 4 characters.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn login-btn">Sign Up</button>
                        </div>

                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="small text-muted">
                            Already have an account? 
                            <a href="index.php?controller=User&action=login" class="login-register-link">Log In</a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>