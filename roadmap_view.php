<?php
// 1. LINK TO BACKEND (Teammate's file)
// require 'actions/fetch_roadmap_details.php';

// --- TEMPORARY MOCK DATA ---
$roadmap = [
    'id' => 1,
    'title' => 'Frontend Web Development',
    'description' => 'A complete guide to becoming a hireable frontend developer.',
    'creator_name' => 'Admin'
];

$steps = [
    ['id' => 101, 'title' => 'HTML Basics', 'description' => 'Learn tags, attributes, and semantic structure.', 'is_completed' => true],
    ['id' => 102, 'title' => 'CSS Fundamentals', 'description' => 'Box model, selectors, and specificity.', 'is_completed' => true],
    ['id' => 103, 'title' => 'Flexbox & Grid', 'description' => 'Modern layout techniques.', 'is_completed' => false],
    ['id' => 104, 'title' => 'JavaScript Variables', 'description' => 'Let, const, var and data types.', 'is_completed' => false],
    ['id' => 105, 'title' => 'DOM Manipulation', 'description' => 'Selecting elements and handling events.', 'is_completed' => false],
];

// Calculate initial progress for the bar
$total_steps = count($steps);
$completed_steps = 0;
foreach($steps as $s) { if($s['is_completed']) $completed_steps++; }
$progress_percent = ($total_steps > 0) ? round(($completed_steps / $total_steps) * 100) : 0;
// ----------------------------
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($roadmap['title']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Progress Bar Styles */
        .progress-container {
            background: #e1e4e8;
            border-radius: 8px;
            height: 20px;
            width: 100%;
            margin: 20px 0;
            overflow: hidden;
        }
        .progress-bar {
            background: #27ae60;
            height: 100%;
            width: <?php echo $progress_percent; ?>%; /* PHP sets initial width */
            transition: width 0.3s ease;
        }
        .progress-text {
            text-align: right;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        /* Timeline Step Styles */
        .step-list {
            list-style: none;
            padding: 0;
            position: relative;
        }
        /* The vertical line */
        .step-list::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e1e4e8;
            z-index: 0;
        }
        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1; /* Sit on top of line */
        }
        .step-checkbox {
            width: 40px;
            height: 40px;
            margin-right: 15px;
            cursor: pointer;
            z-index: 2;
        }
        /* Custom checkbox appearance */
        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-top: 5px;
            cursor: pointer;
        }
        .step-content {
            background: white;
            padding: 1.5rem;
            border: 1px solid #e1e4e8;
            border-radius: 8px;
            flex-grow: 1;
        }
        .step-title {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
        }
        .step-desc {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand"><a href="index.php">Coders' Journey</a></div>
        <div class="nav-links">
            <a href="roadmaps.php" class="active">Roadmaps</a>
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
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h3>Author</h3>
                <p><?php echo htmlspecialchars($roadmap['creator_name']); ?></p>
            </div>
        </aside>

        <main class="content-feed">
            
            <div class="card">
                <h1><?php echo htmlspecialchars($roadmap['title']); ?></h1>
                <p style="color: #555; margin-top: 10px;">
                    <?php echo htmlspecialchars($roadmap['description']); ?>
                </p>

                <div style="margin-top: 30px;">
                    <div class="progress-text">
                        <span id="percent-display"><?php echo $progress_percent; ?></span>% Complete
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar" id="main-progress-bar"></div>
                    </div>
                </div>
            </div>

            <h3 style="margin: 30px 0 20px;">Your Journey</h3>

            <ul class="step-list">
                <?php foreach($steps as $step): ?>
                    <li class="step-item">
                        
                        <div style="background: white; padding: 10px; border-radius: 50%; border: 1px solid #ddd;">
                            <input type="checkbox" 
                                   class="step-check" 
                                   data-step-id="<?php echo $step['id']; ?>"
                                   onclick="updateProgress()"
                                   <?php echo $step['is_completed'] ? 'checked' : ''; ?>>
                        </div>

                        <div class="step-content">
                            <h4 class="step-title"><?php echo htmlspecialchars($step['title']); ?></h4>
                            <p class="step-desc"><?php echo htmlspecialchars($step['description']); ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

        </main>

        <aside class="sidebar-right"></aside>

    </div>

    <script>
        function updateProgress() {
            // 1. Count all checkboxes
            const allChecks = document.querySelectorAll('.step-check');
            const total = allChecks.length;
            
            // 2. Count only CHECKED boxes
            let checkedCount = 0;
            allChecks.forEach(box => {
                if(box.checked) checkedCount++;
            });

            // 3. Calculate %
            const percent = Math.round((checkedCount / total) * 100);

            // 4. Update width and text
            document.getElementById('main-progress-bar').style.width = percent + '%';
            document.getElementById('percent-display').innerText = percent;

            // 5. (Optional) Send data to Backend via AJAX so it saves!
            // This part is for the Backend Guy to implement later.
            /*
            fetch('actions/update_progress.php', {
                method: 'POST',
                body: new URLSearchParams({
                    'step_id': event.target.dataset.stepId,
                    'status': event.target.checked ? 1 : 0
                })
            });
            */
        }
    </script>

</body>
</html>