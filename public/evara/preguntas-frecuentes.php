<?php
// Incluir el head de la página
include 'head.php';
?>

<body>
    <!-- Incluir el header -->
    <?php include 'header.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        
<!-- FAQ: SOLO CONTENIDO, SIN HEAD/HEADER/FOOTER -->
<section class="mt-50">
  <div class="container">
    <!-- Hero -->
    <div class="text-center mb-40">
      <h1 class="mb-10">Preguntas <span class="text-brand">Frecuentes</span></h1>
      <p class="text-muted">Encuentra respuestas a las dudas más comunes sobre PanamaOfertasYMas</p>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills justify-content-center gap-2 mb-30" id="faqTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill px-4" id="consumidores-tab" data-bs-toggle="pill"
                data-bs-target="#consumidores" type="button" role="tab" aria-controls="consumidores" aria-selected="true">
          Para Consumidores <span class="badge bg-primary ms-2">9</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4" id="comercios-tab" data-bs-toggle="pill"
                data-bs-target="#comercios" type="button" role="tab" aria-controls="comercios" aria-selected="false">
          Para Comercios <span class="badge bg-primary ms-2">8</span>
        </button>
      </li>
    </ul>

    <div class="tab-content" id="faqTabsContent">
      <!-- Consumidores -->
      <div class="tab-pane fade show active" id="consumidores" role="tabpanel" aria-labelledby="consumidores-tab">
        <div class="text-center mb-25">
          <h2 class="mb-5">Preguntas Frecuentes para Consumidores</h2>
          <p class="text-muted">Todo lo que necesitas saber para empezar a ahorrar</p>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-10">
            <div class="accordion" id="accordionConsumidores">
              <!-- 1 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="c1">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#c1c" aria-expanded="true" aria-controls="c1c">
                    ¿Qué es PanamaOfertasYMas?
                  </button>
                </h2>
                <div id="c1c" class="accordion-collapse collapse show" aria-labelledby="c1" data-bs-parent="#accordionConsumidores">
                  <div class="accordion-body">
                    PanamaOfertasYMas es una plataforma digital 100% panameña que conecta a los consumidores con las mejores ofertas y descuentos exclusivos de comercios locales en Panamá. Nuestro objetivo es ayudarte a ahorrar mientras disfrutas de experiencias y productos de calidad.
                  </div>
                </div>
              </div>
              <!-- 2 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="c2">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c2c" aria-expanded="false" aria-controls="c2c">
                    ¿Cómo puedo encontrar ofertas en la plataforma?
                  </button>
                </h2>
                <div id="c2c" class="accordion-collapse collapse" aria-labelledby="c2" data-bs-parent="#accordionConsumidores">
                  <div class="accordion-body">
                    Puedes encontrar ofertas navegando por nuestras categorías (Restaurantes, Belleza, Actividades, Servicios, Productos, etc.), usando la barra de búsqueda o explorando las secciones de ofertas destacadas y populares.
                  </div>
                </div>
              </div>
              <!-- 3 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="c3">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c3c" aria-expanded="false" aria-controls="c3c">
                    ¿Cómo compro un cupón?
                  </button>
                </h2>
                <div id="c3c" class="accordion-collapse collapse" aria-labelledby="c3" data-bs-parent="#accordionConsumidores">
                  <div class="accordion-body">
                    Entra a la oferta que te interesa, agrégala al carrito y completa el pago de forma segura. Una vez confirmado, recibirás tu cupón digital por correo y también quedará disponible en tu cuenta.
                  </div>
                </div>
              </div>
              <!-- 4 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="c4">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c4c" aria-expanded="false" aria-controls="c4c">
                    ¿Qué métodos de pago aceptan?
                  </button>
                </h2>
                <div id="c4c" class="accordion-collapse collapse" aria-labelledby="c4" data-bs-parent="#accordionConsumidores">
                  <div class="accordion-body">
                    Aceptamos tarjetas de crédito y débito (Visa, MasterCard) y métodos de pago locales que verás durante el checkout.
                  </div>
                </div>
              </div>
              <!-- 5 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="c5">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c5c" aria-expanded="false" aria-controls="c5c">
                    ¿Cómo redimo mi cupón?
                  </button>
                </h2>
                <div id="c5c" class="accordion-collapse collapse" aria-labelledby="c5" data-bs-parent="#accordionConsumidores">
                  <div class="accordion-body">
                    Presenta tu cupón (impreso o en tu móvil) en el comercio antes de solicitar el servicio o producto. Revisa siempre las condiciones de uso (horarios, días, restricciones).
                  </div>
                </div>
              </div>
              <!-- 6 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="c6">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c6c" aria-expanded="false" aria-controls="c6c">
                    ¿Qué pasa si mi cupón expira?
                  </button>
                </h2>
                <div id="c6c" class="accordion-collapse collapse" aria-labelledby="c6" data-bs-parent="#accordionConsumidores">
                  <div class="accordion-body">
                    Cada cupón indica su fecha de vencimiento. Una vez vencido no puede redimirse ni reembolsarse. Te recomendamos usarlo antes de su expiración.
                  </div>
                </div>
              </div>
              <!-- 7 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="c7">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c7c" aria-expanded="false" aria-controls="c7c">
                    ¿Puedo devolver o cambiar un cupón?
                  </button>
                </h2>
                <div id="c7c" class="accordion-collapse collapse" aria-labelledby="c7" data-bs-parent="#accordionConsumidores">
                  <div class="accordion-body">
                    Por lo general, las ventas de cupones son finales. Revisa la Política de Cupones/Reembolsos y las condiciones de cada oferta para conocer excepciones.
                  </div>
                </div>
              </div>
              <!-- 8 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="c8">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c8c" aria-expanded="false" aria-controls="c8c">
                    ¿Es seguro comprar en PanamaOfertasYMas?
                  </button>
                </h2>
                <div id="c8c" class="accordion-collapse collapse" aria-labelledby="c8" data-bs-parent="#accordionConsumidores">
                  <div class="accordion-body">
                    Sí. Usamos cifrado y buenas prácticas de seguridad para proteger tu información personal y financiera.
                  </div>
                </div>
              </div>
              <!-- 9 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="c9">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c9c" aria-expanded="false" aria-controls="c9c">
                    ¿Cómo contacto al servicio al cliente?
                  </button>
                </h2>
                <div id="c9c" class="accordion-collapse collapse" aria-labelledby="c9" data-bs-parent="#accordionConsumidores">
                  <div class="accordion-body">
                    Escríbenos desde la sección de Contacto, al correo <strong>soporte@panamaofertasymas.com</strong> o llámanos en horario de atención.
                  </div>
                </div>
              </div>
            </div>

            <!-- Controles -->
            <div class="d-flex justify-content-center gap-2 mt-20">
              <button class="btn btn-outline-primary" data-faq-toggle="expand" data-target="#accordionConsumidores">Expandir Todas</button>
              <button class="btn btn-outline-primary" data-faq-toggle="collapse" data-target="#accordionConsumidores">Contraer Todas</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Comercios -->
      <div class="tab-pane fade" id="comercios" role="tabpanel" aria-labelledby="comercios-tab">
        <div class="text-center mb-25">
          <h2 class="mb-5">Preguntas Frecuentes para Comercios</h2>
          <p class="text-muted">Información esencial para hacer crecer tu negocio</p>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-10">
            <div class="accordion" id="accordionComercios">
              <!-- 1 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="b1">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#b1c" aria-expanded="true" aria-controls="b1c">
                    ¿Por qué debería mi negocio unirse a PanamaOfertasYMas?
                  </button>
                </h2>
                <div id="b1c" class="accordion-collapse collapse show" aria-labelledby="b1" data-bs-parent="#accordionComercios">
                  <div class="accordion-body">
                    Aumentarás visibilidad, atraerás nuevos clientes, dinamizarás ventas y tendrás una herramienta de marketing efectiva a bajo costo, enfocada en una audiencia panameña activa.
                  </div>
                </div>
              </div>
              <!-- 2 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="b2">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#b2c" aria-expanded="false" aria-controls="b2c">
                    ¿Cómo registro mi negocio en la plataforma?
                  </button>
                </h2>
                <div id="b2c" class="accordion-collapse collapse" aria-labelledby="b2" data-bs-parent="#accordionComercios">
                  <div class="accordion-body">
                    Inicia en “Registra tu Negocio” o escríbenos a <strong>comercios@panamaofertasymas.com</strong>. Revisamos tu solicitud y te guiamos en los siguientes pasos.
                  </div>
                </div>
              </div>
              <!-- 3 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="b3">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#b3c" aria-expanded="false" aria-controls="b3c">
                    ¿Qué tipo de ofertas puedo publicar?
                  </button>
                </h2>
                <div id="b3c" class="accordion-collapse collapse" aria-labelledby="b3" data-bs-parent="#accordionComercios">
                  <div class="accordion-body">
                    Descuentos en productos/servicios, paquetes, promociones especiales o experiencias. Te apoyamos a diseñarlas para que sean atractivas y efectivas.
                  </div>
                </div>
              </div>
              <!-- 4 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="b4">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#b4c" aria-expanded="false" aria-controls="b4c">
                    ¿Cómo se gestionan las ofertas y los cupones?
                  </button>
                </h2>
                <div id="b4c" class="accordion-collapse collapse" aria-labelledby="b4" data-bs-parent="#accordionComercios">
                  <div class="accordion-body">
                    Tendrás un panel para crear, editar y administrar tus ofertas, y validar cupones de forma simple y rápida.
                  </div>
                </div>
              </div>
              <!-- 5 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="b5">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#b5c" aria-expanded="false" aria-controls="b5c">
                    ¿Hay costos por unirse o publicar?
                  </button>
                </h2>
                <div id="b5c" class="accordion-collapse collapse" aria-labelledby="b5" data-bs-parent="#accordionComercios">
                  <div class="accordion-body">
                    Ofrecemos diferentes planes y modelos. Contáctanos para detalles de tarifas y beneficios. Buscamos que sea una inversión rentable para tu negocio.
                  </div>
                </div>
              </div>
              <!-- 6 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="b6">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#b6c" aria-expanded="false" aria-controls="b6c">
                    ¿Cómo promocionan mi negocio?
                  </button>
                </h2>
                <div id="b6c" class="accordion-collapse collapse" aria-labelledby="b6" data-bs-parent="#accordionComercios">
                  <div class="accordion-body">
                    Además de la visibilidad en la plataforma, impulsamos tus ofertas en redes sociales, email marketing y otras acciones digitales.
                  </div>
                </div>
              </div>
              <!-- 7 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="b7">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#b7c" aria-expanded="false" aria-controls="b7c">
                    ¿Recibiré soporte si tengo dudas?
                  </button>
                </h2>
                <div id="b7c" class="accordion-collapse collapse" aria-labelledby="b7" data-bs-parent="#accordionComercios">
                  <div class="accordion-body">
                    Sí. Nuestro equipo de soporte para comercios te atiende por correo (<strong>comercios@panamaofertasymas.com</strong>) o teléfono.
                  </div>
                </div>
              </div>
              <!-- 8 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="b8">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#b8c" aria-expanded="false" aria-controls="b8c">
                    ¿Cómo se manejan los pagos a los comercios?
                  </button>
                </h2>
                <div id="b8c" class="accordion-collapse collapse" aria-labelledby="b8" data-bs-parent="#accordionComercios">
                  <div class="accordion-body">
                    Los ciclos de pago y liquidación se detallan en el acuerdo comercial. Mantenemos un proceso claro y eficiente de gestión de ingresos.
                  </div>
                </div>
              </div>
            </div>

            <!-- Controles -->
            <div class="d-flex justify-content-center gap-2 mt-20">
              <button class="btn btn-outline-primary" data-faq-toggle="expand" data-target="#accordionComercios">Expandir Todas</button>
              <button class="btn btn-outline-primary" data-faq-toggle="collapse" data-target="#accordionComercios">Contraer Todas</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ¿Aún tienes preguntas? -->
    <div class="mt-60 mb-20 text-center">
      <h2 class="mb-10">¿Aún tienes preguntas?</h2>
      <p class="text-muted mb-20">Si no encontraste la respuesta que buscabas, nuestro equipo de soporte está listo para ayudarte</p>
      <div class="d-flex justify-content-center gap-2">
        <a href="contacto.php" class="btn btn-primary">Contactar Soporte</a>
        <a href="mailto:soporte@panamaofertasymas.com" class="btn btn-outline-primary">Enviar Email</a>
      </div>
    </div>

    <!-- Enlaces útiles -->
    <div class="mt-40">
      <div class="text-center mb-25">
        <h2>Enlaces Útiles</h2>
      </div>
      <div class="row g-3 justify-content-center">
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Términos y Condiciones</h5>
              <p class="text-muted">Conoce los términos de uso de nuestra plataforma</p>
              <a href="terminos-y-condiciones.php" class="btn btn-outline-primary btn-sm">Leer Más</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Política de Privacidad</h5>
              <p class="text-muted">Información sobre cómo protegemos tus datos</p>
              <a href="politica-de-privacidad.php" class="btn btn-outline-primary btn-sm">Leer Más</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Política de Cupones</h5>
              <p class="text-muted">Detalles sobre cupones y reembolsos</p>
              <a href="politica-de-cupones.php" class="btn btn-outline-primary btn-sm">Leer Más</a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- JS mínimo para Expandir/Contraer todas usando Bootstrap 5 -->
<script>
  (function () {
    function setAll(targetSel, action) {
      document.querySelectorAll(targetSel + ' .accordion-collapse').forEach(function(el){
        var c = new bootstrap.Collapse(el, { toggle: false });
        action === 'show' ? c.show() : c.hide();
      });
    }
    document.querySelectorAll('[data-faq-toggle]').forEach(function(btn){
      btn.addEventListener('click', function(){
        var target = btn.getAttribute('data-target');
        var mode = btn.getAttribute('data-faq-toggle');
        setAll(target, mode === 'expand' ? 'show' : 'hide');
      });
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
