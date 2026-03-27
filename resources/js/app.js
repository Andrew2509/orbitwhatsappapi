import './bootstrap';
import Alpine from 'alpinejs';
import { io } from 'socket.io-client';

window.Alpine = Alpine;

// Register Alpine components
    Alpine.data('mainLayout', function () {
        return {
            darkMode: localStorage.getItem('darkMode') === 'true',
            sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            mobileMenuOpen: false,
            init: function () {
                var self = this;
                this.$watch('darkMode', function (v) {
                    localStorage.setItem('darkMode', v);
                });
                this.$watch('sidebarCollapsed', function (v) {
                    localStorage.setItem('sidebarCollapsed', v);
                });
            }
        };
    });

    Alpine.data('deviceManager', () => ({
        showAddModal: false,
        step: 'name',
        deviceName: '',
        deviceId: null,
        qrCode: null,
        qrError: null,
        phone: '',
        pairingCode: null,
        loading: false,
        connected: false,
        serviceOnline: false,
        pollInterval: null,
        socket: null,

        async init() {
            await this.checkServiceHealth();
            this.initSocket();
        },

        initSocket() {
            const serviceUrl = document.querySelector('meta[name="whatsapp-service-url"]')?.getAttribute('content');
            if (!serviceUrl) return;

            console.log('Alpine: Initializing Socket.IO connection to', serviceUrl);
            this.socket = io(serviceUrl, {
                withCredentials: true,
                transports: ['polling', 'websocket']
            });

            this.socket.on('connect', () => {
                console.log('Alpine: Connected to WhatsApp Service Socket.IO');
            });

            this.socket.on('status', (data) => {
                console.log('Alpine: Status update received via socket:', data);
                if (data.deviceId == this.deviceId) {
                    this.updateStateFromStatus(data.status);
                }
            });

            this.socket.on('pairing_code', (data) => {
                console.log('Alpine: Pairing code received via socket:', data);
                if (data.deviceId == this.deviceId) {
                    this.pairingCode = data.pairingCode;
                }
            });

            this.socket.on('connected', (data) => {
                console.log('Alpine: Device connected via socket:', data);
                if (data.deviceId == this.deviceId) {
                    this.connected = true;
                    if (this.pollInterval) clearInterval(this.pollInterval);
                    setTimeout(() => window.location.reload(), 1500);
                }
            });

            this.socket.on('qr', (data) => {
                console.log('Alpine: QR received via socket');
                if (data.deviceId == this.deviceId) {
                    this.qrCode = data.qr;
                }
            });
        },

        updateStateFromStatus(status) {
            if (status === 'connected') {
                this.connected = true;
                if (this.pollInterval) clearInterval(this.pollInterval);
                setTimeout(() => window.location.reload(), 1500);
            }
        },

        async checkServiceHealth() {
            // Proxy health check melalui Laravel untuk menghindari CORS
            const baseUrl = '/whatsapp';

            try {
                const response = await fetch(`${baseUrl}/health`);
                this.serviceOnline = response.ok;
            } catch (e) {
                this.serviceOnline = false;
            }
        },

        openAddModal() {
            this.showAddModal = true;
            this.step = 'name';
            this.deviceName = '';
            this.qrCode = null;
            this.qrError = null;
            this.phone = '';
            this.pairingCode = null;
            this.connected = false;

            if (this.socket && this.deviceId) {
                this.socket.emit('subscribe', this.deviceId);
            }
        },

        closeModal() {
            this.showAddModal = false;
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
        },

        async createDevice() {
            this.loading = true;
            try {
                const response = await fetch('/devices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: this.deviceName })
                });

                if (response.redirected) {
                    window.location.reload();
                    return;
                }

                const data = await response.json();
                if (data.device_id) {
                    await this.setDeviceId(data.device_id);
                    this.step = 'method';
                }
            } catch (e) {
                console.error('Create device error:', e);
            } finally {
                this.loading = false;
            }
        },

        chooseMethod(method) {
            console.log('Alpine: Choosing method', method, 'for device', this.deviceId);

            // Reset state
            this.qrCode = null;
            this.qrError = null;
            this.pairingCode = null;
            this.loading = false;

            // Set step
            this.step = method;
            console.log('Alpine: Step updated to', this.step);

            if (method === 'qr') {
                this.fetchQR();
                this.startPolling();
            }
        },

        async getPairingCode() {
            console.log('Requesting pairing code for device:', this.deviceId);
            this.loading = true;
            try {
                const response = await fetch(`/devices/${this.deviceId}/pairing-code`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ phone: this.phone })
                });
                const data = await response.json();
                if (data.pairingCode) {
                    this.pairingCode = data.pairingCode;
                    // Polling as fallback, but Socket.IO is primary now
                    this.startPolling();
                } else if (data.error) {
                    alert(data.error);
                }
            } catch (e) {
                console.error('Pairing code error:', e);
            } finally {
                this.loading = false;
            }
        },

        async setDeviceId(id) {
            this.deviceId = id;
            if (this.socket) {
                console.log('Alpine: Subscribing to device', id);
                this.socket.emit('subscribe', id);
            }
        },

        async scanDevice(deviceId) {
            await this.setDeviceId(deviceId);
            this.showAddModal = true;
            this.step = 'method';
            this.qrCode = null;
            this.qrError = null;
            this.phone = '';
            this.pairingCode = null;
            this.connected = false;
        },

        async fetchQR() {
            if (!this.deviceId || this.step !== 'qr') return;

            console.log('Alpine: Fetching QR for device', this.deviceId);
            this.qrError = null;

            try {
                const response = await fetch(`/devices/${this.deviceId}/scan`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();
                console.log('Alpine: Scan response status:', data.status);

                if (data.qr_code) {
                    console.log('Alpine: QR Code received');
                    this.qrCode = data.qr_code;
                } else if (data.status === 'connected') {
                    console.log('Alpine: Device already connected');
                    this.connected = true;
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    // Still initializing or no QR yet - retry
                    console.log('Alpine: No QR yet, retrying in 2s...');
                    setTimeout(() => this.fetchQR(), 2000);
                }
            } catch (e) {
                console.error('Alpine: Fetch QR error:', e);
                this.qrError = 'Failed to generate QR code. Retrying...';
                setTimeout(() => this.fetchQR(), 5000);
            }
        },

        startPolling() {
            this.pollInterval = setInterval(async () => {
                try {
                    const response = await fetch(`/devices/${this.deviceId}/status`);
                    const data = await response.json();

                    if (data.status === 'connected') {
                        this.connected = true;
                        clearInterval(this.pollInterval);
                        setTimeout(() => window.location.reload(), 1500);
                    } else if (data.status === 'waiting_qr' && !this.qrCode) {
                        await this.fetchQR();
                    }
                } catch (e) {
                    console.error('Polling error:', e);
                }
            }, 3000);
        }
    }));

    Alpine.data('contactManager', () => ({
        showModal: false,
        editingContact: null,
        form: {
            phone_number: '',
            name: '',
            email: '',
            labels: [],
            notes: ''
        },

        openAddModal() {
            this.editingContact = null;
            this.form = {
                phone_number: '',
                name: '',
                email: '',
                labels: [],
                notes: ''
            };
            this.showModal = true;
        },

        editContact(contact) {
            this.editingContact = contact;
            // Ensure labels is an array
            let labels = contact.labels;
            if (typeof labels === 'string') {
                try {
                    labels = JSON.parse(labels);
                } catch (e) {
                    labels = [];
                }
            }

            this.form = {
                phone_number: contact.phone_number || '',
                name: contact.name || '',
                email: contact.email || '',
                labels: Array.isArray(labels) ? labels : [],
                notes: contact.notes || ''
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingContact = null;
        }
    }));

    Alpine.data('apiKeyManager', () => ({
        showCreateModal: false,
        openCreateModal() { this.showCreateModal = true; },
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('API Key copied to clipboard!');
            });
        }
    }));

    Alpine.data('webhookManager', () => ({
        showModal: false,
        editingId: null,
        showSecrets: {},
        secrets: {},
        form: {
            url: '',
            max_retries: '3',
            events: []
        },

        openAddModal() {
            this.editingId = null;
            this.form = { url: '', max_retries: '3', events: ['message.received'] };
            // Uncheck all checkboxes by resetting the events array
            // The view will react to this change
            this.showModal = true;
        },

        editWebhook(id, url, events, maxRetries) {
            this.editingId = id;
            // Ensure events is an array
            if (typeof events === 'string') {
                try {
                    events = JSON.parse(events);
                } catch (e) {
                    events = [];
                }
            }

            this.form = {
                url: url,
                max_retries: String(maxRetries),
                events: Array.isArray(events) ? events : []
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingId = null;
        },

        toggleEvent(event) {
            // Need to ensure form.events is initialized
            if (!this.form.events) this.form.events = [];

            const idx = this.form.events.indexOf(event);
            if (idx > -1) {
                this.form.events.splice(idx, 1);
            } else {
                this.form.events.push(event);
            }
        },

        async toggleSecret(id) {
            if (this.showSecrets[id]) {
                this.showSecrets[id] = false;
            } else {
                try {
                    const res = await fetch(`/webhooks/${id}/secret`);
                    const data = await res.json();
                    this.secrets[id] = data.secret;
                    this.showSecrets[id] = true;
                } catch (e) {
                    console.error('Failed to fetch secret');
                }
            }
        },

        copyToClipboard(text) {
            navigator.clipboard.writeText(text);
        }
    }));

    Alpine.data('messageManager', () => ({
        showFilters: false,
        showMessageDetail: false,
        selectedMessage: null,

        viewMessage(message) {
            this.selectedMessage = message;
            this.showMessageDetail = true;
        }
    }));

    Alpine.data('uploadManager', function () {
        return {
            previewUrl: null,
            handleFileSelect: function (event) {
                var file = event.target.files[0];
                if (file) {
                    this.previewUrl = URL.createObjectURL(file);
                }
            }
        };
    });

    Alpine.data('singleSendManager', function () {
        return {
            form: {
                device_id: '',
                phone: '',
                message: '',
                type: 'text',
                media_url: '',
                fileName: '',
                imagePreview: ''
            },
            get canSend() {
                return this.form.device_id && this.form.phone && this.form.message;
            },
            formatPhone: function () {
                var phone = this.form.phone.replace(/\D/g, '');
                if (phone.startsWith('0')) {
                    phone = '62' + phone.substring(1);
                }
                this.form.phone = phone;
            },
            handleFileSelect: function (event) {
                var file = event.target.files[0];
                if (file) {
                    this.form.fileName = file.name;
                    this.form.media_url = '';
                    if (file.type.startsWith('image/')) {
                        var self = this;
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            self.form.imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        this.form.imagePreview = '';
                    }
                }
            },
            clearFile: function () {
                this.form.fileName = '';
                this.form.imagePreview = '';
                var fileInput = document.getElementById('mediaFileInput');
                if (fileInput) fileInput.value = '';
            }
        };
    });

    Alpine.data('applicationManager', function () {
        return {
            showApiModal: false,
            currentApp: null,
            activeTab: 'curl',
            openApiModal: function (app) {
                this.currentApp = app;
                this.showApiModal = true;
            },
            copyText: function (text) {
                if (!text) return;
                navigator.clipboard.writeText(text).catch(function (err) {
                    console.error('Failed to copy code:', err);
                });
            }
        };
    });

    Alpine.data('broadcastManager', function () {
        return {
            showCreateModal: false,
            form: {
                message_type: 'text',
                recipients_type: 'csv'
            },
            openCreateModal: function () {
                this.showCreateModal = true;
            }
        };
    });

    Alpine.data('expandableRow', function () {
        return {
            expanded: false,
            toggle: function () {
                this.expanded = !this.expanded;
            }
        };
    });

    Alpine.data('promoCodeForm', function (initialDiscountType, generateUrl) {
        return {
            discountType: initialDiscountType || 'percentage',
            generateCode: async function () {
                if (!generateUrl) return;
                try {
                    var response = await fetch(generateUrl);
                    var data = await response.json();
                    var codeInput = document.getElementById('codeInput');
                    if (codeInput) codeInput.value = data.code;
                } catch (error) {
                    console.error('Failed to generate code:', error);
                }
            }
        };
    });

    Alpine.data('templateManager', function () {
        return {
            showModal: false,
            editingTemplate: null,
            form: {
                name: '',
                category: 'Authentication',
                content: ''
            },
            openCreateModal: function () {
                this.editingTemplate = null;
                this.form = { name: '', category: 'Authentication', content: '' };
                this.showModal = true;
            },
            editTemplate: function (template) {
                this.editingTemplate = template;
                this.form = {
                    name: template.name || '',
                    category: template.category || 'Authentication',
                    content: template.content || ''
                };
                this.showModal = true;
            },
            closeModal: function () {
                this.showModal = false;
            },
            copyToClipboard: function (text) {
                var decodedText = text.replace(/\\n/g, '\n').replace(/\\'/g, "'");
                navigator.clipboard.writeText(decodedText).then(function () {
                    alert('Template copied to clipboard!');
                });
            }
        };
    });

    Alpine.data('autoReplyManager', function () {
        return {
            showModal: false,
            editingRule: null,
            form: {
                keyword: '',
                match_type: 'contains',
                reply_type: 'text',
                reply_value: '',
                template_id: '',
                device_id: '',
                priority: 0
            },
            openCreateModal: function () {
                this.editingRule = null;
                this.form = {
                    keyword: '',
                    match_type: 'contains',
                    reply_type: 'text',
                    reply_value: '',
                    template_id: '',
                    device_id: '',
                    priority: 0
                };
                this.showModal = true;
            },
            editRule: function (rule) {
                this.editingRule = rule;
                this.form = {
                    keyword: rule.keyword || '',
                    match_type: rule.match_type || 'contains',
                    reply_type: rule.reply_type || 'text',
                    reply_value: rule.reply_value || '',
                    template_id: rule.template_id || '',
                    device_id: rule.device_id || '',
                    priority: rule.priority || 0
                };
                this.showModal = true;
            },
            closeModal: function () {
                this.showModal = false;
            }
        };
    });

    Alpine.data('transactionManager', function () {
        return {
            rejectModalOpen: false,
            rejectAction: '',
            openReject: function (id) {
                this.rejectAction = '/admin/transactions/' + id + '/reject';
                this.rejectModalOpen = true;
            },
            closeReject: function () {
                this.rejectModalOpen = false;
                this.rejectAction = '';
            }
        };
    });

// Documentation Tools logic
window.toggleFullscreen = function() {
    const container = document.getElementById('scalar-container');
    const iframe = document.getElementById('scalar-iframe');
    const btn = document.getElementById('fullscreen-btn');

    if (!container || !iframe || !btn) return;

    if (container.classList.contains('fixed')) {
        // Exit Fullscreen
        container.classList.remove('fixed', 'top-16', 'inset-x-0', 'bottom-0', 'z-40', 'bg-white', 'p-4');
        container.classList.add('relative', 'w-full', 'rounded-3xl', 'shadow-xl', 'shadow-slate-200/50');
        iframe.parentElement.style.paddingTop = '60%';
        iframe.parentElement.style.height = 'auto';
        iframe.style.height = '100%';
        btn.innerHTML = '<span class="material-icons-round">fullscreen</span>';
        document.body.style.overflow = 'auto';
    } else {
        // Enter Fullscreen
        container.classList.remove('relative', 'w-full', 'rounded-3xl', 'shadow-xl', 'shadow-slate-200/50');
        container.classList.add('fixed', 'top-16', 'inset-x-0', 'bottom-0', 'z-40', 'bg-white', 'p-4');
        iframe.parentElement.style.paddingTop = '0';
        iframe.parentElement.style.height = '100%';
        iframe.style.height = '100%';
        btn.innerHTML = '<span class="material-icons-round">fullscreen_exit</span>';
        document.body.style.overflow = 'hidden';
    }
};

Alpine.start();
