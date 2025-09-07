<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessments - Asset Assessment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                    <h2><i class="fas fa-clipboard-check"></i> Asset Assessments</h2>
                </div>

                <?php if ($user_role !== 'GA Staff'): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Access denied. Only GA Staff members can perform assessments.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <!-- Start New Assessment -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Start New Assessment</h5>
                                </div>
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-check fa-5x text-primary mb-3"></i>
                                    <p class="card-text">Begin assessing rooms and their assets to determine feasibility.</p>
                                    <a href="/assessments/rooms" class="btn btn-primary btn-lg">
                                        <i class="fas fa-arrow-right"></i> View Available Rooms
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Assessment History -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-history"></i> Assessment History</h5>
                                </div>
                                <div class="card-body text-center">
                                    <i class="fas fa-history fa-5x text-info mb-3"></i>
                                    <p class="card-text">View your previous assessments and their results.</p>
                                    <a href="/assessments/history" class="btn btn-info btn-lg">
                                        <i class="fas fa-eye"></i> View History
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Assessment Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <h3 class="text-success"><?= $total_assessments ?? 0 ?></h3>
                                                <small class="text-muted">Total Assessments</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <h3 class="text-primary"><?= $pending_rooms ?? 0 ?></h3>
                                                <small class="text-muted">Rooms Pending</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <h3 class="text-success"><?= $feasible_count ?? 0 ?></h3>
                                                <small class="text-muted">Feasible Rooms</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <h3 class="text-danger"><?= $not_feasible_count ?? 0 ?></h3>
                                                <small class="text-muted">Not Feasible</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions Card -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Assessment Instructions</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Select a room from the available rooms list</li>
                                        <li>Assess each asset in the room by providing scores (1-10 scale)</li>
                                        <li>Add notes for any specific observations</li>
                                        <li>Submit your assessment to calculate feasibility</li>
                                        <li>Review your assessment history and results</li>
                                    </ol>
                                    <div class="alert alert-info mt-3">
                                        <strong>Scoring Guide:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>1-3:</strong> Poor condition, major issues</li>
                                            <li><strong>4-6:</strong> Average condition, some issues</li>
                                            <li><strong>7-8:</strong> Good condition, minor issues</li>
                                            <li><strong>9-10:</strong> Excellent condition, no issues</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
