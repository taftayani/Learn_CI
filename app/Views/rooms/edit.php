<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room - Asset Assessment System</title>
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
                            <a href="/rooms" class="list-group-item list-group-item-action active">
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
                    <h2><i class="fas fa-door-open"></i> Edit Room</h2>
                    <a href="/rooms" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Rooms
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Room Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="/rooms/edit/<?= esc($room['id']) ?>" id="roomForm">
                            <input type="hidden" name="_method" value="PUT">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Room Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('name') ? 'is-invalid' : '' ?>" 
                                               id="name" name="name" value="<?= old('name', $room['name']) ?>" required 
                                               placeholder="e.g., Conference Room A, Server Room">
                                        <?php if (isset($validation) && $validation->hasError('name')): ?>
                                            <div class="invalid-feedback">
                                                <?= $validation->getError('name') ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-text">Enter a unique and descriptive name for the room.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location <span class="text-muted">(optional)</span></label>
                                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('location') ? 'is-invalid' : '' ?>" 
                                               id="location" name="location" value="<?= old('location', $room['location']) ?>" 
                                               placeholder="e.g., 2nd Floor, Building A, East Wing">
                                        <?php if (isset($validation) && $validation->hasError('location')): ?>
                                            <div class="invalid-feedback">
                                                <?= $validation->getError('location') ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-text">Specify the physical location or address.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label for="description" class="form-label">Description <span class="text-muted">(optional)</span></label>
                                        <textarea class="form-control <?= isset($validation) && $validation->hasError('description') ? 'is-invalid' : '' ?>" 
                                                  id="description" name="description" rows="4" 
                                                  placeholder="Provide additional details about the room, its purpose, capacity, or special features..."><?= old('description', $room['description']) ?></textarea>
                                        <?php if (isset($validation) && $validation->hasError('description')): ?>
                                            <div class="invalid-feedback">
                                                <?= $validation->getError('description') ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-text">
                                            <span id="charCount">0</span>/500 characters
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Room Preview Card -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card bg-light mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-eye"></i> Room Preview</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-door-open text-success fa-2x me-3"></i>
                                                        <div>
                                                            <h6 class="mb-0" id="previewName">Room Name</h6>
                                                            <small class="text-muted" id="previewLocation">Location will appear here</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">Description:</small>
                                                    <p class="mb-0 small" id="previewDescription">Description will appear here...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Room Information Summary -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card bg-info bg-opacity-10 mb-4">
                                        <div class="card-body">
                                            <h6 class="card-title"><i class="fas fa-info-circle text-info"></i> Room Information</h6>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <small class="text-muted">Room ID:</small><br>
                                                    <strong><?= esc($room['id']) ?></strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Created:</small><br>
                                                    <strong><?= date('M d, Y', strtotime($room['created_at'])) ?></strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Last Updated:</small><br>
                                                    <strong><?= date('M d, Y', strtotime($room['updated_at'])) ?></strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Status:</small><br>
                                                    <span class="badge bg-success">Active</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="/rooms" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <div>
                                    <button type="button" class="btn btn-danger me-2" 
                                            onclick="confirmDelete(<?= $room['id'] ?>, '<?= esc($room['name']) ?>')">
                                        <i class="fas fa-trash"></i> Delete Room
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Update Room
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
                    <p>Are you sure you want to delete room <strong id="roomName"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone and may affect related assets.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="post" style="display: inline;">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Room
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(roomId, roomName) {
            document.getElementById('roomName').textContent = roomName;
            document.getElementById('deleteForm').action = '/rooms/delete/' + roomId;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Character counter for description
        const descriptionField = document.getElementById('description');
        const charCount = document.getElementById('charCount');
        const maxLength = 500;

        function updateCharCount() {
            const length = descriptionField.value.length;
            charCount.textContent = length;
            
            if (length > maxLength) {
                charCount.className = 'text-danger';
                descriptionField.value = descriptionField.value.substring(0, maxLength);
                charCount.textContent = maxLength;
            } else if (length > maxLength * 0.9) {
                charCount.className = 'text-warning';
            } else {
                charCount.className = 'text-muted';
            }
        }

        descriptionField.addEventListener('input', updateCharCount);

        // Live preview functionality
        function updatePreview() {
            const name = document.getElementById('name').value || 'Room Name';
            const location = document.getElementById('location').value || 'Location will appear here';
            const description = document.getElementById('description').value || 'Description will appear here...';

            document.getElementById('previewName').textContent = name;
            document.getElementById('previewLocation').textContent = location;
            document.getElementById('previewDescription').textContent = description;
        }

        // Update preview on input
        document.getElementById('name').addEventListener('input', updatePreview);
        document.getElementById('location').addEventListener('input', updatePreview);
        document.getElementById('description').addEventListener('input', updatePreview);

        // Form validation
        document.getElementById('roomForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            
            if (!name) {
                e.preventDefault();
                alert('Room name is required!');
                document.getElementById('name').focus();
                return false;
            }

            if (name.length < 2) {
                e.preventDefault();
                alert('Room name must be at least 2 characters long!');
                document.getElementById('name').focus();
                return false;
            }
        });

        // Initialize character count and preview on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCharCount();
            updatePreview();
        });
    </script>
</body>
</html>
