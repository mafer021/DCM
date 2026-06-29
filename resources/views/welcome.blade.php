<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCM Soluciones Energéticas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>

        body{
            background:#f8f9fa;
        }

        .hero{
            background:
                linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)),
                url('{{ asset('images/iniciop.jpg') }}');

            background-size:cover;
            background-position:center;
            min-height:75vh;

            display:flex;
            align-items:center;
            color:white;
        }

        .hero h1{
            text-shadow:2px 2px 10px rgba(0,0,0,.5);
        }

        .hero p{
            max-width:900px;
            margin:auto;
        }

        .service-card{
            padding:30px;
            border:1px solid #eee;
            border-radius:15px;
            background:white;
            transition:all .4s ease;
            height:100%;
        }

        .service-card:hover{
            transform:translateY(-10px);
            box-shadow:0 10px 25px rgba(0,0,0,.12);
            border-color:#198754;
        }

        .gallery-card{
            transition:.4s;
            overflow:hidden;
        }

        .gallery-card:hover{
            transform:translateY(-10px);
        }

        .gallery-card img{
            transition:.5s;
        }

        .gallery-card:hover img{
            transform:scale(1.1);
        }

        .section-title{
            color:#198754;
            font-weight:bold;
        }

        footer{
            background:#212529;
            color:white;
        }

    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center" href="#">

            <img src="{{ asset('images/logof.png') }}"
                 alt="DCM"
                 height="55"
                 class="me-2">

            <span class="fw-bold text-success">
                DCM Soluciones Energéticas
            </span>

        </a>

        <a href="{{ route('login') }}" class="btn btn-success">
            Iniciar sesión
        </a>

    </div>
</nav>

<!-- HERO -->
<header class="hero text-center">

    <div class="container">

        <h1 class="display-3 fw-bold">
            DCM SOLUCIONES ENERGÉTICAS
        </h1>

        <p class="lead mt-4">
            Somos una empresa orgullosamente Tehuacanera especializada en la implementación
            de proyectos de energías renovables, contando con una plantilla experimentada
            y preparada para brindar soluciones eficientes y de calidad.
        </p>

        <p class="fs-5 mt-3">
            Impulsamos el ahorro energético mediante sistemas fotovoltaicos,
            calentadores solares, bombas solares, biodigestores e instalaciones eléctricas.
        </p>

        

    </div>

</header>

<!-- QUIENES SOMOS -->
<section class="py-5 bg-white">

    <div class="container">

        <h2 class="text-center fw-bold mb-4 section-title">
            ¿Quiénes Somos?
        </h2>

        <div class="row justify-content-center">

            <div class="col-lg-10">

                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4 text-center">

                        <p class="fs-5 mb-0">
                            DCM Soluciones Energéticas es una empresa dedicada a la
                            implementación de proyectos en energías renovables y sistemas
                            eléctricos. Nuestro objetivo es brindar soluciones eficientes,
                            seguras y sustentables que contribuyan al ahorro energético y al
                            bienestar de nuestros clientes.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- MISION Y VISION -->
<section class="py-5">

    <div class="container">

        <h2 class="text-center fw-bold mb-5 section-title">
            Nuestra Filosofía
        </h2>

        <div class="row g-4">

            <div class="col-md-6">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body p-4">

                        <h3 class="text-success fw-bold mb-3">
                            <i class="bi bi-bullseye me-2"></i>
                            Misión
                        </h3>

                        <p>
                            En DCM Soluciones Energéticas trabajamos para brindar soluciones
                            integrales en energías renovables y sistemas eléctricos,
                            ofreciendo servicios de calidad, innovación y confianza que
                            contribuyan al ahorro energético, al cuidado del medio ambiente
                            y al bienestar de nuestros clientes.
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body p-4">

                        <h3 class="text-success fw-bold mb-3">
                            <i class="bi bi-eye me-2"></i>
                            Visión
                        </h3>

                        <p>
                            Ser una empresa líder y reconocida en el estado de Puebla y la
                            región por la calidad de nuestros servicios en energías
                            renovables, destacándonos por nuestra innovación, compromiso
                            ambiental y atención al cliente.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- SERVICIOS -->
<section class="py-5 bg-white">

    <div class="container">

        <h2 class="text-center fw-bold mb-5 section-title">
            Nuestros Servicios
        </h2>

        <div class="row justify-content-center g-4">

            <div class="col-md-4">
                <div class="service-card text-center">
                    <i class="bi bi-sun fs-1 text-warning"></i>
                    <h5 class="mt-3">Sistemas Fotovoltaicos</h5>
                    <p>Reduce el costo de tu recibo de luz con paneles solares.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-card text-center">
                    <i class="bi bi-droplet-half fs-1 text-primary"></i>
                    <h5 class="mt-3">Calentadores Solares</h5>
                    <p>Contamos con excelentes calentadores solares a precios accesibles.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-card text-center">
                    <i class="bi bi-water fs-1 text-info"></i>
                    <h5 class="mt-3">Bombas Solares</h5>
                    <p>Ahorra dinero mediante sistemas de bombeo alimentados por energía solar.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-card text-center">
                    <i class="bi bi-recycle fs-1 text-success"></i>
                    <h5 class="mt-3">Biodigestores</h5>
                    <p>Genera energía a partir de residuos orgánicos.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-card text-center">
                    <i class="bi bi-lightning-charge fs-1 text-danger"></i>
                    <h5 class="mt-3">Instalaciones Eléctricas</h5>
                    <p>Instalaciones seguras y eficientes bajo normas de calidad.</p>
                </div>
            </div>

        </div>

    </div>

</section>

<!-- GALERIA -->
<section class="py-5">

    <div class="container">

        <h2 class="text-center fw-bold mb-5 section-title">
            Proyectos DCM Soluciones Energéticas
        </h2>

        <div class="row justify-content-center g-4">

            @for ($i = 1; $i <= 7; $i++)

                <div class="col-md-4">

                    <div class="card gallery-card border-0 shadow-sm h-100">

                        <img src="{{ asset('images/fotoo' . $i . '.jpeg') }}"
                             class="card-img-top"
                             style="height:260px; object-fit:cover;"
                             alt="Proyecto {{ $i }}">

                        <div class="card-body text-center">

                            <h6 class="fw-bold">
                                Proyecto DCM Soluciones Energéticas
                            </h6>

                        </div>

                    </div>

                </div>

            @endfor

        </div>

    </div>

</section>

<footer class="text-center py-4 mt-5">

    <div class="container">

        <p class="mb-1 fw-bold">
            DCM Soluciones Energéticas
        </p>

        <small>
            © 2026 Todos los derechos reservados.
        </small>

    </div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>