<!-- Mensajes de sesión -->
<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['mensaje']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="container mt-4">
    <div class="mb-4 mt-5 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h2 class="w-auto">Gestión de Plataformas</h2>
        <button type="button" class="btn btn-theme btn-success-theme w-auto" data-bs-toggle="modal" data-bs-target="#addPlataformaModal">
            <i class="fas fa-plus me-1"></i> Nueva Plataforma
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="plataformasTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Contacto Principal</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($plataformas)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay plataformas registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($plataformas as $plataforma): ?>
                                <tr>
                                    <td><?php echo $plataforma['id']; ?></td>
                                    <td><?php echo $plataforma['nombre']; ?></td>
                                    <td><?php echo strlen($plataforma['descripcion']) > 50 ? substr($plataforma['descripcion'], 0, 50) . '...' : $plataforma['descripcion']; ?></td>
                                    <td><?php echo $plataforma['telefono']; ?></td>
                                    <td><?php echo $plataforma['correo']; ?></td>
                                    <td><?php echo $plataforma['contacto_principal']; ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo ($plataforma['estado'] == 'activo') ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $plataforma['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success-theme edit-plataforma"
                                            data-bs-toggle="modal" data-bs-target="#editPlataformaModal"
                                            data-id="<?php echo $plataforma['id']; ?>">
                                            <i class="fas fa-edit me-1"></i>
                                            Editar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning delete-plataforma"
                                            data-id="<?php echo $plataforma['id']; ?>">
                                            <i class="fas fa-trash me-1"></i>
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Plataforma -->
<div class="modal fade" id="addPlataformaModal" tabindex="-1" aria-labelledby="addPlataformaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPlataformaModalLabel">Nueva Plataforma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPlataformaForm" action="?controlador=plataforma&accion=crear" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="correo" name="correo">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contacto_principal" class="form-label">Contacto Principal</label>
                            <input type="text" class="form-control" id="contacto_principal" name="contacto_principal">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sitio_web" class="form-label">Sitio Web</label>
                            <input type="url" class="form-control" id="sitio_web" name="sitio_web" placeholder="https://ejemplo.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="activo" selected>Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Plataforma -->
<div class="modal fade" id="editPlataformaModal" tabindex="-1" aria-labelledby="editPlataformaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPlataformaModalLabel">Editar Plataforma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPlataformaForm" action="?controlador=plataforma&accion=editar" method="POST">
                    <input type="hidden" id="editPlataformaId" name="id" value="">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editTelefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="editTelefono" name="telefono">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editCorreo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="editCorreo" name="correo">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editContactoPrincipal" class="form-label">Contacto Principal</label>
                            <input type="text" class="form-control" id="editContactoPrincipal" name="contacto_principal">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editSitioWeb" class="form-label">Sitio Web</label>
                            <input type="url" class="form-control" id="editSitioWeb" name="sitio_web" placeholder="https://ejemplo.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editEstado" class="form-label">Estado</label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="editDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="editDireccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="editDireccion" name="direccion" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Eliminar Plataforma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar esta plataforma? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="?controlador=plataforma&accion=eliminar" method="POST" id="deletePlataformaForm">
                    <input type="hidden" id="deletePlataformaId" name="id" value="">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Limpiar cualquier backdrop modal que pueda haber quedado de una sesión anterior
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            backdrop.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Configurar modal de edición
        const editPlataformaButtons = document.querySelectorAll('.edit-plataforma');
        editPlataformaButtons.forEach(button => {
            button.addEventListener('click', function() {
                const plataformaId = this.getAttribute('data-id');
                document.getElementById('editPlataformaId').value = plataformaId;

                // Realizar una solicitud AJAX para obtener los datos de la plataforma
                const formData = new FormData();
                formData.append('id', plataformaId);

                fetch('?controlador=plataforma&accion=buscar', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor: ' + response.status);
                        }
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('La respuesta no es de tipo JSON: ' + contentType);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Llenar el formulario con los datos de la plataforma
                            const plataforma = data.data;
                            document.getElementById('editNombre').value = plataforma.nombre || '';
                            document.getElementById('editDescripcion').value = plataforma.descripcion || '';
                            document.getElementById('editTelefono').value = plataforma.telefono || '';
                            document.getElementById('editCorreo').value = plataforma.correo || '';
                            document.getElementById('editDireccion').value = plataforma.direccion || '';
                            document.getElementById('editContactoPrincipal').value = plataforma.contacto_principal || '';
                            document.getElementById('editSitioWeb').value = plataforma.sitio_web || '';
                            
                            // Seleccionar el estado correcto
                            const estadoSelect = document.getElementById('editEstado');
                            for (let i = 0; i < estadoSelect.options.length; i++) {
                                if (estadoSelect.options[i].value === plataforma.estado) {
                                    estadoSelect.options[i].selected = true;
                                    break;
                                }
                            }
                            
                            // Mostrar el modal
                            const editModalElement = document.getElementById('editPlataformaModal');
                            const editModal = new bootstrap.Modal(editModalElement);
                            
                            // Asegurarse de que el backdrop se elimine cuando el modal se cierre
                            editModalElement.addEventListener('hidden.bs.modal', function () {
                                document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                                    backdrop.remove();
                                });
                                document.body.classList.remove('modal-open');
                                document.body.style.overflow = '';
                                document.body.style.paddingRight = '';
                            });
                            
                            editModal.show();
                        } else {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                                return;
                            }
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error detallado:', error);
                        alert('Error al cargar los datos de la plataforma: ' + error.message);
                    });
            });
        });

        // Configurar modal de eliminación
        const deletePlataformaButtons = document.querySelectorAll('.delete-plataforma');
        deletePlataformaButtons.forEach(button => {
            button.addEventListener('click', function() {
                const plataformaId = this.getAttribute('data-id');
                document.getElementById('deletePlataformaId').value = plataformaId;

                // Mostrar modal de confirmación
                const confirmDeleteModalElement = document.getElementById('confirmDeleteModal');
                const confirmDeleteModal = new bootstrap.Modal(confirmDeleteModalElement);
                
                // Asegurarse de que el backdrop se elimine cuando el modal se cierre
                confirmDeleteModalElement.addEventListener('hidden.bs.modal', function () {
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                });
                
                confirmDeleteModal.show();
            });
        });
    });
</script>