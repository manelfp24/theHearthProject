<div class="container login-page-container">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <?php // tarjeta contenedora del formulario de acceso ?>
            <div class="card login-card">
                <div class="card-body">
                    
                    <?php // encabezado del formulario de bienvenida ?>
                    <div class="text-center mb-4">
                        <h2 class="login-title">Welcome Back</h2>
                        <p class="text-muted">Please enter your details to sign in.</p>
                    </div>

                    <?php // bloque para mostrar mensajes de error si las credenciales fallan ?>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger text-center border-0 p-2 small">
                            <?= $error_message ?>
                        </div>
                    <?php endif; ?>

                    <?php // formulario de envío de datos al controlador de autenticación ?>
                    <form action="index.php?controller=User&action=authenticate" method="POST">
                        
                        <?php // campo de entrada para el correo electrónico ?>
                        <div class="mb-3">
                            <label for="email" class="form-label login-form-label">Email Address</label>
                            <input type="email" class="form-control login-input" id="email" name="email" required 
                                   placeholder="name@example.com">
                        </div>

                        <?php // campo de entrada para la contraseña protegida ?>
                        <div class="mb-4">
                            <label for="password" class="form-label login-form-label">Password</label>
                            <input type="password" class="form-control login-input" id="password" name="password" required 
                                   placeholder="••••••••">
                        </div>

                        <?php // botón de acción principal para iniciar sesión ?>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn login-btn">
                                Log In
                            </button>
                        </div>

                    </form>
                    
                    <?php // enlace para redirigir a nuevos usuarios a la página de registro ?>
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