<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assess Room: <?= esc($room['name']) ?> - Asset Assessment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .progress-step {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .score-input {
            width: 80px;
        }
        .asset-card {
            transition: all 0.3s ease;
        }
        .asset-card.completed {
            border-color: #28a745;
            background-color: #f8fff9;
        }
        .score-indicator {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        .score-1-3 { background-color: #dc3545; }
        .score-4-6 { background-color: #ffc107; color: #000; }
        .score-7-8 { background-color: #17a2b8; }
        .score-9-10 { background-color: #28a745; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">Asset Assessment System</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, <?= esc($user_name) ?> (<?= esc($user_role) ?>)</span>
                <a class="nav-link" href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Menu</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="/dashboard" class="list-group-item list-group-item-action">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        
                        <?php if (in_array($user_role, ['Super Admin', 'Admin'])): ?>
                            <a href="/users" class="list-group-item list-group-item-action">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                            <a href="/rooms" class="list-group-item list-group-item-action">
                                <i class="fas fa-door-open"></i> Manage Rooms
                            </a>
                            <a href="/assets" class="list-group-item list-group-item-action">
                                <i class="fas fa-cube"></i> Manage Assets
                            </a>
                            <a href="/room-assets" class="list-group-item list-group-item-action">
                                <i class="fas fa-link"></i> Room-Asset Relations
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($user_role === 'GA Staff'): ?>
                            <a href="/assessments" class="list-group-item list-group-item-action active">
                                <i class="fas fa-clipboard-check"></i> Assessments
                            </a>
                            <a href="/assessments/history" class="list-group-item list-group-item-action">
                                <i class="fas fa-history"></i> Assessment History
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($user_role === 'Leader'): ?>
                            <a href="/reports" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-bar"></i> Reports Dashboard
                            </a>
                            <a href="/reports/assets" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-line"></i> Asset Reports
                            </a>
                            <a href="/reports/rooms" class="list-group-item list-group-item-action">
                                <i class="fas fa-building"></i> Room Reports
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Assessment Progress -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-tasks"></i> Progress</h6>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" style="width: 0%" id="progressBar">
                                0%
                            </div>
                        </div>
                        <small class="text-muted">
                            <span id="completedCount">0</span> of <?= count($room_assets) ?> assets assessed
                        </small>
                    </div>
                </div>

                <!-- Scoring Guide -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Scoring Guide</h6>
                    </div>
                    <div class="card-body small">
                        <div class="mb-2">
                            <span class="score-indicator score-1-3">1-3</span>
                            <span class="ms-2">Poor condition</span>
                        </div>
                        <div class="mb-2">
                            <span class="score-indicator score-4-6">4-6</span>
                            <span class="ms-2">Average condition</span>
                        </div>
                        <div class="mb-2">
                            <span class="score-indicator score-7-8">7-8</span>
                            <span class="ms-2">Good condition</span>
                        </div>
                        <div class="mb-2">
                            <span class="score-indicator score-9-10">9-10</span>
                            <span class="ms-2">Excellent condition</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-clipboard-check"></i> Assess Room: <?= esc($room['name']) ?></h2>
                    <a href="/assessments/rooms" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Rooms
                    </a>
                </div>

                <!-- Room Information -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-door-open"></i> Room Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Room Name:</strong><br>
                                <span class="text-muted"><?= esc($room['name']) ?></span>
                            </div>
                            <div class="col-md-4">
                                <strong>Building:</strong><br>
                                <span class="text-muted"><?= esc($room['building'] ?? 'N/A') ?></span>
                            </div>
                            <div class="col-md-4">
                                <strong>Location:</strong><br>
                                <span class="text-muted"><?= esc($room['location'] ?? 'N/A') ?></span>
                            </div>
                        </div>
                        <?php if ($room['description']): ?>
                            <div class="mt-3">
                                <strong>Description:</strong><br>
                                <span class="text-muted"><?= esc($room['description']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Assessment Form -->
                <form id="assessmentForm" method="post" action="/assessments/store">
                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                    
                    <?php if (empty($room_assets)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No assets are assigned to this room. Please contact an administrator to add assets before assessment.
                        </div>
                    <?php else: ?>
                        <!-- Asset Assessment Cards -->
                        <div class="row" id="assetsContainer">
                            <?php foreach ($room_assets as $index => $asset): ?>
                                <div class="col-lg-6 mb-4">
                                    <div class="card asset-card" data-asset-id="<?= $asset['asset_id'] ?>">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-cube"></i> <?= esc($asset['asset_name']) ?>
                                            </h6>
                                            <div class="score-display" style="display: none;">
                                                <span class="score-indicator" id="indicator-<?= $asset['asset_id'] ?>"></span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    Weight: <?= $asset['weight'] ?>% | 
                                                    Category: <?= esc($asset['asset_category'] ?? 'N/A') ?>
                                                </small>
                                            </div>
                                            
                                            <?php if ($asset['asset_description']): ?>
                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <?= esc($asset['asset_description']) ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="score_<?= $asset['asset_id'] ?>" class="form-label">
                                                        Score <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" 
                                                           class="form-control score-input" 
                                                           id="score_<?= $asset['asset_id'] ?>" 
                                                           name="scores[<?= $asset['asset_id'] ?>]" 
                                                           min="1" 
                                                           max="10" 
                                                           step="1" 
                                                           required
                                                           data-weight="<?= $asset['weight'] ?>">
                                                    <div class="form-text">Rate from 1 (poor) to 10 (excellent)</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="notes_<?= $asset['asset_id'] ?>" class="form-label">
                                                        Notes (Optional)
                                                    </label>
                                                    <textarea class="form-control" 
                                                              id="notes_<?= $asset['asset_id'] ?>" 
                                                              name="notes[<?= $asset['asset_id'] ?>]" 
                                                              rows="2" 
                                                              placeholder="Add any observations..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Overall Assessment Notes -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-sticky-note"></i> Overall Assessment Notes</h6>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" 
                                          id="overall_notes" 
                                          name="overall_notes" 
                                          rows="4" 
                                          placeholder="Add general observations about the room and its overall condition..."></textarea>
                            </div>
                        </div>

                        <!-- Calculated Score Preview -->
                        <div class="card mb-4" id="scorePreview" style="display: none;">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-calculator"></i> Calculated Score Preview</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <h4 id="calculatedScore">0.0</h4>
                                        <small class="text-muted">Weighted Score</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h4 id="feasibilityStatus">-</h4>
                                        <small class="text-muted">Feasibility</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h4 id="completionPercent">0%</h4>
                                        <small class="text-muted">Completion</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between mb-4">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                                <i class="fas fa-save"></i> Submit Assessment
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const totalAssets = <?= count($room_assets) ?>;
        const feasibilityThreshold = 7.0; // Configurable threshold
        
        function updateProgress() {
            const scores = document.querySelectorAll('.score-input');
            let completed = 0;
            let totalWeightedScore = 0;
            let totalWeight = 0;
            
            scores.forEach(input => {
                const value = parseInt(input.value);
                const weight = parseFloat(input.dataset.weight);
                const card = input.closest('.asset-card');
                const indicator = card.querySelector('.score-display');
                const scoreIndicator = card.querySelector('.score-indicator');
                
                if (value >= 1 && value <= 10) {
                    completed++;
                    totalWeightedScore += value * (weight / 100);
                    totalWeight += weight / 100;
                    
                    // Update card appearance
                    card.classList.add('completed');
                    indicator.style.display = 'block';
                    
                    // Update score indicator
                    scoreIndicator.textContent = value;
                    scoreIndicator.className = 'score-indicator';
                    if (value >= 1 && value <= 3) {
                        scoreIndicator.classList.add('score-1-3');
                    } else if (value >= 4 && value <= 6) {
                        scoreIndicator.classList.add('score-4-6');
                    } else if (value >= 7 && value <= 8) {
                        scoreIndicator.classList.add('score-7-8');
                    } else if (value >= 9 && value <= 10) {
                        scoreIndicator.classList.add('score-9-10');
                    }
                } else {
                    card.classList.remove('completed');
                    indicator.style.display = 'none';
                }
            });
            
            // Update progress bar
            const progressPercent = (completed / totalAssets) * 100;
            const progressBar = document.getElementById('progressBar');
            progressBar.style.width = progressPercent + '%';
            progressBar.textContent = Math.round(progressPercent) + '%';
            
            // Update completed count
            document.getElementById('completedCount').textContent = completed;
            
            // Calculate weighted score
            const finalScore = totalWeight > 0 ? totalWeightedScore / totalWeight : 0;
            const isFeasible = finalScore >= feasibilityThreshold;
            
            // Show/hide score preview
            const scorePreview = document.getElementById('scorePreview');
            if (completed > 0) {
                scorePreview.style.display = 'block';
                document.getElementById('calculatedScore').textContent = finalScore.toFixed(1);
                
                const statusElement = document.getElementById('feasibilityStatus');
                if (completed === totalAssets) {
                    statusElement.textContent = isFeasible ? 'Feasible' : 'Not Feasible';
                    statusElement.className = isFeasible ? 'text-success' : 'text-danger';
                } else {
                    statusElement.textContent = 'Incomplete';
                    statusElement.className = 'text-warning';
                }
                
                document.getElementById('completionPercent').textContent = Math.round(progressPercent) + '%';
            } else {
                scorePreview.style.display = 'none';
            }
            
            // Enable/disable submit button
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = completed < totalAssets;
            
            if (completed === totalAssets) {
                progressBar.classList.remove('bg-primary');
                progressBar.classList.add('bg-success');
            } else {
                progressBar.classList.remove('bg-success');
                progressBar.classList.add('bg-primary');
            }
        }
        
        function resetForm() {
            if (confirm('Are you sure you want to reset all scores and notes? This action cannot be undone.')) {
                document.getElementById('assessmentForm').reset();
                document.querySelectorAll('.asset-card').forEach(card => {
                    card.classList.remove('completed');
                    card.querySelector('.score-display').style.display = 'none';
                });
                updateProgress();
            }
        }
        
        // Add event listeners to all score inputs
        document.querySelectorAll('.score-input').forEach(input => {
            input.addEventListener('input', updateProgress);
            input.addEventListener('change', updateProgress);
        });
        
        // Form validation
        document.getElementById('assessmentForm').addEventListener('submit', function(e) {
            const scores = document.querySelectorAll('.score-input');
            let allValid = true;
            
            scores.forEach(input => {
                const value = parseInt(input.value);
                if (!value || value < 1 || value > 10) {
                    allValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!allValid) {
                e.preventDefault();
                alert('Please provide valid scores (1-10) for all assets.');
                return false;
            }
            
            // Confirm submission
            if (!confirm('Are you sure you want to submit this assessment? This action cannot be undone.')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Initial progress update
        updateProgress();
        
        // Auto-save functionality (optional - stores in localStorage)
        function autoSave() {
            const formData = {};
            const roomId = document.querySelector('input[name="room_id"]').value;
            
            document.querySelectorAll('.score-input, textarea').forEach(input => {
                if (input.value) {
                    formData[input.name] = input.value;
                }
            });
            
            localStorage.setItem('assessment_draft_' + roomId, JSON.stringify(formData));
        }
        
        function loadDraft() {
            const roomId = document.querySelector('input[name="room_id"]').value;
            const draft = localStorage.getItem('assessment_draft_' + roomId);
            
            if (draft) {
                const formData = JSON.parse(draft);
                Object.keys(formData).forEach(name => {
                    const input = document.querySelector(`[name="${name}"]`);
                    if (input) {
                        input.value = formData[name];
                    }
                });
                updateProgress();
            }
        }
        
        // Auto-save on input
        document.querySelectorAll('.score-input, textarea').forEach(input => {
            input.addEventListener('input', autoSave);
        });
        
        // Load draft on page load
        loadDraft();
        
        // Clear draft on successful submission
        document.getElementById('assessmentForm').addEventListener('submit', function() {
            const roomId = document.querySelector('input[name="room_id"]').value;
            localStorage.removeItem('assessment_draft_' + roomId);
        });
    </script>
</body>
</html>
