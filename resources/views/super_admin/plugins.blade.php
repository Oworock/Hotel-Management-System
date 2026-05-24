@extends('layouts.app')

@section('title', 'Plugins Management')

@section('content')
<div class="animate-fade-in">
    <!-- Header Panel -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; border-left: 5px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">System Extensions & Plugins</h2>
            <p style="color: var(--text-secondary); margin: 0;">Extend system capabilities dynamically with secure plugins meeting global HMS standards.</p>
        </div>
        <div>
            <form action="{{ route('super_admin.plugins.upload') }}" method="POST" enctype="multipart/form-data" id="upload-form" style="display: flex; align-items: center; gap: 1rem;">
                @csrf
                <div style="position: relative; overflow: hidden; display: inline-block;">
                    <button type="button" class="btn btn-primary" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload Plugin Zip
                    </button>
                    <input type="file" name="plugin_zip" accept=".zip" required style="font-size: 100px; position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer;" onchange="submitUploadForm()">
                </div>
            </form>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Upload/Extract Loader Overlay -->
    <div id="loaderOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(5px); z-index: 10000; align-items: center; justify-content: center; flex-direction: column; color: #fff;">
        <div style="width: 50px; height: 50px; border: 5px solid rgba(255,255,255,0.2); border-top: 5px solid #fff; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 1rem;"></div>
        <p style="font-weight: 600; font-size: 1.1rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">Extracting & Registering Plugin...</p>
    </div>

    <!-- Plugins Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        @forelse($plugins as $plugin)
            <div class="glass-panel" style="padding: 1.75rem; position: relative; display: flex; flex-direction: column; justify-content: space-between; gap: 1.5rem; border-top: 4px solid {{ $plugin->is_enabled ? 'var(--success)' : 'var(--text-muted)' }};">
                
                <div>
                    <!-- Title & Version -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; padding-right: 4.5rem;">
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin: 0; word-break: break-all;">
                            {{ $plugin->name }}
                        </h3>
                        <span style="font-family: monospace; font-size: 0.8rem; background: rgba(255,255,255,0.05); padding: 0.1rem 0.4rem; border-radius: 4px; border: 1px solid var(--border-color); color: var(--text-secondary); margin-left: 0.5rem; white-space: nowrap;">
                            v{{ $plugin->version }}
                        </span>
                    </div>

                    <!-- Status Badge -->
                    <div style="position: absolute; top: 1.75rem; right: 1.75rem;">
                        <span class="badge {{ $plugin->is_enabled ? 'badge-success' : 'badge-outline' }}" style="font-size: 0.7rem; border-radius: var(--radius-sm); @if(!$plugin->is_enabled) border: 1px solid var(--border-color); color: var(--text-secondary); @endif">
                            {{ $plugin->is_enabled ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin: 0 0 1.25rem 0; min-height: 4.5rem;">
                        {{ $plugin->description ?: 'No description provided.' }}
                    </p>

                    <!-- Plugin Cover Image -->
                    <div style="width: 100%; height: 140px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 0.5rem; background: var(--background); display: flex; align-items: center; justify-content: center; position: relative;">
                        <img src="{{ route('super_admin.plugins.logo', $plugin->name) }}" alt="{{ $plugin->name }} Logo" style="width: 100%; height: 100%; object-fit: cover; transition: transform var(--transition-fast);" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                </div>

                <!-- Footer Actions -->
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 1.25rem; flex-wrap: wrap;">
                    <!-- Run QA Compliance Suite -->
                    <button class="btn btn-outline" style="font-size: 0.8rem; padding: 0.5rem 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem;" onclick="runPluginQA({{ $plugin->id }}, '{{ $plugin->name }}')">
                        <i class="fa-solid fa-vial-circle-check" style="color: var(--secondary);"></i> Run QA
                    </button>

                    <!-- Toggle Enable/Disable -->
                    <form action="{{ route('super_admin.plugins.toggle', $plugin->id) }}" method="POST" style="margin: 0; display: inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.5rem 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                            @if($plugin->is_enabled)
                                <i class="fa-solid fa-power-off" style="color: var(--warning);"></i> Deactivate
                            @else
                                <i class="fa-solid fa-circle-check" style="color: var(--success);"></i> Activate
                            @endif
                        </button>
                    </form>

                    <!-- Delete -->
                    <form action="{{ route('super_admin.plugins.delete', $plugin->id) }}" method="POST" style="margin: 0; display: inline-block;" onsubmit="return confirm('WARNING: Deleting this plugin will permanently remove all associated files and purge all plugin tables (order_items, orders, products) and data from the database. Are you sure you want to proceed?');">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.5rem 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem; border-color: var(--danger-glow);">
                            <i class="fa-solid fa-trash-can" style="color: var(--danger);"></i> Delete
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="glass-panel" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; color: var(--text-secondary);">
                <i class="fa-solid fa-cubes" style="font-size: 3rem; margin-bottom: 1.5rem; color: var(--text-muted); opacity: 0.5;"></i>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">No Plugins Installed</h3>
                <p style="margin: 0;">Upload a standard plugin `.zip` archive file to get started.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Programmatic QA Terminal Console Modal -->
<div class="modal" id="qaModal">
    <div class="modal-content" style="max-width: 750px; background: rgba(15, 23, 42, 0.95); color: #fff; padding: 0; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); box-shadow: var(--shadow-lg);">
        
        <!-- Terminal Header -->
        <div style="background: rgba(30, 41, 59, 0.8); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="display: flex; gap: 0.4rem;">
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                </div>
                <span id="qaTerminalTitle" style="font-family: monospace; font-size: 0.875rem; font-weight: 700; color: rgba(255,255,255,0.8);">stayflow-compliance-qa-terminal.sh</span>
            </div>
            <button onclick="closeQaModal()" style="background: none; border: none; color: rgba(255,255,255,0.6); font-size: 1.2rem; cursor: pointer; padding: 0;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Terminal Body -->
        <div id="qaTerminalContent" style="font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; padding: 1.5rem; height: 350px; overflow-y: auto; background: rgba(15, 23, 42, 0.95); line-height: 1.7; color: #38bdf8;">
            <!-- Lines will append dynamically here -->
        </div>

        <!-- Terminal Footer -->
        <div style="background: rgba(30, 41, 59, 0.8); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.1);">
            <span style="font-family: monospace; font-size: 0.75rem; color: var(--text-secondary);" id="qaFinalStatus">Status: IDLE</span>
            <button type="button" class="btn btn-outline" onclick="closeQaModal()" style="border-color: rgba(255,255,255,0.2); color: #fff; font-size: 0.8rem; padding: 0.4rem 1rem;">Close Console</button>
        </div>
        
    </div>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
.terminal-line {
    margin-bottom: 0.4rem;
    white-space: pre-wrap;
    word-break: break-all;
}
.terminal-green { color: #4ade80; }
.terminal-yellow { color: #fbbf24; }
.terminal-red { color: #f87171; }
.terminal-cyan { color: #22d3ee; }
.terminal-muted { color: #64748b; }
</style>
@endsection

@section('scripts')
<script>
    function submitUploadForm() {
        document.getElementById('loaderOverlay').style.display = 'flex';
        document.getElementById('upload-form').submit();
    }

    const qaModal = document.getElementById('qaModal');
    const terminal = document.getElementById('qaTerminalContent');
    const terminalTitle = document.getElementById('qaTerminalTitle');
    const finalStatusText = document.getElementById('qaFinalStatus');

    function closeQaModal() {
        qaModal.classList.remove('active');
    }

    qaModal.addEventListener('click', function(e) {
        if(e.target === qaModal) {
            closeQaModal();
        }
    });

    async function runPluginQA(pluginId, pluginName) {
        // Reset Terminal
        terminalTitle.textContent = `stayflow-compliance-qa-${pluginName.toLowerCase()}.sh`;
        terminal.innerHTML = '';
        finalStatusText.textContent = 'Status: RUNNING';
        finalStatusText.className = 'terminal-cyan';
        
        qaModal.classList.add('active');

        // Initial loading messages to simulate terminal delay and look extremely premium
        appendTerminalLine(`[~] Initializing Compliance QA Auditor for ${pluginName}...`, 'terminal-muted');
        await delay(350);
        appendTerminalLine(`[~] Loading dynamic sandbox Environment...`, 'terminal-muted');
        await delay(250);
        appendTerminalLine(`[~] Fetching QA assertions list...`, 'terminal-muted');
        await delay(200);

        try {
            const response = await fetch(`/super-admin/plugins/${pluginId}/qa`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                throw new Error(`Server returned HTTP ${response.status}`);
            }

            const data = await response.json();
            const results = data.results || [];

            if (results.length === 0) {
                appendTerminalLine(`[!] QA Warning: No compliance tests resolved. Check plugin configurations.`, 'terminal-yellow');
                finalStatusText.textContent = 'Status: WARNING';
                finalStatusText.className = 'terminal-yellow';
                return;
            }

            let allPassed = true;
            let passCount = 0;

            for (const test of results) {
                appendTerminalLine(`[~] Asserting: ${test.name}...`, 'terminal-cyan');
                await delay(400); // dynamic smooth reveal

                if (test.passed) {
                    appendTerminalLine(`    => SUCCESS: ${test.message}`, 'terminal-green');
                    passCount++;
                } else {
                    appendTerminalLine(`    => FAILURE: ${test.message}`, 'terminal-red');
                    allPassed = false;
                }
                await delay(200);
            }

            appendTerminalLine(`--------------------------------------------------------------------------------`, 'terminal-muted');
            if (allPassed) {
                appendTerminalLine(`[+] Global Compliance Audit: SECURE (${passCount}/${results.length} assertions passed)`, 'terminal-green');
                finalStatusText.textContent = 'Status: COMPLIANT';
                finalStatusText.className = 'terminal-green';
            } else {
                appendTerminalLine(`[-] Global Compliance Audit: UNSECURE (${passCount}/${results.length} assertions passed)`, 'terminal-red');
                finalStatusText.textContent = 'Status: FAILED';
                finalStatusText.className = 'terminal-red';
            }

        } catch (error) {
            appendTerminalLine(`[!] CRITICAL ERROR: Failed to execute compliance QA suite: ${error.message}`, 'terminal-red');
            finalStatusText.textContent = 'Status: EXCEPTION';
            finalStatusText.className = 'terminal-red';
        }
    }

    function appendTerminalLine(text, className = '') {
        const line = document.createElement('div');
        line.className = `terminal-line ${className}`;
        line.textContent = text;
        terminal.appendChild(line);
        terminal.scrollTop = terminal.scrollHeight;
    }

    function delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
</script>
@endsection
