<?php
// 1. LINK TO BACKEND (Teammate's file)
// require 'actions/fetch_profile.php';

// --- TEMPORARY MOCK DATA ---
$user = [
    'id' => 101,
    'username' => 'AhmedDev',
    'email' => 'ahmed@example.com',
    'joined_date' => '2023-01-15',
    'reputation' => 1250,
    'role' => 'verified', // 'user' or 'verified' or 'admin'
    'avatar_color' => '#f1c40f' // Just a random color for the default avatar
];

// Roadmaps the user is following
$my_roadmaps = [
    ['id' => 1, 'title' => 'Frontend Mastery', 'percent' => 45],
    ['id' => 3, 'title' => 'Advanced Python', 'percent' => 10]
];

// Questions the user has asked
$my_questions = [
    ['id' => 50, 'title' => 'How to center a div?', 'votes' => 5, 'date' => 'Oct 20'],
    ['id' => 52, 'title' => 'MySQL vs PostgreSQL?', 'votes' => 12, 'date' => 'Oct 22']
];
// ----------------------------
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $user['username']; ?>'s Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Profile Header Styles */
        .profile-header {
            background: white;
            border: 1px solid #e1e4e8;
            border-radius: 8px;
            padding: 2rem;
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: <?php echo $user['avatar_color']; ?>;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            font-weight: bold;
            margin-right: 2rem;
        }
        .profile-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
        }
        .stat-item {
            text-align: center;
        }
        .stat-val { font-size: 1.2rem; font-weight: bold; display: block; }
        .stat-label { font-size: 0.9rem; color: #666; }

        /* Progress Bars in List */
        .mini-progress {
            background: #eee;
            height: 6px;
            border-radius: 3px;
            width: 100px;
            margin-top: 5px;
            overflow: hidden;
        }
        .mini-fill {
            background: #27ae60;
            height: 100%;
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
            <a href="logout.php" class="btn-register" style="background:#e74c3c;">Logout</a>
        </div>
    </nav>


    <div class="container main-layout">
        
        <aside class="sidebar-left">
            <div class="sidebar-menu">
                <a href="index.php" class="menu-item">🏠 Home</a>
                <a href="roadmaps.php" class="menu-item">🗺️ Roadmaps</a>
                <a href="profile.php" class="menu-item active">👤 My Profile</a>
            </div>
        </aside>

        <main class="content-feed">
            
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>

                <div style="flex-grow: 1;">
                    <h1 style="margin: 0;">
                        <?php echo htmlspecialchars($user['username']); ?>
                        <?php if($user['role'] == 'verified'): ?>
                            <span title="Verified" style="font-size: 1rem; vertical-align: middle;">✅</span>
                        <?php endif; ?>
                    </h1>
                    <p style="color: #666; margin: 5px 0;">
                        Member since <?php echo date('M Y', strtotime($user['joined_date'])); ?>
                    </p>

                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-val"><?php echo $user['reputation']; ?></span>
                            <span class="stat-label">Reputation</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-val"><?php echo count($my_questions); ?></span>
                            <span class="stat-label">Questions</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-val"><?php echo count($my_roadmaps); ?></span>
                            <span class="stat-label">Roadmaps</span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card" style="margin-bottom: 2rem;">
                <h3>📚 In Progress Roadmaps</h3>
                
                <?php if(count($my_roadmaps) > 0): ?>
                    <ul style="list-style: none; padding: 0; margin-top: 1rem;">
                        <?php foreach($my_roadmaps as $map): ?>
                            <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding: 10px 0;">
                                <div>
                                    <a href="roadmap_view.php?id=<?php echo $map['id']; ?>" style="font-weight: bold; color: #2c3e50; text-decoration: none;">
                                        <?php echo htmlspecialchars($map['title']); ?>
                                    </a>
                                </div>
                                <div>
                                    <div class="mini-progress">
                                        <div class="mini-fill" style="width: <?php echo $map['percent']; ?>%;"></div>
                                    </div>
                                    <div style="font-size: 0.75rem; text-align: right; color: #666;">
                                        <?php echo $map['percent']; ?>%
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">You are not following any roadmaps yet.</p>
                <?php endif; ?>
            </div>


            <div class="card">
                <h3>📝 Recent Questions</h3>
                
                <?php if(count($my_questions) > 0): ?>
                    <ul style="list-style: none; padding: 0; margin-top: 1rem;">
                        <?php foreach($my_questions as $q): ?>
                            <li style="padding: 10px 0; border-bottom: 1px solid #eee;">
                                <div style="display: flex; justify-content: space-between;">
                                    <a href="question.php?id=<?php echo $q['id']; ?>" style="color: #4a90e2; text-decoration: none;">
                                        <?php echo htmlspecialchars($q['title']); ?>
                                    </a>
                                    <span style="font-size: 0.9rem; color: #666;">
                                        <?php echo $q['votes']; ?> votes
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No questions asked yet.</p>
                <?php endif; ?>
            </div>

        </main>

        <aside class="sidebar-right"></aside>

    </div>

</body>
</html>