<div id="carouselEvento" class="carousel slide hero-carousel" data-bs-ride="carousel" style="height: 400px; overflow: hidden;">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselEvento" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselEvento" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselEvento" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="./assets/img/lugar-evento-2.jpg" class="d-block w-100" alt="Imagen 1">
            <div class="card-img-overlay color-white w-100 h-100 d-flex flex-column align-items-center text-center mt-30" style="top: 0; background-color: rgba(0, 0, 0, 0.5); padding-top: 8rem;">
                <h2 class="mt-10" style="color: #fff; font-size: 3rem;">Reserva de Salón de Eventos</h2>
                <p class=" color-white" style="color: #fff; font-size: 1.5rem;">Selecciona una fecha disponible para tu evento</p>
                <!-- <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success btn-lg">Reservar</a> -->
                <button class="btn btn-lg btn-success-theme" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <i class="bi bi-calendar2-plus"></i>
                    <span class="ms-2">Reservar</span>
                </button>
            </div>
        </div>
        <div class="carousel-item">
            <img src="./assets/img/lugar-evento.jpg" class="d-block w-100 hero-img-overlay" alt="Imagen 2">
            <div class="card-img-overlay color-white w-100 h-100 d-flex flex-column align-items-center text-center mt-30" style="top: 0; background-color: rgba(0, 0, 0, 0.5); padding-top: 8rem;">
                <h2 class="mt-10" style="color: #fff; font-size: 3rem;">Reserva de Salón de Eventos</h2>
                <p class=" color-white" style="color: #fff; font-size: 1.5rem;">Selecciona una fecha disponible para tu evento</p>
                <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success btn-lg">Reservar</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="./assets/img/lugar-evento-2.jpg" class="d-block w-100 hero-img-overlay" alt="Imagen 3">
            <div class="card-img-overlay color-white w-100 h-100 d-flex flex-column align-items-center text-center mt-30" style="top: 0; background-color: rgba(0, 0, 0, 0.5); padding-top: 8rem;">
                <h2 class="mt-10" style="color: #fff; font-size: 3rem;">Reserva de Salón de Eventos</h2>
                <p class=" color-white" style="color: #fff; font-size: 1.5rem;">Selecciona una fecha disponible para tu evento</p>
                <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success btn-lg">Reservar</a>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 container mt-theme">
    <div class="row calendar-container">
        <h2 class="mb-4 mt-2 text-center">
            <i class="bi bi-calendar2-event me-1"></i>
            Calendario de Disponibilidad
        </h2>
        <div id="calendarInicio"></div>
    </div>
</div>



<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasRightLabel">Solicitud de Reserva del Salón</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    
  </div>
</div>