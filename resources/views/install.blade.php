<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Aetheria HMS - Wizard</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: radial-gradient(circle at 10% 20%, rgba(110, 68, 255, 0.05) 0%, rgba(244, 68, 150, 0.05) 90%), var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
            font-family: 'Inter', sans-serif;
        }
        .install-card {
            max-width: 650px;
            width: 100%;
            border-top: 5px solid var(--primary);
            padding: 3rem 2.5rem;
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border-color);
            z-index: 1;
            transform: translateY(-50%);
        }
        .step-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--surface);
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text-muted);
            position: relative;
            z-index: 2;
            transition: all var(--transition-smooth);
        }
        .step-dot.active {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-glow);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }
        .step-dot.completed {
            border-color: var(--success);
            color: #fff;
            background: var(--success);
        }
        .install-step {
            display: none;
        }
        .install-step.active {
            display: block;
            animation: fadeIn var(--transition-smooth);
        }
        .compat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .compat-row:last-child {
            border-bottom: none;
        }
        .compat-status {
            font-size: 1.1rem;
        }
        .compat-status.success { color: var(--success); }
        .compat-status.danger { color: var(--danger); }
    </style>
</head>
<body>
    <div class="glass-panel install-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">Aetheria HMS Installer</h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.25rem;">Set up your stays flow management system in minutes.</p>
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-dot active" id="dot-1">1</div>
            <div class="step-dot" id="dot-2">2</div>
            <div class="step-dot" id="dot-3">3</div>
            <div class="step-dot" id="dot-4">4</div>
        </div>

        <!-- STEP 1: COMPATIBILITY -->
        <div class="install-step active" id="step-1">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;"><i class="fa-solid fa-server"></i> Server Compatibility Check</h3>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.5rem;">We need to make sure your hosting server supports stayFlow requirements.</p>

            <div style="background: rgba(0,0,0,0.01); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 2rem;">
                <div class="compat-row">
                    <span>PHP Version (>= 8.2)</span>
                    <span class="compat-status {{ $compatibilities['php']['passed'] ? 'success' : 'danger' }}">
                        <i class="fa-solid {{ $compatibilities['php']['passed'] ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                    </span>
                </div>
                
                @foreach($compatibilities['extensions'] as $ext => $extMeta)
                    <div class="compat-row">
                        <span>PHP Extension: <code>{{ $ext }}</code></span>
                        <span class="compat-status {{ $extMeta['passed'] ? 'success' : 'danger' }}">
                            <i class="fa-solid {{ $extMeta['passed'] ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                        </span>
                    </div>
                @endforeach

                @foreach($compatibilities['permissions'] as $path => $permMeta)
                    <div class="compat-row">
                        <span>Directory Writable: <code>{{ $path }}</code></span>
                        <span class="compat-status {{ $permMeta['passed'] ? 'success' : 'danger' }}">
                            <i class="fa-solid {{ $permMeta['passed'] ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                        </span>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; justify-content: flex-end;">
                @if($hasFailures)
                    <button class="btn btn-outline" onclick="window.location.reload()"><i class="fa-solid fa-arrows-rotate"></i> Re-check Compatibility</button>
                @else
                    <button class="btn btn-primary" onclick="goToStep(2)">Proceed to Database <i class="fa-solid fa-arrow-right"></i></button>
                @endif
            </div>
        </div>

        <!-- STEP 2: DATABASE CONFIGURATION -->
        <div class="install-step" id="step-2">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;"><i class="fa-solid fa-database"></i> Database Credentials</h3>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.5rem;">Configure the database connection for the software.</p>

            <div id="db-error-alert" class="alert alert-danger" style="display: none;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div id="db-error-text">Failed to connect.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="db-connection">Database Type</label>
                <select name="db_connection" id="db-connection" class="form-control" onchange="toggleDatabaseFields()">
                    <option value="sqlite">SQLite (Built-in file)</option>
                    <option value="mysql" selected>MySQL (External server)</option>
                </select>
            </div>

            <!-- SQLite Fields -->
            <div id="sqlite-fields" style="display: none;">
                <div class="form-group">
                    <label class="form-label" for="db-database-sqlite">Database File Path</label>
                    <input type="text" name="db_database_sqlite" id="db-database-sqlite" class="form-control" value="database/database.sqlite">
                    <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.4rem;">Paths are relative to the project root directory.</p>
                </div>
            </div>

            <!-- MySQL Fields -->
            <div id="mysql-fields">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="db-host">Host address</label>
                        <input type="text" name="db_host" id="db-host" class="form-control" value="127.0.0.1">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="db-port">Port number</label>
                        <input type="text" name="db_port" id="db-port" class="form-control" value="3306">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="db-database-mysql">Database Name</label>
                    <input type="text" name="db_database_mysql" id="db-database-mysql" class="form-control" value="stayflow_hms">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="db-username">Username</label>
                        <input type="text" name="db_username" id="db-username" class="form-control" value="root">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="db-password">Password</label>
                        <input type="password" name="db_password" id="db-password" class="form-control" placeholder="Leave empty if none">
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem;">
                <button class="btn btn-outline" onclick="goToStep(1)"><i class="fa-solid fa-arrow-left"></i> Back</button>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn btn-secondary" onclick="testDatabaseConnection(false)" id="test-connection-btn">
                        <i class="fa-solid fa-vial"></i> Test Connection
                    </button>
                    <button class="btn btn-primary" onclick="validateStep2()" id="proceed-step2-btn">
                        Next Step <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP 3: ADMINISTRATOR ACCOUNT -->
        <div class="install-step" id="step-3">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;"><i class="fa-solid fa-user-shield"></i> Administrator Account</h3>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.5rem;">Create the primary super administrator account to log in.</p>

            <div id="admin-error-alert" class="alert alert-danger" style="display: none;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div id="admin-error-text">Please resolve errors.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="admin-name">Full Name</label>
                <input type="text" id="admin-name" class="form-control" placeholder="e.g. Elizabeth Vance" value="Elizabeth Vance" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="admin-email">Email Address</label>
                <input type="email" id="admin-email" class="form-control" placeholder="e.g. admin@hotel.com" value="super@hotel.com" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="admin-password">Password</label>
                    <input type="password" id="admin-password" class="form-control" placeholder="Minimum 8 characters" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="admin-confirm">Confirm Password</label>
                    <input type="password" id="admin-confirm" class="form-control" placeholder="Re-type password" required>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem;">
                <button class="btn btn-outline" onclick="goToStep(2)"><i class="fa-solid fa-arrow-left"></i> Back</button>
                <button class="btn btn-primary" onclick="validateStep3()">
                    Begin Installation <i class="fa-solid fa-check"></i>
                </button>
            </div>
        </div>

        <!-- STEP 4: INSTALLATION PROGRESS -->
        <div class="install-step" id="step-4">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;"><i class="fa-solid fa-gears"></i> Installing stayFlow HMS...</h3>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.5rem;" id="progress-desc">We are creating tables, seeding core values, and finalizing settings. Please do not close this window.</p>

            <!-- Loader / Progress -->
            <div style="text-align: center; padding: 2rem 0; display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                <div style="width: 50px; height: 50px; border: 5px solid rgba(110,68,255,0.1); border-top: 5px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite;" id="install-loader"></div>
                <div style="font-weight: 600; font-size: 1.1rem; color: var(--primary);" id="install-status">Executing migrations...</div>
                
                <!-- Success icon (hidden initially) -->
                <i class="fa-solid fa-circle-check" style="font-size: 4rem; color: var(--success); display: none; margin-bottom: 1rem;" id="success-icon"></i>
            </div>

            <div style="display: flex; justify-content: flex-end;" id="finish-btn-wrapper">
                <button class="btn btn-outline" disabled id="finish-btn">Preparing...</button>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        
        const compatFailures = {{ $hasFailures ? 'true' : 'false' }};

        function goToStep(step) {
            if (step > 1 && compatFailures) {
                alert('Your server must meet all compatibility checks before installing.');
                return;
            }

            // Hide active step
            document.getElementById('step-' + currentStep).classList.remove('active');
            document.getElementById('dot-' + currentStep).classList.remove('active');
            if (currentStep < step) {
                document.getElementById('dot-' + currentStep).classList.add('completed');
            }

            // Show target step
            document.getElementById('step-' + step).classList.add('active');
            document.getElementById('dot-' + step).classList.add('active');
            currentStep = step;
        }

        function toggleDatabaseFields() {
            const dbType = document.getElementById('db-connection').value;
            if (dbType === 'sqlite') {
                document.getElementById('sqlite-fields').style.display = 'block';
                document.getElementById('mysql-fields').style.display = 'none';
            } else {
                document.getElementById('sqlite-fields').style.display = 'none';
                document.getElementById('mysql-fields').style.display = 'block';
            }
        }

        async function testDatabaseConnection(silent = false) {
            const dbErrorAlert = document.getElementById('db-error-alert');
            const dbErrorText = document.getElementById('db-error-text');
            const testBtn = document.getElementById('test-connection-btn');
            
            dbErrorAlert.style.display = 'none';
            if (!silent) {
                testBtn.disabled = true;
                testBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Testing...';
            }

            const data = {
                _token: '{{ csrf_token() }}',
                db_connection: document.getElementById('db-connection').value,
                db_database_sqlite: document.getElementById('db-database-sqlite').value,
                db_host: document.getElementById('db-host').value,
                db_port: document.getElementById('db-port').value,
                db_database_mysql: document.getElementById('db-database-mysql').value,
                db_username: document.getElementById('db-username').value,
                db_password: document.getElementById('db-password').value,
            };

            try {
                const response = await fetch('/install/test-db', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (!silent) {
                    testBtn.disabled = false;
                    testBtn.innerHTML = '<i class="fa-solid fa-vial"></i> Test Connection';
                }

                if (result.success) {
                    if (!silent) {
                        alert('Connection Successful!');
                    }
                    return true;
                } else {
                    dbErrorText.textContent = result.message || 'Database connection failed. Please verify credentials.';
                    dbErrorAlert.style.display = 'flex';
                    return false;
                }
            } catch (err) {
                if (!silent) {
                    testBtn.disabled = false;
                    testBtn.innerHTML = '<i class="fa-solid fa-vial"></i> Test Connection';
                }
                dbErrorText.textContent = 'Server error during connection test: ' + err.message;
                dbErrorAlert.style.display = 'flex';
                return false;
            }
        }

        async function validateStep2() {
            const isConnected = await testDatabaseConnection(false);
            if (isConnected) {
                goToStep(3);
            }
        }

        function validateStep3() {
            const errorAlert = document.getElementById('admin-error-alert');
            const errorText = document.getElementById('admin-error-text');
            
            errorAlert.style.display = 'none';

            const name = document.getElementById('admin-name').value;
            const email = document.getElementById('admin-email').value;
            const password = document.getElementById('admin-password').value;
            const confirm = document.getElementById('admin-confirm').value;

            if (!name || !email || !password) {
                errorText.textContent = 'Please fill out all fields.';
                errorAlert.style.display = 'flex';
                return;
            }

            if (password.length < 8) {
                errorText.textContent = 'Password must be at least 8 characters long.';
                errorAlert.style.display = 'flex';
                return;
            }

            if (password !== confirm) {
                errorText.textContent = 'Passwords do not match.';
                errorAlert.style.display = 'flex';
                return;
            }

            goToStep(4);
            runInstallation();
        }

        async function runInstallation() {
            const loader = document.getElementById('install-loader');
            const status = document.getElementById('install-status');
            const desc = document.getElementById('progress-desc');
            const successIcon = document.getElementById('success-icon');
            const finishBtnWrapper = document.getElementById('finish-btn-wrapper');

            status.textContent = 'Executing database setup & seeding...';

            const data = {
                _token: '{{ csrf_token() }}',
                db_connection: document.getElementById('db-connection').value,
                db_database_sqlite: document.getElementById('db-database-sqlite').value,
                db_host: document.getElementById('db-host').value,
                db_port: document.getElementById('db-port').value,
                db_database_mysql: document.getElementById('db-database-mysql').value,
                db_username: document.getElementById('db-username').value,
                db_password: document.getElementById('db-password').value,
                admin_name: document.getElementById('admin-name').value,
                admin_email: document.getElementById('admin-email').value,
                admin_password: document.getElementById('admin-password').value,
            };

            try {
                const response = await fetch('/install', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    status.textContent = 'Installation Complete!';
                    status.style.color = 'var(--success)';
                    desc.textContent = 'stayFlow HMS has been successfully installed. You can now log in using your administrator details.';
                    
                    loader.style.display = 'none';
                    successIcon.style.display = 'block';

                    finishBtnWrapper.innerHTML = `<a href="/login" class="btn btn-primary">Go To Login <i class="fa-solid fa-arrow-right-to-bracket"></i></a>`;
                } else {
                    status.textContent = 'Installation Failed';
                    status.style.color = 'var(--danger)';
                    desc.textContent = result.message || 'An error occurred during migrations and seeding.';
                    loader.style.display = 'none';
                    finishBtnWrapper.innerHTML = `<button class="btn btn-danger" onclick="goToStep(3)"><i class="fa-solid fa-arrow-left"></i> Fix Settings & Retry</button>`;
                }
            } catch (err) {
                status.textContent = 'Installation Error';
                status.style.color = 'var(--danger)';
                desc.textContent = 'Server response error: ' + err.message;
                loader.style.display = 'none';
                finishBtnWrapper.innerHTML = `<button class="btn btn-danger" onclick="goToStep(3)"><i class="fa-solid fa-arrow-left"></i> Fix Settings & Retry</button>`;
            }
        }
    </script>
</body>
</html>
