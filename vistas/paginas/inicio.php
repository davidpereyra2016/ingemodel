<div id="carouselEvento" class="carousel slide hero-carousel" data-bs-ride="carousel" style="height: 400px; overflow: hidden;">
    <div class="carousel-indicators bg-success-theme">
        <button type="button" data-bs-target="#carouselEvento" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselEvento" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselEvento" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="./assets/img/slider-1.jpg" class="d-block w-100 hero-img-overlay object-fit-fill" alt="Imagen 1">
            <div class="card-img-overlay card-img-overlay-theme color-white d-flex flex-column align-items-center text-center mt-30">
                <h2 class="card-title color-white b5 mt-10 mb-3">Reserva de Salón de Eventos</h2>
                <p class="card-subtitle color-white mb-3">Selecciona una fecha disponible para tu evento</p>
                <!-- <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success btn-lg">Reservar</a> -->
                <button type="button" class="btn btn-lg btn-success-theme" data-bs-toggle="offcanvas" data-bs-target="#offcanvasForm" aria-controls="offcanvasRight">
                    <i class="bi bi-calendar2-plus"></i>
                    <span class="ms-2">Reservar</span>
                </button>
            </div>
        </div>
        <div class="carousel-item">
            <img src="./assets/img/slider-3.jpg" class="d-block w-100 hero-img-overlay object-fit-cover" alt="Imagen 2">
            <div class="card-img-overlay card-img-overlay-theme color-white d-flex flex-column align-items-center text-center mt-30">
                <h2 class="card-title color-white mt-10 mb-3">Reserva Cancha de Futbol</h2>
                <p class="card-subtitle color-white mb-3">Selecciona una fecha y hora disponible para tu partido</p>
                <button class="btn btn-lg btn-success-theme" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <i class="bi bi-calendar2-plus"></i>
                    <span class="ms-2">Reservar</span>
                </button>
            </div>
        </div>
        <div class="carousel-item">
            <img src="./assets/img/slider-2.jpg" class="d-block w-100 hero-img-overlay object-fit-cover" alt="Imagen 3">
            <div class="card-img-overlay card-img-overlay-theme color-white d-flex flex-column align-items-center text-center mt-30">
                <h2 class="card-title color-white mt-10 mb-3">Reserva de Salón de Eventos</h2>
                <p class="card-subtitle color-white mb-3">Selecciona una fecha disponible para tu evento</p>
                <button class="btn btn-lg btn-success-theme" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasForm" aria-controls="offcanvasRight">
                    <i class="bi bi-calendar2-plus"></i>
                    <span class="ms-2">Reservar</span>
                </button>
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

<?php include __DIR__ . './../reservas/formulario.php'; ?>



<!-- <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasRightLabel">Solicitud de Reserva del Salón</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    
  </div>
</div> -->