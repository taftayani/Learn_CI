<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Reports - Asset Assessment System</title>
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
                            <a href="/reports" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-bar"></i> Reports Dashboard
                            </a>
                            <a href="/reports/assets" class="list-group-item list-group-item-action active">
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
                    <h2><i class="fas fa-chart-line"></i> Asset Reports</h2>
                    <div>
                        <button type="button" class="btn btn-outline-success" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="exportToExcel()">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-filter"></i> Filters</h5>
                    </div>
                    <div class="card-body">
                        <form id="filterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $from_date ?? date('Y-m-01') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $to_date ?? date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Room</label>
                                    <select class="form-select" name="room_id" id="room_id">
                                        <option value="">All Rooms</option>
                                        <?php if (isset($rooms) && !empty($rooms)): ?>
                                            <?php foreach ($rooms as $room): ?>
                                                <option value="<?= $room['id'] ?>" <?= ($selected_room ?? '') == $room['id'] ? 'selected' : '' ?>>
                                                    <?= esc($room['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                                        <i class="fas fa-search"></i> Apply Filters
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Search Assets</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Enter asset name..." id="searchInput">
                                        <button class="btn btn-outline-secondary" type="button" onclick="searchAssets()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Feasibility Range</label>
                                    <select class="form-select" name="feasibility_range" id="feasibility_range">
                                        <option value="">All Ranges</option>
                                        <option value="high" <?= ($selected_range ?? '') == 'high' ? 'selected' : '' ?>>High (80-100%)</option>
                                        <option value="medium" <?= ($selected_range ?? '') == 'medium' ? 'selected' : '' ?>>Medium (60-79%)</option>
                                        <option value="low" <?= ($selected_range ?? '') == 'low' ? 'selected' : '' ?>>Low (0-59%)</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Asset Performance Chart -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Asset Performance Overview</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="assetPerformanceChart" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Asset Scores -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Detailed Asset Scores</h5>
                        <div>
                            <small class="text-muted">Total: <?= $total_count ?? 0 ?> assets</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="assetTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Asset Name</th>
                                        <th>Room</th>
                                        <th>Category</th>
                                        <th>Last Assessment</th>
                                        <th>Feasibility Score</th>
                                        <th>Physical Condition</th>
                                        <th>Functionality</th>
                                        <th>Safety Compliance</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($asset_reports) && !empty($asset_reports)): ?>
                                        <?php foreach ($asset_reports as $report): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($report['asset_name'] ?? 'N/A') ?></strong>
                                                    <br><small class="text-muted"><?= esc($report['asset_code'] ?? 'N/A') ?></small>
                                                </td>
                                                <td><?= esc($report['room_name'] ?? 'N/A') ?></td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= esc($report['asset_category'] ?? 'N/A') ?></span>
                                                </td>
                                                <td><?= isset($report['last_assessment']) ? date('M d, Y', strtotime($report['last_assessment'])) : 'Never' ?></td>
                                                <td>
                                                    <?php
                                                    $feasibility = $report['feasibility_percentage'] ?? 0;
                                                    $feasibilityClass = $feasibility >= 80 ? 'success' : ($feasibility >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                            <div class="progress-bar bg-<?= $feasibilityClass ?>" style="width: <?= $feasibility ?>%">
                                                                <?= number_format($feasibility, 1) ?>%
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $physical = $report['physical_condition_score'] ?? 0;
                                                    $physicalClass = $physical >= 80 ? 'success' : ($physical >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $physicalClass ?>"><?= number_format($physical, 1) ?>%</span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $functionality = $report['functionality_score'] ?? 0;
                                                    $functionalityClass = $functionality >= 80 ? 'success' : ($functionality >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $functionalityClass ?>"><?= number_format($functionality, 1) ?>%</span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $safety = $report['safety_compliance_score'] ?? 0;
                                                    $safetyClass = $safety >= 80 ? 'success' : ($safety >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $safetyClass ?>"><?= number_format($safety, 1) ?>%</span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status = $feasibility >= 80 ? 'Excellent' : ($feasibility >= 60 ? 'Good' : 'Needs Attention');
                                                    $statusClass = $feasibility >= 80 ? 'success' : ($feasibility >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>"><?= $status ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewAssetDetails(<?= $report['asset_id'] ?? 0 ?>)" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="viewAssessmentHistory(<?= $report['asset_id'] ?? 0 ?>)" title="View History">
                                                            <i class="fas fa-history"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                <i class="fas fa-search fa-3x mb-3"></i><br>
                                                No asset assessments found for the selected criteria.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if (isset($pager) && $pager): ?>
                            <div class="mt-3">
                                <?= $pager->links() ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Details Modal -->
    <div class="modal fade" id="assetDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Asset Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="assetDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            initializeAssetPerformanceChart();
        });

        function initializeAssetPerformanceChart() {
            const ctx = document.getElementById('assetPerformanceChart').getContext('2d');
            
            const data = {
                labels: <?= json_encode(array_column($asset_reports ?? [], 'asset_name')) ?>,
                datasets: [
                    {
                        label: 'Feasibility %',
                        data: <?= json_encode(array_column($asset_reports ?? [], 'feasibility_percentage')) ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Physical Condition %',
                        data: <?= json_encode(array_column($asset_reports ?? [], 'physical_condition_score')) ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Functionality %',
                        data: <?= json_encode(array_column($asset_reports ?? [], 'functionality_score')) ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2
                    }
                ]
            };

            new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }

        function applyFilters() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            window.location.href = '/reports/assets?' + params.toString();
        }

        function resetFilters() {
            document.getElementById('filterForm').reset();
            window.location.href = '/reports/assets';
        }

        function searchAssets() {
            const searchTerm = document.getElementById('searchInput').value;
            const table = document.getElementById('assetTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const assetName = rows[i].getElementsByTagName('td')[0];
                if (assetName) {
                    const textValue = assetName.textContent || assetName.innerText;
                    if (textValue.toLowerCase().indexOf(searchTerm.toLowerCase()) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }

        function exportToPDF() {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);
            window.open('/reports/assets/export/pdf?' + params.toString(), '_blank');
        }

        function exportToExcel() {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);
            window.open('/reports/assets/export/excel?' + params.toString(), '_blank');
        }

        function viewAssetDetails(assetId) {
            fetch(`/api/assets/${assetId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('assetDetailsContent').innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Asset Information</h6>
                                <p><strong>Name:</strong> ${data.name}</p>
                                <p><strong>Code:</strong> ${data.code}</p>
                                <p><strong>Category:</strong> ${data.category}</p>
                                <p><strong>Room:</strong> ${data.room_name}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Latest Scores</h6>
                                <p><strong>Feasibility:</strong> ${data.feasibility_percentage}%</p>
                                <p><strong>Physical Condition:</strong> ${data.physical_condition_score}%</p>
                                <p><strong>Functionality:</strong> ${data.functionality_score}%</p>
                                <p><strong>Safety Compliance:</strong> ${data.safety_compliance_score}%</p>
                            </div>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('assetDetailsModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading asset details');
                });
        }

        function viewAssessmentHistory(assetId) {
            window.open(`/assessments/history?asset_id=${assetId}`, '_blank');
        }
    </script>
</body>
</html>
