<?php
// Incluir el head de la página
include 'head.php';
?>
<body>
    <!-- Incluir el header -->
    <?php include 'header.php'; ?>
    <!-- Contenido principal -->
    <main class="main-content">

    <!-- CONTACTO — SOLO CONTENIDO -->
<!-- Breadcrumb -->
<div class="page-header breadcrumb-wrap">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php" rel="nofollow">Inicio</a>
      <span></span> Páginas
      <span></span> Contacto
    </div>
  </div>
</div>

<!-- Hero -->
<section class="hero-2 bg-green">
  <div class="hero-content">
    <div class="container">
      <div class="text-center">
        <h4 class="text-brand mb-20">Estamos para ayudarte</h4>
        <h1 class="mb-20 wow fadeIn animated font-xxl fw-900">
          Ponte en <span class="text-style-1">Contacto</span>
        </h1>
        <p class="w-50 m-auto mb-40 wow fadeIn animated">
          Tu opinión y consultas son fundamentales para seguir mejorando.
        </p>
        <p class="wow fadeIn animated">
          <a class="btn btn-brand btn-lg text-white border-radius-5 btn-shadow-brand hover-up" href="preguntas-frecuentes.php">Preguntas Frecuentes</a>
          <a class="btn btn-outline btn-lg font-weight-bold ml-15 border-radius-5 btn-shadow-brand hover-up" href="#contacto-form">Ir al formulario</a>
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Motivos de contacto -->
<section class="pt-50 pb-20">
  <div class="container">
    <div class="text-center mb-30">
      <h2 class="mb-10">¿En qué podemos ayudarte?</h2>
      <p class="text-muted">Selecciona el motivo para una atención más rápida</p>
    </div>
    <div class="row">
      <div class="col-md-3 mb-30">
        <div class="card h-100 hover-up wow fadeIn animated">
          <div class="card-body text-center">
            <div class="display-6 mb-10"><i class="fi-rs-user"></i></div>
            <h5 class="mb-10">Consulta General</h5>
            <p class="text-muted mb-0">Dudas sobre servicios o funcionamiento de la plataforma.</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-30">
        <div class="card h-100 hover-up wow fadeIn animated">
          <div class="card-body text-center">
            <div class="display-6 mb-10"><i class="fi-rs-building"></i></div>
            <h5 class="mb-10">Alianza Comercial</h5>
            <p class="text-muted mb-0">Únete como comercio y publica tus ofertas.</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-30">
        <div class="card h-100 hover-up wow fadeIn animated">
          <div class="card-body text-center">
            <div class="display-6 mb-10"><i class="fi-rs-headphones"></i></div>
            <h5 class="mb-10">Soporte Técnico</h5>
            <p class="text-muted mb-0">Problemas con cupones, cuenta o navegación.</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-30">
        <div class="card h-100 hover-up wow fadeIn animated">
          <div class="card-body text-center">
            <div class="display-6 mb-10"><i class="fi-rs-comment"></i></div>
            <h5 class="mb-10">Sugerencias</h5>
            <p class="text-muted mb-0">Ideas para mejorar y feedback de tu experiencia.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Mapa + info rápida -->
<section class="section-border pt-40 pb-40">
  <div class="container">
    <div id="map-panama" class="leaflet-map mb-40" style="height:420px;border-radius:10px;overflow:hidden;"></div>
    <div class="row">
      <div class="col-md-4 mb-30">
        <div class="card h-100">
          <div class="card-body">
            <h4 class="mb-15 text-brand"><i class="fi-rs-marker mr-10"></i>Ubicación</h4>
            Ciudad de Panamá, Panamá<br>
            <abbr title="Horario"><strong>Horario:</strong></abbr> Lun–Vie 9:00–17:00<br>
            <abbr title="Zona horaria"><strong>Zona:</strong></abbr> GMT-5
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-30">
        <div class="card h-100">
          <div class="card-body">
            <h4 class="mb-15 text-brand"><i class="fi-rs-envelope mr-10"></i>Correos</h4>
            <div><strong>Soporte:</strong> soporte@panamaofertasymas.com</div>
            <div><strong>Comercios:</strong> comercios@panamaofertasymas.com</div>
            <div><strong>Info:</strong> info@panamaofertasymas.com</div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-30">
        <div class="card h-100">
          <div class="card-body">
            <h4 class="mb-15 text-brand"><i class="fi-rs-smartphone mr-10"></i>Teléfonos</h4>
            <div><strong>Principal:</strong> +507 XXXX-XXXX</div>
            <div><strong>WhatsApp:</strong> +507 XXXX-XXXX</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Formulario de contacto -->
<section class="pt-30 pb-60" id="contacto-form">
  <div class="container">
    <div class="row">
      <div class="col-xl-8 col-lg-10 m-auto">
        <div class="contact-from-area padding-20-row-col wow FadeInUp">
          <h3 class="mb-10 text-center">Envíanos un Mensaje</h3>
          <p class="text-muted mb-30 text-center font-sm">Responderemos lo antes posible.</p>

          <!-- Nota: ajusta action/method según tu backend -->
          <form class="contact-form-style text-center" id="contact-form" action="#" method="post" novalidate>
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <div class="input-style mb-20">
                  <input name="name" id="name" placeholder="Nombre completo *" type="text" required>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="input-style mb-20">
                  <input name="email" id="email" placeholder="Correo electrónico *" type="email" required>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="input-style mb-20">
                  <input name="telephone" id="telephone" placeholder="Teléfono" type="tel">
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="input-style mb-20">
                  <input name="subject" id="subject" placeholder="Asunto *" type="text" required>
                </div>
              </div>
              <div class="col-lg-12 col-md-12">
                <div class="textarea-style mb-30">
                  <textarea name="message" id="message" placeholder="Mensaje *" rows="6" required></textarea>
                </div>
                <button class="submit submit-auto-width" type="submit">
                  Enviar mensaje
                </button>
              </div>
            </div>
          </form>
          <p class="form-messege"></p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Comercios -->
<section class="newsletter p-30 text-white wow fadeIn animated" style="background:#2eb67d;">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8 mb-md-3 mb-lg-0">
        <h3 class="mb-5">¿Eres un comercio?</h3>
        <p class="mb-0">Únete a nuestra red y llega a miles de clientes potenciales en Panamá.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="panel-comercio.php" class="btn bg-dark text-white me-10 hover-up">Registrar mi negocio</a>
        <a href="mailto:comercios@panamaofertasymas.com" class="btn btn-outline text-white hover-up">Contactar equipo comercial</a>
      </div>
    </div>
  </div>
</section>

<!-- Inicialización del mapa (Leaflet ya está incluido por el template) -->
<!-- Mapa Panamá (cargador robusto con fallback a CDN) -->
<script>
(function () {
  var LAT = 8.9824, LNG = -79.5199;
  var TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

  function initMap() {
    var el = document.getElementById('map-panama');
    if (!el) return; // no hay contenedor, no hacemos nada
    try {
      var map = L.map(el, { scrollWheelZoom: false }).setView([LAT, LNG], 12);
      L.tileLayer(TILE_URL, {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      L.marker([LAT, LNG]).addTo(map)
       .bindPopup('<strong>PanamaOfertasYMas</strong><br>Ciudad de Panamá, Panamá')
       .openPopup();

      setTimeout(function(){ map.invalidateSize(); }, 300);
    } catch (e) {
      console.warn('Error iniciando Leaflet:', e);
    }
  }

  function ensureLeafletThen(cb) {
    if (window.L && typeof L.map === 'function') { cb(); return; }

    // Inyecta CSS si falta
    if (!document.querySelector('link[href*="leaflet.css"]')) {
      var link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      document.head.appendChild(link);
    }

    // Inyecta JS y ejecuta callback al cargar
    var script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    script.async = true;
    script.onload = cb;
    script.onerror = function(){ console.warn('No se pudo cargar Leaflet desde CDN'); };
    document.head.appendChild(script);
  }

  document.addEventListener('DOMContentLoaded', function () {
    ensureLeafletThen(initMap);
  });
})();
</script>



    </main>
<!-- Incluir el footer -->
<?php include 'footer.php'; ?>

<!-- Incluir el foot con los scripts -->
<?php include 'foot.php'; ?>
</body>
</html>