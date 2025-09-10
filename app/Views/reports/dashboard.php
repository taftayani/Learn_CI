<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Dashboard - Asset Assessment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                            <a href="/assessments/history" class="list-group-item list-group-item-action">
                                <i class="fas fa-history"></i> Assessment History
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($user_role === 'Leader'): ?>
                            <a href="/reports" class="list-group-item list-group-item-action active">
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
                    <h2><i class="fas fa-chart-bar"></i> Reports Dashboard</h2>
                    <div>
                        <button type="button" class="btn btn-outline-success" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="exportToExcel()">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" class="form-control" id="from_date" value="<?= date('Y-m-01') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" class="form-control" id="to_date" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-primary" onclick="refreshReports()">
                                    <i class="fas fa-sync"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?= $total_assessments ?? 0 ?></h4>
                                        <small>Total Assessments</small>
                                    </div>
                                    <i class="fas fa-clipboard-check fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?= $total_rooms ?? 0 ?></h4>
                                        <small>Total Rooms</small>
                                    </div>
                                    <i class="fas fa-door-open fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?= $total_assets ?? 0 ?></h4>
                                        <small>Total Assets</small>
                                    </div>
                                    <i class="fas fa-cube fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?= number_format($avg_feasibility ?? 0, 1) ?>%</h4>
                                        <small>Avg Feasibility</small>
                                    </div>
                                    <i class="fas fa-percentage fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Feasibility Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="feasibilityChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Assessments Trend</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="trendChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feasibility Overview -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Feasibility Overview</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <h6 class="text-success">High Feasibility (80-100%)</h6>
                                            <div class="progress mb-2" style="height: 25px;">
                                                <div class="progress-bar bg-success" style="width: <?= ($high_feasibility_count ?? 0) > 0 ? (($high_feasibility_count ?? 0) / ($total_assessments ?? 1)) * 100 : 0 ?>%">
                                                    <?= $high_feasibility_count ?? 0 ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <h6 class="text-warning">Medium Feasibility (60-79%)</h6>
                                            <div class="progress mb-2" style="height: 25px;">
                                                <div class="progress-bar bg-warning" style="width: <?= ($medium_feasibility_count ?? 0) > 0 ? (($medium_feasibility_count ?? 0) / ($total_assessments ?? 1)) * 100 : 0 ?>%">
                                                    <?= $medium_feasibility_count ?? 0 ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <h6 class="text-danger">Low Feasibility (0-59%)</h6>
                                            <div class="progress mb-2" style="height: 25px;">
                                                <div class="progress-bar bg-danger" style="width: <?= ($low_feasibility_count ?? 0) > 0 ? (($low_feasibility_count ?? 0) / ($total_assessments ?? 1)) * 100 : 0 ?>%">
                                                    <?= $low_feasibility_count ?? 0 ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Assessments -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Recent Assessments</h5>
                        <a href="/reports/assets" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Asset</th>
                                        <th>Assessment Date</th>
                                        <th>Feasibility</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($recent_assessments) && !empty($recent_assessments)): ?>
                                        <?php foreach ($recent_assessments as $assessment): ?>
                                            <tr>
                                                <td><?= esc($assessment['room_name'] ?? 'N/A') ?></td>
                                                <td><?= esc($assessment['asset_name'] ?? 'N/A') ?></td>
                                                <td><?= date('M d, Y', strtotime($assessment['created_at'] ?? 'now')) ?></td>
                                                <td>
                                                    <?php
                                                    $feasibility = $assessment['feasibility_score'] ?? 0;
                                                    $class = $feasibility >= 80 ? 'success' : ($feasibility >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $class ?>"><?= number_format($feasibility, 1) ?>%</span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status = $feasibility >= 80 ? 'Excellent' : ($feasibility >= 60 ? 'Good' : 'Needs Attention');
                                                    $statusClass = $feasibility >= 80 ? 'success' : ($feasibility >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>"><?= $status ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-clipboard-list fa-3x mb-3"></i><br>
                                                No assessments found for the selected period.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            initializeFeasibilityChart();
            initializeTrendChart();
        });

        function initializeFeasibilityChart() {
            const ctx = document.getElementById('feasibilityChart').getContext('2d');
            
            const data = {
                labels: ['High (80-100%)', 'Medium (60-79%)', 'Low (0-59%)'],
                datasets: [{
                    data: [
                        <?= $high_feasibility_count ?? 0 ?>,
                        <?= $medium_feasibility_count ?? 0 ?>,
                        <?= $low_feasibility_count ?? 0 ?>
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        function initializeTrendChart() {
            const ctx = document.getElementById('trendChart').getContext('2d');
            
            const data = {
                labels: <?= json_encode($trend_labels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) ?>,
                datasets: [{
                    label: 'Assessments',
                    data: <?= json_encode($trend_data ?? [10, 15, 12, 18, 20, 25]) ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            };

            new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function refreshReports() {
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            
            if (fromDate && toDate) {
                window.location.href = `/reports?from=${fromDate}&to=${toDate}`;
            } else {
                alert('Please select both from and to dates.');
            }
        }

        function exportToPDF() {
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            window.open(`/reports/export/pdf?from=${fromDate}&to=${toDate}`, '_blank');
        }

        function exportToExcel() {
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            window.open(`/reports/export/excel?from=${fromDate}&to=${toDate}`, '_blank');
        }
    </script>
</body>
</html>
