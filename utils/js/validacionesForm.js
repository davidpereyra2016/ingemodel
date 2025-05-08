document.addEventListener('DOMContentLoaded', function () {
    const formularioCrear = document.querySelector('#formularioCrear');
    if (!formularioCrear) return; // Si no existe el formulario, salir de la función
    // Validación básica para horarios
    const horaInicioInput = document.getElementById('hora_inicio');
    const horaFinInput = document.getElementById('hora_fin');
    const formulario = document.querySelector('form');
    const aceptaTerminos = document.getElementById('acepta_terminos');
    const terminosCheck = document.getElementById('terminos_check');
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
    rangoHorarioSelect.addEventListener('change', function () {
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
        radio.addEventListener('change', function () {
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

    addButton.addEventListener('click', function () {
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

    // Terminos y condiciones

    const btnTerminos = document.getElementById('terminos_condiciones');
    terminosCheck.style.display = 'none'; // Ocultar checkbox inicialmente
    btnEnviar.disabled = true; // Deshabilitar el botón de enviar inicialmente
    btnEnviar.classList.add('disabled'); // Agregar clase para indicar que está deshabilitado


    if (btnTerminos) {
        btnTerminos.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            window.open(url, '_blank');

            // Mostrar el checkbox de términos y condiciones despues de 5 segundos
            setTimeout(() => {
                terminosCheck.style.display = 'block';
                terminosCheck.removeAttribute('hidden');
            }, 5000);

            // Agregar un evento de cambio al checkbox de términos y condiciones
        
            aceptaTerminos.addEventListener('change', function() {
                if (aceptaTerminos.checked) {
                    // Habilitar el botón de enviar si se aceptan los términos
                    btnEnviar.disabled = false;
                    btnEnviar.classList.remove('disabled');
                } else {
                    // Deshabilitar el botón de enviar si no se aceptan los términos
                    btnEnviar.disabled = true;
                    btnEnviar.classList.add('disabled');
                }
            });

        });
    }
    // Validar formulario antes de enviar
    formulario.addEventListener('submit', function (e) {
    
        e.preventDefault();

        Swal.fire({
            title: '¿Está seguro de enviar el formulario?',
            text: "Al enviar la solicitud quedará en constancia que ah leído y aceptado los términos y condiciones, La reserva del salón no tiene que ser con fines de lucro y si se registra alguna irregularidad se iniciará acciones legales",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            } else {
                e.preventDefault();
            }
        });

        if (aceptaTerminos.style.display === 'block' && !aceptaTerminos.checked) {
            e.preventDefault(); // Detener el envío del formulario

            // Mostrar mensaje de error
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes aceptar los términos y condiciones para continuar.',
                confirmButtonText: 'Aceptar'
            });

            // Hacer scroll al checkbox de términos
            aceptaTerminos.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Destacar la sección con un efecto visual
            const termsCard = aceptaTerminos.closest('.card');
            termsCard.classList.add('border-danger');
            setTimeout(function () {
                termsCard.classList.remove('border-danger');
            }, 2000);

            return false;
        }

        // Verificar que los términos y condiciones estén aceptados
        /*if (!aceptaTerminos.checked) {
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
        }*/
    });

    // Quitar mensaje de error cuando el usuario marca el checkbox
    // aceptaTerminos.addEventListener('change', function() {
    //     if (this.checked) {
    //         this.classList.remove('is-invalid');
    //         this.closest('.card').classList.remove('border-danger');
    //     }
    // });

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