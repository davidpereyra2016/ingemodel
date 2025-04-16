<?php
// Mostrar mensajes de error si existen
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Solicitud de Reserva del Salón</h3>
                </div>
                <div class="card-body">
                    <form action="index.php?controlador=reservas&accion=crear" method="POST">
                        <div class="alert alert-info">
                            <p><strong>Importante:</strong> Complete este formulario inicial para solicitar la reserva del salón. Después de enviar este formulario, deberá cargar el formulario de solicitud completo y el comprobante de pago del anticipo.</p>
                        </div>

                        <div class="form-group">
                            <label for="fecha_evento">Fecha del Evento:</label>
                            <input type="date" class="form-control" id="fecha_evento" name="fecha_evento" required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="hora_inicio">Hora de Inicio:</label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="hora_fin">Hora de Finalización:</label>
                                <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tipo_uso">Tipo de Uso:</label>
                            <select class="form-control" id="tipo_uso" name="tipo_uso" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Evento personal">Evento personal</option>
                                <option value="Reunión profesional">Reunión profesional</option>
                                <option value="Conferencia">Conferencia</option>
                                <option value="Capacitación">Capacitación</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
<<<<<<< HEAD

=======
                        <div class="form-group">
                            <label for="motivo_de_uso">Motivo de uso:</label>
                            <textarea class="form-control" id="motivo_de_uso" name="motivo_de_uso" rows="3" required></textarea>
                        </div>
                        
>>>>>>> origin/develop-david
                        <div class="form-group">
                            <label>¿Es una solicitud de grupo de matriculados?</label>
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

                            <div id="matriculados_container">
                                <!-- Aquí se agregarán los campos dinámicamente -->
                                <div class="matriculado-row form-row">
                                    <div class="form-group col-md-4">
                                        <label>Matrícula:</label>
                                        <input type="text" class="form-control" name="matriculas[]">
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>Nombre Completo:</label>
                                        <input type="text" class="form-control" name="nombres[]">
                                    </div>
                                </div>
                                <div class="matriculado-row form-row">
                                    <div class="form-group col-md-4">
                                        <label>Matrícula:</label>
                                        <input type="text" class="form-control" name="matriculas[]">
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>Nombre Completo:</label>
                                        <input type="text" class="form-control" name="nombres[]">
                                    </div>
                                </div>
                                <div class="matriculado-row form-row">
                                    <div class="form-group col-md-4">
                                        <label>Matrícula:</label>
                                        <input type="text" class="form-control" name="matriculas[]">
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>Nombre Completo:</label>
                                        <input type="text" class="form-control" name="nombres[]">
                                    </div>
                                </div>
                                <div class="matriculado-row form-row">
                                    <div class="form-group col-md-4">
                                        <label>Matrícula:</label>
                                        <input type="text" class="form-control" name="matriculas[]">
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>Nombre Completo:</label>
                                        <input type="text" class="form-control" name="nombres[]">
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add_matriculado">+ Agregar otro matriculado</button>
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

                        <div class="form-group mt-4 text-center">
                            <a href="index.php?controlador=reservas&accion=listar" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
            row.className = 'matriculado-row form-row';
            row.innerHTML = `
            <div class="form-group col-md-4">
                <label>Matrícula:</label>
                <input type="text" class="form-control" name="matriculas[]">
            </div>
            <div class="form-group col-md-8">
                <label>Nombre Completo:</label>
                <input type="text" class="form-control" name="nombres[]">
            </div>
        `;
            container.appendChild(row);
        });
    });
</script>