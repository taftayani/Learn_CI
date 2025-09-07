<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Reports - Asset Assessment System</title>
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
                            <a href="/reports/assets" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-line"></i> Asset Reports
                            </a>
                            <a href="/reports/rooms" class="list-group-item list-group-item-action active">
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
                    <h2><i class="fas fa-building"></i> Room Reports</h2>
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
                        <h5 class="mb-0"><i class="fas fa-filter"></i> Filters & Search</h5>
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
                                    <label class="form-label">Building/Floor</label>
                                    <select class="form-select" name="location_filter" id="location_filter">
                                        <option value="">All Locations</option>
                                        <?php if (isset($locations) && !empty($locations)): ?>
                                            <?php foreach ($locations as $location): ?>
                                                <option value="<?= esc($location) ?>" <?= ($selected_location ?? '') == $location ? 'selected' : '' ?>>
                                                    <?= esc($location) ?>
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
                                    <label class="form-label">Search Rooms</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Enter room name..." id="searchInput">
                                        <button class="btn btn-outline-secondary" type="button" onclick="searchRooms()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Performance Range</label>
                                    <select class="form-select" name="performance_range" id="performance_range">
                                        <option value="">All Ranges</option>
                                        <option value="excellent" <?= ($selected_performance ?? '') == 'excellent' ? 'selected' : '' ?>>Excellent (80-100%)</option>
                                        <option value="good" <?= ($selected_performance ?? '') == 'good' ? 'selected' : '' ?>>Good (60-79%)</option>
                                        <option value="poor" <?= ($selected_performance ?? '') == 'poor' ? 'selected' : '' ?>>Needs Attention (0-59%)</option>
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

                <!-- Room Comparison Chart -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-radar"></i> Room Performance Comparison</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="roomComparisonChart" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room Statistics -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?= $excellent_rooms ?? 0 ?></h4>
                                        <small>Excellent Rooms</small>
                                        <div class="small">(80-100% avg score)</div>
                                    </div>
                                    <i class="fas fa-thumbs-up fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?= $good_rooms ?? 0 ?></h4>
                                        <small>Good Rooms</small>
                                        <div class="small">(60-79% avg score)</div>
                                    </div>
                                    <i class="fas fa-check fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?= $attention_rooms ?? 0 ?></h4>
                                        <small>Needs Attention</small>
                                        <div class="small">(0-59% avg score)</div>
                                    </div>
                                    <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room Assessment Summary -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-table"></i> Room Assessment Summary</h5>
                        <div>
                            <small class="text-muted">Total: <?= $total_rooms ?? 0 ?> rooms</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="roomTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Room Name</th>
                                        <th>Location</th>
                                        <th>Total Assets</th>
                                        <th>Assessments</th>
                                        <th>Avg Feasibility</th>
                                        <th>Physical Condition</th>
                                        <th>Functionality</th>
                                        <th>Safety Compliance</th>
                                        <th>Overall Performance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($room_reports) && !empty($room_reports)): ?>
                                        <?php foreach ($room_reports as $room): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($room['room_name'] ?? 'N/A') ?></strong>
                                                    <br><small class="text-muted"><?= esc($room['room_code'] ?? 'N/A') ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?= esc($room['location'] ?? 'N/A') ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= $room['total_assets'] ?? 0 ?> assets</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary"><?= $room['total_assessments'] ?? 0 ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $avg_feasibility = $room['avg_feasibility'] ?? 0;
                                                    $feasibilityClass = $avg_feasibility >= 80 ? 'success' : ($avg_feasibility >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                            <div class="progress-bar bg-<?= $feasibilityClass ?>" style="width: <?= $avg_feasibility ?>%">
                                                                <?= number_format($avg_feasibility, 1) ?>%
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $physical = $room['avg_physical_condition'] ?? 0;
                                                    $physicalClass = $physical >= 80 ? 'success' : ($physical >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $physicalClass ?>"><?= number_format($physical, 1) ?>%</span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $functionality = $room['avg_functionality'] ?? 0;
                                                    $functionalityClass = $functionality >= 80 ? 'success' : ($functionality >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $functionalityClass ?>"><?= number_format($functionality, 1) ?>%</span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $safety = $room['avg_safety_compliance'] ?? 0;
                                                    $safetyClass = $safety >= 80 ? 'success' : ($safety >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $safetyClass ?>"><?= number_format($safety, 1) ?>%</span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $overall = ($avg_feasibility + $physical + $functionality + $safety) / 4;
                                                    $overallStatus = $overall >= 80 ? 'Excellent' : ($overall >= 60 ? 'Good' : 'Needs Attention');
                                                    $overallClass = $overall >= 80 ? 'success' : ($overall >= 60 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?= $overallClass ?>"><?= $overallStatus ?></span>
                                                    <br><small class="text-muted"><?= number_format($overall, 1) ?>%</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewRoomDetails(<?= $room['room_id'] ?? 0 ?>)" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="viewRoomAssets(<?= $room['room_id'] ?? 0 ?>)" title="View Assets">
                                                            <i class="fas fa-cube"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="generateRoomReport(<?= $room['room_id'] ?? 0 ?>)" title="Generate Report">
                                                            <i class="fas fa-file-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                <i class="fas fa-building fa-3x mb-3"></i><br>
                                                No room data found for the selected criteria.
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

    <!-- Room Details Modal -->
    <div class="modal fade" id="roomDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Room Details & Performance Metrics</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="roomDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            initializeRoomComparisonChart();
        });

        function initializeRoomComparisonChart() {
            const ctx = document.getElementById('roomComparisonChart').getContext('2d');
            
            const data = {
                labels: <?= json_encode(array_column($room_reports ?? [], 'room_name')) ?>,
                datasets: [
                    {
                        label: 'Feasibility %',
                        data: <?= json_encode(array_column($room_reports ?? [], 'avg_feasibility')) ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Physical Condition %',
                        data: <?= json_encode(array_column($room_reports ?? [], 'avg_physical_condition')) ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Functionality %',
                        data: <?= json_encode(array_column($room_reports ?? [], 'avg_functionality')) ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Safety Compliance %',
                        data: <?= json_encode(array_column($room_reports ?? [], 'avg_safety_compliance')) ?>,
                        backgroundColor: 'rgba(255, 206, 86, 0.6)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 2
                    }
                ]
            };

            new Chart(ctx, {
                type: 'radar',
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
                        r: {
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
            window.location.href = '/reports/rooms?' + params.toString();
        }

        function resetFilters() {
            document.getElementById('filterForm').reset();
            window.location.href = '/reports/rooms';
        }

        function searchRooms() {
            const searchTerm = document.getElementById('searchInput').value;
            const table = document.getElementById('roomTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const roomName = rows[i].getElementsByTagName('td')[0];
                if (roomName) {
                    const textValue = roomName.textContent || roomName.innerText;
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
            window.open('/reports/rooms/export/pdf?' + params.toString(), '_blank');
        }

        function exportToExcel() {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);
            window.open('/reports/rooms/export/excel?' + params.toString(), '_blank');
        }

        function viewRoomDetails(roomId) {
            fetch(`/api/rooms/${roomId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('roomDetailsContent').innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Room Information</h6>
                                <p><strong>Name:</strong> ${data.name}</p>
                                <p><strong>Code:</strong> ${data.code}</p>
                                <p><strong>Location:</strong> ${data.location}</p>
                                <p><strong>Total Assets:</strong> ${data.total_assets}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Performance Summary</h6>
                                <p><strong>Avg Feasibility:</strong> ${data.avg_feasibility}%</p>
                                <p><strong>Avg Physical Condition:</strong> ${data.avg_physical_condition}%</p>
                                <p><strong>Avg Functionality:</strong> ${data.avg_functionality}%</p>
                                <p><strong>Avg Safety Compliance:</strong> ${data.avg_safety_compliance}%</p>
                            </div>
                        </div>
                        <hr>
                        <h6>Asset Performance Chart</h6>
                        <canvas id="roomAssetChart" height="300"></canvas>
                    `;
                    
                    // Initialize chart for room assets
                    const ctx = document.getElementById('roomAssetChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.asset_names,
                            datasets: [{
                                label: 'Feasibility %',
                                data: data.asset_feasibilities,
                                backgroundColor: 'rgba(54, 162, 235, 0.8)'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                    
                    new bootstrap.Modal(document.getElementById('roomDetailsModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading room details');
                });
        }

        function viewRoomAssets(roomId) {
            window.open(`/reports/assets?room_id=${roomId}`, '_blank');
        }

        function generateRoomReport(roomId) {
            window.open(`/reports/rooms/${roomId}/detailed`, '_blank');
        }
    </script>
</body>
</html>
