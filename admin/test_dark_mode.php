<?php
require '../connect.php';
require_once __DIR__ . '/auth.php';
include('header.php');
include('sidebar.php');
include('navbar.php');
?>

<div class="content p-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-soft rounded-4 p-4 mb-4">
                <h3 class="section-heading mb-4">
                    <i class="fas fa-palette me-2"></i>
                    Dark Mode Test Page
                </h3>
                <p class="lead">This page tests various UI elements in both light and dark modes.</p>
            </div>
        </div>
    </div>

    <!-- Test Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="info-card shadow-soft rounded-4 p-4 h-100 modern-card">
                <div class="d-flex align-items-center mb-3">
                    <div class="tile-icon-wrapper me-3">
                        <i class="fas fa-check tile-icon text-white"></i>
                    </div>
                    <span class="fw-semibold">Test Card 1</span>
                </div>
                <div class="display-6 fw-bold text-primary mb-2">123</div>
                <div class="metric-small">Sample metric</div>
                <div class="mt-3">
                    <div class="mini-progress">
                        <div style="width: 75%;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="summary-tile shadow-soft rounded-4 p-3 h-100 clickable-card">
                <div class="d-flex align-items-center mb-3">
                    <div class="tile-icon-wrapper me-3">
                        <i class="fas fa-star tile-icon text-white"></i>
                    </div>
                    <span class="fw-semibold">Clickable Card</span>
                </div>
                <div class="display-6 fw-bold text-success mb-2">456</div>
                <div class="metric-small text-muted">Click me!</div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="chart-card">
                <h6>Chart Card</h6>
                <div class="canvas-wrapper p-3">
                    <p class="text-center text-muted">Chart content would go here</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-soft">
                <div class="card-header">
                    <h5 class="mb-0">Test Table</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Test Item 1</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Test Item 2</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Test Item 3</td>
                                <td><span class="badge bg-secondary">Inactive</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Form -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-soft">
                <div class="card-header">
                    <h5 class="mb-0">Test Form</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label for="testInput" class="form-label">Text Input</label>
                            <input type="text" class="form-control" id="testInput" placeholder="Enter text here">
                        </div>
                        <div class="mb-3">
                            <label for="testSelect" class="form-label">Select</label>
                            <select class="form-select" id="testSelect">
                                <option selected>Choose...</option>
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="testTextarea" class="form-label">Textarea</label>
                            <textarea class="form-control" id="testTextarea" rows="3" placeholder="Enter description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary ms-2">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-soft">
                <div class="card-header">
                    <h5 class="mb-0">Test Alerts</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-primary" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        This is a primary alert with an icon.
                    </div>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        This is a success alert with an icon.
                    </div>
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This is a warning alert with an icon.
                    </div>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-times-circle me-2"></i>
                        This is a danger alert with an icon.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-soft">
                <div class="card-header">
                    <h5 class="mb-0">Test Buttons</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary me-2">Primary</button>
                    <button type="button" class="btn btn-secondary me-2">Secondary</button>
                    <button type="button" class="btn btn-success me-2">Success</button>
                    <button type="button" class="btn btn-danger me-2">Danger</button>
                    <button type="button" class="btn btn-warning me-2">Warning</button>
                    <button type="button" class="btn btn-info me-2">Info</button>
                    <button type="button" class="btn btn-outline-primary me-2">Outline Primary</button>
                    <button type="button" class="btn btn-outline-secondary">Outline Secondary</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-soft">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Testing Instructions
                    </h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Click the dark mode toggle button in the navbar (moon/sun icon)</li>
                        <li>Verify that all elements change colors appropriately</li>
                        <li>Check that text remains readable in both modes</li>
                        <li>Test form inputs and buttons</li>
                        <li>Verify that the dark mode preference persists after page refresh</li>
                    </ol>
                    <div class="alert alert-info mt-3">
                        <strong>Note:</strong> The dark mode setting is saved in localStorage and will persist across browser sessions.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click handler to clickable card
    document.querySelector('.clickable-card').addEventListener('click', function() {
        alert('Clickable card clicked! Dark mode is ' + (document.body.classList.contains('dark-mode') ? 'enabled' : 'disabled'));
    });
});
</script>

<?php include('footer.php'); ?>
