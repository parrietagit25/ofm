<?php
// Incluir el head de la página
include 'head.php';
?>

<body>
    <!-- Incluir el header -->
    <?php include 'header.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">

    <!-- CÓMO FUNCIONA — SOLO CONTENIDO -->
<section class="mt-50">
  <div class="container">
    <!-- Hero -->
    <div class="text-center mb-40">
      <h1>¿Cómo <span class="text-brand">Funciona?</span></h1>
      <p class="text-muted">Descubre lo fácil que es ahorrar y hacer crecer tu negocio con PanamaOfertasYMas</p>
      <div class="d-inline-flex gap-2 mt-10">
        <span class="badge bg-primary rounded-pill px-3 py-2">Para Consumidores</span>
        <span class="badge bg-light text-dark rounded-pill px-3 py-2 border">Para Comercios</span>
      </div>
    </div>

    <!-- PARA CONSUMIDORES -->
    <div class="text-center mb-20">
      <h2>Para Consumidores</h2>
      <p class="text-muted">¡Ahorra y disfruta en 3 sencillos pasos!</p>
    </div>

    <div class="row g-3 mb-30">
      <!-- Paso 1 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center mb-10">
              <span class="badge bg-brand rounded-circle me-2 d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">1</span>
              <h5 class="mb-0">Explora y Encuentra</h5>
            </div>
            <p class="text-muted mb-10">Navega por categorías o usa el buscador para hallar las mejores ofertas.</p>
            <ul class="mb-0">
              <li>Categorías por tipo de negocio</li>
              <li>Buscador por ubicación y precio</li>
              <li>Ofertas destacadas y populares</li>
              <li>Filtros para afinar búsqueda</li>
            </ul>
          </div>
        </div>
      </div>
      <!-- Paso 2 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center mb-10">
              <span class="badge bg-brand rounded-circle me-2 d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">2</span>
              <h5 class="mb-0">Compra tu Cupón</h5>
            </div>
            <p class="text-muted mb-10">Adquiere tu cupón de forma segura y rápida.</p>
            <ul class="mb-0">
              <li>Checkout seguro</li>
              <li>Múltiples métodos de pago</li>
              <li>Confirmación inmediata por email</li>
              <li>Cupones en tu cuenta</li>
            </ul>
          </div>
        </div>
      </div>
      <!-- Paso 3 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center mb-10">
              <span class="badge bg-brand rounded-circle me-2 d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">3</span>
              <h5 class="mb-0">Disfruta y Ahorra</h5>
            </div>
            <p class="text-muted mb-10">Presenta tu cupón en el comercio y vive la experiencia.</p>
            <ul class="mb-0">
              <li>Cupón impreso o digital</li>
              <li>Respeta condiciones y vigencia</li>
              <li>Descuento garantizado</li>
              <li>Experiencia de calidad</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mb-60">
      <a href="ofertas.php" class="btn btn-secondary btn-lg">Explorar Ofertas Ahora</a>
    </div>

    <!-- PARA COMERCIOS -->
    <div class="text-center mb-20">
      <h2>Para Comercios</h2>
      <p class="text-muted">¡Conecta, crece y vende más!</p>
    </div>

    <div class="row g-3 mb-30">
      <!-- Paso 1 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center mb-10">
              <span class="badge bg-primary rounded-circle me-2 d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">1</span>
              <h5 class="mb-0">Regístrate y Crea tu Perfil</h5>
            </div>
            <p class="text-muted mb-10">Únete a nuestra red y configura tu espacio.</p>
            <ul class="mb-0">
              <li>Registro simple y verificado</li>
              <li>Perfil completo del negocio</li>
              <li>Fotos y descripción</li>
              <li>Listo para publicar</li>
            </ul>
          </div>
        </div>
      </div>
      <!-- Paso 2 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center mb-10">
              <span class="badge bg-primary rounded-circle me-2 d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">2</span>
              <h5 class="mb-0">Publica tus Ofertas</h5>
            </div>
            <p class="text-muted mb-10">Diseña promociones atractivas en tu panel.</p>
            <ul class="mb-0">
              <li>Panel intuitivo</li>
              <li>Herramientas para ofertas</li>
              <li>Imágenes de alta calidad</li>
              <li>Condiciones y restricciones</li>
            </ul>
          </div>
        </div>
      </div>
      <!-- Paso 3 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center mb-10">
              <span class="badge bg-primary rounded-circle me-2 d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">3</span>
              <h5 class="mb-0">Conecta con Nuevos Clientes</h5>
            </div>
            <p class="text-muted mb-10">Aumenta alcance y ventas con apoyo de marketing.</p>
            <ul class="mb-0">
              <li>Visibilidad ante miles de usuarios</li>
              <li>Impulso en redes y email</li>
              <li>Métricas y estadísticas</li>
              <li>Soporte continuo</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mb-60">
      <a href="contacto.php" class="btn btn-primary btn-lg">Registra tu Negocio</a>
    </div>

    <!-- BENEFICIOS -->
    <div class="text-center mb-20">
      <h2>Beneficios para tu Negocio</h2>
      <p class="text-muted">Descubre por qué más comercios confían en nosotros</p>
    </div>

    <div class="row g-3">
      <div class="col-md-3">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="mb-5">Aumenta tus Ventas</h5>
            <p class="text-muted mb-0">Atrae nuevos clientes y convierte visitas en recurrencia.</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="mb-5">Marketing a Bajo Costo</h5>
            <p class="text-muted mb-0">Herramienta de alto impacto con inversión mínima.</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="mb-5">Mejora tu Reputación</h5>
            <p class="text-muted mb-0">Gana visibilidad y construye confianza en el mercado.</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="mb-5">Pagos Seguros</h5>
            <p class="text-muted mb-0">Proceso confiable y liquidaciones claras y puntuales.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA FINAL -->
    <div class="bg-light rounded-3 p-4 p-md-5 mt-50 text-center">
      <h2 class="mb-10">¿Listo para Comenzar?</h2>
      <p class="text-muted mb-20">Únete a la plataforma que está transformando la forma de ahorrar y vender en Panamá</p>
      <div class="d-flex justify-content-center gap-2">
        <a href="ofertas.php" class="btn btn-secondary btn-lg">Explorar Ofertas</a>
        <a href="contacto.php" class="btn btn-outline-primary btn-lg">Registrar Comercio</a>
      </div>
    </div>
  </div>
</section>


    </main>


<!-- Incluir el footer -->
<?php include 'footer.php'; ?>

<!-- Incluir el foot con los scripts -->
<?php include 'foot.php'; ?>
</body>
</html>
