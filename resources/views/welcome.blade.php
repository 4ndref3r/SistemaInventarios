{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promaq</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff4d4d;
        }

        body {
            background: linear-gradient(135deg, #1a1b2f 0%, #2d1b30 100%);
        }
        ::-webkit-scrollbar {
            width: 12px; /* Ancho de la barra de scroll */
            background: rgba(26, 27, 47, 0.95); /* Color de fondo de la barra */
        }

        /* Estilo para la "track" (la pista de fondo del scroll) */
        ::-webkit-scrollbar-track {
            background: rgba(26, 27, 47, 0.95); /* Mismo color de fondo de tu diseño */
            border-radius: 10px; /* Opcional: redondea los bordes */
        }

        /* Estilo para el "thumb" (la parte que se arrastra) */
        ::-webkit-scrollbar-thumb {
            background: #FF5733; /* Color rojo del "thumb" */
            border-radius: 10px; /* Opcional: redondea los bordes */
        }

        /* Estilo cuando el "thumb" está en hover (pasar el mouse por encima) */
        ::-webkit-scrollbar-thumb:hover {
            background: #FF3D00; /* Color más oscuro o vivo al hacer hover */
        }

        /* Navegación */
        .nav-top {
            background: rgba(26, 27, 47, 0.95);
            border-radius: 0 0 50px 50px;
            padding: 0rem 1rem;
            position: relative;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom:2px solid #ff3333;
            transition: all 0.3s ease;
        }

        #navButtom {
            background: rgba(26, 27, 47, 0.99);
            border-radius: 50px;
            padding: 0rem 2rem;
            position: fixed;
            width: 90%;
            bottom: 2.5rem;
            left: 50%;
            border-bottom: 2px solid #ff3333;
            z-index: 10000;
            display:none;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .product-section {
            background: #1a1b2f;
            min-height: 100vh;
            padding: 120px 0;
            position: relative;
            z-index: 2;
        }

        .about-section {
            background: #1a1b2f;
            min-height: 100vh;
            padding: 120px 0;
            position: relative;
            z-index: 2;
        }

        .experence-section {
          min-height: 100vh;
          position: relative;
          background: #1a1b2f;
          z-index: 2;
          padding: 40px 0;
        }

        .testimonial-section{
          min-height: 100vh;
          background: #1a1b2f;
          z-index: 2;
          padding: 40px 0;
        }

        .about-title {
            color: white;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }

        .about-subtitle {
            color: var(--primary-color);
            font-size: 1.2rem;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .about-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .feature-icon {
            color: var(--primary-color);
            font-size: 2rem;
        }

        .feature-text {
            color: white;
            font-size: 1.2rem;
            margin: 0;
        }

        .company-cta {
            background: linear-gradient(to right, #ff4d4d, #ff758c);
            border-radius: 30px;
            padding: 1rem 2rem;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
            transition: all 0.3s ease;
        }

        .company-cta:hover {
            transform: translateY(-2px);
            color: white;
        }

        .contact-phone {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
            margin-top: 2rem;
        }

        .contact-phone i {
            background: var(--primary-color);
            padding: 1rem;
            border-radius: 50%;
        }


        .navbar-brand {
            font-size: 2rem;
            font-weight: bold;
            color: white;
        }

        .nav-link {
            color: white !important;
            font-size: 1.1rem;
            margin: 0 0.5rem;
        }

        /* Contenido Principal */
        .main-content {
            padding-top: 80px;
            position: relative;
            overflow: hidden;
        }

        .outline-text {
            font-size: 11rem;
            font-weight: 800;
            color: transparent;
            -webkit-text-stroke: 2px var(--primary-color);
            line-height: 1;
            position: relative;
            z-index: 2;
        }

        .creative-text {
            font-size: 7rem;
            font-weight: 800;
            color: white;
            line-height: 1;
            margin-top: -20px;
            position: relative;
            z-index: 2;
        }

        .promaq-text {
            font-size: 17.5rem;
            font-weight: 800;
            color: rgb(226, 96, 96);
            line-height: 1;
            margin-top: -20px;
            padding-top: 5%;
            position: absolute;
            z-index: 2;
        }

        .hero-description {
            color: white;
            font-size: 1.2rem;
            max-width: 300px;
            margin: 2rem 0;
            position: absolute;
            top: 15%;
            right: 1rem;
            text-align: right;
            z-index: 2;
        }

        .get-started-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border-radius: 30px;
            text-decoration: none;
            font-size: 1.2rem;
            display: inline-block;
            transition: all 0.3s;
            position: absolute;
            right: 1rem;
            top: 29%;
            z-index: 2;
        }

        .get-started-btn:hover {
            background-color: #ff3333;
            color: white;
            transform: translateY(-2px);
        }

        /* Imagen Hero */
        .hero-image {
            position: relative;
            height: 100vh;
            z-index: 9999;
        }

        .hero-image img {
            position: absolute;
            left: 50%;
            transform: translate(-40%, -40%);
            width: auto;
            height: 100%;
            object-fit: cover;
        }

        /* Iconos sociales */
        .social-icons {
            display: flex;
            gap: 1rem;
        }

        .social-icons a {
            color: white;
            font-size: 1.2rem;
            text-decoration: none;
        }

        /* Cart icon */
        .cart-icon {
            position: relative;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .gradient-background {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-30%,-70%);
          width: 50%;
          height: 50%;
          border-radius: 50%;
          background: radial-gradient(circle, #ff3333 2%, #fc6969 3%, #231B30 40%);
          box-shadow: 0 0 50px #1a1b2f;
        }

        @media (max-width: 992px) {
            .outline-text, .creative-text {
                font-size: 4rem;
            }

            .hero-image {
                position: relative;
                width: 100%;
                right: 0;
                margin-top: 5rem;
                transform: none;
            }

            .main-content {
                padding-top: 100px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        .product-card {
          width: 450px;
          height: 500px;
          margin: 20px;
          border-radius: 15px;
          overflow: hidden;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          transition: transform 0.3s ease, box-shadow 0.3s ease;
          position: relative;
          flex-direction: column;
        }

        .product-img {
          width: 100%;
          height: 100%; /* Ajusta el alto según lo necesites */
          object-fit: cover; /* Asegura que la imagen cubra completamente sin distorsionarse */
          border-radius: 15px;
          transition: transform 0.3s ease;
        }

        .product-info {
          padding: 20px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          position: absolute;
          bottom: 0;
          left: 0;
          right: 0;
        }

        .product-title {
          color: #ffffff;
          font-size: 2rem;
          font-weight: bold;
          margin: 0;
        }

        .carrusel-container {
          position: relative;
          width: 100%;
          height: 500px; /* La altura del carrusel será de 500px */
          overflow: hidden; /* Oculta el scroll horizontal */
        }

        .carrusel {
          display: flex;
          width: max-content;
          height: 100%;
          animation: moveCarousel 10s linear infinite; /* Movimiento lento y continuo */
        }

        @keyframes moveCarousel {
        0% {
          transform: translateX(0); /* Comienza en la posición inicial */
        }
        100% {
          transform: translateX(calc(-200px * 5));
        }
      }

        .carrusel-container:hover .carrusel {
          animation-play-state: paused; /* Detiene la animación cuando se hace hover */
        }

      .btn-product{
        width: 60px;
        height: 60px;
        display:inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #F65248;
        border-radius: 50%;
        color: #ffffff;
        font-size: 30px;
        font-weight: bold;
      }

      .experence-container{
        text-align: center;
        max-width: 500px;
      }

      #experience-text{
        margin-top: 1.5rem;
        color: #ffffff;
        font-weight: bold;
        font-size: 35px;
        font-family: Verdana;
      }

      #experence-box{
        position: relative;
        background: #1a1b2f;
        width: 100%;
        height: 480px;
        overflow: hidden;
        border-radius: 80px;
        border-bottom: 2px solid #ff3333;
      }

      .word-bubble{
        position: absolute;
        padding: 5px 90px;
        color: white;
        border-radius: 20px;
        border: 1px solid #ffffff;
        font-weight: bold;
        cursor:grab;
        user-select: none;
        white-space: nowrap;
        z-index: 1;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      }

      .word-bubble:active {
        cursor: grabbing;
      }

      @keyframes bounce {
        0% {
          transform: translateY(0);
        }
        50% {
          transform: translateY(-30px);
        }
        100% {
          transform: translateY(0);
        }
      }

      #testimonial-bg{
        text-align: center;
        color: rgba(53, 54, 75, 0.4);
        font-weight: bold;
        font-family: fantasy;
        font-size: 145px;
      }

      #testimonial-separador{
        position: relative;
        text-align: center;
        transform: translateY(-200%);
        font-size: 20px;
        color: #ff3333;
        hyphens: none;
        z-index: 3;
      }

      #clientes{
        color: #ffffff;
        font-weight: bold;
        font-family:Georgia;
        font-size: 50px;
        text-align: center;
      }
        
        .card-members {
          height: 500px;
          width: 400px;
          background: rgba(255, 255, 255, 0.05);
          border: 1px solid white;
          border-radius: 10px;
          margin: 10px;
          padding: 2rem;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: flex-start;
          text-align: center;
          transition: transform 0.3s ease;
          position: relative;
          overflow: hidden;
        }

        .name-member {
          color: #ffffff;
          font-family: Gerogia;
          font-weight: bold;
          margin-bottom: 15px;
        }

        .pos-member {
          position: relative;
          color:#ff3333;
          font-family: Georgia;
          margin: 0;
        }

        .member-image {
          background: rgba(255, 255, 255, 0);;
          border-radius: 50%;
          width: 300px;
          height: 300px;
          display: flex;
          justify-content: center;
          align-items: center;
          margin-bottom: 50px;
          transition: all 0.3s ease;
        }
        
        .member-image img{
          width: 100%;
          object-fit: fill;
          border-radius: 50%;
        }

        .card-members:hover .member-image {
          background: rgba(26, 27, 47, 0.3);
          height: 200px;
          width: 200px;
        }
        
        .card-members:hover .pos-member {
          color: black;
        }

        .card-members:hover {
          background: linear-gradient(135deg, #FF4E50 0%, #F9445B 65%, #F73B6C 100%);
        }

        .redes {
          color: #ffffff;
          border-radius: 50%;
          border: 1px solid white;
          padding: 15px;
          font-size: 30px;
          margin-right: 8px;
        }
        
        .redes:hover {
          background: white;
          color:#F73B6C;
        }

        .faq-section{
          background-color: #0a0f2c;
          color: #ffffff;
          position: relative;
        }

        .faq-image-wrapper {
          position: relative;
          padding: 20px;
        }

        .curved-border {
          position: absolute;
          position: absolute;
          width: calc(100% - 30px);
          height: calc(100% - 30px);
          border: 2px solid #264de4;
          border-radius: 35% 65% 60% 40% / 45% 45% 55% 55%;
          top: 0;
          left: 0;
          z-index: 0;
        }

        .image-container {
          position: relative;
          z-index: 1;
          border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
          overflow: hidden;
        }

        .image-container img {
          width: 500px;
          height: 500px;
          object-fit: cover;
        }

        .decoration-star-1 {
            position: absolute;
            top: 10%;
            right: 15%;
            color: #ff6b6b;
            font-size: 24px;
            z-index: 2;
        }

        .decoration-star-2 {
            position: absolute;
            bottom: 15%;
            left: 10%;
            color: #264de4;
            font-size: 20px;
            z-index: 2;
        }

        .decoration-x {
            position: absolute;
            top: 20%;
            left: 5%;
            color: #4a90e2;
            font-size: 18px;
            z-index: 2;
        }

        .accordion-button {
          color: #ffffff;
          font-weight: 600;
        }

        .custom-accordion .accordion-button {
            background-color: #151937;  /* Dark background */
            color: #ffffff;  /* White text */
            font-weight: 500;
            padding: 1rem 1.25rem;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .custom-accordion .accordion-button:not(.collapsed) {
            background-color: #151937; 
            color: #ffffff;
        }

        .custom-accordion .accordion-body {
            background: linear-gradient(135deg, #FF4E50 0%, #F9445B 65%, #F73B6C 100%); /* Dark background */
            color: #ffffff;  /* White text */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-top: none;
        }

        .custom-accordion .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(175, 5, 90, 0.5);
        }

        /* Custom plus/minus icons */
        .custom-accordion .accordion-button::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23ffffff' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z'/%3E%3C/svg%3E");
        }

        .custom-accordion .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23ffffff' viewBox='0 0 16 16'%3E%3Cpath d='M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z'/%3E%3C/svg%3E");
        }

    </style>
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg nav-top" id="navTop">
        <div class="container">
            <a class="navbar-brand" href="#"><img src="{{asset('images/logo_white.png')}}" style="height: 80px;"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#heroSection">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#productSection">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#experenceSection">Experiencias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cotizationSection">Cotiza</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#memberSection">Sobre Nosotros</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="social-icons me-3">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                    <div class="cart-icon">
                        <a href="admin" class="text-white">
                            <i class="fas fa-shield"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <nav class="navbar navbar-expand-lg nav-bottom" id="navButtom">
      <div class="container">
          <a class="navbar-brand" href="#"><img src="{{asset('images/logo_white.png')}}" style="height: 80px;"></a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav mx-auto">
                  <li class="nav-item">
                      <a class="nav-link" href="#heroSection">Inicio</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="#productSection">Catálogo</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="#testimonialSection">Experiencias</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="#cotizationSection">Cotiza</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="#memberSection">Sobre Nosotros</a>
                  </li>
              </ul>
              <div class="d-flex align-items-center">
                  <div class="social-icons me-3">
                      <a href="#"><i class="fab fa-instagram"></i></a>
                      <a href="#"><i class="fab fa-linkedin"></i></a>
                      <a href="#"><i class="fab fa-facebook"></i></a>
                      <a href="#"><i class="fab fa-youtube"></i></a>
                  </div>
                  <div class="cart-icon">
                      <a href="admin" class="text-white">
                          <i class="fas fa-shield"></i>
                      </a>
                  </div>
              </div>
          </div>
      </div>
    </nav>

    <!-- Contenido Principal -->
    <section class="hero-section" id="heroSection" style="height: 100vh;">
      <div class="container main-content">
          <div class="row">
              <div class="col-lg-8">
                  <h1 class="outline-text">SOMOS</h1>
                  <h2 class="creative-text">CREATIVOS</h2>
                  <h2 class="promaq-text">PROMAQ</h2>
                  <p class="hero-description">
                      Empresa metalmecánica dedicada a la fabricación y mantenimiento de maquinaría industrial
                  </p>
                  <a href="#cotizationSection" class="get-started-btn">
                      <strong>COTIZAR AHORA</strong> 
                      <i class="fas fa-arrow-right ms-2"></i>
                  </a>
              </div>
          </div>
          <div class="gradient-background"></div>
          <div class="hero-image">
              <img src="{{asset('images/principal.png')}}" alt="Creative Professional">
          </div>
      </div>
    </section>

    <section class="product-section" id="productSection" style="height: 100vh;">
      <div class="container">
            <div class="row">
              <div class="col-lg-6">
                  <h3 class="about-subtitle">Catalogo</h3>
                  <h2 class="about-title">Nuestros Productos y Servicios</h2>
                  <a href="#" class="get-started-btn">
                    <strong>COTIZAR AHORA</strong> 
                    <i class="fas fa-arrow-right ms-2"></i>
                  </a>
              </div>
            </div>
          </div>
      </div>
      <div class="container-fluid p-0">
        <div class="carrusel-container">
          <div class="carrusel">
            <div class="product-card">
              <img src="{{asset('images/caldera.png')}}" alt="Producto 1" class="product-img">
              <div class="product-info">
                <h4 class="product-title">Calderas</h4>
                <a href="#" class="btn-product"><i class="fas fa-arrow-right ms-2"></i></a>
              </div>
            </div>
            <div class="product-card">
              <img src="{{asset('images/caldera2.png')}}" alt="Producto 1" class="product-img">
              <div class="product-info">
                <h4 class="product-title">Automatización</h4>
                <a href="#" class="btn-product"><i class="fas fa-arrow-right ms-2"></i></a>
              </div>
            </div>
            <div class="product-card">
              <img src="{{asset('images/basureros.jpg')}}" alt="Producto 1" class="product-img">
              <div class="product-info">
                <h4 class="product-title">Basureros</h4>
                <a href="#" class="btn-product"><i class="fas fa-arrow-right ms-2"></i></a>
              </div>
            </div>
            <div class="product-card">
              <img src="{{asset('images/caldera.png')}}" alt="Producto 1" class="product-img">
              <div class="product-info">
                <h4 class="product-title">Calderas</h4>
                <a href="#" class="btn-product"><i class="fas fa-arrow-right ms-2"></i></a>
              </div>
            </div>
            <div class="product-card">
              <img src="{{asset('images/caldera2.png')}}" alt="Producto 1" class="product-img">
              <div class="product-info">
                <h4 class="product-title">Automatización</h4>
                <a href="#" class="btn-product"><i class="fas fa-arrow-right ms-2"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="contariner">.</div>
    </section>

    <section class="experence-section" id="experenceSection">
      <div class="container experence-container">
        <img src="{{asset('images/smile.png')}}" alt="Sonrisa">
        <h2 id="experience-text">Deshaste de lo manual, es hora de decir adiós</h2>
      </div>
      <div class="container-fluid" id="experence-box">
        <div class="word-bubble" draggable="true">Tareas manuales</div>
        <div class="word-bubble" draggable="true">Horas Extras</div>
        <div class="word-bubble" draggable="true">Seguridad del personal</div>
        <div class="word-bubble" draggable="true">Automatización procesos</div>
        <div class="word-bubble" draggable="true">Procesos peligrosos</div>
        <div class="word-bubble" draggable="true">Pagar accidentes</div>
        <div class="word-bubble" draggable="true">Pérdidas de obra</div>
      </div>
    </section>    

    <section class="testimonial-section" id="testimonialSection">
      <div class="container">
        <h3 id="testimonial-bg">Nuestros Testimonios</h3>
        <h2 id="testimonial-separador">&ndash;&ndash;&ndash;&ndash;&ndash;&ndash; NUESTROS TESTIMONIOS &ndash;&ndash;&ndash;&ndash;&ndash;&ndash;</h2>
        <div class="container" style="max-width: 800px;">
          <h3 id="clientes">Nuestros Clientes Hablan Sobre Nuestros Servicios</h3>
        </div>  
        <div id="carouselExampleCaptions" class="carousel slide">
          <div class="carousel-inner" style="height: 350px;">
            <div class="carousel-item active">
              <img src="{{asset('images/tm2.png')}}" class="d-block w-20 mx-auto" alt="...">
              <div class="carousel-caption d-none d-md-block" style="bottom: -80px;">
                <h5>Embol S.A.</h5>
                <p>Buen servicio y comprometidos con la entrega.</p>
              </div>
            </div>
            <div class="carousel-item">
              <img src="{{asset('images/tm2.png')}}" class="d-block w-20 mx-auto" alt="...">
              <div class="carousel-caption d-none d-md-block mt-5" style="bottom: -80px;">
                <h5>Taquiña S.A.</h5>
                <p>Muy comprometidos con la entrega en el lugar e instalación, todo al completo como se indicó.</p>
              </div>
            </div>
            <div class="carousel-item">
              <img src="{{asset('images/tm2.png')}}" class="d-block w-20 mx-auto" alt="...">
              <div class="carousel-caption d-none d-md-block mt-5" style="bottom: -80px;">
                <h5>UMSS</h5>
                <p>Excelente servicio.</p>
              </div>
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
          </button>
        </div>
      </div>
    </section>
    <section class="testimonial-section" id="cotizationSection">
      <div class="container">
        <h3 id="testimonial-bg">COTIZACION</h3>
        <h2 id="testimonial-separador">&ndash;&ndash;&ndash;&ndash;&ndash;&ndash; COTIZANDO &ndash;&ndash;&ndash;&ndash;&ndash;&ndash;</h2>
        <div class="row">
          <div class="col-6 text-center">
            <img src="{{asset('images/whc1.png')}}" alt="" style="height: 420px;">
          </div>
          <div class="col-6" data-bs-theme="dark">
            <h5 class="text-light">Realiza tu cotización con este formulario!</h5>
            <form class="row g-3">
              <div class="col-md-6">
                <label for="InputEmail" class="form-label text-light">Nombre</label>
                <input type="text" class="form-control" id="inputEmail4">
              </div>
              <div class="col-md-6">
                <label for="inputPassword4" class="form-label text-light">Apellido</label>
                <input type="text" class="form-control" id="inputPassword4">
              </div>
              <div class="col-12">
                <label for="inputAddress" class="form-label text-light">Correo electrónico</label>
                <input type="text" class="form-control" id="inputAddress" placeholder="example@dominio.com">
              </div>
              <div class="col-12">
                <label for="inputAddress2" class="form-label text-light">Caracteristicas</label>
                <textarea type="text" class="form-control" id="inputAddress2" placeholder="Describa las Caracteristicas, Capacidad, etc."></textarea>
              </div>
              <div class="col-md-5">
                <label for="inputCity" class="form-label text-light">Dirección</label>
                <input type="text" class="form-control" id="inputCity">
              </div>
              <div class="col-md-4">
                <label for="inputState" class="form-label text-light">Servicio</label>
                <select id="inputState" class="form-select">
                  <option selected>Escoge...</option>
                  <option>Mant. Preventivo</option>
                  <option>Mant. Correctivo</option>
                  <option>Fabricación</option>
                  <option>Diseño</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="inputZip" class="form-label text-light">WhatsApp</label>
                <input type="text" class="form-control" id="inputZip">
              </div>
              <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary px-5 mt-2">Cotizar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
    <section class="testimonial-section" id="memberSection">
      <div class="container">
        <div class="expert-team" style="text-align: center;">
          <h5 id="members-title" style="color:#ff3333;font-size:15px;">&ndash;&ndash;&ndash;&ndash;&ndash;&ndash; NUESTROS MIEMBROS &ndash;&ndash;&ndash;&ndash;&ndash;&ndash;</h5>
          <h1 id="members-subtitle" style="color: #ffffff; font-family: Georgia; font-weight: bold;">Nuestro Equipo Experto</h1>
        </div>
        <div class="row">
          <div class="col-4 card-members">
            <div class="member-image">
              <img src="{{asset('images/smile.png')}}" alt="imagen">
            </div>
            <h2 class="name-member">JONATHAN LEE</h2>
            <h3 class="pos-member">ING MECANICO</h3>
            <div class="social-media" style="margin-top: 30px;">
              <a href="#"><i class="fab fa-instagram redes"></i></a>
              <a href="#"><i class="fab fa-linkedin redes" ></i></a>
              <a href="#"><i class="fab fa-facebook redes"></i></a>
              <a href="#"><i class="fab fa-youtube redes"></i></a>
            </div>
          </div>
          <div class="col-4 card-members">
            <div class="member-image">
              <img src="{{asset('images/tm2.png')}}" alt="imagen">
            </div>
            <h2 class="name-member">TORIBIO BAUTISTA</h2>
            <h3 class="pos-member">ING MECANICO</h3>
            <div class="social-media" style="margin-top: 30px;">
              <a href="#"><i class="fab fa-instagram redes"></i></a>
              <a href="#"><i class="fab fa-linkedin redes"></i></a>
              <a href="#"><i class="fab fa-facebook redes"></i></a>
              <a href="#"><i class="fab fa-youtube redes"></i></a>
            </div>
          </div>
          <div class="col-4 card-members">
            <div class="member-image">
              <img src="{{asset('images/smile.png')}}" alt="imagen">
            </div>
            <h2 class="name-member">JONATHAN LEE</h2>
            <h3 class="pos-member">ING MECANICO</h3>
            <div class="social-media" style="margin-top: 30px;">
              <a href="#"><i class="fab fa-instagram redes"></i></a>
              <a href="#"><i class="fab fa-linkedin redes"></i></a>
              <a href="#"><i class="fab fa-facebook redes"></i></a>
              <a href="#"><i class="fab fa-youtube redes"></i></a>
            </div>         
          </div>
        </div>
      </div>
    </section>
    <section class="faq-section py-5" id="faqSection">
      <div class="container">
        <div class="row">
            <!-- Left Column - Image -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="faq-image-wrapper position-relative">
                    <div class="curved-border"></div>
                    <div class="image-container">
                        <img src="{{asset('images/wh3.png')}}" alt="FAQ Image" class="img-fluid">
                    </div>
                    <div class="decoration-star-1">✦</div>
                    <div class="decoration-star-2">✦</div>
                    <div class="decoration-x">✕</div>
                </div>
            </div>
            
            <!-- Right Column - FAQ Content -->
            <div class="col-lg-6">
                <div class="faq-content">
                    <span class="text-danger text-uppercase fw-bold">PREGUNTAS FRECUENTES</span>
                    <h2 class="display-5 fw-bold mb-4">Estás confundido!<br>Preguntas & Respuestas.</h2>
                    
                    <!-- FAQ Accordion -->
                    <div class="accordion custom-accordion" id="faqAccordion">
                        <!-- FAQ Item 1 -->
                        <div class="accordion-item border-0 mb-3">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed rounded" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapseOne">
                                    Que hace la empresa?
                                </button>
                            </h2>
                            <div id="collapseOne" 
                                 class="accordion-collapse collapse" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body rounded-bottom">
                                    Realiza la fabricación, diseño y mantenimiento de maquinarias industriales que busca satisfacer los requerimientos de nustros clientes, brindándoles servicios de fabricación de alta calidad gracias a la experiencia denuestro equipo.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="accordion-item border-0 mb-3">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed rounded" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapseTwo">
                                    Porqué elegirnos?
                                </button>
                            </h2>
                            <div id="collapseTwo" 
                                 class="accordion-collapse collapse" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body rounded-bottom">
                                    Por la experiencia y calidad de nuestros productos y servicios. Llevamos más de 23 años de experiencia en el mercado.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="accordion-item border-0 mb-3">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed rounded" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapseThree">
                                    Como puedo contactarlos?
                                </button>
                            </h2>
                            <div id="collapseThree" 
                                 class="accordion-collapse collapse" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body rounded-bottom">
                                    Contamos con números de referencia, correos electrónicos y nuestras redes sociales.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer" style="text-align: center;">
      <img src="{{asset('images/logo_promaq.png')}}" alt="logo" style="height: 200px;">
      <h6>Todos los derechos reservados @4ndrefer</h6>
    </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        window.addEventListener('scroll', function() {
          const navBottom = document.getElementById('navButtom');  
          if (window.scrollY > 400) {
            navButtom.style.display='block';
          } else {
            navButtom.style.display='none';
          }
        });
      });
    </script>
    <script>
     const container = document.getElementById("experence-box");
     const bubbles = [...document.querySelectorAll(".word-bubble")];

     const gravity = 0.4; // Gravedad
const elasticity = 0.6; // Rebote controlado
const bubbleSpacing = 5; // Espaciado entre burbujas
const fps = 60; // Fotogramas por segundo

bubbles.forEach((bubble, index) => {
  // Posición inicial aleatoria horizontal
  bubble.style.left = `${Math.random() * (container.offsetWidth - bubble.offsetWidth)}px`;
  bubble.style.top = `${-index * 50}px`; // Espaciado vertical inicial para que caigan progresivamente

  let speedY = Math.random() * 2 + 1;
  let speedX = (Math.random() - 0.5) * 2;

  function move() {
    let x = parseFloat(bubble.style.left);
    let y = parseFloat(bubble.style.top);

    // Aplicar gravedad
    y += speedY;
    speedY += gravity;

    // Actualizar posición horizontal
    x += speedX;
    speedX *= 0.98; // Fricción horizontal

    // Detectar colisión con bordes del contenedor
    if (x < 0) {
      x = 0;
      speedX = -speedX;
    } else if (x + bubble.offsetWidth > container.offsetWidth) {
      x = container.offsetWidth - bubble.offsetWidth;
      speedX = -speedX;
    }

    // Detectar colisión con el suelo
    if (y + bubble.offsetHeight > container.offsetHeight) {
      y = container.offsetHeight - bubble.offsetHeight;
      speedY = -speedY * elasticity;

      // Detener oscilación cuando la velocidad es mínima
      if (Math.abs(speedY) < 0.5) {
        speedY = 0;
      }
    }

    // Detectar colisiones con otras burbujas
    bubbles.forEach((otherBubble) => {
      if (bubble !== otherBubble) {
        const rect1 = bubble.getBoundingClientRect();
        const rect2 = otherBubble.getBoundingClientRect();

        if (
          rect1.left < rect2.right &&
          rect1.right > rect2.left &&
          rect1.top < rect2.bottom &&
          rect1.bottom > rect2.top
        ) {
          // Resolver colisión vertical
          if (y + bubble.offsetHeight > parseFloat(otherBubble.style.top)) {
            y = parseFloat(otherBubble.style.top) - bubble.offsetHeight - bubbleSpacing;
            speedY = -speedY * elasticity;
          }
        }
      }
    });

    // Aplicar posición actualizada
    bubble.style.left = `${x}px`;
    bubble.style.top = `${y}px`;

    // Continuar la animación
    setTimeout(move, 1000 / fps);
  }

  move();

  // Arrastrar burbujas
  bubble.addEventListener("mousedown", (e) => {
    const offsetX = e.clientX - bubble.getBoundingClientRect().left;
    const offsetY = e.clientY - bubble.getBoundingClientRect().top;

    function moveBubble(event) {
      const x = event.clientX - offsetX;
      const y = event.clientY - offsetY;

      bubble.style.left = `${Math.max(0, Math.min(x, container.offsetWidth - bubble.offsetWidth))}px`;
      bubble.style.top = `${Math.max(0, Math.min(y, container.offsetHeight - bubble.offsetHeight))}px`;
    }

    function stopDragging() {
      document.removeEventListener("mousemove", moveBubble);
      document.removeEventListener("mouseup", stopDragging);
    }

    document.addEventListener("mousemove", moveBubble);
    document.addEventListener("mouseup", stopDragging);
  });
});
    </script>
</body>
</html>