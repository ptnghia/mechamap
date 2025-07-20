@extends('admin.layouts.dason')

@section('title', 'Workflow Builder')

@section('css')
<link href="{{ asset('assets/libs/dragula/dragula.min.css') }}" rel="stylesheet" type="text/css" />
<style>
.workflow-canvas {
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    min-height: 600px;
    padding: 20px;
    position: relative;
    overflow: auto;
}

.workflow-node {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 16px;
    margin: 10px;
    min-width: 200px;
    max-width: 300px;
    cursor: move;
    transition: all 0.3s ease;
    position: relative;
}

.workflow-node:hover {
    border-color: #1c84ee;
    box-shadow: 0 4px 12px rgba(28, 132, 238, 0.15);
}

.workflow-node.selected {
    border-color: #1c84ee;
    background: #f8f9ff;
}

.workflow-node.trigger {
    border-left: 4px solid #28a745;
}

.workflow-node.action {
    border-left: 4px solid #1c84ee;
}

.workflow-node.condition {
    border-left: 4px solid #ffc107;
}

.node-header {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.node-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 14px;
    color: white;
}

.node-icon.trigger { background: #28a745; }
.node-icon.action { background: #1c84ee; }
.node-icon.condition { background: #ffc107; }

.node-title {
    font-weight: 600;
    font-size: 14px;
    flex: 1;
}

.node-menu {
    position: relative;
}

.node-content {
    font-size: 13px;
    color: #6c757d;
    line-height: 1.4;
}

.component-library {
    background: white;
    border-radius: 12px;
    padding: 20px;
    height: 600px;
    overflow-y: auto;
}

.component-category {
    margin-bottom: 24px;
}

.component-category h6 {
    font-size: 14px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.component-item {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
    cursor: grab;
    transition: all 0.3s ease;
}

.component-item:hover {
    background: #e9ecef;
    border-color: #1c84ee;
}

.component-item:active {
    cursor: grabbing;
}

.component-header {
    display: flex;
    align-items: center;
    margin-bottom: 4px;
}

.component-icon {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
    font-size: 10px;
    color: white;
}

.component-name {
    font-weight: 500;
    font-size: 13px;
    flex: 1;
}

.component-description {
    font-size: 11px;
    color: #6c757d;
    line-height: 1.3;
}

.workflow-toolbar {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.workflow-actions {
    display: flex;
    gap: 8px;
}

.workflow-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.workflow-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-indicator.active { background: #28a745; }
.status-indicator.inactive { background: #6c757d; }

.connection-line {
    position: absolute;
    border-top: 2px solid #1c84ee;
    z-index: 1;
    pointer-events: none;
}

.connection-arrow {
    position: absolute;
    right: -6px;
    top: -4px;
    width: 0;
    height: 0;
    border-left: 6px solid #1c84ee;
    border-top: 4px solid transparent;
    border-bottom: 4px solid transparent;
}

.workflow-empty {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.workflow-empty i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.node-connector {
    position: absolute;
    right: -8px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    background: #1c84ee;
    border: 2px solid white;
    border-radius: 50%;
    cursor: pointer;
    z-index: 10;
}

.node-connector:hover {
    background: #1a73d1;
    transform: translateY(-50%) scale(1.2);
}

.properties-panel {
    background: white;
    border-radius: 12px;
    padding: 20px;
    height: 600px;
    overflow-y: auto;
}

.properties-panel h6 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    color: #495057;
}

.property-group {
    margin-bottom: 20px;
}

.property-group label {
    font-size: 13px;
    font-weight: 500;
    color: #495057;
    margin-bottom: 6px;
    display: block;
}

.property-group .form-control,
.property-group .form-select {
    font-size: 13px;
    border-radius: 6px;
}

.variable-tag {
    display: inline-block;
    background: #e7f3ff;
    color: #0d6efd;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    margin: 2px;
    cursor: pointer;
}

.variable-tag:hover {
    background: #cce7ff;
}

.workflow-minimap {
    position: absolute;
    bottom: 20px;
    right: 20px;
    width: 200px;
    height: 120px;
    background: rgba(255,255,255,0.9);
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 8px;
    backdrop-filter: blur(4px);
}

.minimap-viewport {
    border: 1px solid #1c84ee;
    background: rgba(28, 132, 238, 0.1);
    position: absolute;
    cursor: move;
}

@media (max-width: 768px) {
    .workflow-builder-container {
        flex-direction: column;
    }
    
    .component-library,
    .properties-panel {
        height: 300px;
        margin-bottom: 20px;
    }
    
    .workflow-canvas {
        min-height: 400px;
    }
    
    .workflow-minimap {
        display: none;
    }
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">
                <i class="fas fa-project-diagram me-2"></i>
                Workflow Builder
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.automation.index') }}">Automation</a></li>
                    <li class="breadcrumb-item active">Builder</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Workflow Toolbar -->
<div class="workflow-toolbar">
    <div class="workflow-info">
        <div>
            <input type="text" id="workflow-name" class="form-control form-control-sm" 
                   placeholder="Workflow Name" value="{{ $workflow->name ?? 'New Workflow' }}" 
                   style="width: 200px; border: none; background: transparent; font-weight: 600;">
        </div>
        <div class="workflow-status">
            <span class="status-indicator {{ $workflow && $workflow->is_active ? 'active' : 'inactive' }}"></span>
            <span>{{ $workflow && $workflow->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
    </div>
    
    <div class="workflow-actions">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="workflowBuilder.clear()">
            <i class="fas fa-trash me-1"></i> Clear
        </button>
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="workflowBuilder.test()">
            <i class="fas fa-play me-1"></i> Test
        </button>
        <button type="button" class="btn btn-outline-success btn-sm" onclick="workflowBuilder.save()">
            <i class="fas fa-save me-1"></i> Save
        </button>
        <div class="dropdown">
            <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-cog me-1"></i> Options
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="workflowBuilder.export()">
                    <i class="fas fa-download me-2"></i> Export
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="workflowBuilder.import()">
                    <i class="fas fa-upload me-2"></i> Import
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="workflowBuilder.showTemplates()">
                    <i class="fas fa-layer-group me-2"></i> Templates
                </a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Main Builder Interface -->
<div class="row workflow-builder-container">
    <!-- Component Library -->
    <div class="col-xl-3 col-lg-4">
        <div class="component-library">
            <h6 class="mb-3">
                <i class="fas fa-puzzle-piece me-2"></i>
                Components
            </h6>
            
            <!-- Triggers -->
            <div class="component-category">
                <h6>Triggers</h6>
                @foreach($availableTriggers as $key => $trigger)
                <div class="component-item" data-type="trigger" data-key="{{ $key }}">
                    <div class="component-header">
                        <div class="component-icon trigger">
                            <i class="{{ $trigger['icon'] }}"></i>
                        </div>
                        <div class="component-name">{{ $trigger['name'] }}</div>
                    </div>
                    <div class="component-description">{{ $trigger['description'] }}</div>
                </div>
                @endforeach
            </div>
            
            <!-- Actions -->
            <div class="component-category">
                <h6>Actions</h6>
                @foreach($availableActions as $key => $action)
                <div class="component-item" data-type="action" data-key="{{ $key }}">
                    <div class="component-header">
                        <div class="component-icon action">
                            <i class="{{ $action['icon'] }}"></i>
                        </div>
                        <div class="component-name">{{ $action['name'] }}</div>
                    </div>
                    <div class="component-description">{{ $action['description'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Workflow Canvas -->
    <div class="col-xl-6 col-lg-8">
        <div class="workflow-canvas" id="workflow-canvas">
            <div class="workflow-empty">
                <i class="fas fa-project-diagram"></i>
                <h6>Start Building Your Workflow</h6>
                <p>Drag components from the library to create your automation workflow</p>
            </div>
            
            <!-- Minimap -->
            <div class="workflow-minimap" id="workflow-minimap">
                <div class="minimap-viewport"></div>
            </div>
        </div>
    </div>
    
    <!-- Properties Panel -->
    <div class="col-xl-3">
        <div class="properties-panel" id="properties-panel">
            <h6>Properties</h6>
            <div class="text-muted text-center py-4">
                <i class="fas fa-mouse-pointer mb-2" style="font-size: 24px;"></i>
                <p>Select a component to edit its properties</p>
            </div>
        </div>
    </div>
</div>

<!-- Templates Modal -->
<div class="modal fade" id="templatesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Workflow Templates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="templates-container">
                    <!-- Templates will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Workflow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Test Data (JSON)</label>
                    <textarea class="form-control" id="test-data" rows="6" 
                              placeholder='{"user_id": 1, "email": "test@example.com"}'></textarea>
                </div>
                <div id="test-results" class="d-none">
                    <h6>Test Results:</h6>
                    <pre id="test-output" class="bg-light p-3 rounded"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="workflowBuilder.runTest()">
                    <i class="fas fa-play me-1"></i> Run Test
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/libs/dragula/dragula.min.js') }}"></script>
<script>
// Workflow Builder JavaScript will be added in the next file
const workflowData = @json($workflow ? $workflow->getConfiguration() : null);
const availableTriggers = @json($availableTriggers);
const availableActions = @json($availableActions);
const availableConditions = @json($availableConditions);
</script>
<script src="{{ asset('assets/js/workflow-builder.js') }}"></script>
@endsection
