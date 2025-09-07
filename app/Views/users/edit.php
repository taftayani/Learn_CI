<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Asset Assessment System</title>
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
                            <a href="/users" class="list-group-item list-group-item-action active">
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
                    <h2><i class="fas fa-user-edit"></i> Edit User</h2>
                    <a href="/users" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">User Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="/users/update/<?= esc($edit_user['id']) ?>">
                            <input type="hidden" name="_method" value="PUT">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('name') ? 'is-invalid' : '' ?>" 
                                               id="name" name="name" value="<?= old('name', $edit_user['name']) ?>" required>
                                        <?php if (isset($validation) && $validation->hasError('name')): ?>
                                            <div class="invalid-feedback">
                                                <?= $validation->getError('name') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>" 
                                               id="email" name="email" value="<?= old('email', $edit_user['email']) ?>" required>
                                        <?php if (isset($validation) && $validation->hasError('email')): ?>
                                            <div class="invalid-feedback">
                                                <?= $validation->getError('email') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password <span class="text-muted">(optional)</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>" 
                                                   id="password" name="password">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                                <i class="fas fa-eye" id="password-eye"></i>
                                            </button>
                                            <?php if (isset($validation) && $validation->hasError('password')): ?>
                                                <div class="invalid-feedback">
                                                    <?= $validation->getError('password') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-text">Leave empty to keep current password. Must be at least 6 characters if changing.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control <?= isset($validation) && $validation->hasError('confirm_password') ? 'is-invalid' : '' ?>" 
                                                   id="confirm_password" name="confirm_password">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                                <i class="fas fa-eye" id="confirm_password-eye"></i>
                                            </button>
                                            <?php if (isset($validation) && $validation->hasError('confirm_password')): ?>
                                                <div class="invalid-feedback">
                                                    <?= $validation->getError('confirm_password') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">User Role <span class="text-danger">*</span></label>
                                        <select class="form-select <?= isset($validation) && $validation->hasError('role') ? 'is-invalid' : '' ?>" 
                                                id="role" name="role" required>
                                            <option value="">Select Role</option>
                                            <?php if ($user_role === 'Super Admin'): ?>
                                                <option value="Super Admin" <?= old('role', $edit_user['role']) == 'Super Admin' ? 'selected' : '' ?>>Super Admin</option>
                                            <?php endif; ?>
                                            <option value="Admin" <?= old('role', $edit_user['role']) == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="Leader" <?= old('role', $edit_user['role']) == 'Leader' ? 'selected' : '' ?>>Leader</option>
                                            <option value="GA Staff" <?= old('role', $edit_user['role']) == 'GA Staff' ? 'selected' : '' ?>>GA Staff</option>
                                        </select>
                                        <?php if (isset($validation) && $validation->hasError('role')): ?>
                                            <div class="invalid-feedback">
                                                <?= $validation->getError('role') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Role Permissions</label>
                                        <div class="form-text" id="roleDescription">
                                            Select a role to see permissions
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title"><i class="fas fa-info-circle"></i> User Information</h6>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <small class="text-muted">User ID:</small><br>
                                                        <strong><?= esc($edit_user['id']) ?></strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <small class="text-muted">Created:</small><br>
                                                        <strong><?= date('M d, Y', strtotime($edit_user['created_at'])) ?></strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <small class="text-muted">Last Updated:</small><br>
                                                        <strong><?= date('M d, Y', strtotime($edit_user['updated_at'])) ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="/users" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <div>
                                    <?php if ($edit_user['id'] != session()->get('user_id')): ?>
                                        <button type="button" class="btn btn-danger me-2" 
                                                onclick="confirmDelete(<?= $edit_user['id'] ?>, '<?= esc($edit_user['name']) ?>')">
                                            <i class="fas fa-trash"></i> Delete User
                                        </button>
                                    <?php endif; ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update User
                                    </button>
                                </div>
                            </div>
                        </form>
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
                    <p>Are you sure you want to delete user <strong id="userName"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="post" style="display: inline;">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        function confirmDelete(userId, userName) {
            document.getElementById('userName').textContent = userName;
            document.getElementById('deleteForm').action = '/users/delete/' + userId;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            const description = document.getElementById('roleDescription');
            
            switch(role) {
                case 'Super Admin':
                    description.innerHTML = '<i class="fas fa-crown text-danger"></i> Full system access, can manage all users and settings';
                    break;
                case 'Admin':
                    description.innerHTML = '<i class="fas fa-user-shield text-warning"></i> Can manage users, rooms, and assets';
                    break;
                case 'Leader':
                    description.innerHTML = '<i class="fas fa-chart-bar text-primary"></i> Can view reports and analytics';
                    break;
                case 'GA Staff':
                    description.innerHTML = '<i class="fas fa-clipboard-check text-success"></i> Can perform asset assessments';
                    break;
                default:
                    description.innerHTML = 'Select a role to see permissions';
            }
        });

        // Trigger role description on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('role').dispatchEvent(new Event('change'));
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password && password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
        });
    </script>
</body>
</html>
