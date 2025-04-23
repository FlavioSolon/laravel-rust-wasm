<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel + Rust WebAssembly - Particle Animation</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col items-center justify-center p-4 sm:p-6">
<header class="w-full max-w-4xl text-center mb-6">
    <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 text-transparent bg-clip-text">
        Rust WebAssembly com Laravel
    </h1>
    <p class="mt-2 text-lg sm:text-xl text-gray-300">
        Animação de Partículas Interativa em 2D e 3D
    </p>
</header>

<main class="w-full max-w-4xl bg-gray-800 rounded-2xl shadow-2xl p-6 sm:p-8">
    <div class="controls grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="flex flex-col">
            <label for="particleCount" class="text-sm font-semibold text-gray-200 mb-2">
                Número de Partículas
            </label>
            <input type="range" id="particleCount" min="10" max="500" value="100" class="w-full">
            <span class="text-xs text-gray-400 mt-1">10 - 500</span>
        </div>
        <div class="flex flex-col">
            <label for="maxSpeed" class="text-sm font-semibold text-gray-200 mb-2">
                Velocidade Máxima
            </label>
            <input type="range" id="maxSpeed" min="1" max="10" value="4" class="w-full">
            <span class="text-xs text-gray-400 mt-1">1 - 10</span>
        </div>
        <div class="flex flex-col">
            <label for="particleSize" class="text-sm font-semibold text-gray-200 mb-2">
                Tamanho da Partícula
            </label>
            <input type="range" id="particleSize" min="1" max="10" value="3" class="w-full">
            <span class="text-xs text-gray-400 mt-1">1 - 10</span>
        </div>
        <div class="flex items-center">
            <input type="checkbox" id="collisions" checked class="mr-2">
            <label for="collisions" class="text-sm font-semibold text-gray-200">
                Habilitar Colisões
            </label>
        </div>
    </div>

    <div class="flex justify-center mb-4">
        <button id="to3D" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-300 mr-2">
            Alternar para 3D
        </button>
        <button id="to2D" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-300">
            Alternar para 2D
        </button>
    </div>

    <div class="relative flex justify-center">
        <canvas id="canvas" class="rounded-lg shadow-md w-full" style="display: block;"></canvas>
        <div id="webgl" class="absolute inset-0" style="display: none;"></div>
    </div>

    <p class="text-center text-sm text-gray-400 mt-4">
        Clique no canvas para reiniciar a animação. No modo 3D, arraste para rotacionar, role para zoom.
    </p>
</main>

<footer class="mt-6 text-center text-gray-500 text-sm">
    Desenvolvido com Rust, WebAssembly, Laravel e Three.js
</footer>

<script type="module">
    import init, { ParticleSystem } from '/wasm/rust_wasm.js';
    import * as THREE from '/js/build/three.module.js';
    import { OrbitControls } from '/js/examples/jsm/controls/OrbitControls.js';

    async function run() {
        await init();

        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const webglContainer = document.getElementById('webgl');
        let width = 800;
        let height = 600;
        const depth = 400; // Profundidade do espaço 3D

        // Controles
        const particleCountInput = document.getElementById('particleCount');
        const maxSpeedInput = document.getElementById('maxSpeed');
        const particleSizeInput = document.getElementById('particleSize');
        const collisionsInput = document.getElementById('collisions');
        const to3DButton = document.getElementById('to3D');
        const to2DButton = document.getElementById('to2D');

        let particleSystem = null;
        let is3D = false;
        let scene, camera, renderer, particles3D, controls;

        // Ajustar tamanho do canvas
        function resizeCanvas() {
            const container = canvas.parentElement;
            width = container.clientWidth;
            height = Math.floor(width * 3 / 4); // Aspect ratio 4:3
            canvas.width = width;
            canvas.height = height;
            if (ctx) {
                ctx.clearRect(0, 0, width, height);
            }
            // Só ajustar o renderer WebGL se estiver em modo 3D e os objetos existirem
            if (is3D && renderer && camera) {
                renderer.setSize(width, height);
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
            }
        }

        function createParticleSystem() {
            const count = parseInt(particleCountInput.value);
            const maxSpeed = parseFloat(maxSpeedInput.value);
            const particleSize = parseFloat(particleSizeInput.value);
            return ParticleSystem.new(count, width, height, maxSpeed, particleSize);
        }

        // Configurar WebGL
        function initWebGL() {
            try {
                console.log('Inicializando WebGL...'); // Debug
                scene = new THREE.Scene();
                camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
                camera.position.set(100, 100, 400);

                renderer = new THREE.WebGLRenderer({ antialias: true });
                renderer.setSize(width, height);
                renderer.setClearColor(0x000000);
                webglContainer.appendChild(renderer.domElement);
                console.log('Renderer criado:', renderer.domElement); // Debug

                // Controles orbitais
                controls = new OrbitControls(camera, renderer.domElement);
                controls.enableDamping = true;
                controls.dampingFactor = 0.05;

                // Iluminação
                const ambientLight = new THREE.AmbientLight(0x404040, 0.5);
                scene.add(ambientLight);
                const directionalLight1 = new THREE.DirectionalLight(0xffffff, 0.4);
                directionalLight1.position.set(1, 1, 1);
                scene.add(directionalLight1);
                const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.4);
                directionalLight2.position.set(-1, -1, -1);
                scene.add(directionalLight2);

                // Textura para partículas
                const textureLoader = new THREE.TextureLoader();
                const particleTexture = textureLoader.load('https://threejs.org/examples/textures/sprites/disc.png');

                // Inicializar partículas
                const particles = particleSystem.get_particles();
                console.log('Partículas:', particles.length); // Debug
                const vertices = new Float32Array(particles.length * 3);
                const colors = new Float32Array(particles.length * 3);

                for (let i = 0; i < particles.length; i++) {
                    const p = particles[i];
                    if (!p || !p.x || !p.y || !p.z) {
                        console.warn('Partícula inválida:', i, p); // Debug
                        continue;
                    }
                    vertices[i * 3] = p.x - width / 2;
                    vertices[i * 3 + 1] = -(p.y - height / 2);
                    vertices[i * 3 + 2] = p.z;

                    const speed = Math.sqrt(p.vx * p.vx + p.vy * p.vy + p.vz * p.vz);
                    const maxSpeed = parseFloat(maxSpeedInput.value);
                    const r = Math.min(1, speed / maxSpeed);
                    colors[i * 3] = r;
                    colors[i * 3 + 1] = 0.5;
                    colors[i * 3 + 2] = 1;
                }

                const geometry = new THREE.BufferGeometry();
                geometry.setAttribute('position', new THREE.BufferAttribute(vertices, 3));
                geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

                const material = new THREE.PointsMaterial({
                    size: parseFloat(particleSizeInput.value) * 4,
                    map: particleTexture,
                    vertexColors: true,
                    transparent: true,
                    alphaTest: 0.5,
                    blending: THREE.AdditiveBlending
                });

                particles3D = new THREE.Points(geometry, material);
                scene.add(particles3D);
                console.log('WebGL inicializado com sucesso'); // Debug
            } catch (error) {
                console.error('Erro ao inicializar WebGL:', error); // Debug
            }
        }

        // Inicializar o sistema de partículas
        resizeCanvas(); // Chamar após definir is3D, renderer, camera
        particleSystem = createParticleSystem();

        // Adicionar listener de redimensionamento
        window.addEventListener('resize', resizeCanvas);

        // Alternar para 3D
        to3DButton.addEventListener('click', () => {
            console.log('Alternando para 3D'); // Debug
            if (!is3D) {
                is3D = true;
                canvas.style.display = 'none';
                webglContainer.style.display = 'block';
                initWebGL();
            }
        });

        // Alternar para 2D
        to2DButton.addEventListener('click', () => {
            console.log('Alternando para 2D'); // Debug
            if (is3D) {
                is3D = false;
                canvas.style.display = 'block';
                webglContainer.style.display = 'none';
                if (renderer) {
                    renderer.domElement.remove();
                    renderer = null;
                }
            }
        });

        // Reiniciar ao clicar no canvas
        canvas.addEventListener('click', () => {
            particleSystem = createParticleSystem();
            if (is3D) {
                scene.remove(particles3D);
                initWebGL();
            }
        });

        // Loop de animação
        function animate() {
            particleSystem.update(width, height, depth, collisionsInput.checked);
            const particles = particleSystem.get_particles();

            if (is3D && particles3D && renderer && scene && camera) {
                const positions = particles3D.geometry.attributes.position.array;
                const colors = particles3D.geometry.attributes.color.array;

                for (let i = 0; i < particles.length; i++) {
                    const p = particles[i];
                    if (!p || !p.x || !p.y || !p.z) {
                        continue;
                    }
                    positions[i * 3] = p.x - width / 2;
                    positions[i * 3 + 1] = -(p.y - height / 2);
                    positions[i * 3 + 2] = p.z;

                    const speed = Math.sqrt(p.vx * p.vx + p.vy * p.vy + p.vz * p.vz);
                    const maxSpeed = parseFloat(maxSpeedInput.value);
                    const r = Math.min(1, speed / maxSpeed);
                    colors[i * 3] = r;
                    colors[i * 3 + 1] = 0.5;
                    colors[i * 3 + 2] = 1;
                }

                particles3D.geometry.attributes.position.needsUpdate = true;
                particles3D.geometry.attributes.color.needsUpdate = true;
                controls.update();
                renderer.render(scene, camera);
            } else {
                ctx.clearRect(0, 0, width, height);
                for (let i = 0; i < particles.length; i++) {
                    const p1 = particles[i];
                    if (!p1 || !p1.x || !p1.y || !p1.radius) {
                        continue;
                    }
                    const speed = Math.sqrt(p1.vx * p1.vx + p1.vy * p1.vy + p1.vz * p1.vz);
                    const maxSpeed = parseFloat(maxSpeedInput.value);
                    const r = Math.min(255, Math.floor((speed / maxSpeed) * 255));
                    ctx.fillStyle = `rgb(${r}, 100, 255)`;

                    ctx.beginPath();
                    ctx.arc(p1.x, p1.y, p1.radius, 0, 2 * Math.PI);
                    ctx.fill();

                    for (let j = i + 1; j < particles.length; j++) {
                        const p2 = particles[j];
                        if (!p2 || !p2.x || !p2.y) {
                            continue;
                        }
                        const dx = p1.x - p2.x;
                        const dy = p1.y - p2.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < 100) {
                            ctx.strokeStyle = `rgba(255, 255, 255, ${1 - distance / 100})`;
                            ctx.lineWidth = 1;
                            ctx.beginPath();
                            ctx.moveTo(p1.x, p1.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.stroke();
                        }
                    }
                }
            }

            requestAnimationFrame(animate);
        }

        animate();
    }

    run();
</script>
</body>
</html>
