// livescomp/assets/js/admin.js

document.addEventListener('DOMContentLoaded', function() {
    // ===== Sidebar Toggle =====
    const sidebarToggle = document.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

    // ===== DataTables Initialization =====
    const tables = document.querySelectorAll('table[data-table]');
    tables.forEach(table => {
        $(table).DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            initComplete: function() {
                // Add custom filter for status
                if (table.hasAttribute('data-status-filter')) {
                    this.api().columns(4).every(function() {
                        const column = this;
                        const select = document.createElement('select');
                        select.classList.add('form-select', 'form-select-sm', 'ms-2');
                        select.innerHTML = `
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="enviado">Enviado</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        `;
                        
                        select.addEventListener('change', function() {
                            column.search(this.value).draw();
                        });
                        
                        const filterRow = document.querySelector('.dataTables_filter');
                        if (filterRow) {
                            filterRow.appendChild(select);
                        }
                    });
                }
            }
        });
    });

    // ===== Form Validations =====
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // ===== Image Preview for Uploads =====
    const imageUploads = document.querySelectorAll('.image-upload');
    imageUploads.forEach(upload => {
        const input = upload.querySelector('input[type="file"]');
        const preview = upload.querySelector('.image-preview');
        
        if (input && preview) {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // ===== Toggle Password Visibility =====
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // ===== Confirmation for Delete Actions =====
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });

    // ===== Tooltips Initialization =====
    $('[data-bs-toggle="tooltip"]').tooltip();

    // ===== Toast Notifications =====
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    const toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 5000
        }).show();
    });
});
