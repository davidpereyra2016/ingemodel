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
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Gestión de Usuarios</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                Cargar Usuarios
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="1" class="text-center">No hay usuarios registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo $usuario['nombre']; ?></td>
                                    <td><?php echo $usuario['email']; ?></td>
                                    <td><?php echo $usuario['rol']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning edit-user" 
                                        data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                        data-id="<?php echo $usuario['id']; ?>">
                                            <i class="fas fa-edit me-1"></i>
                                            Editar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-user" 
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Cargar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="mb-3">
                        <label for="newUserName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="newUserName" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="newUserEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserPassword" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newUserPassword" name="password" required>
                            <button type="button" class="btn btn-outline-secondary" id="toggleNewPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="newUserRole" class="form-label">Rol</label>
                        <select class="form-select" id="newUserRole" name="rol" required>
                            <option value="">Seleccionar Rol</option>
                            <option value="admin">Admin</option>
                            <option value="usuario">Usuario</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveNewUser">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id" value="<?php echo isset($_SESSION['temp_usuario']) ? $_SESSION['temp_usuario']['id'] : ''; ?>">
                    <div class="mb-3">
                        <label for="editUserName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editUserName" name="nombre" required value="<?php echo isset($_SESSION['temp_usuario']) ? $_SESSION['temp_usuario']['nombre'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="editUserEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" required value="<?php echo isset($_SESSION['temp_usuario']) ? $_SESSION['temp_usuario']['email'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="editUserPassword" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="editUserPassword" name="password">
                            <button type="button" class="btn btn-outline-secondary" id="toggleEditPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
                    </div>
                    <div class="mb-3">
                        <label for="editUserRole" class="form-label">Rol</label>
                        <select class="form-select" id="editUserRole" name="rol" required>
                            <option value="">Seleccionar Rol</option>
                            <option value="admin" <?php echo (isset($_SESSION['temp_usuario']) && $_SESSION['temp_usuario']['rol'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="usuario" <?php echo (isset($_SESSION['temp_usuario']) && $_SESSION['temp_usuario']['rol'] == 'usuario') ? 'selected' : ''; ?>>Usuario</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveEditUser">Editar cambios</button>
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
                ¿Estás seguro de que deseas eliminar este usuario?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar modales
        const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
        const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));

        // Referencias a formularios
        const addUserForm = document.getElementById('addUserForm');
        const editUserForm = document.getElementById('editUserForm');

        // Referencias a botones
        const saveNewUserButton = document.getElementById('saveNewUser');
        const saveEditUserButton = document.getElementById('saveEditUser');
        const confirmDeleteButton = document.getElementById('confirmDelete');

        let deleteUserId = null;

        // Mostrar modal de edición si hay datos temporales
        <?php if (isset($_SESSION['temp_usuario'])): ?>
            editUserModal.show();
            <?php unset($_SESSION['temp_usuario']); ?>
        <?php endif; ?>

        // Manejar clic en botón editar
        document.querySelectorAll('.edit-user').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.getAttribute('data-id');

                // Crear y enviar formulario para obtener datos del usuario
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?controlador=usuarios&accion=buscar';

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = userId;

                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            });
        });

        // Manejar guardado de edición
        saveEditUserButton.addEventListener('click', function() {
            if (!editUserForm.checkValidity()) {
                editUserForm.reportValidity();
                return;
            }
            editUserForm.action = 'index.php?controlador=usuarios&accion=editar';
            editUserForm.method = 'POST';
            editUserForm.submit();
        });

        // Guardar nuevo usuario
        saveNewUserButton.addEventListener('click', function() {
            if (!addUserForm.checkValidity()) {
                addUserForm.reportValidity();
                return;
            }
            addUserForm.action = 'index.php?controlador=usuarios&accion=crear';
            addUserForm.method = 'POST';
            addUserForm.submit();
        });

        // Preparar eliminación de usuario
        document.querySelectorAll('.delete-user').forEach(button => {
            button.addEventListener('click', function() {
                deleteUserId = this.getAttribute('data-id');
                deleteModal.show();
            });
        });

        // Confirmar eliminación de usuario
        confirmDeleteButton.addEventListener('click', function() {
            if (deleteUserId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?controlador=usuarios&accion=eliminar';

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = deleteUserId;

                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Limpiar formulario al abrir modal de crear usuario
        document.querySelector('[data-bs-target="#addUserModal"]').addEventListener('click', function() {
            addUserForm.reset();
        });
       
        // Alternar visibilidad de la contraseña en el modal de agregar usuario
        document.getElementById('toggleNewPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('newUserPassword');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Alternar visibilidad de la contraseña en el modal de editar usuario
        document.getElementById('toggleEditPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('editUserPassword');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>