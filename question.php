<?php
// require 'actions/fetch_single_question.php';

// --- TEMPORARY MOCK DATA (For testing without DB) ---
if (!isset($question)) {
    $question = [
        'id' => 1,
        'title' => 'How do I center a div in CSS?',
        'description' => 'I have tried using margin: 0 auto but it does not work. My container is flex but nothing moves.',
        'tags' => 'css, html, flexbox',
        'vote_count' => 12,
        'user_id' => 101, 
        'username' => 'FrontendFan',
        'created_at' => '2023-10-25 10:00:00'
    ];
}

if (!isset($answers)) {
    $answers = [
        [
            'id' => 5,
            'body' => 'You need to set justify-content: center on the parent container.',
            'username' => 'CSS_Wizard',
            'user_id' => 202, // Different user than the asker
            'is_best' => true, 
            'created_at' => '2023-10-25 12:30:00'
        ],
        [
            'id' => 6,
            'body' => 'Make sure the parent has a width defined, otherwise it cannot center anything.',
            'username' => 'DevHelper',
            'user_id' => 303,
            'is_best' => false,
            'created_at' => '2023-10-25 13:00:00'
        ]
    ];
}
// ----------------------------
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
                <a href="tags.php" class="menu-item">🏷️ Tags</a>
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
                                        $clean_t = trim($t);
                                ?>
                                    <a href="tag.php?name=<?php echo urlencode($clean_t); ?>" class="tag">
                                        <?php echo htmlspecialchars($clean_t); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="user-info">
                                <span class="text-muted">asked by</span>
                                <a href="profile.php?id=<?php echo $question['user_id']; ?>">
                                    <strong style="color: #4a90e2;"><?php echo htmlspecialchars($question['username']); ?></strong>
                                </a>
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
                                    <a href="profile.php?id=<?php echo $ans['user_id']; ?>">
                                        <strong><?php echo htmlspecialchars($ans['username']); ?></strong>
                                    </a>
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
             </aside>

    </div>

</body>
</html>