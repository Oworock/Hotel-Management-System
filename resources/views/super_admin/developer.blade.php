@extends('layouts.app')

@section('title', 'Developer Portal')

@section('content')
<div class="animate-fade-in" style="display: flex; flex-direction: column; gap: 2rem;">
    <!-- Header -->
    <div class="glass-panel" style="padding: 2rem; border-left: 5px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Developer Portal & Integrations</h2>
            <p style="color: var(--text-secondary); margin: 0;">Manage secure API tokens, configure webhook endpoints, inspect event logs, and review documentation.</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Newly Generated API Key Alert -->
    @if(session('new_api_key'))
        <div class="glass-panel" style="border: 2px dashed var(--warning); background: var(--warning-glow); padding: 1.5rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; color: var(--warning); font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Copy Your API Key
            </div>
            <p style="font-size: 0.9rem; color: var(--text-primary); margin-bottom: 1rem;">
                For security reasons, this key will only be shown to you once. Make sure to copy it now!
            </p>
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div style="background: var(--background); border: 1px solid var(--border-color); padding: 0.75rem 1.25rem; border-radius: var(--radius-sm); font-family: monospace; font-size: 1rem; color: var(--primary); font-weight: bold; word-break: break-all; min-width: 320px;" id="new-key-value">
                    {{ session('new_api_key')['token'] }}
                </div>
                <button class="btn btn-primary" onclick="copyToClipboard('new-key-value', this)" style="padding: 0.6rem 1.2rem; font-size: 0.85rem;">
                    <i class="fa-regular fa-copy"></i> Copy Key
                </button>
            </div>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; flex-wrap: wrap;">
        <button class="tab-btn active" onclick="switchTab(event, 'api-keys-tab')" style="background: none; border: none; font-size: 1rem; font-weight: 600; padding: 0.75rem 1.25rem; cursor: pointer; color: var(--text-secondary); border-bottom: 3px solid transparent; transition: all var(--transition-fast);">
            <i class="fa-solid fa-key"></i> API Keys
        </button>
        <button class="tab-btn" onclick="switchTab(event, 'webhooks-tab')" style="background: none; border: none; font-size: 1rem; font-weight: 600; padding: 0.75rem 1.25rem; cursor: pointer; color: var(--text-secondary); border-bottom: 3px solid transparent; transition: all var(--transition-fast);">
            <i class="fa-solid fa-circle-nodes"></i> Webhooks
        </button>
        <button class="tab-btn" onclick="switchTab(event, 'logs-tab')" style="background: none; border: none; font-size: 1rem; font-weight: 600; padding: 0.75rem 1.25rem; cursor: pointer; color: var(--text-secondary); border-bottom: 3px solid transparent; transition: all var(--transition-fast);">
            <i class="fa-solid fa-list-check"></i> Delivery Logs
        </button>
        <button class="tab-btn" onclick="switchTab(event, 'docs-tab')" style="background: none; border: none; font-size: 1rem; font-weight: 600; padding: 0.75rem 1.25rem; cursor: pointer; color: var(--text-secondary); border-bottom: 3px solid transparent; transition: all var(--transition-fast);">
            <i class="fa-solid fa-book"></i> Interactive Docs
        </button>
    </div>

    <!-- Tab Contents -->
    <div>
        <!-- API KEYS TAB -->
        <div id="api-keys-tab" class="tab-content active-content">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start; flex-wrap: wrap;">
                <!-- Key List -->
                <div class="glass-panel" style="padding: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;"><i class="fa-solid fa-list"></i> Active API Keys</h3>
                    
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Assigned User</th>
                                    <th>Token Hint</th>
                                    <th>Last Used</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($apiTokens as $token)
                                    <tr>
                                        <td><strong>{{ $token->name }}</strong></td>
                                        <td>
                                            <span style="font-size: 0.85rem; color: var(--text-secondary);">{{ $token->user->name }}</span>
                                            <span class="badge badge-info" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">{{ $token->user->role }}</span>
                                        </td>
                                        <td><code style="font-family: monospace; font-size: 0.8rem; background: rgba(0,0,0,0.03); padding: 0.2rem 0.4rem; border-radius: 4px;">Hash {{ substr($token->token, 0, 8) }}...{{ substr($token->token, -4) }}</code></td>
                                        <td>
                                            <span style="font-size: 0.85rem; color: var(--text-secondary);">
                                                {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Never used' }}
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <form action="{{ route('super_admin.developer.keys.destroy', $token->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke this API token? Any client using it will immediately be blocked.');" style="margin: 0; display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; border-color: var(--danger-glow); color: var(--danger);">
                                                    <i class="fa-solid fa-ban"></i> Revoke
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No active API keys found. Create one to get started.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Key Form -->
                <div class="glass-panel" style="padding: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;"><i class="fa-solid fa-plus"></i> Generate API Key</h3>
                    <form action="{{ route('super_admin.developer.keys.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="key-name">Token Name</label>
                            <input type="text" name="name" id="key-name" class="form-control" placeholder="e.g. External CRM" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="key-user">Associate User context</label>
                            <select name="user_id" id="key-user" class="form-control" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                                @endforeach
                            </select>
                            <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.4rem;">
                                API operations will run with the roles and permissions of the associated user.
                            </p>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Key
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- WEBHOOKS TAB -->
        <div id="webhooks-tab" class="tab-content">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
                <!-- Webhook List -->
                <div class="glass-panel" style="padding: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;"><i class="fa-solid fa-network-wired"></i> Webhook Subscriptions</h3>
                    
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name / URL</th>
                                    <th>Secret</th>
                                    <th>Subscribed Events</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($webhooks as $webhook)
                                    <tr>
                                        <td>
                                            <strong>{{ $webhook->name }}</strong>
                                            <div style="font-size: 0.8rem; color: var(--text-secondary); word-break: break-all; margin-top: 0.2rem;">{{ $webhook->url }}</div>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <code style="font-family: monospace; font-size: 0.8rem; background: rgba(0,0,0,0.03); padding: 0.2rem 0.4rem;" id="webhook-sec-{{ $webhook->id }}">••••••••••••••••</code>
                                                <button class="btn btn-outline" onclick="toggleRevealSecret('webhook-sec-{{ $webhook->id }}', @js($webhook->secret))" style="padding: 0.2rem 0.4rem; font-size: 0.7rem; border: none; background: none;">
                                                    <i class="fa-solid fa-eye" id="webhook-sec-{{ $webhook->id }}-icon"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; flex-wrap: wrap; gap: 0.25rem; max-width: 250px;">
                                                @foreach($webhook->events as $ev)
                                                    <span class="badge badge-info" style="font-size: 0.6rem; text-transform: none; padding: 0.15rem 0.4rem;">{{ $ev }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $webhook->is_active ? 'badge-success' : 'badge-danger' }}" style="font-size: 0.65rem;">
                                                {{ $webhook->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                                <form action="{{ route('super_admin.developer.webhooks.toggle', $webhook->id) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline" style="padding: 0.4rem 0.6rem; font-size: 0.75rem;">
                                                        @if($webhook->is_active)
                                                            <i class="fa-solid fa-power-off" style="color: var(--warning);"></i> Pause
                                                        @else
                                                            <i class="fa-solid fa-play" style="color: var(--success);"></i> Resume
                                                        @endif
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('super_admin.developer.webhooks.destroy', $webhook->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this webhook subscription?');" style="margin: 0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline" style="padding: 0.4rem 0.6rem; font-size: 0.75rem; border-color: var(--danger-glow); color: var(--danger);">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No webhook subscriptions configured.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Webhook Form -->
                <div class="glass-panel" style="padding: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;"><i class="fa-solid fa-plus"></i> Add Subscription</h3>
                    <form action="{{ route('super_admin.developer.webhooks.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="wh-name">Subscription Name</label>
                            <input type="text" name="name" id="wh-name" class="form-control" placeholder="e.g. My Zapier Hook" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="wh-url">Destination URL (POST)</label>
                            <input type="url" name="url" id="wh-url" class="form-control" placeholder="https://api.mycrm.com/webhooks" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Events to subscribe</label>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem; background: rgba(0,0,0,0.02); padding: 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); max-height: 250px; overflow-y: auto;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; cursor: pointer;">
                                    <input type="checkbox" name="events[]" value="*"> <strong>* (All Events)</strong>
                                </label>
                                @foreach($registeredWebhooks as $groupName => $eventsList)
                                    <div style="font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary); margin-top: 0.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.2rem;">
                                        {{ $groupName }}
                                    </div>
                                    @foreach($eventsList as $eventMeta)
                                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; cursor: pointer; padding-left: 0.5rem;">
                                            <input type="checkbox" name="events[]" value="{{ $eventMeta['event'] }}"> 
                                            <span>{{ $eventMeta['event'] }}</span>
                                        </label>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                            <i class="fa-solid fa-circle-plus"></i> Add Webhook
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- LOGS TAB -->
        <div id="logs-tab" class="tab-content">
            <div class="glass-panel" style="padding: 1.5rem;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;"><i class="fa-solid fa-list-check"></i> Webhook Delivery Logs</h3>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Webhook / Event</th>
                                <th>Target URL</th>
                                <th>HTTP Status</th>
                                <th>Response Time</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr style="cursor: pointer;" onclick="openLogDetails({{ $log->id }}, event)">
                                    <td>
                                        <span style="font-size: 0.85rem; color: var(--text-secondary);">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; font-size: 0.9rem;">{{ $log->subscription->name ?? 'Deleted' }}</span>
                                        <div style="font-size: 0.75rem;"><code style="color: var(--primary);">{{ $log->event }}</code></div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary); word-break: break-all; max-width: 250px;">{{ $log->url }}</div>
                                    </td>
                                    <td>
                                        @if($log->response_status >= 200 && $log->response_status < 300)
                                            <span class="badge badge-success">{{ $log->response_status }} OK</span>
                                        @elseif($log->response_status)
                                            <span class="badge badge-danger">{{ $log->response_status }} FAIL</span>
                                        @else
                                            <span class="badge badge-danger">ERROR</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size: 0.85rem; color: var(--text-secondary); font-family: monospace;">{{ $log->duration_ms }} ms</span>
                                    </td>
                                    <td style="text-align: right;" onclick="event.stopPropagation()">
                                        <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                            <button class="btn btn-outline" onclick="openLogDetails({{ $log->id }}, event)" style="padding: 0.35rem 0.6rem; font-size: 0.75rem;">
                                                <i class="fa-solid fa-magnifying-glass"></i> View
                                            </button>
                                            @if($log->webhook_subscription_id)
                                                <form action="{{ route('super_admin.developer.logs.retry', $log->id) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline" style="padding: 0.35rem 0.6rem; font-size: 0.75rem; color: var(--secondary); border-color: var(--secondary-glow);">
                                                        <i class="fa-solid fa-arrows-rotate"></i> Retry
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Hidden Log Detail JSON storage -->
                                <div style="display: none;" id="log-payload-{{ $log->id }}">{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</div>
                                <div style="display: none;" id="log-response-{{ $log->id }}">{{ $log->response_body ?: ($log->error ?: 'No response body returned.') }}</div>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No webhook logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- INTERACTIVE DOCS TAB -->
        <div id="docs-tab" class="tab-content">
            <div style="display: grid; grid-template-columns: 280px 1fr; gap: 2rem; align-items: start;">
                <!-- Sidebar navigation for docs -->
                <div class="glass-panel" style="padding: 1.25rem; position: sticky; top: 100px;">
                    <div style="font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.05em; margin-bottom: 0.75rem;">Authentication</div>
                    <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem 0; display: flex; flex-direction: column; gap: 0.35rem;">
                        <li><a href="#auth-intro" class="doc-nav-link" style="font-size: 0.9rem; color: var(--text-secondary); font-weight: 500;">Bearer Token Auth</a></li>
                    </ul>

                    <div style="font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.05em; margin-bottom: 0.75rem;">API Endpoints</div>
                    <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem 0; display: flex; flex-direction: column; gap: 0.35rem;">
                        @foreach($registeredApis as $group => $endpoints)
                            <li>
                                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary); margin-top: 0.4rem; padding-left: 0.2rem;">{{ $group }}</div>
                                <ul style="list-style: none; padding-left: 0.75rem; display: flex; flex-direction: column; gap: 0.25rem; margin-top: 0.25rem;">
                                    @foreach($endpoints as $idx => $api)
                                        @php $slug = Str::slug($api['method'] . '-' . $api['uri']); @endphp
                                        <li>
                                            <a href="#{{ $slug }}" class="doc-nav-link" style="font-size: 0.8rem; color: var(--text-secondary); display: inline-flex; align-items: center; gap: 0.35rem;">
                                                <span style="font-size: 0.6rem; font-weight: 800; font-family: monospace; padding: 0.1rem 0.3rem; border-radius: 3px; 
                                                    background: @if($api['method']=='GET') var(--success-glow) @elseif($api['method']=='POST') var(--info-glow) @elseif($api['method']=='PUT') var(--warning-glow) @else var(--danger-glow) @endif;
                                                    color: @if($api['method']=='GET') var(--success) @elseif($api['method']=='POST') var(--info) @elseif($api['method']=='PUT') var(--warning) @else var(--danger) @endif;">
                                                    {{ $api['method'] }}
                                                </span>
                                                <span style="font-family: monospace; font-size: 0.75rem; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 150px;">{{ $api['uri'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>

                    <div style="font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.05em; margin-bottom: 0.75rem;">Webhook Events</div>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.35rem;">
                        @foreach($registeredWebhooks as $group => $events)
                            <li>
                                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary); margin-top: 0.4rem; padding-left: 0.2rem;">{{ $group }}</div>
                                <ul style="list-style: none; padding-left: 0.75rem; display: flex; flex-direction: column; gap: 0.25rem; margin-top: 0.25rem;">
                                    @foreach($events as $ev)
                                        @php $slug = Str::slug('wh-' . $ev['event']); @endphp
                                        <li>
                                            <a href="#{{ $slug }}" class="doc-nav-link" style="font-size: 0.8rem; color: var(--text-secondary); font-family: monospace;">
                                                {{ $ev['event'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Docs Main Area -->
                <div style="display: flex; flex-direction: column; gap: 3rem;">
                    <!-- Auth Intro -->
                    <div class="glass-panel" id="auth-intro" style="padding: 2rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                            API Authentication
                        </h2>
                        <p style="font-size: 0.95rem; line-height: 1.6; color: var(--text-secondary); margin-bottom: 1rem;">
                            All API requests must be authenticated using a Bearer token in the <code>Authorization</code> header. You can generate API keys in the <strong>API Keys</strong> tab above.
                        </p>
                        <div style="background: rgba(15, 23, 42, 0.95); color: #fff; padding: 1.25rem; border-radius: var(--radius-sm); font-family: monospace; font-size: 0.85rem; line-height: 1.6; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 1rem;">
                            <span style="color: #64748b;"># Example Request Header</span><br>
                            <strong>Authorization:</strong> Bearer <span style="color: #38bdf8;">sf_live_aBcDeF12345...</span>
                        </div>
                        
                        @if(class_exists('Plugins\MultiHotel\MultiHotelServiceProvider'))
                            <div class="alert alert-warning" style="margin-top: 1rem;">
                                <i class="fa-solid fa-circle-info"></i>
                                <div>
                                    <strong>Multi-Hotel Plugin Active:</strong> You can include an <code>X-Hotel-ID</code> header in your API requests to scope operations to a specific hotel. If you are a Super Admin and omit this header, queries will return records across all hotels.
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- API Endpoints Listings -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 800; border-bottom: 2px solid var(--primary); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                            API Reference
                        </h2>

                        @foreach($registeredApis as $group => $endpoints)
                            @foreach($endpoints as $api)
                                @php $slug = Str::slug($api['method'] . '-' . $api['uri']); @endphp
                                <div class="glass-panel" id="{{ $slug }}" style="padding: 2rem; border-top: 4px solid 
                                    @if($api['method']=='GET') var(--success) @elseif($api['method']=='POST') var(--info) @elseif($api['method']=='PUT') var(--warning) @else var(--danger) @endif;">
                                    
                                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                                        <span style="font-size: 0.85rem; font-weight: 800; font-family: monospace; padding: 0.25rem 0.75rem; border-radius: var(--radius-sm);
                                            background: @if($api['method']=='GET') var(--success-glow) @elseif($api['method']=='POST') var(--info-glow) @elseif($api['method']=='PUT') var(--warning-glow) @else var(--danger-glow) @endif;
                                            color: @if($api['method']=='GET') var(--success) @elseif($api['method']=='POST') var(--info) @elseif($api['method']=='PUT') var(--warning) @else var(--danger) @endif;">
                                            {{ $api['method'] }}
                                        </span>
                                        <code style="font-family: monospace; font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">{{ $api['uri'] }}</code>
                                    </div>

                                    <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1.5rem;">
                                        {{ $api['description'] }}
                                    </p>

                                    <!-- Headers -->
                                    @if(isset($api['headers']) && count($api['headers']) > 0)
                                        <h4 style="font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 0.5rem; letter-spacing: 0.05em;">Request Headers</h4>
                                        <ul style="font-size: 0.85rem; color: var(--text-primary); margin-bottom: 1.5rem; padding-left: 1.25rem; display: flex; flex-direction: column; gap: 0.4rem;">
                                            @foreach($api['headers'] as $headerName => $headerDesc)
                                                <li><code>{{ $headerName }}</code> - {{ $headerDesc }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <!-- Parameters -->
                                    @if(isset($api['parameters']) && count($api['parameters']) > 0)
                                        <h4 style="font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 0.75rem; letter-spacing: 0.05em;">Parameters</h4>
                                        <div class="table-container" style="margin-bottom: 1.5rem;">
                                            <table class="table" style="font-size: 0.85rem;">
                                                <thead>
                                                    <tr>
                                                        <th>Parameter</th>
                                                        <th>Type</th>
                                                        <th>Required</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($api['parameters'] as $paramName => $paramMeta)
                                                        <tr>
                                                            <td><code style="font-weight: bold;">{{ $paramName }}</code></td>
                                                            <td><span style="font-family: monospace; color: var(--secondary);">{{ $paramMeta['type'] }}</span></td>
                                                            <td>
                                                                @if($paramMeta['required'])
                                                                    <span class="badge badge-danger" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Yes</span>
                                                                @else
                                                                    <span class="badge badge-outline" style="font-size: 0.6rem; padding: 0.1rem 0.3rem; border: 1px solid var(--border-color); color: var(--text-secondary);">No</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $paramMeta['description'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    <!-- Response Example -->
                                    <h4 style="font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 0.5rem; letter-spacing: 0.05em;">Response Example (200 OK / 201 Created)</h4>
                                    <pre style="background: rgba(15, 23, 42, 0.95); color: #85d7ff; padding: 1.25rem; border-radius: var(--radius-sm); font-family: monospace; font-size: 0.8rem; overflow-x: auto; line-height: 1.5; border: 1px solid rgba(255,255,255,0.1);">{{ json_encode($api['response'], JSON_PRETTY_PRINT) }}</pre>

                                </div>
                            @endforeach
                        @endforeach
                    </div>

                    <!-- Webhook Events Listings -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 800; border-bottom: 2px solid var(--secondary); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                            Webhook Events Reference
                        </h2>

                        @foreach($registeredWebhooks as $group => $events)
                            @foreach($events as $ev)
                                @php $slug = Str::slug('wh-' . $ev['event']); @endphp
                                <div class="glass-panel" id="{{ $slug }}" style="padding: 2rem; border-left: 4px solid var(--secondary);">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                        <span style="font-size: 0.7rem; font-weight: 700; color: #fff; background: var(--secondary); padding: 0.2rem 0.5rem; border-radius: 4px;">EVENT</span>
                                        <code style="font-family: monospace; font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">{{ $ev['event'] }}</code>
                                    </div>
                                    <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1.5rem;">
                                        {{ $ev['description'] }}
                                    </p>

                                    <h4 style="font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 0.5rem; letter-spacing: 0.05em;">POST Body Schema</h4>
                                    <pre style="background: rgba(15, 23, 42, 0.95); color: #f472b6; padding: 1.25rem; border-radius: var(--radius-sm); font-family: monospace; font-size: 0.8rem; overflow-x: auto; line-height: 1.5; border: 1px solid rgba(255,255,255,0.1);">{{ json_encode($ev['payload'], JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Log Delivery Details -->
<div class="modal" id="logDetailsModal">
    <div class="modal-content" style="max-width: 800px; background: rgba(15, 23, 42, 0.95); color: #fff; padding: 0; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); box-shadow: var(--shadow-lg);">
        <!-- Header -->
        <div style="background: rgba(30, 41, 59, 0.8); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="display: flex; gap: 0.4rem;">
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                </div>
                <span style="font-family: monospace; font-size: 0.875rem; font-weight: 700; color: rgba(255,255,255,0.8);">webhook-request-auditor.sh</span>
            </div>
            <button onclick="closeLogDetailsModal()" style="background: none; border: none; color: rgba(255,255,255,0.6); font-size: 1.2rem; cursor: pointer; padding: 0;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Content -->
        <div style="padding: 1.5rem; font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; line-height: 1.6; max-height: 550px; overflow-y: auto;">
            <div style="margin-bottom: 1.5rem;">
                <div style="color: #64748b; margin-bottom: 0.25rem;">[>] Target Payload</div>
                <pre style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.05); color: #38bdf8; overflow-x: auto;" id="detail-payload"></pre>
            </div>
            <div>
                <div style="color: #64748b; margin-bottom: 0.25rem;">[<] Response Header & Body</div>
                <pre style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.05); color: #f472b6; overflow-x: auto; white-space: pre-wrap; word-break: break-all;" id="detail-response"></pre>
            </div>
        </div>

        <!-- Footer -->
        <div style="background: rgba(30, 41, 59, 0.8); padding: 1rem 1.5rem; display: flex; justify-content: flex-end; border-top: 1px solid rgba(255,255,255,0.1);">
            <button type="button" class="btn btn-outline" onclick="closeLogDetailsModal()" style="border-color: rgba(255,255,255,0.2); color: #fff; font-size: 0.8rem; padding: 0.4rem 1rem;">Close Details</button>
        </div>
    </div>
</div>

<style>
/* CSS Tabs */
.tab-content {
    display: none;
}
.tab-content.active-content {
    display: block;
}
.tab-btn.active {
    color: var(--primary) !important;
    border-bottom-color: var(--primary) !important;
}
.tab-btn:hover {
    color: var(--text-primary);
}
.doc-nav-link:hover {
    color: var(--primary) !important;
}
</style>
@endsection

@section('scripts')
<script>
    function switchTab(evt, tabId) {
        // Hide all tab contents
        const contents = document.getElementsByClassName('tab-content');
        for (let i = 0; i < contents.length; i++) {
            contents[i].classList.remove('active-content');
        }

        // Deactivate all tab buttons
        const buttons = document.getElementsByClassName('tab-btn');
        for (let i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('active');
        }

        // Show current tab and activate current button
        document.getElementById(tabId).classList.add('active-content');
        evt.currentTarget.classList.add('active');
    }

    function toggleRevealSecret(elementId, secret) {
        const elem = document.getElementById(elementId);
        const icon = document.getElementById(elementId + '-icon');
        
        if (elem.textContent === '••••••••••••••••') {
            elem.textContent = secret;
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            elem.textContent = '••••••••••••••••';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function copyToClipboard(elementId, button) {
        const text = document.getElementById(elementId).textContent.trim();
        navigator.clipboard.writeText(text).then(() => {
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
            button.classList.remove('btn-primary');
            button.style.backgroundColor = 'var(--success)';
            button.style.color = '#fff';
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.add('btn-primary');
                button.style.backgroundColor = '';
                button.style.color = '';
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    }

    const detailsModal = document.getElementById('logDetailsModal');
    const detailPayload = document.getElementById('detail-payload');
    const detailResponse = document.getElementById('detail-response');

    function openLogDetails(logId, event) {
        const payloadText = document.getElementById('log-payload-' + logId).textContent;
        const responseText = document.getElementById('log-response-' + logId).textContent;

        detailPayload.textContent = payloadText;
        detailResponse.textContent = responseText;

        detailsModal.classList.add('active');
    }

    function closeLogDetailsModal() {
        detailsModal.classList.remove('active');
    }

    detailsModal.addEventListener('click', function(e) {
        if(e.target === detailsModal) {
            closeLogDetailsModal();
        }
    });
</script>
@endsection
