<?php if (isset($_SESSION['error'])): ?>
    <div class="container row mb-4 mt-4 mx-auto" id="alertError">
        <div class="alert alert-danger alert-dismissible col-md-8 offset-md-2 fade show d-flex align-items-center" role="alert">
            <p class="mb-0"> <?php echo $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </p>
            <button type="button" class="btn-close top-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="row mb-4 mt-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="mb-0 card-title color-success">Solicitud de Reserva del Salón</h3>
                </div>
                <div class="card-body">
                    <form action="index.php?controlador=reservas&accion=crear" method="POST">
                        <div class="alert alert-success">
                            <p>
                                <strong>Importante:</strong> Complete este formulario inicial para solicitar la reserva del salón.
                                Después de enviar este formulario, deberá cargar el formulario de solicitud completo y el comprobante de pago del anticipo.
                            </p>
                        </div>

                        <div class="form-group row mb-4">
                            <div class="col-md-6">
                                <label for="fecha_evento" class="mb-2">Fecha del Evento:</label>
                                <input type="date" class="form-control" id="fecha_evento" name="fecha_evento" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="rango_horario" class="mb-2">Rango Horario:</label>
                                <select class="form-control" id="rango_horario" name="rango_horario" required>
                                    <option value="">Seleccione un rango horario</option>
                                    <option value="manana_tarde">Mañana/Tarde: 11:00 a 16:00</option>
                                    <option value="tarde_noche">Tarde/Noche: 17:00 a 21:00</option>
                                    <option value="noche_madrugada">Noche/Madrugada: 22:00 a 05:00</option>
                                </select>
                                <input type="hidden" id="hora_inicio" name="hora_inicio">
                                <input type="hidden" id="hora_fin" name="hora_fin">
                            </div>
                        </div>

                        <div class="alert alert-success mt-2">
                            <small><strong>Importante:</strong> Seleccione uno de los rangos horarios disponibles para la reserva del salón.</small>
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
                            <label for="motivo_de_uso" class="mb-2">Motivo de uso:</label>
                            <textarea class="form-control" id="motivo_de_uso" name="motivo_de_uso" rows="3" required></textarea>
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

                        <!-- Términos y Condiciones -->
                        <div class="form-group mt-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Términos y Condiciones</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($formularios_condiciones)): ?>
                                        <p>Por favor, lea detenidamente los términos y condiciones antes de continuar:</p>
                                        <div class="mb-3">
                                            <?php foreach ($formularios_condiciones as $form): ?>
                                                <a href="assets/docs/<?php echo $form['archivo']; ?>" class="btn btn-outline-primary mb-2" target="_blank">
                                                    <i class="fas fa-file-pdf"></i> Ver <?php echo htmlspecialchars($form['nombre']); ?>
                                                </a><br>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="acepta_terminos" id="acepta_terminos" required>
                                            <label class="form-check-label" for="acepta_terminos">
                                                <strong>He leído y acepto los términos y condiciones para la reserva del salón.</strong>
                                            </label>
                                            <div class="invalid-feedback">
                                                Debe aceptar los términos y condiciones para continuar.
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No hay términos y condiciones disponibles en este momento.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4 border-top pt-3 d-flex justify-content-between align-items-center">
                            <a href="index.php?controlador=reservas&accion=listar" class="btn btn-light">Cancelar</a>
                            <button type="submit" id="btn-enviar" class="btn btn-success-theme">Enviar Solicitud</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para manejar el formulario de grupo de matriculados y validar términos -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validación básica para horarios
        const horaInicioInput = document.getElementById('hora_inicio');
        const horaFinInput = document.getElementById('hora_fin');
        const formulario = document.querySelector('form');
        const aceptaTerminos = document.getElementById('acepta_terminos');
        const btnEnviar = document.getElementById('btn-enviar');

        // Definir los rangos permitidos
        const rangosPermitidos = {
            'manana_tarde': {
                inicio: '11:00',
                fin: '16:00'
            },
            'tarde_noche': {
                inicio: '17:00',
                fin: '21:00'
            },
            'noche_madrugada': {
                inicio: '22:00',
                fin: '05:00'
            }
        };
        
        // Manejar cambio en selección de rango horario
        const rangoHorarioSelect = document.getElementById('rango_horario');
        rangoHorarioSelect.addEventListener('change', function() {
            const rangoSeleccionado = this.value;
            if (rangoSeleccionado && rangosPermitidos[rangoSeleccionado]) {
                horaInicioInput.value = rangosPermitidos[rangoSeleccionado].inicio;
                horaFinInput.value = rangosPermitidos[rangoSeleccionado].fin;
            } else {
                horaInicioInput.value = '';
                horaFinInput.value = '';
            }
        });

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
            row.className = 'matriculado-row form-row mb-3 row';
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
        
        // Validar formulario antes de enviar
        formulario.addEventListener('submit', function(e) {
            // Verificar que se haya seleccionado un rango horario
            if (!horaInicioInput.value || !horaFinInput.value) {
                e.preventDefault();
                alert('Por favor, seleccione un rango horario válido.');
                return;
            }
            
            // Verificar que los términos y condiciones estén aceptados
            if (!aceptaTerminos.checked) {
                e.preventDefault(); // Detener el envío del formulario
                
                // Mostrar mensaje de error
                aceptaTerminos.classList.add('is-invalid');
                
                // Hacer scroll al checkbox de términos
                aceptaTerminos.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Destacar la sección con un efecto visual
                const termsCard = aceptaTerminos.closest('.card');
                termsCard.classList.add('border-danger');
                setTimeout(function() {
                    termsCard.classList.remove('border-danger');
                }, 2000);
                
                return false;
            } else {
                aceptaTerminos.classList.remove('is-invalid');
                return true;
            }
        });
        
        // Quitar mensaje de error cuando el usuario marca el checkbox
        aceptaTerminos.addEventListener('change', function() {
            if (this.checked) {
                this.classList.remove('is-invalid');
                this.closest('.card').classList.remove('border-danger');
            }
        });

        // Seleccionar si existe en la url fecha de reserva
        const urlParams = new URLSearchParams(window.location.search);
        const fechaReserva = urlParams.get('fecha');
        if (fechaReserva) {
            const fechaInput = document.getElementById('fecha_evento');
            fechaInput.value = fechaReserva;
        }

        // Desaparecer el alerta de error después de 10 segundos si existe
        const alertError = document.getElementById('alertError');
        if (alertError) {
            setTimeout(() => {
                alertError.style.display = 'none';
            }, 10000);
        }
    });
</script>