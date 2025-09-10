<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room-Asset Relations - Asset Assessment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .room-card {
            transition: transform 0.2s ease-in-out;
        }
        .room-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .asset-badge {
            font-size: 0.8rem;
            margin: 2px;
        }
        .empty-room {
            border: 2px dashed #dee2e6;
            background-color: #f8f9fa;
        }
        .room-stats {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
        }
        .badge-score {
            font-size: 0.75rem;
        }
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
                            <a href="/room-assets" class="list-group-item list-group-item-action active">
                                <i class="fas fa-link"></i> Room-Asset Relations
                            </a>
                        <?php endif; ?>
                        
                        <?php if (in_array($user_role, ['Super Admin', 'Admin', 'Leader'])): ?>
                            <a href="/assessments/admin" class="list-group-item list-group-item-action">
                                <i class="fas fa-clipboard-list"></i> View All Assessments
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
                    <h2><i class="fas fa-link"></i> Room-Asset Relations</h2>
                </div>

                <!-- Statistics Card -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card room-stats">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4><?= $total_rooms ?? 0 ?></h4>
                                        <p class="mb-0">Total Rooms</p>
                                    </div>
                                    <i class="fas fa-door-open fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4><?= $rooms_with_assets ?? 0 ?></h4>
                                        <p class="mb-0">Rooms with Assets</p>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4><?= $empty_rooms ?? 0 ?></h4>
                                        <p class="mb-0">Empty Rooms</p>
                                    </div>
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4><?= $total_relations ?? 0 ?></h4>
                                        <p class="mb-0">Total Relations</p>
                                    </div>
                                    <i class="fas fa-link fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search rooms by name or location...">
                                    <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select id="filterStatus" class="form-select">
                                    <option value="">All Rooms</option>
                                    <option value="with-assets">Rooms with Assets</option>
                                    <option value="empty">Empty Rooms</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex justify-content-end">
                                    <small class="text-muted align-self-center">
                                        <span id="resultCount"><?= isset($rooms) ? count($rooms) : 0 ?></span> room(s) found
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rooms Grid -->
                <div class="row" id="roomsContainer">
                    <?php if (isset($rooms) && count($rooms) > 0): ?>
                        <?php foreach ($rooms as $room): ?>
                            <div class="col-lg-6 col-xl-4 mb-4 room-item" 
                                 data-room-name="<?= esc(strtolower($room['name'])) ?>"
                                 data-room-location="<?= esc(strtolower($room['location'] ?? '')) ?>"
                                 data-asset-count="<?= count($room['assets'] ?? []) ?>">
                                
                                <div class="card room-card <?= count($room['assets'] ?? []) == 0 ? 'empty-room' : '' ?> h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="fas fa-door-open text-primary me-2"></i>
                                            <?= esc($room['name']) ?>
                                        </h6>
                                        <div>
                                            <span class="badge <?= count($room['assets'] ?? []) > 0 ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= count($room['assets'] ?? []) ?> assets
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body">
                                        <!-- Room Details -->
                                        <div class="mb-3">
                                            <?php if (!empty($room['location'])): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i> <?= esc($room['location']) ?>
                                                </small>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($room['description'])): ?>
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <?= esc(strlen($room['description']) > 80 ? substr($room['description'], 0, 80) . '...' : $room['description']) ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Assets List -->
                                        <?php if (count($room['assets'] ?? []) > 0): ?>
                                            <div class="mb-3">
                                                <small class="fw-bold text-muted mb-2 d-block">Associated Assets:</small>
                                                <div class="d-flex flex-wrap">
                                                    <?php foreach ($room['assets'] as $asset): ?>
                                                        <span class="badge bg-primary asset-badge me-1 mb-1" 
                                                              data-bs-toggle="tooltip" 
                                                              title="Weight: <?= $asset['weight'] ?> | Benefit: <?= $asset['benefit_score'] ?>">
                                                            <i class="fas fa-cube"></i> <?= esc($asset['asset_name']) ?>
                                                            <span class="badge-score ms-1">(W:<?= $asset['weight'] ?>, B:<?= $asset['benefit_score'] ?>)</span>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-3">
                                                <i class="fas fa-cube fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">No assets assigned</p>
                                                <small class="text-muted">Click "Manage Assets" to assign assets to this room</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> 
                                                <?= date('M d, Y', strtotime($room['created_at'])) ?>
                                            </small>
                                            <div>
                                                <a href="/room-assets/show/<?= $room['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-cog"></i> Manage Assets
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-link fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No room-asset relations found</h5>
                                <p class="text-muted">Start by creating rooms and assets, then establish relations between them.</p>
                                <div>
                                    <a href="/rooms/create" class="btn btn-success me-2">
                                        <i class="fas fa-plus"></i> Add Room
                                    </a>
                                    <a href="/assets/create" class="btn btn-info">
                                        <i class="fas fa-plus"></i> Add Asset
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            filterRooms();
        });

        document.getElementById('filterStatus').addEventListener('change', function() {
            filterRooms();
        });

        function filterRooms() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('filterStatus').value;
            const roomItems = document.querySelectorAll('.room-item');
            let visibleCount = 0;

            roomItems.forEach(function(item) {
                const roomName = item.getAttribute('data-room-name');
                const roomLocation = item.getAttribute('data-room-location');
                const assetCount = parseInt(item.getAttribute('data-asset-count'));
                
                let showBySearch = true;
                let showByStatus = true;

                // Search filter
                if (searchTerm) {
                    showBySearch = roomName.includes(searchTerm) || roomLocation.includes(searchTerm);
                }

                // Status filter
                if (statusFilter) {
                    if (statusFilter === 'with-assets') {
                        showByStatus = assetCount > 0;
                    } else if (statusFilter === 'empty') {
                        showByStatus = assetCount === 0;
                    }
                }

                if (showBySearch && showByStatus) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            document.getElementById('resultCount').textContent = visibleCount;
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterStatus').value = '';
            filterRooms();
        }
    </script>
</body>
</html>
