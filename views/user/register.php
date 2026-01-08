<div class="container login-page-container">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <?php // tarjeta contenedora del formulario de creación de cuenta ?>
            <div class="card login-card">
                <div class="card-body">
                    
                    <?php // encabezado con el título de la sección ?>
                    <div class="text-center mb-4">
                        <h2 class="login-title">Create Account</h2>
                        <p class="text-muted">Join The Hearth for an exclusive experience.</p>
                    </div>

                    <?php // formulario que envía los datos al método store del controlador de usuario ?>
                    <form action="index.php?controller=User&action=store" method="POST">
                        
                        <?php // fila para capturar el nombre y apellidos por separado ?>
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

                        <?php // entrada para el correo electrónico del nuevo usuario ?>
                        <div class="mb-3">
                            <label class="form-label login-form-label">Email Address</label>
                            <input type="email" class="form-control login-input" name="email" required>
                        </div>

                        <?php // entrada para el número de teléfono de contacto ?>
                        <div class="mb-3">
                            <label class="form-label login-form-label">Phone Number</label>
                            <input type="tel" class="form-control login-input" name="phone" required>
                        </div>

                        <?php // entrada para la contraseña con validación mínima de longitud ?>
                        <div class="mb-4">
                            <label class="form-label login-form-label">Password</label>
                            <input type="password" class="form-control login-input" name="password" required>
                            <div class="form-text small">Must be at least 4 characters.</div>
                        </div>

                        <?php // botón para procesar el alta del nuevo cliente ?>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn login-btn">Sign Up</button>
                        </div>

                    </form>
                    
                    <?php // enlace para usuarios que ya poseen una cuenta activa ?>
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