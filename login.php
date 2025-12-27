<?php
// 1. CHECK IF ALREADY LOGGED IN
session_start();

if (isset($_SESSION['user_id'])) {
    // If user is already logged in, go to Home
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Coders' Journey</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh; /* Centers vertically */
        }
        .auth-card {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 1px solid #e1e4e8;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand">
            <a href="index.php">Coders' Journey</a>
        </div>
        <div class="nav-links">
            <a href="roadmaps.php">Roadmaps</a>
        </div>
        <div class="nav-auth">
            <a href="register.php" class="btn-register">Sign Up</a>
        </div>
    </nav>


    <div class="container auth-container">
        
        <div class="auth-card">
            <h2 style="text-align: center; margin-bottom: 1rem;">Welcome Back</h2>
            <p style="text-align: center; color: #666; margin-bottom: 2rem;">
                Log in to track your progress and answer questions.
            </p>

            <?php if(isset($_GET['error'])): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; font-size: 0.9rem;">
                    <?php 
                        if($_GET['error'] == 'wrong_login') echo "❌ Incorrect email or password.";
                        if($_GET['error'] == 'unverified') echo "⚠️ Please verify your email first.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; font-size: 0.9rem;">
                    ✅ Registration successful! Please log in.
                </div>
            <?php endif; ?>


            <form action="actions/login_logic.php" method="POST">
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="email" style="display:block; font-weight:600; margin-bottom: 5px;">Email</label>
                    <input type="email" id="email" name="email" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <label for="password" style="font-weight:600; margin-bottom: 5px;">Password</label>
                        <a href="#" style="font-size: 0.85rem; color: #4a90e2; text-decoration: none;">Forgot password?</a>
                    </div>
                    <input type="password" id="password" name="password" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1rem; font-weight: bold;">
                    Log In
                </button>

            </form>

            <div style="margin-top: 2rem; text-align: center; font-size: 0.9rem;">
                <p>Don't have an account? <a href="register.php" style="color: #4a90e2; font-weight: bold;">Sign Up</a></p>
            </div>

        </div>

    </div>

</body>
</html>