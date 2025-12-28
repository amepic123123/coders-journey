<?php
// 1. LINK TO BACKEND
// require 'actions/fetch_roadmaps.php';

// --- TEMPORARY MOCK DATA (DELETE LATER) ---
$roadmaps = [
    [
        'id' => 1,
        'title' => 'Frontend Web Development',
        'description' => 'Master HTML, CSS, and Vanilla JavaScript to build responsive websites.',
        'difficulty' => 'Beginner',
        'steps_count' => 12,
        'creator_name' => 'Admin',
        'is_verified' => true
    ],
    [
        'id' => 2,
        'title' => 'PHP Backend Mastery',
        'description' => 'Learn how to build dynamic web applications using PHP and MySQL.',
        'difficulty' => 'Intermediate',
        'steps_count' => 20,
        'creator_name' => 'CodeMaster99',
        'is_verified' => true
    ],
    [
        'id' => 3,
        'title' => 'Data Structures in C++',
        'description' => 'Deep dive into algorithms, memory management, and pointers.',
        'difficulty' => 'Advanced',
        'steps_count' => 15,
        'creator_name' => 'AlgoKing',
        'is_verified' => false
    ]
];
// ------------------------------------------

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Roadmaps - Coders' Journey</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Specific Styles for Roadmap Cards */
        .roadmap-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        .roadmap-card {
            background: white;
            border: 1px solid #e1e4e8;
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }
        .roadmap-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: #4a90e2;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-beginner { background: #e6fffa; color: #2c7a7b; }
        .badge-intermediate { background: #fffaf0; color: #dd6b20; }
        .badge-advanced { background: #fff5f5; color: #c53030; }
        
        .steps-count {
            color: #666;
            font-size: 0.9rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand"><a href="index.php">Coders' Journey</a></div>
        <div class="nav-links">
            <a href="roadmaps.php" class="active">Roadmaps</a>
            <a href="ask.php">Ask Question</a>
        </div>
        <div class="nav-auth">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="btn-register" style="background:#e74c3c;">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Log In</a>
            <?php endif; ?>
        </div>
    </nav>


    <div class="container main-layout">
        
        <aside class="sidebar-left">
            <div class="sidebar-menu">
                <a href="index.php" class="menu-item">🏠 Home</a>
                <a href="roadmaps.php" class="menu-item active">🗺️ Roadmaps</a>
                <a href="tags.php" class="menu-item">🏷️ Tags</a>
            </div>
            
            <?php if(isset($_SESSION['is_verified']) && $_SESSION['is_verified']): ?>
                <div style="margin-top: 20px;">
                    <a href="create_roadmap.php" class="btn btn-primary" style="width: 100%; text-align: center;">+ Create Roadmap</a>
                </div>
            <?php endif; ?>
        </aside>


        <main class="content-feed">
            
            <div class="feed-header">
                <h2>Explore Learning Paths</h2>
                <p class="text-muted">Follow step-by-step guides created by the community.</p>
            </div>

            <div class="roadmap-grid">
                
                <?php foreach($roadmaps as $map): ?>
                    <div class="roadmap-card">
                        
                        <div style="margin-bottom: 10px;">
                            <?php 
                                $badgeClass = 'badge-beginner';
                                if($map['difficulty'] == 'Intermediate') $badgeClass = 'badge-intermediate';
                                if($map['difficulty'] == 'Advanced') $badgeClass = 'badge-advanced';
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $map['difficulty']; ?></span>
                        </div>

                        <h3 style="font-size: 1.2rem; margin-bottom: 10px;">
                            <a href="roadmap_view.php?id=<?php echo $map['id']; ?>" style="color: #2c3e50; text-decoration: none;">
                                <?php echo htmlspecialchars($map['title']); ?>
                            </a>
                        </h3>

                        <p style="font-size: 0.9rem; color: #555; line-height: 1.5; flex-grow: 1;">
                            <?php echo htmlspecialchars($map['description']); ?>
                        </p>

                        <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px;">
                            <div class="steps-count">
                                📚 <strong><?php echo $map['steps_count']; ?></strong> Steps
                            </div>
                            <div style="font-size: 0.85rem; color: #888; margin-top: 5px;">
                                Created by 
                                <strong><?php echo htmlspecialchars($map['creator_name']); ?></strong>
                                <?php if($map['is_verified']): ?>
                                    <span title="Verified Author">✅</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <a href="roadmap_view.php?id=<?php echo $map['id']; ?>" class="btn btn-primary" style="margin-top: 15px; text-align: center; text-decoration: none;">
                            Start Journey
                        </a>

                    </div>
                <?php endforeach; ?>

            </div>

        </main>
        
        <aside class="sidebar-right"></aside>

    </div>

</body>
</html>