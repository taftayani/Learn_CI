<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms - Asset Assessment System</title>
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
                    <h2><i class="fas fa-door-open"></i> Available Rooms for Assessment</h2>
                    <a href="/assessments" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Assessments
                    </a>
                </div>

                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search Rooms</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Room name or location..." value="<?= $search ?? '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="location" class="form-label">Location</label>
                                <select class="form-select" id="location" name="location">
                                    <option value="">All Locations</option>
                                    <?php if (isset($locations)) foreach ($locations as $location): ?>
                                        <option value="<?= esc($location) ?>" <?= ($location_filter ?? '') == $location ? 'selected' : '' ?>>
                                            <?= esc($location) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="not_assessed" <?= ($status_filter ?? '') == 'not_assessed' ? 'selected' : '' ?>>Not Assessed</option>
                                    <option value="assessed" <?= ($status_filter ?? '') == 'assessed' ? 'selected' : '' ?>>Previously Assessed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Rooms Grid -->
                <?php if (empty($rooms)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h5>No rooms available</h5>
                        <p>There are no rooms matching your search criteria or no rooms have been configured for assessment.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($rooms as $room): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><?= esc($room['name']) ?></h5>
                                        <?php if (isset($room['last_assessed']) && $room['last_assessed']): ?>
                                            <span class="badge bg-info">
                                                <i class="fas fa-history"></i> Assessed
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-exclamation"></i> New
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-door-open"></i> Room #<?= $room['id'] ?>
                                            </small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt"></i> <?= esc($room['location'] ?? 'N/A') ?>
                                            </small>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-cube"></i> <?= $room['asset_count'] ?? 0 ?> Assets
                                            </small>
                                        </div>
                                        
                                        <?php if (isset($room['last_assessment'])): ?>
                                            <div class="mb-2">
                                                <small class="text-muted">Last Assessment:</small><br>
                                                <small><?= date('M j, Y', strtotime($room['last_assessment']['created_at'])) ?></small>
                                                <?php 
                                                    $feasible = $room['last_assessment']['is_feasible'];
                                                    $badgeClass = $feasible ? 'bg-success' : 'bg-danger';
                                                    $icon = $feasible ? 'fa-check-circle' : 'fa-times-circle';
                                                ?>
                                                <span class="badge <?= $badgeClass ?> ms-2">
                                                    <i class="fas <?= $icon ?>"></i> 
                                                    <?= $feasible ? 'Feasible' : 'Not Feasible' ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-grid gap-2">
                                            <a href="/assessments/assess/<?= $room['id'] ?>" class="btn btn-primary">
                                                <i class="fas fa-clipboard-check"></i> Start Assessment
                                            </a>
                                            <?php if (isset($room['last_assessment'])): ?>
                                                <a href="/assessments/details/<?= $room['last_assessment']['id'] ?>" 
                                                   class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-eye"></i> View Last Assessment
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($pager)): ?>
                        <nav aria-label="Rooms pagination">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Showing <?= ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1 ?> to 
                                    <?= min($pager->getCurrentPage() * $pager->getPerPage(), $pager->getTotal()) ?> 
                                    of <?= $pager->getTotal() ?> rooms
                                </small>
                                <?= $pager->links() ?>
                            </div>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Legend -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Legend</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <small>
                                    <span class="badge bg-warning me-2"><i class="fas fa-exclamation"></i> New</span>
                                    Room has not been assessed yet
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small>
                                    <span class="badge bg-info me-2"><i class="fas fa-history"></i> Assessed</span>
                                    Room has been previously assessed
                                </small>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <small>
                                    <span class="badge bg-success me-2"><i class="fas fa-check-circle"></i> Feasible</span>
                                    Last assessment result was feasible
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small>
                                    <span class="badge bg-danger me-2"><i class="fas fa-times-circle"></i> Not Feasible</span>
                                    Last assessment result was not feasible
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit form on select change
        document.getElementById('location').addEventListener('change', function() {
            this.form.submit();
        });
        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>
