<div class="xct-component-wrapper">

    <style>
        /* Paleta de Colores y Fuentes */
        :root {
            --xcaret-turquoise: #00A09A;
            --xcaret-orange: #F37021;
            --xcaret-dark: #1a1a1a;
            --xcaret-midnight: #0f1419;
            --xcaret-night-blue: #1e293b;
            --xcaret-deep-blue: #334155;
            --xcaret-light-blue: #64748b;
            --xcaret-white: #FFFFFF;
            --xcaret-gray: #94a3b8;
            --xcaret-light-gray: #e2e8f0;
        }

        .xct-component-wrapper * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .xct-component-wrapper {
            background: linear-gradient(135deg, var(--xcaret-midnight) 0%, var(--xcaret-night-blue) 50%, var(--xcaret-dark) 100%);
            padding: 40px 15px;
            min-height: 100vh;
        }

        .xct-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Estilos de Pestañas de Navegación */
        .xct-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .xct-tab-item {
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--xcaret-light-gray);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .xct-tab-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .xct-tab-item:hover::before {
            left: 100%;
        }

        .xct-tab-item:hover {
            background: rgba(0, 160, 154, 0.2);
            color: var(--xcaret-white);
            border-color: var(--xcaret-turquoise);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 160, 154, 0.3);
        }

        .xct-tab-item.xct-active {
            background: linear-gradient(135deg, var(--xcaret-turquoise), var(--xcaret-orange));
            color: var(--xcaret-white);
            border-color: transparent;
            box-shadow: 0 8px 30px rgba(0, 160, 154, 0.4);
            transform: translateY(-2px);
        }

        /* Contenido de las Pestañas */
        .xct-tab-content {
            display: none; /* Oculto por defecto por JS */
            background: rgba(255, 255, 255, 0.05);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .xct-tab-content.xct-active {
            display: block; /* Visible cuando está activo */
        }
        
        /* Títulos de Sección */
        .xct-section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .xct-section-title h2 {
            font-size: 42px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--xcaret-white);
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--xcaret-white), var(--xcaret-light-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .xct-section-title h2::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--xcaret-turquoise), var(--xcaret-orange));
            margin: 20px auto 0;
            border-radius: 2px;
            box-shadow: 0 2px 10px rgba(0, 160, 154, 0.5);
        }

        .xct-section-title p {
            font-size: 20px;
            color: var(--xcaret-gray);
            max-width: 800px;
            margin: 15px auto 0;
            line-height: 1.6;
        }

        /* Estilos de las Tarjetas (Cards) */
        .xct-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 40px;
        }

        .xct-cards-grid.hotel-grid {
            grid-template-columns: 1fr;
        }

        .xct-hotel-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .xct-hotel-main {
            grid-column: 1 / -1;
        }

        .xct-hotel-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .xct-detail-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .xct-detail-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(0, 160, 154, 0.3);
            transform: translateY(-5px);
        }

        .xct-detail-card h4 {
            color: var(--xcaret-turquoise);
            font-size: 20px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .xct-detail-card ul {
            list-style: none;
            padding: 0;
        }

        .xct-detail-card li {
            color: var(--xcaret-light-gray);
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .xct-detail-card li::before {
            content: '•';
            color: var(--xcaret-orange);
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .xct-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .xct-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--xcaret-turquoise), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .xct-card:hover::before {
            opacity: 1;
        }

        .xct-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            border-color: rgba(0, 160, 154, 0.3);
        }

        .xct-card-image {
            height: 250px;
            background-size: cover;
            background-position: center;
            position: relative;
            transition: transform 0.5s ease;
        }

        .xct-card:hover .xct-card-image {
            transform: scale(1.05);
        }

        .xct-card-image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 25px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.4), transparent);
            color: var(--xcaret-white);
            backdrop-filter: blur(5px);
        }

        .xct-card-image-overlay h3 {
            font-size: 26px;
            margin-bottom: 8px;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);
        }

        .xct-card-image-overlay .xct-location {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .xct-card-content {
            padding: 30px;
        }

        .xct-card-content p {
            margin-bottom: 20px;
            color: var(--xcaret-light-gray);
            line-height: 1.6;
            font-size: 16px;
        }

        .xct-card-tag {
            display: inline-block;
            background: linear-gradient(135deg, var(--xcaret-turquoise), var(--xcaret-orange));
            color: var(--xcaret-white);
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 160, 154, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Estilos para el botón Paseo 360 */
        .xct-360-btn {
            background: linear-gradient(135deg, var(--xcaret-orange), #ff6b35) !important;
            border-color: var(--xcaret-orange) !important;
            animation: pulse360 2s infinite;
        }

        .xct-360-btn:hover {
            background: linear-gradient(135deg, #ff6b35, var(--xcaret-orange)) !important;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 30px rgba(243, 112, 33, 0.5) !important;
        }

        @keyframes pulse360 {
            0% { box-shadow: 0 8px 25px rgba(243, 112, 33, 0.3); }
            50% { box-shadow: 0 8px 35px rgba(243, 112, 33, 0.6); }
            100% { box-shadow: 0 8px 25px rgba(243, 112, 33, 0.3); }
        }

        /* Estilos para el Modal */
        .xct-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.3s ease-out;
        }

        .xct-modal-content {
            position: relative;
            width: 95%;
            height: 90%;
            margin: 2% auto;
            background: var(--xcaret-dark);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            border: 2px solid var(--xcaret-turquoise);
            overflow: hidden;
        }

        .xct-modal-header {
            background: linear-gradient(135deg, var(--xcaret-turquoise), var(--xcaret-orange));
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--xcaret-white);
        }

        .xct-modal-title {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .xct-modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: var(--xcaret-white);
            font-size: 28px;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .xct-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .xct-modal-body {
            height: calc(100% - 80px);
            padding: 0;
        }

        .xct-modal-iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 0 0 18px 18px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .xct-modal-content {
            animation: slideIn 0.4s ease-out;
        }
    </style>

    <div class="xct-container">

        <div class="xct-tabs">
            <div class="xct-tab-item xct-active" data-target="#xct-hoteles">🏨 Hotel</div>
            <div class="xct-tab-item" data-target="#xct-restaurantes">🍽️ Restaurantes</div>
            <div class="xct-tab-item" data-target="#xct-parques">🌳 Parques y Tours</div>
            <div class="xct-tab-item xct-360-btn" id="xct-paseo360-btn">🔄 Paseo 360</div>
        </div>

        <div class="xct-content-wrapper">

            <section id="xct-hoteles" class="xct-tab-content xct-active">
                <div class="xct-section-title">
                    <h2>Hotel</h2>
                    <p>Una experiencia de lujo sostenible donde la hospitalidad mexicana te abraza. Tu estancia incluye acceso ilimitado a todos los parques y tours.</p>
                </div>
                <div class="xct-cards-grid hotel-grid">
                    <div class="xct-card xct-hotel-main">
                        <div class="xct-card-image" style="background-image: url('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1c/fe/c7/06/hotel-xcaret-arte.jpg?w=500&h=400&s=1');">
                            <div class="xct-card-image-overlay">
                                <h3>Hotel Xcaret Arte</h3>
                                <div class="xct-location">📍 Riviera Maya (Solo Adultos) | All-Fun Inclusive®</div>
                            </div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Arte, Gastronomía y Cultura</span>
                            <p>El Hotel Xcaret Arte es un homenaje a la creatividad mexicana, donde el arte se convierte en una experiencia de vida. Este resort exclusivo para adultos combina la elegancia del lujo con la autenticidad de las tradiciones artesanales mexicanas, creando un espacio único donde cada momento está diseñado para inspirar y transformar.</p>
                            
                            <div class="xct-hotel-details">
                                <div class="xct-detail-card">
                                    <h4>🏨 Habitaciones & Suites</h4>
                                    <ul>
                                        <li>900 suites de lujo con vistas al mar Caribe</li>
                                        <li>Diseño inspirado en artistas mexicanos</li>
                                        <li>Balcones privados con hamacas</li>
                                        <li>Amenidades de lujo y servicio 24/7</li>
                                        <li>Vistas panorámicas a la selva y el mar</li>
                                    </ul>
                                </div>

                                <div class="xct-detail-card">
                                    <h4>🎨 Experiencias Artísticas</h4>
                                    <ul>
                                        <li>Talleres de tejido con maestros artesanos</li>
                                        <li>Clases de alfarería y cerámica</li>
                                        <li>Pintura y artesanías mexicanas</li>
                                        <li>Galerías de arte contemporáneo</li>
                                        <li>Exposiciones de artistas locales</li>
                                    </ul>
                                </div>

                                <div class="xct-detail-card">
                                    <h4>🍽️ Gastronomía Exclusiva</h4>
                                    <ul>
                                        <li>10 restaurantes de clase mundial</li>
                                        <li>Chefs reconocidos internacionalmente</li>
                                        <li>Menús degustación únicos</li>
                                        <li>Vinos y mezcales premium</li>
                                        <li>Experiencias culinarias privadas</li>
                                    </ul>
                                </div>

                                <div class="xct-detail-card">
                                    <h4>🏊‍♂️ Amenidades Premium</h4>
                                    <ul>
                                        <li>Piscinas infinitas con vistas al mar</li>
                                        <li>Spa de lujo con tratamientos mayas</li>
                                        <li>Gimnasio de última generación</li>
                                        <li>Playa privada con servicio exclusivo</li>
                                        <li>Acceso ilimitado a todos los parques Xcaret</li>
                                    </ul>
                                </div>

                                <div class="xct-detail-card">
                                    <h4>🌿 Sostenibilidad & Naturaleza</h4>
                                    <ul>
                                        <li>Certificación LEED Gold</li>
                                        <li>Prácticas eco-friendly en todo el resort</li>
                                        <li>Integración con la selva maya</li>
                                        <li>Conservación de especies locales</li>
                                        <li>Energía renovable y reciclaje</li>
                                    </ul>
                                </div>

                                <div class="xct-detail-card">
                                    <h4>🎭 Entretenimiento & Cultura</h4>
                                    <ul>
                                        <li>Shows nocturnos espectaculares</li>
                                        <li>Música en vivo y performances</li>
                                        <li>Experiencias culturales únicas</li>
                                        <li>Festivales de arte y gastronomía</li>
                                        <li>Actividades temáticas diarias</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="xct-restaurantes" class="xct-tab-content">
                <div class="xct-section-title">
                    <h2>Gastronomía de Clase Mundial</h2>
                    <p>Un viaje culinario que fusiona la tradición mexicana con las cocinas del mundo, liderado por chefs de renombre internacional.</p>
                </div>
                <div class="xct-cards-grid">
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://blog.mexicodestinationclub.com/es/wp-content/uploads/2023/12/xingao-3-1024x812.jpg');">
                            <div class="xct-card-image-overlay"><h3>Xin-Gao</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Fusión Asiática</span>
                            <p>Disfruta desde un Omakase tradicional, robatayaki y mesas de teppanyaki hasta una fusión de sabores en un ambiente sofisticado.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80');">
                            <div class="xct-card-image-overlay"><h3>Xaak</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Menú Degustación</span>
                            <p>Menú degustación diseñado por 5 chefs de nuestro Colectivo Gastronómico: Roberto Solís, Paco Méndez, Jonatan Gómez Luna y Alejandro Ruiz.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://hotel-xcaret-arte.firstview.us/es/chino-poblano');">
                            <div class="xct-card-image-overlay"><h3>Chino Poblano</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Fusión Mexicana-China</span>
                            <p>Una experiencia gastronómica única que combina la cocina china con los sabores de Puebla, creando platos innovadores y deliciosos.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://hotel-xcaret-arte.firstview.us/es/cantina-vi-ai-py');">
                            <div class="xct-card-image-overlay"><h3>Cantina VI.AI.PY.</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Cantina Mexicana</span>
                            <p>Una auténtica cantina mexicana donde podrás disfrutar de los mejores tequilas, mezcales y antojitos tradicionales en un ambiente festivo.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://hotel-xcaret-arte.firstview.us/es/mercado-de-san-juan');">
                            <div class="xct-card-image-overlay"><h3>Mercado de San Juan</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Mercado Gastronómico</span>
                            <p>Un mercado inspirado en el famoso Mercado de San Juan de la Ciudad de México, ofreciendo una gran variedad de sabores y productos mexicanos.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://hotel-xcaret-arte.firstview.us/es/kibi-kibi');">
                            <div class="xct-card-image-overlay"><h3>Kibi-Kibi</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Cocina Internacional</span>
                            <p>Un restaurante que ofrece una experiencia culinaria internacional con los mejores ingredientes y técnicas de cocina moderna.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://hotel-xcaret-arte.firstview.us/es/tah-xido');">
                            <div class="xct-card-image-overlay"><h3>Tah-Xido</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Cocina Japonesa</span>
                            <p>Una experiencia gastronómica japonesa auténtica con sushi fresco, sashimi y platos tradicionales preparados por expertos chefs.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://hotel-xcaret-arte.firstview.us/es/encanta');">
                            <div class="xct-card-image-overlay"><h3>Encanta</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Cocina Mexicana Contemporánea</span>
                            <p>Un restaurante que encanta con su cocina mexicana contemporánea, fusionando tradición e innovación en cada plato.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://hotel-xcaret-arte.firstview.us/es/cayuco');">
                            <div class="xct-card-image-overlay"><h3>Cayuco</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Cocina del Mar</span>
                            <p>Especializado en los mejores pescados y mariscos frescos, ofreciendo una experiencia culinaria inspirada en las costas mexicanas.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://hotel-xcaret-arte.firstview.us/es/arenal');">
                            <div class="xct-card-image-overlay"><h3>Arenal</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Cocina Mediterránea</span>
                            <p>Un restaurante que transporta los sabores del Mediterráneo a la Riviera Maya, con platos frescos y sabores auténticos.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://hotel-xcaret-arte.firstview.us/es/apapachoa');">
                            <div class="xct-card-image-overlay"><h3>Apapachoa</h3><div class="xct-location">📍 Hotel Xcaret Arte</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Cocina Saludable</span>
                            <p>Un espacio dedicado a la cocina saludable y nutritiva, donde cada plato está diseñado para nutrir el cuerpo y el alma.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="xct-parques" class="xct-tab-content">
                <div class="xct-section-title">
                    <h2>Parques y Tours Incluidos</h2>
                    <p>Tu aventura no tiene límites. Con el concepto All-Fun Inclusive®, tienes acceso a los parques más emocionantes de la Riviera Maya.</p>
                </div>
                 <div class="xct-cards-grid">
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTKd5m7JNiz-0GIv9TnKQX90tO-f5HMok0wbg&s');">
                            <div class="xct-card-image-overlay"><h3>Parque Xcaret</h3><div class="xct-location">💚 Naturaleza y Cultura</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">El Corazón de México</span>
                            <p>Celebra la cultura de México con ríos subterráneos, shows espectaculares, fauna local y el impresionante show "Xcaret México Espectacular".</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://www.grupoxcaret.com/es/wp-content/uploads/2017/04/tirolesa_xplor_10-web.jpg');">
                           <div class="xct-card-image-overlay"><h3>Parque Xplor</h3><div class="xct-location">🔥 Aventura Extrema</div></div>
                        </div>
                        <div class="xct-card-content">
                             <span class="xct-card-tag">Tirolesas y Cavernas</span>
                            <p>Siente la adrenalina en las tirolesas más altas de la Riviera Maya, maneja vehículos anfibios por la selva y explora ríos de estalactitas.</p>
                        </div>
                    </div>
                     <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://cdn-3.expansion.mx/infographic/2022/03/30-14/37/44-0000017f-dc0d-dd4f-abff-dd6dd1a80002-default/img/caleta-2.jpg');">
                            <div class="xct-card-image-overlay"><h3>Parque Xel-Há</h3><div class="xct-location">🐠 Snorkel Ilimitado</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Maravilla Natural</span>
                            <p>Descubre un paraíso para el snorkel en una caleta natural que desemboca en el mar Caribe. Disfruta de buffet y barra libre todo el día.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://www.grupoxcaret.com/es/wp-content/uploads/2017/04/xenses_web.jpg');">
                            <div class="xct-card-image-overlay"><h3>Parque Xenses</h3><div class="xct-location">🎭 Sentidos y Sensaciones</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Experiencia Sensorial</span>
                            <p>Un parque único que desafía tus sentidos con atracciones que te harán cuestionar la realidad. Una experiencia inmersiva y divertida.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://www.grupoxcaret.com/es/wp-content/uploads/2017/04/xavage_web.jpg');">
                            <div class="xct-card-image-overlay"><h3>Parque Xavage</h3><div class="xct-location">💪 Aventura Extrema</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Deportes Extremos</span>
                            <p>El parque más extremo de la Riviera Maya con rafting, tirolesas, escalada y vehículos anfibios para los más aventureros.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://www.grupoxcaret.com/es/wp-content/uploads/2017/04/xenotes_web.jpg');">
                            <div class="xct-card-image-overlay"><h3>Xenotes</h3><div class="xct-location">💎 Cenotes Sagrados</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Aventura en Cenotes</span>
                            <p>Explora los cenotes sagrados de la península de Yucatán con rappel, tirolesas, kayak y snorkel en aguas cristalinas.</p>
                        </div>
                    </div>
                    <div class="xct-card">
                        <div class="xct-card-image" style="background-image: url('https://www.grupoxcaret.com/es/wp-content/uploads/2017/04/xoximilco_web.jpg');">
                            <div class="xct-card-image-overlay"><h3>Xoximilco</h3><div class="xct-location">🎵 Fiesta Mexicana</div></div>
                        </div>
                        <div class="xct-card-content">
                            <span class="xct-card-tag">Tradición Mexicana</span>
                            <p>Una fiesta mexicana en trajineras con música de mariachis, comida tradicional y bebidas típicas en un ambiente festivo.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Modal Paseo 360 -->
    <div id="xct-paseo360-modal" class="xct-modal">
        <div class="xct-modal-content">
            <div class="xct-modal-header">
                <div class="xct-modal-title">🔄 Paseo 360 - Hotel Xcaret Arte</div>
                <button class="xct-modal-close" onclick="closePaseo360Modal()">&times;</button>
            </div>
            <div class="xct-modal-body">
                <iframe class="xct-modal-iframe" src="https://hotel-xcaret-arte.firstview.us/es/xaak" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Selecciona todos los botones de las pestañas y los paneles de contenido
            const xctTabs = document.querySelectorAll('.xct-tab-item');
            const xctTabContents = document.querySelectorAll('.xct-tab-content');

            // Agregar efecto de hover a los enlaces
            const links = document.querySelectorAll('a[href*="firstview.us"]');
            links.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.borderBottomColor = 'var(--xcaret-turquoise)';
                    this.style.transform = 'translateY(-1px)';
                });
                
                link.addEventListener('mouseleave', function() {
                    this.style.borderBottomColor = 'transparent';
                    this.style.transform = 'translateY(0)';
                });
            });

            xctTabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const targetContent = document.querySelector(targetId);

                    // 1. Quitar la clase 'xct-active' de todas las pestañas y contenidos
                    xctTabs.forEach(t => t.classList.remove('xct-active'));
                    xctTabContents.forEach(c => c.classList.remove('xct-active'));

                    // 2. Añadir la clase 'xct-active' solo a la pestaña clickeada y a su contenido
                    this.classList.add('xct-active');
                    if (targetContent) {
                        targetContent.classList.add('xct-active');
                    }
                });
            });

            // Agregar efecto de scroll suave
            const cards = document.querySelectorAll('.xct-card');
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Funcionalidad para el botón Paseo 360
            const paseo360Btn = document.getElementById('xct-paseo360-btn');
            if (paseo360Btn) {
                paseo360Btn.addEventListener('click', function() {
                    openPaseo360Modal();
                });
            }

            // Cerrar modal al hacer clic fuera de él
            const modal = document.getElementById('xct-paseo360-modal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closePaseo360Modal();
                    }
                });
            }

            // Cerrar modal con la tecla Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closePaseo360Modal();
                }
            });
        });

        // Funciones para el modal
        function openPaseo360Modal() {
            const modal = document.getElementById('xct-paseo360-modal');
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Prevenir scroll del body
            }
        }

        function closePaseo360Modal() {
            const modal = document.getElementById('xct-paseo360-modal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Restaurar scroll del body
            }
        }
    </script>

</div>