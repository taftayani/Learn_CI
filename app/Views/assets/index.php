<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Assets - Asset Assessment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .weight-high { color: #28a745; font-weight: bold; }
        .weight-medium { color: #ffc107; font-weight: bold; }
        .weight-low { color: #dc3545; font-weight: bold; }
        .benefit-high { color: #28a745; font-weight: bold; }
        .benefit-medium { color: #ffc107; font-weight: bold; }
        .benefit-low { color: #dc3545; font-weight: bold; }
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
                            <a href="/assets" class="list-group-item list-group-item-action active">
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
                    <h2><i class="fas fa-cube"></i> Manage Assets</h2>
                    <a href="/assets/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Asset
                    </a>
                </div>

                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="/assets">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="search" class="form-label">Search by Name</label>
                                        <input type="text" class="form-control" id="search" name="search" 
                                               value="<?= isset($search) ? esc($search) : '' ?>" 
                                               placeholder="Enter asset name...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Filter by Category</label>
                                        <select class="form-control" id="category" name="category">
                                            <option value="">All Categories</option>
                                            <?php if (!empty($categories)): ?>
                                                <?php foreach ($categories as $cat): ?>
                                                    <option value="<?= esc($cat) ?>" 
                                                            <?= (isset($category) && $category === $cat) ? 'selected' : '' ?>>
                                                        <?= esc($cat) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-grid gap-2 d-md-flex">
                                            <button type="submit" class="btn btn-outline-primary flex-fill">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                            <a href="/assets" class="btn btn-outline-secondary">
                                                <i class="fas fa-undo"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Assets Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Assets List (<?= count($assets ?? []) ?> items)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($assets)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th>
                                                Weight 
                                                <i class="fas fa-info-circle" data-bs-toggle="tooltip" 
                                                   title="Weight represents the importance/priority of the asset (higher = more important)"></i>
                                            </th>
                                            <th>
                                                Benefit Score
                                                <i class="fas fa-info-circle" data-bs-toggle="tooltip" 
                                                   title="Benefit Score represents the value/utility of the asset (higher = more beneficial)"></i>
                                            </th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($assets as $asset): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <strong><?= esc($asset['name']) ?></strong>
                                                </td>
                                                <td>
                                                    <?php if (!empty($asset['category'])): ?>
                                                        <span class="badge bg-secondary"><?= esc($asset['category']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= !empty($asset['description']) ? esc(substr($asset['description'], 0, 100)) . (strlen($asset['description']) > 100 ? '...' : '') : '-' ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $weight = floatval($asset['weight'] ?? 1.00);
                                                    $weightClass = $weight >= 2.0 ? 'weight-high' : ($weight >= 1.5 ? 'weight-medium' : 'weight-low');
                                                    ?>
                                                    <span class="<?= $weightClass ?>">
                                                        <?= number_format($weight, 2) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $benefit = floatval($asset['benefit_score'] ?? 0.00);
                                                    $benefitClass = $benefit >= 2.0 ? 'benefit-high' : ($benefit >= 1.0 ? 'benefit-medium' : 'benefit-low');
                                                    ?>
                                                    <span class="<?= $benefitClass ?>">
                                                        <?= number_format($benefit, 2) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="/assets/edit/<?= $asset['id'] ?>" 
                                                           class="btn btn-outline-primary btn-sm" 
                                                           data-bs-toggle="tooltip" title="Edit Asset">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                data-bs-toggle="tooltip" title="Delete Asset"
                                                                onclick="confirmDelete(<?= $asset['id'] ?>, '<?= esc($asset['name']) ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No assets found</h5>
                                <p class="text-muted">Start by adding your first asset.</p>
                                <a href="/assets/create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Asset
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the asset: <strong id="assetName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        This action cannot be undone and will also remove any related room-asset associations.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Asset
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        function confirmDelete(id, name) {
            document.getElementById('assetName').textContent = name;
            document.getElementById('deleteForm').action = '/assets/delete/' + id;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>
