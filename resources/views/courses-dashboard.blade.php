<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Cursos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .card h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5em;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button {
            background: #667eea;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            font-weight: 600;
        }

        button:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        button:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: #6c757d;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #218838;
        }

        .courses-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .course-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #667eea;
        }

        .course-card h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.3em;
        }

        .course-id {
            color: #667eea;
            font-weight: bold;
            font-size: 0.9em;
            margin-bottom: 8px;
        }

        .course-description {
            color: #666;
            margin-bottom: 12px;
            font-size: 0.95em;
        }

        .course-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9em;
            color: #777;
        }

        .course-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .course-actions button {
            flex: 1;
            padding: 8px;
            font-size: 0.9em;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .token-display {
            background: #f5f5f5;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 0.85em;
            color: #333;
            max-height: 80px;
            overflow-y: auto;
        }

        .user-info {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            color: #333;
        }

        .user-info p {
            margin: 5px 0;
        }

        .user-role {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.85em;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .content {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 1.8em;
            }

            .courses-list {
                grid-template-columns: 1fr;
            }
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Dashboard de Cursos</h1>
            <p>Gestiona tus cursos e inscripciones</p>
        </div>

        <div class="content">
            <!-- Panel de Login -->
            <div class="card">
                <h2>Iniciar Sesión</h2>
                <div id="loginForm">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" placeholder="ejemplo@test.com" value="profesor@test.com">
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" placeholder="password" value="password">
                    </div>
                    <button onclick="login()">Iniciar Sesión</button>
                </div>

                <div id="userInfo" style="display: none;">
                    <div class="user-info">
                        <p><strong>Usuario:</strong> <span id="userName"></span></p>
                        <p><strong>Email:</strong> <span id="userEmail"></span></p>
                        <p><strong>Rol:</strong> <span class="user-role" id="userRole"></span></p>
                    </div>
                    <div class="token-display" id="tokenDisplay"></div>
                    <button onclick="logout()" class="btn-secondary">Cerrar Sesión</button>
                </div>
            </div>

            <!-- Panel de Crear Curso (solo para profesores) -->
            <div class="card" id="createCoursePanel" style="display: none;">
                <h2>Crear Nuevo Curso</h2>
                <div class="form-group">
                    <label for="courseId">ID del Curso:</label>
                    <input type="text" id="courseId" placeholder="MATH101">
                </div>
                <div class="form-group">
                    <label for="courseName">Nombre:</label>
                    <input type="text" id="courseName" placeholder="Matemáticas Básicas">
                </div>
                <div class="form-group">
                    <label for="courseDesc">Descripción:</label>
                    <textarea id="courseDesc" placeholder="Descripción del curso..."></textarea>
                </div>
                <div class="form-group">
                    <label for="courseMax">Máximo de Estudiantes:</label>
                    <input type="number" id="courseMax" placeholder="30" min="1">
                </div>
                <button onclick="createCourse()">Crear Curso</button>
            </div>

            <!-- Panel de Búsqueda (solo para estudiantes) -->
            <div class="card" id="searchCoursePanel" style="display: none;">
                <h2>Buscar e Inscribirse</h2>
                <div class="form-group">
                    <label for="searchCourseId">ID del Curso:</label>
                    <input type="text" id="searchCourseId" placeholder="MATH101">
                </div>
                <button onclick="searchCourse()">Buscar Curso</button>
                <div id="searchResult" style="display: none; margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 5px;">
                    <h3 id="resultTitle"></h3>
                    <p id="resultDesc"></p>
                    <p><strong>Profesor:</strong> <span id="resultTeacher"></span></p>
                    <p><strong>Cupos disponibles:</strong> <span id="resultQuotas"></span></p>
                    <button onclick="enrollCourse()" id="enrollBtn">Inscribirse</button>
                </div>
            </div>
        </div>

        <!-- Sección de Cursos -->
        <div class="card">
            <h2 id="coursesTitle">Cursos Disponibles</h2>
            <div id="loading" class="loading">
                <div class="spinner"></div>
                Cargando cursos...
            </div>
            <div class="courses-list" id="coursesList"></div>
        </div>

        <!-- Alertas -->
        <div id="alertContainer"></div>
    </div>

    <script>
        let token = null;
        let currentUser = null;
        let currentSearchResult = null;

        async function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                showAlert('Por favor completa todos los campos', 'error');
                return;
            }

            try {
                const response = await fetch('http://127.0.0.1:8001/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    token = data.token;
                    currentUser = data.user;
                    showAlert('Login exitoso', 'success');
                    updateUI();
                    loadCourses();
                } else {
                    showAlert(data.message || 'Error al iniciar sesión', 'error');
                }
            } catch (error) {
                showAlert('Error de conexión: ' + error.message, 'error');
            }
        }

        function logout() {
            token = null;
            currentUser = null;
            updateUI();
            loadCourses();
            showAlert('Sesión cerrada', 'success');
        }

        function updateUI() {
            const loginForm = document.getElementById('loginForm');
            const userInfo = document.getElementById('userInfo');
            const createCoursePanel = document.getElementById('createCoursePanel');
            const searchCoursePanel = document.getElementById('searchCoursePanel');

            if (token && currentUser) {
                loginForm.style.display = 'none';
                userInfo.style.display = 'block';
                document.getElementById('userName').textContent = currentUser.name;
                document.getElementById('userEmail').textContent = currentUser.email;
                document.getElementById('userRole').textContent = currentUser.roles[0];
                document.getElementById('tokenDisplay').textContent = 'Token: ' + token;

                if (currentUser.roles.includes('profesor')) {
                    createCoursePanel.style.display = 'block';
                    searchCoursePanel.style.display = 'none';
                } else if (currentUser.roles.includes('estudiante')) {
                    createCoursePanel.style.display = 'none';
                    searchCoursePanel.style.display = 'block';
                }
            } else {
                loginForm.style.display = 'block';
                userInfo.style.display = 'none';
                createCoursePanel.style.display = 'none';
                searchCoursePanel.style.display = 'none';
            }
        }

        async function createCourse() {
            if (!token) {
                showAlert('Debes iniciar sesión primero', 'error');
                return;
            }

            const courseId = document.getElementById('courseId').value;
            const courseName = document.getElementById('courseName').value;
            const courseDesc = document.getElementById('courseDesc').value;
            const courseMax = document.getElementById('courseMax').value;

            if (!courseId || !courseName) {
                showAlert('Por favor completa los campos requeridos', 'error');
                return;
            }

            try {
                const response = await fetch('http://127.0.0.1:8001/api/courses', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        course_id: courseId,
                        name: courseName,
                        description: courseDesc,
                        max_students: courseMax || null
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert('Curso creado exitosamente', 'success');
                    document.getElementById('courseId').value = '';
                    document.getElementById('courseName').value = '';
                    document.getElementById('courseDesc').value = '';
                    document.getElementById('courseMax').value = '';
                    loadCourses();
                } else {
                    showAlert(data.message || 'Error al crear curso', 'error');
                }
            } catch (error) {
                showAlert('Error de conexión: ' + error.message, 'error');
            }
        }

        async function searchCourse() {
            if (!token) {
                showAlert('Debes iniciar sesión primero', 'error');
                return;
            }

            const courseId = document.getElementById('searchCourseId').value;

            if (!courseId) {
                showAlert('Ingresa el ID del curso', 'error');
                return;
            }

            try {
                const response = await fetch('http://127.0.0.1:8001/api/courses/search', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ course_id: courseId })
                });

                const data = await response.json();

                if (response.ok) {
                    currentSearchResult = data;
                    document.getElementById('resultTitle').textContent = data.name;
                    document.getElementById('resultDesc').textContent = data.description || 'Sin descripción';
                    document.getElementById('resultTeacher').textContent = data.teacher?.name || 'Desconocido';
                    document.getElementById('resultQuotas').textContent = data.max_students ? data.max_students + ' cupos' : 'Ilimitado';
                    document.getElementById('searchResult').style.display = 'block';
                } else {
                    showAlert(data.message || 'Curso no encontrado', 'error');
                    document.getElementById('searchResult').style.display = 'none';
                }
            } catch (error) {
                showAlert('Error de conexión: ' + error.message, 'error');
            }
        }

        async function enrollCourse() {
            if (!currentSearchResult) return;

            try {
                const response = await fetch(`http://127.0.0.1:8001/api/courses/${currentSearchResult.id}/enroll`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ course_id: currentSearchResult.id })
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert('¡Inscripción exitosa!', 'success');
                    document.getElementById('searchCourseId').value = '';
                    document.getElementById('searchResult').style.display = 'none';
                    loadCourses();
                } else {
                    showAlert(data.message || 'Error al inscribirse', 'error');
                }
            } catch (error) {
                showAlert('Error de conexión: ' + error.message, 'error');
            }
        }

        async function loadCourses() {
            document.getElementById('loading').style.display = 'block';

            try {
                const response = await fetch('http://127.0.0.1:8001/api/courses', {
                    headers: token ? { 'Authorization': `Bearer ${token}` } : {}
                });

                const data = await response.json();
                document.getElementById('loading').style.display = 'none';

                if (Array.isArray(data)) {
                    displayCourses(data);
                } else if (data.courses) {
                    displayCourses(data.courses);
                }
            } catch (error) {
                document.getElementById('loading').style.display = 'none';
                showAlert('Error al cargar cursos: ' + error.message, 'error');
            }
        }

        function displayCourses(courses) {
            const coursesList = document.getElementById('coursesList');

            if (courses.length === 0) {
                coursesList.innerHTML = '<p style="text-align: center; color: #999; grid-column: 1/-1;">No hay cursos disponibles</p>';
                return;
            }

            coursesList.innerHTML = courses.map(course => `
                <div class="course-card">
                    <div class="course-id">${course.course_id}</div>
                    <h3>${course.name}</h3>
                    <p class="course-description">${course.description || 'Sin descripción'}</p>
                    <div class="course-info">
                        <span>👤 ${course.teacher?.name || 'Desconocido'}</span>
                        <span>${course.max_students ? course.max_students + ' cupos' : 'Ilimitado'}</span>
                    </div>
                </div>
            `).join('');
        }

        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;

            alertContainer.appendChild(alert);

            setTimeout(() => alert.remove(), 4000);
        }

        // Cargar cursos al iniciar
        loadCourses();
    </script>
</body>
</html>
