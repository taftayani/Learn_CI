<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Room Assets - <?= esc($room['name']) ?> - Asset Assessment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .asset-item {
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }
        .asset-item:hover {
            border-left-color: #007bff;
            background-color: #f8f9fa;
        }
        .score-badge {
            font-size: 0.75rem;
        }
        .room-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }
        .available-assets {
            max-height: 400px;
            overflow-y: auto;
        }
        .asset-select-item {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .asset-select-item:hover {
            background-color: #e9ecef;
        }
        .asset-select-item.selected {
            background-color: #cfe2ff;
            border-color: #007bff;
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

                <!-- Breadcrumb and Header -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/room-assets"><i class="fas fa-link"></i> Room-Asset Relations</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= esc($room['name']) ?></li>
                    </ol>
                </nav>

                <!-- Room Information Card -->
                <div class="card mb-4 room-header">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-2">
                                    <i class="fas fa-door-open me-2"></i>
                                    <?= esc($room['name']) ?>
                                </h3>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($room['location'])): ?>
                                        <span class="me-3">
                                            <i class="fas fa-map-marker-alt"></i> <?= esc($room['location']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-cube"></i> <?= count($room['assets']) ?> assets assigned
                                    </span>
                                </div>
                                <?php if (!empty($room['description'])): ?>
                                    <p class="mt-2 mb-0 text-white-75">
                                        <?= esc($room['description']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                                    <i class="fas fa-plus"></i> Add Assets
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Current Room Assets -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-cube text-primary"></i> Assets in <?= esc($room['name']) ?>
                                </h5>
                                <div>
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <input type="text" id="assetSearch" class="form-control" placeholder="Search assets...">
                                        <button class="btn btn-outline-secondary" type="button" onclick="clearAssetSearch()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (isset($room['assets']) && count($room['assets']) > 0): ?>
                                    <div id="assetsList">
                                        <?php foreach ($room['assets'] as $asset): ?>
                                            <div class="asset-item border rounded p-3 mb-3" 
                                                 data-asset-name="<?= esc(strtolower($asset['asset_name'])) ?>"
                                                 data-asset-category="<?= esc(strtolower($asset['asset_category'] ?? '')) ?>">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h6 class="mb-1">
                                                            <i class="fas fa-cube text-primary me-2"></i>
                                                            <?= esc($asset['asset_name']) ?>
                                                        </h6>
                                                        <?php if (!empty($asset['asset_category'])): ?>
                                                            <small class="text-muted">
                                                                <i class="fas fa-tags"></i> <?= esc($asset['asset_category']) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                        <?php if (!empty($asset['asset_description'])): ?>
                                                            <p class="mb-0 mt-1">
                                                                <small class="text-muted">
                                                                    <?= esc(strlen($asset['asset_description']) > 100 ? substr($asset['asset_description'], 0, 100) . '...' : $asset['asset_description']) ?>
                                                                </small>
                                                            </p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <div class="text-center me-3">
                                                                <div class="badge bg-info score-badge">
                                                                    Weight: <?= $asset['weight'] ?>
                                                                </div>
                                                            </div>
                                                            <div class="text-center">
                                                                <div class="badge bg-success score-badge">
                                                                    Benefit: <?= $asset['benefit_score'] ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="confirmRemoveAsset(<?= $asset['asset_id'] ?>, '<?= esc($asset['asset_name']) ?>')">
                                                            <i class="fas fa-times"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div id="noAssetResults" style="display: none;">
                                        <div class="text-center py-4">
                                            <i class="fas fa-search fa-2x text-muted mb-3"></i>
                                            <h6 class="text-muted">No assets found matching your search</h6>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-cube fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">No assets assigned to this room</h5>
                                        <p class="text-muted">Start by adding assets to this room using the "Add Assets" button above.</p>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                                            <i class="fas fa-plus"></i> Add First Asset
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Asset Modal -->
    <div class="modal fade" id="addAssetModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" action="/room-assets/add-assets/<?= $room['id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Assets to <?= esc($room['name']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php if (isset($available_assets) && count($available_assets) > 0): ?>
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" id="modalAssetSearch" class="form-control" placeholder="Search available assets...">
                                    <button class="btn btn-outline-secondary" type="button" onclick="clearModalAssetSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="selectAllAssets">
                                <label class="form-check-label fw-bold" for="selectAllAssets">
                                    Select All Available Assets
                                </label>
                            </div>

                            <div class="available-assets" id="availableAssetsList">
                                <?php foreach ($available_assets as $asset): ?>
                                    <div class="asset-select-item border rounded p-2 mb-2" 
                                         data-asset-id="<?= $asset['id'] ?>"
                                         data-asset-name="<?= esc(strtolower($asset['asset_name'])) ?>"
                                         data-asset-category="<?= esc(strtolower($asset['category'] ?? '')) ?>">
                                        <div class="form-check">
                                            <input class="form-check-input asset-checkbox" 
                                                   type="checkbox" 
                                                   name="asset_ids[]" 
                                                   value="<?= $asset['id'] ?>" 
                                                   id="asset_<?= $asset['id'] ?>">
                                            <label class="form-check-label w-100" for="asset_<?= $asset['id'] ?>">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?= esc($asset['asset_name']) ?></strong>
                                                        <?php if (!empty($asset['category'])): ?>
                                                            <br><small class="text-muted">
                                                                <i class="fas fa-tags"></i> <?= esc($asset['category']) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-info score-badge me-1">W: <?= $asset['weight'] ?></span>
                                                        <span class="badge bg-success score-badge">B: <?= $asset['benefit_score'] ?></span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div id="noModalResults" style="display: none;">
                                <div class="text-center py-4">
                                    <i class="fas fa-search fa-2x text-muted mb-3"></i>
                                    <h6 class="text-muted">No available assets found matching your search</h6>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No available assets</h5>
                                <p class="text-muted">All assets are already assigned to this room, or no assets exist in the system.</p>
                                <a href="/assets/create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create New Asset
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <?php if (isset($available_assets) && count($available_assets) > 0): ?>
                            <button type="submit" class="btn btn-primary" id="addSelectedAssets" disabled>
                                <i class="fas fa-plus"></i> Add Selected Assets
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Asset Confirmation Modal -->
    <div class="modal fade" id="removeAssetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Asset Removal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove <strong id="assetName"></strong> from this room?</p>
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        This will only remove the asset from this room, not delete the asset entirely.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="removeAssetForm" method="post" style="display: inline;">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Remove Asset
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality for current assets
        document.getElementById('assetSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const assetItems = document.querySelectorAll('.asset-item');
            let visibleCount = 0;

            assetItems.forEach(function(item) {
                const assetName = item.getAttribute('data-asset-name');
                const assetCategory = item.getAttribute('data-asset-category');
                
                if (assetName.includes(searchTerm) || assetCategory.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            document.getElementById('noAssetResults').style.display = visibleCount === 0 ? 'block' : 'none';
        });

        function clearAssetSearch() {
            document.getElementById('assetSearch').value = '';
            document.getElementById('assetSearch').dispatchEvent(new Event('keyup'));
        }

        // Search functionality for available assets in modal
        document.getElementById('modalAssetSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const assetItems = document.querySelectorAll('.asset-select-item');
            let visibleCount = 0;

            assetItems.forEach(function(item) {
                const assetName = item.getAttribute('data-asset-name');
                const assetCategory = item.getAttribute('data-asset-category');
                
                if (assetName.includes(searchTerm) || assetCategory.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            document.getElementById('noModalResults').style.display = visibleCount === 0 ? 'block' : 'none';
        });

        function clearModalAssetSearch() {
            document.getElementById('modalAssetSearch').value = '';
            document.getElementById('modalAssetSearch').dispatchEvent(new Event('keyup'));
        }

        // Select all functionality
        document.getElementById('selectAllAssets').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.asset-checkbox');
            const isChecked = this.checked;
            
            checkboxes.forEach(function(checkbox) {
                if (checkbox.closest('.asset-select-item').style.display !== 'none') {
                    checkbox.checked = isChecked;
                }
            });
            
            updateAddButton();
        });

        // Asset selection handling
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('asset-checkbox')) {
                updateAddButton();
                
                // Update select all checkbox state
                const allCheckboxes = document.querySelectorAll('.asset-checkbox');
                const visibleCheckboxes = Array.from(allCheckboxes).filter(cb => 
                    cb.closest('.asset-select-item').style.display !== 'none'
                );
                const checkedCount = visibleCheckboxes.filter(cb => cb.checked).length;
                
                const selectAllCheckbox = document.getElementById('selectAllAssets');
                selectAllCheckbox.checked = checkedCount === visibleCheckboxes.length && visibleCheckboxes.length > 0;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < visibleCheckboxes.length;
            }
        });

        function updateAddButton() {
            const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
            const addButton = document.getElementById('addSelectedAssets');
            
            if (addButton) {
                addButton.disabled = checkedBoxes.length === 0;
                addButton.innerHTML = checkedBoxes.length === 0 ? 
                    '<i class="fas fa-plus"></i> Add Selected Assets' : 
                    `<i class="fas fa-plus"></i> Add Selected Assets (${checkedBoxes.length})`;
            }
        }

        // Remove asset confirmation
        function confirmRemoveAsset(assetId, assetName) {
            document.getElementById('assetName').textContent = assetName;
            document.getElementById('removeAssetForm').action = `/room-assets/remove-asset/<?= $room['id'] ?>/${assetId}`;
            const removeModal = new bootstrap.Modal(document.getElementById('removeAssetModal'));
            removeModal.show();
        }

        // Asset item click to select
        document.addEventListener('click', function(e) {
            if (e.target.closest('.asset-select-item')) {
                const item = e.target.closest('.asset-select-item');
                const checkbox = item.querySelector('.asset-checkbox');
                
                if (e.target !== checkbox && e.target.type !== 'checkbox') {
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
</body>
</html>
