<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asset - Asset Assessment System</title>
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
                    <h2><i class="fas fa-edit"></i> Edit Asset</h2>
                    <a href="/assets" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Assets
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Asset Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/assets/update/<?= $asset['id'] ?>">
                            <input type="hidden" name="_method" value="PUT">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            Asset Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control <?= (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('name')) ? 'is-invalid' : '' ?>" 
                                               id="name" 
                                               name="name" 
                                               value="<?= old('name', $asset['name']) ?>" 
                                               placeholder="Enter asset name..." 
                                               required>
                                        <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('name')): ?>
                                            <div class="invalid-feedback">
                                                <?= session()->getFlashdata('validation')->getError('name') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <input type="text" 
                                               class="form-control <?= (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('category')) ? 'is-invalid' : '' ?>" 
                                               id="category" 
                                               name="category" 
                                               value="<?= old('category', $asset['category']) ?>" 
                                               placeholder="Enter asset category (optional)..."
                                               list="categoryList">
                                        <datalist id="categoryList">
                                            <option value="Furniture">
                                            <option value="Electronics">
                                            <option value="Equipment">
                                            <option value="Tools">
                                            <option value="Fixtures">
                                            <option value="Vehicles">
                                            <option value="Software">
                                            <option value="Infrastructure">
                                        </datalist>
                                        <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('category')): ?>
                                            <div class="invalid-feedback">
                                                <?= session()->getFlashdata('validation')->getError('category') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control <?= (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('description')) ? 'is-invalid' : '' ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          placeholder="Enter asset description..."><?= old('description', $asset['description']) ?></textarea>
                                <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('description')): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('validation')->getError('description') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="weight" class="form-label">
                                            Weight 
                                            <i class="fas fa-info-circle" data-bs-toggle="tooltip" 
                                               title="Weight represents the importance/priority of the asset. Higher values indicate more critical assets. Default: 1.00"></i>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" 
                                                   class="form-control <?= (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('weight')) ? 'is-invalid' : '' ?>" 
                                                   id="weight" 
                                                   name="weight" 
                                                   value="<?= old('weight', number_format((float)($asset['weight'] ?? 1.00), 2, '.', '')) ?>" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="10" 
                                                   placeholder="1.00">
                                            <span class="input-group-text">
                                                <i class="fas fa-balance-scale"></i>
                                            </span>
                                        </div>
                                        <div class="form-text">
                                            <small>
                                                <span class="text-success">2.0+: High importance</span> | 
                                                <span class="text-warning">1.5-1.9: Medium importance</span> | 
                                                <span class="text-danger">&lt;1.5: Low importance</span>
                                            </small>
                                        </div>
                                        <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('weight')): ?>
                                            <div class="invalid-feedback">
                                                <?= session()->getFlashdata('validation')->getError('weight') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="benefit_score" class="form-label">
                                            Benefit Score 
                                            <i class="fas fa-info-circle" data-bs-toggle="tooltip" 
                                               title="Benefit Score represents the value/utility this asset provides. Higher scores indicate more beneficial assets. Default: 0.00"></i>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" 
                                                   class="form-control <?= (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('benefit_score')) ? 'is-invalid' : '' ?>" 
                                                   id="benefit_score" 
                                                   name="benefit_score" 
                                                   value="<?= old('benefit_score', number_format((float)($asset['benefit_score'] ?? 0.00), 2, '.', '')) ?>" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="10" 
                                                   placeholder="0.00">
                                            <span class="input-group-text">
                                                <i class="fas fa-star"></i>
                                            </span>
                                        </div>
                                        <div class="form-text">
                                            <small>
                                                <span class="text-success">2.0+: High benefit</span> | 
                                                <span class="text-warning">1.0-1.9: Medium benefit</span> | 
                                                <span class="text-danger">&lt;1.0: Low benefit</span>
                                            </small>
                                        </div>
                                        <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('benefit_score')): ?>
                                            <div class="invalid-feedback">
                                                <?= session()->getFlashdata('validation')->getError('benefit_score') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Values Display -->
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info text-primary"></i> Current Asset Details
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="card-text">
                                                <strong>Current Weight:</strong> 
                                                <?php
                                                $currentWeight = floatval($asset['weight'] ?? 1.00);
                                                $weightClass = $currentWeight >= 2.0 ? 'text-success' : ($currentWeight >= 1.5 ? 'text-warning' : 'text-danger');
                                                ?>
                                                <span class="<?= $weightClass ?> fw-bold">
                                                    <?= number_format($currentWeight, 2) ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="card-text">
                                                <strong>Current Benefit Score:</strong> 
                                                <?php
                                                $currentBenefit = floatval($asset['benefit_score'] ?? 0.00);
                                                $benefitClass = $currentBenefit >= 2.0 ? 'text-success' : ($currentBenefit >= 1.0 ? 'text-warning' : 'text-danger');
                                                ?>
                                                <span class="<?= $benefitClass ?> fw-bold">
                                                    <?= number_format($currentBenefit, 2) ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-lightbulb text-warning"></i> Understanding Weight and Benefit Score
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="card-text mb-0">
                                                <strong>Weight:</strong> How critical is this asset to operations? 
                                                A server might have high weight (2.5), while a decorative plant has low weight (0.5).
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="card-text mb-0">
                                                <strong>Benefit Score:</strong> What value does this asset provide? 
                                                An efficient printer might have high benefit (3.0), while an old broken chair has low benefit (0.1).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="/assets" class="btn btn-secondary me-md-2">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Asset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Asset History/Relations (if applicable) -->
                <?php if (!empty($asset['created_at']) || !empty($asset['updated_at'])): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history"></i> Asset History
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if (!empty($asset['created_at'])): ?>
                            <div class="col-md-6">
                                <p><strong>Created:</strong> <?= date('d M Y, H:i', strtotime($asset['created_at'])) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($asset['updated_at'])): ?>
                            <div class="col-md-6">
                                <p><strong>Last Updated:</strong> <?= date('d M Y, H:i', strtotime($asset['updated_at'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
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

        // Real-time validation feedback
        document.getElementById('weight').addEventListener('input', function() {
            const value = parseFloat(this.value);
            
            if (value >= 2.0) {
                this.classList.remove('border-warning', 'border-danger');
                this.classList.add('border-success');
            } else if (value >= 1.5) {
                this.classList.remove('border-success', 'border-danger');
                this.classList.add('border-warning');
            } else {
                this.classList.remove('border-success', 'border-warning');
                this.classList.add('border-danger');
            }
        });

        document.getElementById('benefit_score').addEventListener('input', function() {
            const value = parseFloat(this.value);
            
            if (value >= 2.0) {
                this.classList.remove('border-warning', 'border-danger');
                this.classList.add('border-success');
            } else if (value >= 1.0) {
                this.classList.remove('border-success', 'border-danger');
                this.classList.add('border-warning');
            } else {
                this.classList.remove('border-success', 'border-warning');
                this.classList.add('border-danger');
            }
        });

        // Apply initial border colors based on current values
        document.addEventListener('DOMContentLoaded', function() {
            const weightInput = document.getElementById('weight');
            const benefitInput = document.getElementById('benefit_score');
            
            // Trigger input events to apply initial styling
            weightInput.dispatchEvent(new Event('input'));
            benefitInput.dispatchEvent(new Event('input'));
        });
    </script>
</body>
</html>
