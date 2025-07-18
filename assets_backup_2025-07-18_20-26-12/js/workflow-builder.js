/**
 * MechaMap Workflow Builder
 * Visual workflow builder for automation system
 */

class WorkflowBuilder {
    constructor() {
        this.canvas = document.getElementById('workflow-canvas');
        this.propertiesPanel = document.getElementById('properties-panel');
        this.selectedNode = null;
        this.nodes = new Map();
        this.connections = [];
        this.dragula = null;
        this.nodeCounter = 0;
        this.isConnecting = false;
        this.connectionStart = null;
        
        this.init();
    }

    init() {
        this.setupDragAndDrop();
        this.setupEventListeners();
        this.loadWorkflow();
        this.setupMinimap();
    }

    setupDragAndDrop() {
        // Setup dragula for component library to canvas
        this.dragula = dragula([
            document.querySelector('.component-library'),
            this.canvas
        ], {
            copy: (el, source) => source === document.querySelector('.component-library'),
            accepts: (el, target) => target === this.canvas,
            removeOnSpill: true
        });

        this.dragula.on('drop', (el, target, source) => {
            if (target === this.canvas && source !== this.canvas) {
                this.createNodeFromComponent(el);
            }
        });

        // Setup dragula for reordering nodes in canvas
        this.canvasDragula = dragula([this.canvas], {
            moves: (el) => el.classList.contains('workflow-node'),
            accepts: () => true
        });

        this.canvasDragula.on('drop', () => {
            this.updateConnections();
        });
    }

    setupEventListeners() {
        // Canvas click to deselect
        this.canvas.addEventListener('click', (e) => {
            if (e.target === this.canvas) {
                this.selectNode(null);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Delete' && this.selectedNode) {
                this.deleteNode(this.selectedNode);
            }
            if (e.ctrlKey || e.metaKey) {
                if (e.key === 's') {
                    e.preventDefault();
                    this.save();
                }
                if (e.key === 'z') {
                    e.preventDefault();
                    this.undo();
                }
            }
        });

        // Window resize
        window.addEventListener('resize', () => {
            this.updateMinimap();
        });
    }

    createNodeFromComponent(componentEl) {
        const type = componentEl.dataset.type;
        const key = componentEl.dataset.key;
        
        // Remove the dropped component element
        componentEl.remove();
        
        // Create actual workflow node
        this.createNode(type, key);
        
        // Hide empty state
        const emptyState = this.canvas.querySelector('.workflow-empty');
        if (emptyState) {
            emptyState.style.display = 'none';
        }
    }

    createNode(type, key, config = {}) {
        const nodeId = `node-${++this.nodeCounter}`;
        const nodeData = this.getNodeData(type, key);
        
        const node = document.createElement('div');
        node.className = `workflow-node ${type}`;
        node.dataset.nodeId = nodeId;
        node.dataset.type = type;
        node.dataset.key = key;
        
        node.innerHTML = `
            <div class="node-header">
                <div class="node-icon ${type}">
                    <i class="${nodeData.icon}"></i>
                </div>
                <div class="node-title">${nodeData.name}</div>
                <div class="node-menu">
                    <button class="btn btn-sm btn-outline-secondary" onclick="workflowBuilder.showNodeMenu('${nodeId}', event)">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
            <div class="node-content">
                ${nodeData.description}
            </div>
            <div class="node-connector" onclick="workflowBuilder.startConnection('${nodeId}', event)"></div>
        `;
        
        // Add click handler
        node.addEventListener('click', (e) => {
            e.stopPropagation();
            this.selectNode(nodeId);
        });
        
        this.canvas.appendChild(node);
        
        // Store node data
        this.nodes.set(nodeId, {
            id: nodeId,
            type: type,
            key: key,
            config: config,
            element: node,
            data: nodeData
        });
        
        // Select the new node
        this.selectNode(nodeId);
        
        this.updateMinimap();
        
        return nodeId;
    }

    getNodeData(type, key) {
        if (type === 'trigger') {
            return availableTriggers[key];
        } else if (type === 'action') {
            return availableActions[key];
        }
        return { name: 'Unknown', description: '', icon: 'fas fa-question' };
    }

    selectNode(nodeId) {
        // Deselect previous node
        if (this.selectedNode) {
            const prevNode = this.nodes.get(this.selectedNode);
            if (prevNode) {
                prevNode.element.classList.remove('selected');
            }
        }
        
        this.selectedNode = nodeId;
        
        if (nodeId) {
            const node = this.nodes.get(nodeId);
            if (node) {
                node.element.classList.add('selected');
                this.showNodeProperties(node);
            }
        } else {
            this.showDefaultProperties();
        }
    }

    showNodeProperties(node) {
        const configFields = node.data.config_fields || {};
        
        let html = `
            <h6>${node.data.name} Properties</h6>
            <div class="property-group">
                <label>Node ID</label>
                <input type="text" class="form-control" value="${node.id}" readonly>
            </div>
        `;
        
        // Generate form fields based on config
        Object.entries(configFields).forEach(([fieldKey, field]) => {
            html += this.generatePropertyField(fieldKey, field, node.config[fieldKey]);
        });
        
        // Add variable helper
        if (node.type === 'action') {
            html += `
                <div class="property-group">
                    <label>Available Variables</label>
                    <div class="variable-tags">
                        <span class="variable-tag" onclick="workflowBuilder.insertVariable('{{trigger.user_id}}')">
                            {{trigger.user_id}}
                        </span>
                        <span class="variable-tag" onclick="workflowBuilder.insertVariable('{{trigger.user_email}}')">
                            {{trigger.user_email}}
                        </span>
                        <span class="variable-tag" onclick="workflowBuilder.insertVariable('{{system.current_date}}')">
                            {{system.current_date}}
                        </span>
                    </div>
                </div>
            `;
        }
        
        html += `
            <div class="property-group">
                <button class="btn btn-danger btn-sm w-100" onclick="workflowBuilder.deleteNode('${node.id}')">
                    <i class="fas fa-trash me-1"></i> Delete Node
                </button>
            </div>
        `;
        
        this.propertiesPanel.innerHTML = html;
        
        // Add event listeners for property changes
        this.propertiesPanel.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('change', () => {
                this.updateNodeConfig(node.id, input.name, input.value);
            });
        });
    }

    generatePropertyField(key, field, value = '') {
        const fieldId = `prop-${key}`;
        let html = `
            <div class="property-group">
                <label for="${fieldId}">${field.label}</label>
        `;
        
        switch (field.type) {
            case 'text':
            case 'email':
            case 'url':
                html += `<input type="${field.type}" class="form-control" id="${fieldId}" name="${key}" 
                        value="${value}" placeholder="${field.placeholder || ''}">`;
                break;
            case 'textarea':
                html += `<textarea class="form-control" id="${fieldId}" name="${key}" rows="3" 
                        placeholder="${field.placeholder || ''}">${value}</textarea>`;
                break;
            case 'select':
                html += `<select class="form-select" id="${fieldId}" name="${key}">`;
                if (Array.isArray(field.options)) {
                    field.options.forEach(option => {
                        const selected = option === value ? 'selected' : '';
                        html += `<option value="${option}" ${selected}>${option}</option>`;
                    });
                }
                html += `</select>`;
                break;
            case 'number':
                html += `<input type="number" class="form-control" id="${fieldId}" name="${key}" 
                        value="${value}" min="${field.min || ''}" max="${field.max || ''}">`;
                break;
            case 'date':
                html += `<input type="date" class="form-control" id="${fieldId}" name="${key}" value="${value}">`;
                break;
            case 'time':
                html += `<input type="time" class="form-control" id="${fieldId}" name="${key}" value="${value}">`;
                break;
            default:
                html += `<input type="text" class="form-control" id="${fieldId}" name="${key}" value="${value}">`;
        }
        
        html += `</div>`;
        return html;
    }

    showDefaultProperties() {
        this.propertiesPanel.innerHTML = `
            <h6>Workflow Properties</h6>
            <div class="property-group">
                <label>Name</label>
                <input type="text" class="form-control" id="workflow-name-prop" 
                       value="${document.getElementById('workflow-name').value}">
            </div>
            <div class="property-group">
                <label>Description</label>
                <textarea class="form-control" id="workflow-description" rows="3"></textarea>
            </div>
            <div class="property-group">
                <label>Status</label>
                <select class="form-select" id="workflow-status">
                    <option value="true">Active</option>
                    <option value="false">Inactive</option>
                </select>
            </div>
        `;
    }

    updateNodeConfig(nodeId, key, value) {
        const node = this.nodes.get(nodeId);
        if (node) {
            node.config[key] = value;
        }
    }

    deleteNode(nodeId) {
        if (confirm('Are you sure you want to delete this node?')) {
            const node = this.nodes.get(nodeId);
            if (node) {
                node.element.remove();
                this.nodes.delete(nodeId);
                
                // Remove connections
                this.connections = this.connections.filter(conn => 
                    conn.from !== nodeId && conn.to !== nodeId
                );
                
                this.updateConnections();
                this.selectNode(null);
                
                // Show empty state if no nodes
                if (this.nodes.size === 0) {
                    const emptyState = this.canvas.querySelector('.workflow-empty');
                    if (emptyState) {
                        emptyState.style.display = 'block';
                    }
                }
            }
        }
    }

    startConnection(nodeId, event) {
        event.stopPropagation();
        
        if (!this.isConnecting) {
            this.isConnecting = true;
            this.connectionStart = nodeId;
            this.canvas.style.cursor = 'crosshair';
            
            // Add temporary connection line
            this.addTemporaryConnection(event);
        } else {
            // Complete connection
            if (this.connectionStart !== nodeId) {
                this.createConnection(this.connectionStart, nodeId);
            }
            this.cancelConnection();
        }
    }

    createConnection(fromId, toId) {
        // Check if connection already exists
        const exists = this.connections.some(conn => 
            conn.from === fromId && conn.to === toId
        );
        
        if (!exists) {
            this.connections.push({ from: fromId, to: toId });
            this.updateConnections();
        }
    }

    cancelConnection() {
        this.isConnecting = false;
        this.connectionStart = null;
        this.canvas.style.cursor = 'default';
        
        // Remove temporary connection line
        const tempLine = this.canvas.querySelector('.temp-connection');
        if (tempLine) {
            tempLine.remove();
        }
    }

    updateConnections() {
        // Remove existing connection lines
        this.canvas.querySelectorAll('.connection-line').forEach(line => line.remove());
        
        // Draw new connection lines
        this.connections.forEach(conn => {
            this.drawConnection(conn.from, conn.to);
        });
    }

    drawConnection(fromId, toId) {
        const fromNode = this.nodes.get(fromId);
        const toNode = this.nodes.get(toId);
        
        if (!fromNode || !toNode) return;
        
        const fromRect = fromNode.element.getBoundingClientRect();
        const toRect = toNode.element.getBoundingClientRect();
        const canvasRect = this.canvas.getBoundingClientRect();
        
        const fromX = fromRect.right - canvasRect.left;
        const fromY = fromRect.top + fromRect.height / 2 - canvasRect.top;
        const toX = toRect.left - canvasRect.left;
        const toY = toRect.top + toRect.height / 2 - canvasRect.top;
        
        const line = document.createElement('div');
        line.className = 'connection-line';
        line.style.left = `${fromX}px`;
        line.style.top = `${fromY}px`;
        line.style.width = `${toX - fromX}px`;
        
        const arrow = document.createElement('div');
        arrow.className = 'connection-arrow';
        line.appendChild(arrow);
        
        this.canvas.appendChild(line);
    }

    setupMinimap() {
        // Minimap implementation
        this.updateMinimap();
    }

    updateMinimap() {
        // Update minimap view
        const minimap = document.getElementById('workflow-minimap');
        if (minimap) {
            // Simplified minimap update
            const viewport = minimap.querySelector('.minimap-viewport');
            if (viewport) {
                viewport.style.width = '50%';
                viewport.style.height = '50%';
            }
        }
    }

    // Public API methods

    save() {
        const workflowData = this.getWorkflowData();
        
        fetch('/admin/automation/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(workflowData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Workflow saved successfully!', 'success');
            } else {
                this.showNotification('Failed to save workflow: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            this.showNotification('Failed to save workflow', 'error');
        });
    }

    test() {
        const modal = new bootstrap.Modal(document.getElementById('testModal'));
        modal.show();
    }

    runTest() {
        const testData = document.getElementById('test-data').value;
        const workflowData = this.getWorkflowData();
        
        try {
            const data = testData ? JSON.parse(testData) : {};
            
            fetch('/admin/automation/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    workflow: workflowData,
                    test_data: data
                })
            })
            .then(response => response.json())
            .then(result => {
                document.getElementById('test-results').classList.remove('d-none');
                document.getElementById('test-output').textContent = JSON.stringify(result, null, 2);
            })
            .catch(error => {
                document.getElementById('test-results').classList.remove('d-none');
                document.getElementById('test-output').textContent = 'Test failed: ' + error.message;
            });
            
        } catch (error) {
            alert('Invalid test data JSON: ' + error.message);
        }
    }

    clear() {
        if (confirm('Are you sure you want to clear the workflow?')) {
            this.nodes.clear();
            this.connections = [];
            this.canvas.innerHTML = `
                <div class="workflow-empty">
                    <i class="fas fa-project-diagram"></i>
                    <h6>Start Building Your Workflow</h6>
                    <p>Drag components from the library to create your automation workflow</p>
                </div>
            `;
            this.selectNode(null);
        }
    }

    getWorkflowData() {
        const triggers = [];
        const actions = [];
        
        this.nodes.forEach(node => {
            if (node.type === 'trigger') {
                triggers.push({
                    trigger_type: node.key,
                    trigger_config: node.config
                });
            } else if (node.type === 'action') {
                actions.push({
                    action_type: node.key,
                    action_config: node.config
                });
            }
        });
        
        return {
            name: document.getElementById('workflow-name').value,
            description: document.getElementById('workflow-description')?.value || '',
            triggers: triggers,
            actions: actions,
            connections: this.connections,
            is_active: document.getElementById('workflow-status')?.value === 'true'
        };
    }

    loadWorkflow() {
        if (workflowData) {
            // Load existing workflow
            console.log('Loading workflow:', workflowData);
            // Implementation for loading existing workflow
        }
    }

    showNotification(message, type = 'info') {
        if (window.showMobileNotification) {
            window.showMobileNotification(message, type);
        } else {
            alert(message);
        }
    }

    insertVariable(variable) {
        // Insert variable into focused input
        const focused = document.activeElement;
        if (focused && (focused.tagName === 'INPUT' || focused.tagName === 'TEXTAREA')) {
            const start = focused.selectionStart;
            const end = focused.selectionEnd;
            const value = focused.value;
            focused.value = value.substring(0, start) + variable + value.substring(end);
            focused.focus();
            focused.setSelectionRange(start + variable.length, start + variable.length);
        }
    }
}

// Initialize workflow builder when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.workflowBuilder = new WorkflowBuilder();
});
