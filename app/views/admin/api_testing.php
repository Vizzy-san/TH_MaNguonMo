<?php ob_start(); ?>

<div class="container-fluid">
    <h1 class="h3 mb-4">API Testing Interface</h1>
    
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Request</h6>
                </div>
                <div class="card-body">
                    <!-- API Testing Form -->
                    <form id="apiTestForm" action="javascript:void(0);">
                        <div class="form-group">
                            <label for="apiEndpoint">API Endpoint</label>
                            <select class="form-control" id="apiEndpointSelect">
                                <option value="">-- Select an endpoint --</option>
                                <?php foreach ($endpoints as $group => $group_endpoints): ?>
                                    <optgroup label="<?= ucfirst($group) ?>">
                                        <?php foreach ($group_endpoints as $ep): ?>
                                            <option value="<?= $ep['endpoint'] ?>" 
                                                    data-method="<?= $ep['method'] ?>"
                                                    data-description="<?= $ep['description'] ?>">
                                                <?= $ep['method'] ?> <?= $ep['endpoint'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="customEndpoint">Custom Endpoint</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="customEndpoint" placeholder="/BFYL/api/...">
                                <div class="input-group-append">
                                    <select class="form-control" id="httpMethod">
                                        <option value="GET">GET</option>
                                        <option value="POST">POST</option>
                                        <option value="PUT">PUT</option>
                                        <option value="DELETE">DELETE</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="endpointIdParam">ID Parameter <small class="text-muted">(for endpoints with {id})</small></label>
                            <input type="text" class="form-control" id="endpointIdParam" placeholder="ID value">
                        </div>
                        
                        <div class="form-group">
                            <label for="authToken">JWT Token <small class="text-muted">(Required for authorized endpoints)</small></label>
                            <input type="text" class="form-control" id="authToken" placeholder="Bearer token">
                        </div>
                        
                        <div class="form-group">
                            <label for="requestBody">Request Body <small class="text-muted">(for POST and PUT requests)</small></label>
                            <textarea class="form-control" id="requestBody" rows="6" placeholder='{
    "name": "Product Name",
    "description": "Product Description",
    "price": 10000,
    "category_id": 1
}'></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="sendRequest">
                            <i class="fas fa-paper-plane"></i> Send Request
                        </button>
                        <button type="button" class="btn btn-secondary" id="generateToken">
                            <i class="fas fa-key"></i> Generate Token
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Response</h6>
                    <div>
                        <span class="badge badge-secondary" id="statusCode"></span>
                        <span class="badge badge-secondary" id="responseTime"></span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="responseHeaders" class="mb-3"></div>
                    <pre id="responseBody" class="border p-3 bg-light" style="height: 400px; overflow: auto;"></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- API Documentation Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">API Documentation</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Endpoint</th>
                            <th>Description</th>
                            <th>Required Auth</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($endpoints as $group => $group_endpoints): ?>
                            <?php foreach ($group_endpoints as $ep): ?>
                                <tr>
                                    <td>
                                        <span class="badge badge-<?= $ep['method'] === 'GET' ? 'primary' : 
                                            ($ep['method'] === 'POST' ? 'success' : 
                                            ($ep['method'] === 'PUT' ? 'warning' : 'danger')) ?>">
                                            <?= $ep['method'] ?>
                                        </span>
                                    </td>
                                    <td><code><?= $ep['endpoint'] ?></code></td>
                                    <td><?= $ep['description'] ?></td>
                                    <td>
                                        <span class="badge badge-dark">Yes</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal for token generation with password masking -->
<div class="modal fade" id="tokenGenerationModal" tabindex="-1" role="dialog" aria-labelledby="tokenModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tokenModalLabel">Generate JWT Token</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="tokenPhone">Phone Number</label>
                    <input type="text" class="form-control" id="tokenPhone" placeholder="Enter phone number">
                </div>
                <div class="form-group">
                    <label for="tokenPassword">Password</label>
                    <input type="password" class="form-control" id="tokenPassword" placeholder="Enter password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmTokenGeneration">Generate</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiEndpointSelect = document.getElementById('apiEndpointSelect');
    const customEndpoint = document.getElementById('customEndpoint');
    const httpMethod = document.getElementById('httpMethod');
    const endpointIdParam = document.getElementById('endpointIdParam');
    const authToken = document.getElementById('authToken');
    const requestBody = document.getElementById('requestBody');
    const sendRequest = document.getElementById('sendRequest');
    const generateToken = document.getElementById('generateToken');
    const statusCode = document.getElementById('statusCode');
    const responseTime = document.getElementById('responseTime');
    const responseHeaders = document.getElementById('responseHeaders');
    const responseBody = document.getElementById('responseBody');
    
    // When selecting a predefined endpoint
    apiEndpointSelect.addEventListener('change', function() {
        if (this.value) {
            customEndpoint.value = this.value;
            httpMethod.value = this.options[this.selectedIndex].dataset.method;
            
            // If endpoint has {id} parameter, show the ID input field
            if (this.value.includes('{id}')) {
                endpointIdParam.parentElement.style.display = 'block';
            } else {
                endpointIdParam.parentElement.style.display = 'none';
            }
            
            // Show/hide request body input based on method
            if (httpMethod.value === 'POST' || httpMethod.value === 'PUT') {
                requestBody.parentElement.style.display = 'block';
            } else {
                requestBody.parentElement.style.display = 'none';
            }
        }
    });
    
    // When changing the HTTP method
    httpMethod.addEventListener('change', function() {
        // Show/hide request body input based on method
        if (this.value === 'POST' || this.value === 'PUT') {
            requestBody.parentElement.style.display = 'block';
        } else {
            requestBody.parentElement.style.display = 'none';
        }
    });
    
    // Generate JWT token - Updated to use modal with password masking
    generateToken.addEventListener('click', function() {
        // Show the modal instead of using prompt
        $('#tokenGenerationModal').modal('show');
    });
    
    // Handle token generation when user clicks the Generate button in the modal
    document.getElementById('confirmTokenGeneration').addEventListener('click', function() {
        const phone = document.getElementById('tokenPhone').value;
        const password = document.getElementById('tokenPassword').value;
        
        if (!phone || !password) {
            alert('Phone and password are required to generate a token');
            return;
        }
        
        // Make API request to get token
        fetch('/BFYL/account/apiLogin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ phone, password }),
            credentials: 'include'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Invalid credentials');
            }
            return response.json();
        })
        .then(data => {
            if (data.token) {
                authToken.value = "Bearer " + data.token;
                $('#tokenGenerationModal').modal('hide');
                // Clear the password field for security
                document.getElementById('tokenPassword').value = '';
                alert('Token generated successfully!');
            } else {
                alert('Failed to generate token.');
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    });
    
    // Send API request - Ensure token is properly included
    document.getElementById('apiTestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let url = customEndpoint.value;
        
        // Replace {id} with actual value if provided
        if (url.includes('{id}') && endpointIdParam.value) {
            url = url.replace('{id}', endpointIdParam.value);
        } else if (url.includes('{id}')) {
            alert('Please provide an ID parameter');
            return;
        }
        
        // Prepare headers
        const headers = new Headers();
        headers.append('Content-Type', 'application/json');
        
        // Add authorization header if provided - Ensure it's formatted correctly
        if (authToken.value) {
            const tokenValue = authToken.value.trim();
            headers.append('Authorization', tokenValue.startsWith('Bearer ') ? tokenValue : 'Bearer ' + tokenValue);
        }
        
        // Prepare fetch options
        const options = {
            method: httpMethod.value,
            headers: headers,
            credentials: 'include'
        };
        
        // Add body for POST and PUT requests
        if ((httpMethod.value === 'POST' || httpMethod.value === 'PUT') && requestBody.value) {
            try {
                const bodyJson = JSON.parse(requestBody.value);
                options.body = JSON.stringify(bodyJson);
            } catch (error) {
                alert('Invalid JSON in request body');
                return;
            }
        }
        
        // Clear previous response
        statusCode.textContent = '';
        responseTime.textContent = '';
        responseHeaders.innerHTML = '';
        responseBody.textContent = 'Loading...';
        
        // Start timer
        const startTime = Date.now();
        
        // Make the API request
        fetch(url, options)
            .then(response => {
                // Display status code
                statusCode.textContent = response.status + ' ' + response.statusText;
                statusCode.className = 'badge badge-' + (response.ok ? 'success' : 'danger');
                
                // Display response headers
                responseHeaders.innerHTML = '<strong>Headers:</strong><br>';
                response.headers.forEach((value, key) => {
                    responseHeaders.innerHTML += `<code>${key}: ${value}</code><br>`;
                });
                
                return response.text();
            })
            .then(text => {
                // Calculate response time
                const endTime = Date.now();
                responseTime.textContent = (endTime - startTime) + 'ms';
                
                // Try to parse as JSON
                try {
                    const json = JSON.parse(text);
                    responseBody.textContent = JSON.stringify(json, null, 2);
                } catch (error) {
                    // If not JSON, display as text
                    responseBody.textContent = text;
                }
            })
            .catch(error => {
                statusCode.textContent = 'Error';
                statusCode.className = 'badge badge-danger';
                responseBody.textContent = error.toString();
            });
    });
});
</script>

<?php
$content = ob_get_clean();
include 'app/views/admin/layout.php';
?>
