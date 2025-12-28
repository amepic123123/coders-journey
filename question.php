<?php
// require 'actions/fetch_single_question.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($question['title']); ?> - Coders' Journey</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .vote-sidebar {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 1.5rem;
            min-width: 50px;
        }
        .vote-btn {
            background: none;
            border: 1px solid #ddd;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            color: #666;
            font-size: 0.8rem;
        }
        .vote-count {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 10px 0;
            color: #2c3e50;
        }
        .post-layout {
            display: flex;
            align-items: flex-start;
        }
        .best-answer {
            border: 2px solid #27ae60; /* Green border for best answer */
            background-color: #f0fdf4;
        }
        .verified-badge {
            background: #27ae60;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 4px;
            margin-right: 5px;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand"><a href="index.php">Coders' Journey</a></div>
        <div class="nav-links">
            <a href="roadmaps.php">Roadmaps</a>
            <a href="ask.php">Ask Question</a>
        </div>
        <div class="nav-auth">
            <a href="login.php" class="btn-login">Log In</a>
        </div>
    </nav>


    <div class="container main-layout">
        
        <aside class="sidebar-left">
            <div class="sidebar-menu">
                <a href="index.php" class="menu-item">🏠 Home</a>
                <a href="roadmaps.php" class="menu-item">🗺️ Roadmaps</a>
            </div>
        </aside>

        <main class="content-feed">
            
            <div class="card">
                <div class="post-layout">
                    <div class="vote-sidebar">
                        <button class="vote-btn">▲</button>
                        <span class="vote-count"><?php echo $question['vote_count']; ?></span>
                        <button class="vote-btn">▼</button>
                    </div>

                    <div style="flex-grow: 1;">
                        <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">
                            <?php echo htmlspecialchars($question['title']); ?>
                        </h1>
                        
                        <div style="line-height: 1.6; color: #333; margin-bottom: 1.5rem;">
                            <?php echo nl2br(htmlspecialchars($question['description'])); ?>
                        </div>

                        <div class="meta-footer">
                            <div class="tags-list">
                                <?php 
                                    $tags = explode(',', $question['tags']);
                                    foreach($tags as $t): 
                                ?>
                                    <span class="tag"><?php echo trim($t); ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="user-info">
                                <span class="text-muted">asked by</span>
                                <strong style="color: #4a90e2;"><?php echo htmlspecialchars($question['username']); ?></strong>
                                <span class="text-muted">on <?php echo date('M d', strtotime($question['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h3 style="margin: 2rem 0 1rem;">
                <?php echo count($answers); ?> Answers
            </h3>


            <?php foreach ($answers as $ans): ?>
                
                <div class="card <?php echo $ans['is_best'] ? 'best-answer' : ''; ?>">
                    
                    <div class="post-layout">
                        <div class="vote-sidebar">
                            <button class="vote-btn">▲</button>
                            <span class="vote-count">0</span>
                            <button class="vote-btn">▼</button>
                            
                            <?php if($ans['is_best']): ?>
                                <span style="font-size:1.5rem; color:#27ae60; margin-top:10px;" title="Best Answer">✓</span>
                            <?php endif; ?>
                        </div>

                        <div style="flex-grow: 1;">
                            <div style="margin-bottom: 1rem;">
                                <?php echo nl2br(htmlspecialchars($ans['body'])); ?>
                            </div>

                            <div class="meta-footer" style="justify-content: flex-end;">
                                <div class="user-info">
                                    <span class="text-muted">answered by</span>
                                    <strong><?php echo htmlspecialchars($ans['username']); ?></strong>
                                    <span class="text-muted"><?php echo date('M d', strtotime($ans['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            <?php endforeach; ?>


            <div class="card" style="margin-top: 2rem; background: #f9f9f9;">
                <h3>Your Answer</h3>
                
                <form action="actions/post_answer.php" method="POST">
                    
                    <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">

                    <div class="form-group">
                        <textarea name="answer_body" rows="6" required placeholder="Type your solution here..." 
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Post Answer</button>
                </form>
            </div>

        </main>

        <aside class="sidebar-right">
            <div class="card">
                <h3>Related Questions</h3>
                <ul style="padding-left: 20px; font-size: 0.9rem; color: #4a90e2;">
                    <li><a href="#">How flexbox works?</a></li>
                    <li><a href="#">CSS Grid vs Flexbox</a></li>
                </ul>
            </div>
        </aside>

    </div>

</body>
</html>