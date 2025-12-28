<?php 
//require 'actions/fetch_questions.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Coders' Journey</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand">
            <a href="index.php">Coders' Journey</a>
        </div>
        
        <div class="nav-links">
            <a href="roadmaps.php">Roadmaps</a>
            <a href="ask.php">Ask Question</a>
        </div>

        <div class="nav-auth">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span style="color:white; margin-right:15px;">Welcome!</span>
                <a href="logout.php" class="btn-register" style="background:#e74c3c;">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Log In</a>
                <a href="register.php" class="btn-register">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>


    <div class="container main-layout">
        
        <aside class="sidebar-left">
            <div class="sidebar-menu">
                <a href="index.php" class="menu-item active">🏠 Home</a>
                <a href="roadmaps.php" class="menu-item">🗺️ Roadmaps</a>
                <a href="tags.php" class="menu-item">🏷️ Tags</a>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h3>Must Read</h3>
                <p style="font-size: 0.9rem; color: #666;">
                    Check out the <a href="#">Beginner's Guide</a> to setting up your environment.
                </p>
            </div>
        </aside>


        <main class="content-feed">
            
            <div class="feed-header">
                <h2>Top Questions</h2>
                <a href="ask.php" class="btn btn-primary">Ask Question</a>
            </div>

            <?php if (count($questions) > 0): ?>

                <?php foreach ($questions as $q): ?>
                
                <div class="question-card">
                    
                    <div class="stats-container">
                        <div class="stat-box">
                            <span class="stat-value">
                                <?php echo $q['vote_count']; //moh you rename this to the variable you set for the upvotes, downvotes num?>
                            </span>
                            <span class="stat-label">views</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-value">0</span>
                            <span class="stat-label">answers</span>
                        </div>
                    </div>

                    <div class="question-content">
                        <h3 class="question-title">
                            <a href="question.php?id=<?php echo $q['id']; ?>">
                                <?php echo htmlspecialchars($q['title']); ?>
                            </a>
                        </h3>
                        
                        <p class="question-excerpt">
                            <?php 
                                // Show first 150 chars of description
                                echo htmlspecialchars(substr($q['description'], 0, 150)) . '...'; 
                            ?>
                        </p>
                        
                        <div class="meta-footer">
                            <div class="tags-list">
                                <?php 
                                    $tags_array = explode(',', $q['tags']); 
                                    foreach($tags_array as $tag):
                                ?>
                                    <span class="tag"><?php echo trim($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="user-info">
                                <span class="text-muted">asked by</span>
                                <a href="#" class="author-name">
                                    <?php echo isset($q['username']) ? htmlspecialchars($q['username']) : 'User #' . $q['user_id']; ?>
                                </a>
                                <span class="text-muted">
                                    • <?php echo date('M d', strtotime($q['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="card" style="text-align:center; padding: 40px;">
                    <h3>No questions found.</h3>
                    <p>Be the first to ask one!</p>
                </div>
            <?php endif; ?>
            
            </main>


        <aside class="sidebar-right">
            <div class="card">
                <h3>Community Stats</h3>
                <ul class="stats-list">
                    <li><strong><?php echo count($questions); ?></strong> Questions</li>
                    <li><strong>890</strong> Users</li>
                </ul>
            </div>
        </aside>

    </div>

</body>
</html>