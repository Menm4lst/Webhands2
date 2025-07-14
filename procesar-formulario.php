<?php
// Configuración básica de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Verificar que el método de solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Método no permitido");
}

// Validar y sanitizar los datos de entrada
$nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$mensaje = filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_STRING);

// Validaciones adicionales
if (empty($nombre) || empty($email) || empty($mensaje)) {
    http_response_code(400);
    die("Todos los campos son obligatorios");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die("El email proporcionado no es válido");
}

if (strlen($nombre) < 3) {
    http_response_code(400);
    die("El nombre debe tener al menos 3 caracteres");
}

if (strlen($mensaje) < 10) {
    http_response_code(400);
    die("El mensaje debe tener al menos 10 caracteres");
}

// Configuración del correo
$destinatario = "manosalsite@gmail.com"; // Cambiar por tu email real
$asunto = "Nuevo mensaje de $nombre desde Manos al Site";

// Construir el cuerpo del mensaje
$cuerpo = "Has recibido un nuevo mensaje a través del formulario de contacto:\n\n";
$cuerpo .= "Nombre: $nombre\n";
$cuerpo .= "Email: $email\n\n";
$cuerpo .= "Mensaje:\n$mensaje\n\n";
$cuerpo .= "Enviado el: " . date('d/m/Y H:i:s');

// Cabeceras para prevenir inyección de cabeceras
$headers = "From: no-reply@tudominio.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Intentar enviar el correo
if (mail($destinatario, $asunto, $cuerpo, $headers)) {
    // Redireccionar a página de éxito
    header('Location: gracias.html');
    exit();
} else {
    http_response_code(500);
    die("Error al enviar el mensaje. Por favor, inténtalo nuevamente más tarde.");
}
?>