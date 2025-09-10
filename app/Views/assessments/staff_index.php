<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assessments - Asset Assessment System</title>
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
                    <h2><i class="fas fa-clipboard-check"></i> My Assessments</h2>
                    <a href="/assessments/rooms" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Assessment
                    </a>
                </div>

                <?php if (empty($assessments)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-clipboard fa-3x mb-3"></i>
                        <h5>No assessments found</h5>
                        <p>You haven't completed any assessments yet.</p>
                        <a href="/assessments/rooms" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Start Your First Assessment
                        </a>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Assessment History</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Room</th>
                                            <th>Assets Assessed</th>
                                            <th>Assessment Date</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $grouped = [];
                                        foreach ($assessments as $assessment) {
                                            $key = $assessment['room_id'] . '_' . date('Y-m-d', strtotime($assessment['created_at']));
                                            if (!isset($grouped[$key])) {
                                                $grouped[$key] = [
                                                    'room_name' => $assessment['room_name'],
                                                    'room_id' => $assessment['room_id'],
                                                    'created_at' => $assessment['created_at'],
                                                    'count' => 0,
                                                    'total_score' => 0,
                                                    'assessments' => []
                                                ];
                                            }
                                            $grouped[$key]['count']++;
                                            $grouped[$key]['total_score'] += $assessment['score'];
                                            $grouped[$key]['assessments'][] = $assessment;
                                        }
                                        ?>
                                        <?php foreach ($grouped as $group): ?>
                                            <?php 
                                                $avgScore = $group['total_score'] / $group['count'];
                                                $feasible = $avgScore >= 7;
                                                $badgeClass = $feasible ? 'bg-success' : 'bg-danger';
                                                $icon = $feasible ? 'fa-check-circle' : 'fa-times-circle';
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($group['room_name']) ?></strong>
                                                </td>
                                                <td><?= $group['count'] ?> assets</td>
                                                <td><?= date('M j, Y H:i', strtotime($group['created_at'])) ?></td>
                                                <td>
                                                    <span class="fw-bold"><?= number_format($avgScore, 1) ?>/10</span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <i class="fas <?= $icon ?>"></i> 
                                                        <?= $feasible ? 'Feasible' : 'Not Feasible' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="/assessments/details/<?= $group['room_id'] ?>" 
                                                           class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a href="/assessments/room/<?= $group['room_id'] ?>" 
                                                           class="btn btn-outline-warning">
                                                            <i class="fas fa-redo"></i> Reassess
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
