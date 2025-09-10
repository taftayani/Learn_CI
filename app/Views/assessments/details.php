<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Details - Asset Assessment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .score-indicator {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 14px;
        }
        .score-1-3 { background-color: #dc3545; }
        .score-4-6 { background-color: #ffc107; color: #000; }
        .score-7-8 { background-color: #17a2b8; }
        .score-9-10 { background-color: #28a745; }
        
        .asset-score-card {
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }
        .asset-score-card.score-1-3 { border-left-color: #dc3545; }
        .asset-score-card.score-4-6 { border-left-color: #ffc107; }
        .asset-score-card.score-7-8 { border-left-color: #17a2b8; }
        .asset-score-card.score-9-10 { border-left-color: #28a745; }
        
        @media print {
            .no-print { display: none !important; }
            .card { border: 1px solid #000 !important; page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark no-print">
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
            <div class="col-md-3 no-print">
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
                            <a href="/assessments" class="list-group-item list-group-item-action">
                                <i class="fas fa-clipboard-check"></i> Assessments
                            </a>
                            <a href="/assessments/history" class="list-group-item list-group-item-action active">
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

                <!-- Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-tools"></i> Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                            <a href="/assessments/export/pdf/<?= $assessment['id'] ?>" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <?php if ($user_role === 'GA Staff' && $assessment['user_id'] == $user_id): ?>
                                <a href="/assessments/reassess/<?= $assessment['room_id'] ?>" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-redo"></i> Reassess Room
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show no-print">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show no-print">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <h2><i class="fas fa-clipboard-check"></i> Assessment Details</h2>
                    <a href="/assessments/history" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to History
                    </a>
                </div>

                <!-- Assessment Summary -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-0">
                                    <i class="fas fa-door-open"></i> <?= esc($assessment['room_name']) ?>
                                </h5>
                                <small>Assessment conducted on <?= date('F j, Y \a\t H:i', strtotime($assessment['created_at'])) ?></small>
                            </div>
                            <div class="col-md-4 text-end">
                                <?php 
                                    $feasible = isset($assessment['is_feasible']) ? $assessment['is_feasible'] : ($assessment['score'] >= 7);
                                    $badgeClass = $feasible ? 'bg-success' : 'bg-danger';
                                    $icon = $feasible ? 'fa-check-circle' : 'fa-times-circle';
                                ?>
                                <h4>
                                    <span class="badge <?= $badgeClass ?>">
                                        <i class="fas <?= $icon ?>"></i> 
                                        <?= $feasible ? 'FEASIBLE' : 'NOT FEASIBLE' ?>
                                    </span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="text-primary"><?= number_format($assessment['score'], 1) ?></h4>
                                <small class="text-muted">Asset Score</small>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-info"><?= count($asset_scores) ?></h4>
                                <small class="text-muted">Assets Assessed</small>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-success"><?= esc($assessment['assessor_name']) ?></h4>
                                <small class="text-muted">Assessed By</small>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-warning"><?= date('M j, Y', strtotime($assessment['created_at'])) ?></h4>
                                <small class="text-muted">Date</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-building"></i> Room Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Assessor:</strong><br>
                                <span class="text-muted"><?= esc($assessment['assessor_name'] ?? 'N/A') ?></span>
                            </div>
                            <div class="col-md-4">
                                <strong>Location:</strong><br>
                                <span class="text-muted"><?= esc($assessment['location'] ?? 'N/A') ?></span>
                            </div>
                            <div class="col-md-4">
                                <strong>Room ID:</strong><br>
                                <span class="text-muted">#<?= $assessment['room_id'] ?></span>
                            </div>
                        </div>
                        <?php if ($assessment['room_description']): ?>
                            <div class="mt-3">
                                <strong>Description:</strong><br>
                                <span class="text-muted"><?= esc($assessment['room_description']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Score Distribution -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Score Distribution</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="border rounded p-3">
                                    <div class="score-indicator score-1-3 mb-2 mx-auto">1-3</div>
                                    <h5><?= $score_distribution['poor'] ?? 0 ?></h5>
                                    <small class="text-muted">Poor</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-3">
                                    <div class="score-indicator score-4-6 mb-2 mx-auto">4-6</div>
                                    <h5><?= $score_distribution['average'] ?? 0 ?></h5>
                                    <small class="text-muted">Average</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-3">
                                    <div class="score-indicator score-7-8 mb-2 mx-auto">7-8</div>
                                    <h5><?= $score_distribution['good'] ?? 0 ?></h5>
                                    <small class="text-muted">Good</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-3">
                                    <div class="score-indicator score-9-10 mb-2 mx-auto">9-10</div>
                                    <h5><?= $score_distribution['excellent'] ?? 0 ?></h5>
                                    <small class="text-muted">Excellent</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asset Scores -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-cube"></i> Individual Asset Scores</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($asset_scores)): ?>
                            <div class="alert alert-warning">
                                No asset scores found for this assessment.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($asset_scores as $asset): ?>
                                    <?php
                                        $scoreClass = '';
                                        if ($asset['score'] >= 1 && $asset['score'] <= 3) $scoreClass = 'score-1-3';
                                        elseif ($asset['score'] >= 4 && $asset['score'] <= 6) $scoreClass = 'score-4-6';
                                        elseif ($asset['score'] >= 7 && $asset['score'] <= 8) $scoreClass = 'score-7-8';
                                        elseif ($asset['score'] >= 9 && $asset['score'] <= 10) $scoreClass = 'score-9-10';
                                    ?>
                                    <div class="col-lg-6 mb-3">
                                        <div class="card asset-score-card <?= $scoreClass ?>">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-8">
                                                        <h6 class="mb-1"><?= esc($asset['asset_name']) ?></h6>
                                                        <small class="text-muted">
                                                            Weight: <?= $asset['weight'] ?>% | 
                                                            Category: <?= esc($asset['asset_category'] ?? 'N/A') ?>
                                                        </small>
                                                        <div class="mt-2">
                                                            <strong>Weighted Score: </strong>
                                                            <?= number_format($asset['weighted_score'], 2) ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 text-center">
                                                        <div class="score-indicator <?= $scoreClass ?>">
                                                            <?= $asset['score'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if (!empty($asset['notes'])): ?>
                                                    <div class="mt-3">
                                                        <strong>Notes:</strong><br>
                                                        <small class="text-muted"><?= esc($asset['notes']) ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Overall Assessment Notes -->
                <?php if (!empty($assessment['overall_notes'])): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-sticky-note"></i> Overall Assessment Notes</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0"><?= nl2br(esc($assessment['overall_notes'])) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Feasibility Analysis -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-analytics"></i> Feasibility Analysis</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Calculation Method:</h6>
                                <p class="small text-muted">
                                    Final score is calculated using weighted averages where each asset contributes based on its assigned weight percentage.
                                </p>
                                <div class="alert alert-light">
                                    <strong>Formula:</strong><br>
                                    <code>Final Score = Σ(Asset Score × Weight) / 100</code>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Feasibility Criteria:</h6>
                                <ul class="small">
                                    <li>Score ≥ 7.0: <span class="text-success">Feasible</span></li>
                                    <li>Score < 7.0: <span class="text-danger">Not Feasible</span></li>
                                </ul>
                                <div class="mt-3">
                                    <strong>This Assessment:</strong><br>
                                    Score: <?= number_format($assessment['score'], 1) ?> → 
                                    <span class="<?= (isset($assessment['is_feasible']) ? $assessment['is_feasible'] : ($assessment['score'] >= 7)) ? 'text-success' : 'text-danger' ?>">
                                        <?= (isset($assessment['is_feasible']) ? $assessment['is_feasible'] : ($assessment['score'] >= 7)) ? 'Good Score' : 'Poor Score' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assessment Timeline -->
                <?php if (!empty($assessment_history)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-timeline"></i> Assessment History for This Room</h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <?php foreach ($assessment_history as $index => $hist): ?>
                                    <div class="row mb-3 <?= $hist['id'] == $assessment['id'] ? 'bg-light' : '' ?>">
                                        <div class="col-md-3">
                                            <?= date('M j, Y H:i', strtotime($hist['created_at'])) ?>
                                            <?php if ($hist['id'] == $assessment['id']): ?>
                                                <span class="badge bg-primary ms-2">Current</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <strong><?= number_format($hist['score'], 1) ?></strong>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <?php 
                                                $histFeasible = isset($hist['is_feasible']) ? $hist['is_feasible'] : ($hist['score'] >= 7);
                                                $histBadgeClass = $histFeasible ? 'bg-success' : 'bg-danger';
                                            ?>
                                            <span class="badge <?= $histBadgeClass ?>">
                                                <?= $histFeasible ? 'Feasible' : 'Not Feasible' ?>
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted"><?= esc($hist['assessor_name']) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Footer -->
                <div class="card">
                    <div class="card-body text-center text-muted small">
                        <p class="mb-0">
                            Assessment ID: #<?= $assessment['id'] ?> | 
                            Generated on <?= date('F j, Y \a\t H:i') ?> | 
                            Asset Assessment System
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhance print functionality
        window.addEventListener('beforeprint', function() {
            document.title = 'Assessment-' + <?= $assessment['id'] ?> + '-' + '<?= esc($assessment['room_name']) ?>';
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            if (e.key === 'Escape') {
                window.location.href = '/assessments/history';
            }
        });

        // Auto-scroll to hash if present
        if (window.location.hash) {
            setTimeout(() => {
                const element = document.querySelector(window.location.hash);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth' });
                }
            }, 100);
        }
    </script>
</body>
</html>
