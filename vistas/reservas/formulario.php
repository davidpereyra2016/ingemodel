<?php if (isset($_SESSION['error'])): ?>
    <div class="toast-container top-0 end-0 p-3" style="top: -2rem;">
        <div id="toastError" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php echo $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="offcanvas offcanvas-end w-50 formulario" tabindex="-1" id="offcanvasForm" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header mt-2 border-bottom">
        <h3 class="offcanvas-title color-success" id="offcanvasRightLabel">Solicitud de Reserva del Salón</h3>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="index.php?controlador=reservas&accion=crear" method="POST">
            <div class="alert alert-info">
                <p><strong>Importante:</strong> Complete este formulario inicial para solicitar la reserva del salón. Después de enviar este formulario, deberá cargar el formulario de solicitud completo y el comprobante de pago del anticipo.</p>
            </div>

            <div class="form-group row mb-4">
                <div class="col-md-4">
                    <label for="fecha_evento mb-2">Fecha del Evento:</label>
                    <input type="date" class="form-control" id="fecha_evento" name="fecha_evento" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4">
                    <label for="hora_inicio mb-2">Hora de Inicio:</label>
                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                </div>
                <div class="col-md-4">
                    <label for="hora_fin mb-2">Hora de Finalización:</label>
                    <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="tipo_uso mb-2">Tipo de Uso:</label>
                <select class="form-control" id="tipo_uso" name="tipo_uso" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Evento personal">Evento personal</option>
                    <option value="Reunión profesional">Reunión profesional</option>
                    <option value="Conferencia">Conferencia</option>
                    <option value="Capacitación">Capacitación</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div class="form-group mb-4">
                <label for="es_grupo mb-2">¿Es una solicitud de grupo de matriculados?</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="es_grupo" id="no_grupo" value="0" checked>
                    <label class="form-check-label" for="no_grupo">No, solicitud individual</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="es_grupo" id="si_grupo" value="1">
                    <label class="form-check-label" for="si_grupo">Sí, solicitud de grupo</label>
                </div>
            </div>

            <div id="grupo_matriculados" style="display: none;">
                <h4 class="mt-3">Otros Matriculados del Grupo</h4>
                <p class="text-muted">Agregue al menos 4 matriculados adicionales (requisito para solicitudes grupales)</p>
                <div class="border p-3 mb-4">
                    <div id="matriculados_container">
                        <!-- Aquí se agregarán los campos dinámicamente -->
                        <div class="matriculado-row form-group row mb-3">
                            <div class="col-md-8">
                                <label class="mb-2">Nombre Completo:</label>
                                <input type="text" class="form-control" name="nombres[]">
                            </div>
                            <div class="col-md-4">
                                <label class="mb-2">Matrícula:</label>
                                <input type="text" class="form-control" name="matriculas[]">
                            </div>
                        </div>
                        <div class="matriculado-row form-group row mb-3">
                            <div class="col-md-8">
                                <label class="mb-2">Nombre Completo:</label>
                                <input type="text" class="form-control" name="nombres[]">
                            </div>
                            <div class="col-md-4">
                                <label class="mb-2">Matrícula:</label>
                                <input type="text" class="form-control" name="matriculas[]">
                            </div>
                        </div>
                        <div class="matriculado-row form-group row mb-3">
                            <div class="col-md-8">
                                <label class="mb-2">Nombre Completo:</label>
                                <input type="text" class="form-control" name="nombres[]">
                            </div>
                            <div class="col-md-4">
                                <label class="mb-2">Matrícula:</label>
                                <input type="text" class="form-control" name="matriculas[]">
                            </div>
                        </div>
                        <div class="matriculado-row form-group row mb-3">
                            <div class="col-md-8">
                                <label class="mb-2">Nombre Completo:</label>
                                <input type="text" class="form-control" name="nombres[]">
                            </div>
                            <div class="col-md-4">
                                <label class="mb-2">Matrícula:</label>
                                <input type="text" class="form-control" name="matriculas[]">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success" id="add_matriculado">
                        <i class="fas fa-plus"></i>
                        Agregar otro matriculado
                    </button>
                </div>

            </div>

            <div class="alert alert-warning mt-4">
                <p><strong>Por favor tenga en cuenta:</strong></p>
                <ul>
                    <li>Para confirmar la reserva, deberá abonar un anticipo del 50% del arancel correspondiente.</li>
                    <li>El uso del salón después de las 22:00 hs tiene un arancel diferente.</li>
                    <li>Deberá presentar los comprobantes de pago y el formulario firmado.</li>
                    <li>La solicitud quedará pendiente hasta que un administrador la apruebe.</li>
                </ul>
            </div>

            <div class="form-group mt-4 border-top pt-3 d-flex justify-content-between align-items-center">
                <!-- <button type="submit" class="btn btn-success-theme" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFormDocumento" aria-controls="offcanvasRight">Enviar Solicitud</button> -->
                <button type="submit" class="btn btn-success-theme">Enviar Solicitud</button>
                <button type="reset" class="btn btn-light" data-bs-dismiss="offcanvas">Cancelar</button> 
            </div>
        </form>
    </div>
</div>

<!-- Script para manejar el formulario de grupo de matriculados -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/Ocultar sección de grupo según selección
        const grupoRadios = document.querySelectorAll('input[name="es_grupo"]');
        const grupoSection = document.getElementById('grupo_matriculados');

        grupoRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === '1') {
                    grupoSection.style.display = 'block';
                } else {
                    grupoSection.style.display = 'none';
                }
            });
        });

        // Agregar nuevo matriculado
        const addButton = document.getElementById('add_matriculado');
        const container = document.getElementById('matriculados_container');

        addButton.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'matriculado-row form-group row';
            row.innerHTML = `
            <div class="col-md-8">
                <label>Nombre Completo:</label>
                <input type="text" class="form-control" name="nombres[]">
            </div>
            <div class="col-md-4">
                <label>Matrícula:</label>
                <input type="text" class="form-control" name="matriculas[]">
            </div>
        `;
            container.appendChild(row);
        });

        // Mostrar el toast al cargar la página
        const toastError = document.getElementById('toastError')

        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastError)

        toastBootstrap.show()

    });
</script>