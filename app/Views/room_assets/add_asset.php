<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Asset to Room - <?= esc($room['name']) ?> - Asset Assessment System</title>
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
                            <a href="/room-assets" class="list-group-item list-group-item-action active">
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

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/room-assets">Room-Asset Relations</a></li>
                        <li class="breadcrumb-item"><a href="/room-assets/show/<?= $room['id'] ?>"><?= esc($room['name']) ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Asset</li>
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-plus"></i> Add Asset to <?= esc($room['name']) ?></h2>
                    <a href="/room-assets/show/<?= $room['id'] ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Room
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if (isset($availableAssets) && count($availableAssets) > 0): ?>
                            <form method="post" action="/room-assets/add-asset/<?= $room['id'] ?>">
                                <div class="mb-3">
                                    <label for="asset_id" class="form-label">Select Asset</label>
                                    <select class="form-select" id="asset_id" name="asset_id" required>
                                        <option value="">Choose an asset to add</option>
                                        <?php foreach ($availableAssets as $asset): ?>
                                            <option value="<?= $asset['id'] ?>">
                                                <?= esc($asset['asset_name']) ?> - <?= esc($asset['category']) ?>
                                                (Weight: <?= $asset['weight'] ?>, Benefit: <?= $asset['benefit_score'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="/room-assets/show/<?= $room['id'] ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add Asset
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-cube fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No available assets</h5>
                                <p class="text-muted">All assets are already assigned to this room, or no assets exist in the system.</p>
                                <a href="/assets/create" class="btn btn-primary me-2">
                                    <i class="fas fa-plus"></i> Create New Asset
                                </a>
                                <a href="/room-assets/show/<?= $room['id'] ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Room
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
