<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=must_login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask a Question - Coders' Journey</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand">
            <a href="index.php">Coders' Journey</a>
        </div>
        
        <div class="nav-links">
            <a href="roadmaps.php">Roadmaps</a>
            <a href="ask.php" class="active">Ask Question</a>
        </div>

        <div class="nav-auth">
            <span style="color:white; margin-right:15px;">Welcome back!</span>
            <a href="logout.php" class="btn-register" style="background:#e74c3c;">Logout</a>
        </div>
    </nav>


    <div class="container main-layout">
        
        <aside class="sidebar-left">
            <div class="sidebar-menu">
                <a href="index.php" class="menu-item">🏠 Home</a>
                <a href="roadmaps.php" class="menu-item">🗺️ Roadmaps</a>
                <a href="tags.php" class="menu-item">🏷️ Tags</a>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h3>Writing Tips</h3>
                <ul style="padding-left: 20px; font-size: 0.9rem; color: #555;">
                    <li>Be specific</li>
                    <li>Include error messages</li>
                    <li>Show your code</li>
                </ul>
            </div>
        </aside>


        <main class="content-feed">
            
            <div class="card">
                <h2>Ask a Public Question</h2>
                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">

                <?php if(isset($_GET['error'])): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        ⚠️ Please fill in all fields correctly.
                    </div>
                <?php endif; ?>

                <form action="actions/ask_logic.php" method="POST">
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="title" style="display:block; font-weight:bold; margin-bottom: 5px;">Title</label>
                        <small style="display:block; color:#666; margin-bottom: 5px;">Be specific and imagine you’re asking a question to another person.</small>
                        <input type="text" id="title" name="q_title" placeholder="e.g. How do I center a div in CSS?" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="desc" style="display:block; font-weight:bold; margin-bottom: 5px;">Body</label>
                        <small style="display:block; color:#666; margin-bottom: 5px;">Include all the information someone would need to answer your question.</small>
                        <textarea id="desc" name="q_desc" rows="10" placeholder="Describe your problem..." required
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="tags" style="display:block; font-weight:bold; margin-bottom: 5px;">Tags</label>
                        <small style="display:block; color:#666; margin-bottom: 5px;">Add up to 5 tags (comma separated).</small>
                        <input type="text" id="tags" name="q_tags" placeholder="e.g. css, html, flexbox"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1rem;">
                        Post Question
                    </button>

                </form>
            </div>

        </main>


        <aside class="sidebar-right">
            <div class="card">
                <h3>Drafts</h3>
                <p class="text-muted">No saved drafts.</p>
            </div>
        </aside>

    </div>

</body>
</html>