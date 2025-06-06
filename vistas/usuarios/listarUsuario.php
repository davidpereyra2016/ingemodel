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

        <h2 class="w-auto">Gestión de Usuarios</h2>
        <button type="button" class="btn btn-theme btn-success-theme w-auto" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
        </button>

    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No hay usuarios registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo $usuario['matricula']; ?></td>
                                    <td><?php echo $usuario['nombre']; ?></td>
                                    <td><?php echo $usuario['apellido']; ?></td>
                                    <td><?php echo $usuario['email']; ?></td>
                                    <td><?php echo $usuario['telefono']; ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo ($usuario['rol'] == 'administrador') ? 'bg-danger' : 'bg-primary'; ?>">
                                            <?php echo $usuario['rol']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo ($usuario['estado'] == 'activo') ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $usuario['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success-theme edit-user"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal"
                                            data-id="<?php echo $usuario['id']; ?>">
                                            <i class="fas fa-edit me-1"></i>
                                            Editar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning delete-user"
                                            data-id="<?php echo $usuario['id']; ?>">
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

<!-- Modal para Cargar Usuario -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm" action="?controlador=usuarios&accion=crear" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="matricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="matricula" name="matricula" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="domicilio" class="form-label">Domicilio</label>
                            <input type="text" class="form-control" id="domicilio" name="domicilio" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Seleccionar Rol</option>
                                <option value="administrador">Administrador</option>
                                <option value="ingeniero">Ingeniero</option>
                                <option value="encargado">Encargado</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="activo" selected>Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
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

<!-- Modal para Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" action="?controlador=usuarios&accion=editar" method="POST">
                    <input type="hidden" id="editUserId" name="id" value="">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editMatricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="editMatricula" name="matricula" required value="">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required value="">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editApellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="editApellido" name="apellido" required value="">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required value="">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editTelefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="editTelefono" name="telefono" required value="">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editDomicilio" class="form-label">Domicilio</label>
                            <input type="text" class="form-control" id="editDomicilio" name="domicilio" required value="">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editPassword" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="editPassword" name="password">
                                <button type="button" class="btn btn-outline-secondary toggle-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editRol" class="form-label">Rol</label>
                            <select class="form-select" id="editRol" name="rol" required>
                                <option value="">Seleccionar Rol</option>
                                <option value="administrador">Administrador</option>
                                <option value="ingeniero">Ingeniero</option>
                                <option value="encargado">Encargado</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editEstado" class="form-label">Estado</label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
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
                <h5 class="modal-title" id="confirmDeleteModalLabel">Eliminar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="?controlador=usuarios&accion=eliminar" method="POST" id="deleteUserForm">
                    <input type="hidden" id="deleteUserId" name="id" value="">
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
        
        // Mostrar/ocultar contraseña
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

        // Configurar modal de edición
        const editUserButtons = document.querySelectorAll('.edit-user');
        editUserButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                document.getElementById('editUserId').value = userId;

                // Realizar una solicitud AJAX para obtener los datos del usuario
                const formData = new FormData();
                formData.append('id', userId);

                fetch('?controlador=usuarios&accion=buscar', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest' // Indicar que es una solicitud AJAX
                        }
                    })
                    .then(response => {
                        // Verificar que la respuesta sea correcta
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor: ' + response.status);
                        }
                        // Verificar que la respuesta sea JSON
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('La respuesta no es de tipo JSON: ' + contentType);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Llenar el formulario con los datos del usuario
                            const usuario = data.data;
                            document.getElementById('editMatricula').value = usuario.matricula || '';
                            document.getElementById('editNombre').value = usuario.nombre || '';
                            document.getElementById('editApellido').value = usuario.apellido || '';
                            document.getElementById('editEmail').value = usuario.email || '';
                            document.getElementById('editTelefono').value = usuario.telefono || '';
                            document.getElementById('editDomicilio').value = usuario.domicilio || '';
                            document.getElementById('editPassword').value = '';
                            
                            // Seleccionar el rol correcto
                            const rolSelect = document.getElementById('editRol');
                            for (let i = 0; i < rolSelect.options.length; i++) {
                                if (rolSelect.options[i].value === usuario.rol) {
                                    rolSelect.options[i].selected = true;
                                    break;
                                }
                            }
                            
                            // Seleccionar el estado correcto
                            const estadoSelect = document.getElementById('editEstado');
                            for (let i = 0; i < estadoSelect.options.length; i++) {
                                if (estadoSelect.options[i].value === usuario.estado) {
                                    estadoSelect.options[i].selected = true;
                                    break;
                                }
                            }
                            
                            // Mostrar el modal
                            const editModalElement = document.getElementById('editUserModal');
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
                            // Verificar si es necesario redirigir (usuario no autenticado)
                            if (data.redirect) {
                                window.location.href = data.redirect;
                                return;
                            }
                            // Mostrar mensaje de error
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error detallado:', error);
                        alert('Error al cargar los datos del usuario: ' + error.message);
                    });
            });
        });

        // Configurar modal de eliminación
        const deleteUserButtons = document.querySelectorAll('.delete-user');
        deleteUserButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                document.getElementById('deleteUserId').value = userId;

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