<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment History - Asset Assessment System</title>
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

                <!-- Quick Stats -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Your Stats</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h4 class="text-primary"><?= $total_assessments ?? 0 ?></h4>
                            <small class="text-muted">Total Assessments</small>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <strong class="text-success"><?= $feasible_count ?? 0 ?></strong>
                                    <br><small class="text-muted">Feasible</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <strong class="text-danger"><?= $not_feasible_count ?? 0 ?></strong>
                                    <br><small class="text-muted">Not Feasible</small>
                                </div>
                            </div>
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
                    <h2><i class="fas fa-history"></i> Assessment History</h2>
                    <a href="/assessments" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Assessment
                    </a>
                </div>

                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Room name..." value="<?= $search ?? '' ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="feasibility" class="form-label">Feasibility</label>
                                <select class="form-select" id="feasibility" name="feasibility">
                                    <option value="">All</option>
                                    <option value="1" <?= ($feasibility_filter ?? '') == '1' ? 'selected' : '' ?>>Feasible</option>
                                    <option value="0" <?= ($feasibility_filter ?? '') == '0' ? 'selected' : '' ?>>Not Feasible</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="<?= $date_from ?? '' ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="<?= $date_to ?? '' ?>">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="/assessments/history" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Assessment History -->
                <?php if (empty($assessments)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h5>No assessments found</h5>
                        <p>You haven't conducted any assessments yet or no assessments match your search criteria.</p>
                        <a href="/assessments" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Start Your First Assessment
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Group assessments by date -->
                    <?php 
                    $groupedAssessments = [];
                    foreach ($assessments as $assessment) {
                        $date = date('Y-m-d', strtotime($assessment['created_at']));
                        if (!isset($groupedAssessments[$date])) {
                            $groupedAssessments[$date] = [];
                        }
                        $groupedAssessments[$date][] = $assessment;
                    }
                    ?>

                    <?php foreach ($groupedAssessments as $date => $dailyAssessments): ?>
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-day"></i> 
                                        <?= date('F j, Y', strtotime($date)) ?>
                                    </h6>
                                    <span class="badge bg-secondary"><?= count($dailyAssessments) ?> assessments</span>
                                </div>
                            </div>
                            <div class="list-group list-group-flush">
                                <?php foreach ($dailyAssessments as $assessment): ?>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <h6 class="mb-1">
                                                    <i class="fas fa-door-open"></i> <?= esc($assessment['room_name']) ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <?php if (isset($assessment['location']) && $assessment['location']): ?>
                                                        <i class="fas fa-map-marker-alt"></i> <?= esc($assessment['location']) ?>
                                                    <?php else: ?>
                                                        <i class="fas fa-door-open"></i> Room Assessment
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <div class="badge bg-info fs-6">
                                                    <?= number_format($assessment['score'], 1) ?>
                                                </div>
                                                <br><small class="text-muted">Asset Score</small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <?php 
                                                    $feasible = isset($assessment['is_feasible']) ? $assessment['is_feasible'] : ($assessment['score'] >= 7);
                                                    $badgeClass = $feasible ? 'bg-success' : 'bg-danger';
                                                    $icon = $feasible ? 'fa-check-circle' : 'fa-times-circle';
                                                ?>
                                                <span class="badge <?= $badgeClass ?>">
                                                    <i class="fas <?= $icon ?>"></i> 
                                                    <?= $feasible ? 'Good' : 'Poor' ?>
                                                </span>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> 
                                                    <?= date('H:i', strtotime($assessment['created_at'])) ?>
                                                </small>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <a href="/assessments/details/<?= $assessment['id'] ?>" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> Details
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <!-- Asset Count and Score Breakdown -->
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-cube"></i> <?= $assessment['asset_count'] ?? 0 ?> assets assessed
                                                <?php if (isset($assessment['score_range'])): ?>
                                                    | Scores: <?= $assessment['score_range'] ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        
                                        <!-- Notes Preview -->
                                        <?php if (!empty($assessment['overall_notes'])): ?>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-sticky-note"></i> 
                                                    <?= substr(esc($assessment['overall_notes']), 0, 100) ?><?= strlen($assessment['overall_notes']) > 100 ? '...' : '' ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Pagination -->
                    <?php if (isset($pager)): ?>
                        <nav aria-label="Assessment history pagination">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Showing <?= ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1 ?> to 
                                    <?= min($pager->getCurrentPage() * $pager->getPerPage(), $pager->getTotal()) ?> 
                                    of <?= $pager->getTotal() ?> assessments
                                </small>
                                <?= $pager->links() ?>
                            </div>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Export Options -->
                <?php if (!empty($assessments)): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-download"></i> Export Options</h6>
                        </div>
                        <div class="card-body">
                            <div class="btn-group" role="group">
                                <a href="/assessments/export/csv<?= !empty($_GET) ? '?' . http_build_query($_GET) : '' ?>" 
                                   class="btn btn-outline-success">
                                    <i class="fas fa-file-csv"></i> Export to CSV
                                </a>
                                <a href="/assessments/export/pdf<?= !empty($_GET) ? '?' . http_build_query($_GET) : '' ?>" 
                                   class="btn btn-outline-danger">
                                    <i class="fas fa-file-pdf"></i> Export to PDF
                                </a>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Export will include all assessments matching your current filters.
                            </small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit form on select change
        document.getElementById('feasibility').addEventListener('change', function() {
            this.form.submit();
        });
        
        // Date validation
        document.getElementById('date_from').addEventListener('change', function() {
            const fromDate = this.value;
            const toDateField = document.getElementById('date_to');
            if (fromDate && toDateField.value && fromDate > toDateField.value) {
                alert('From date cannot be later than To date');
                this.value = '';
            }
        });
        
        document.getElementById('date_to').addEventListener('change', function() {
            const toDate = this.value;
            const fromDateField = document.getElementById('date_from');
            if (toDate && fromDateField.value && toDate < fromDateField.value) {
                alert('To date cannot be earlier than From date');
                this.value = '';
            }
        });
        
        // Keyboard shortcut for new assessment
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                window.location.href = '/assessments';
            }
        });
        
        // Auto-refresh data every 5 minutes if no user interaction
        let lastActivity = Date.now();
        let refreshTimer;
        
        function resetRefreshTimer() {
            lastActivity = Date.now();
            clearTimeout(refreshTimer);
            refreshTimer = setTimeout(() => {
                if (Date.now() - lastActivity >= 300000) { // 5 minutes
                    window.location.reload();
                }
            }, 300000);
        }
        
        // Track user activity
        ['click', 'keydown', 'scroll', 'mousemove'].forEach(event => {
            document.addEventListener(event, resetRefreshTimer);
        });
        
        resetRefreshTimer();
    </script>
</body>
</html>
