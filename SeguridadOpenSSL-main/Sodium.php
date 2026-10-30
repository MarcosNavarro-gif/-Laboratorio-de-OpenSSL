<?php
$resultado = "";
$mensajeOriginal = "";
$mensajeCifrado = "";
$mensajeDescifrado = "";
$tipoResultado = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mensajeOriginal = trim($_POST["mensaje"] ?? "");

    if ($mensajeOriginal === "") {
        $resultado = "Debes escribir un mensaje para continuar.";
        $tipoResultado = "error";
    } elseif (!function_exists("sodium_crypto_secretbox")) {
        $resultado = "La extensión Sodium no está disponible en PHP.";
        $tipoResultado = "error";
    } else {
        // Generar una clave secreta nueva
        $clave = sodium_crypto_secretbox_keygen();

        // Generar un nonce único para este cifrado
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        // Cifrar y autenticar el mensaje
        $cifrado = sodium_crypto_secretbox(
            $mensajeOriginal,
            $nonce,
            $clave
        );

        // Guardar nonce + texto cifrado en una representación Base64
        $mensajeCifrado = base64_encode($nonce . $cifrado);

        // Descifrar y verificar usando la misma clave y el mismo nonce
        $descifrado = sodium_crypto_secretbox_open(
            $cifrado,
            $nonce,
            $clave
        );

        if ($descifrado === false) {
            $resultado = "No se pudo verificar o descifrar el mensaje.";
            $tipoResultado = "error";
        } else {
            $mensajeDescifrado = $descifrado;
            $resultado = "El mensaje se cifró y autenticó correctamente.";
            $tipoResultado = "exito";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cifrado de mensajes | Sodium</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            margin: 0;
            padding: 35px 15px;
            color: #334155;
        }

        .contenedor {
            max-width: 760px;
            margin: auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        .encabezado {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        h2, h3 {
            color: #243b53;
        }

        h2 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        h3 {
            margin-top: 0;
        }

        .subtitulo {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 0;
        }

        .etiqueta {
            display: inline-block;
            background: #e8f1fb;
            color: #1769aa;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        label {
            display: block;
            margin: 18px 0 8px;
            font-weight: bold;
            color: #243b53;
        }

        textarea {
            width: 100%;
            min-height: 125px;
            padding: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-family: Arial, sans-serif;
            font-size: 15px;
            resize: vertical;
        }

        textarea:focus {
            outline: none;
            border-color: #1769aa;
            box-shadow: 0 0 0 3px rgba(23, 105, 170, 0.12);
        }

        button {
            width: 100%;
            margin-top: 18px;
            padding: 13px;
            background: #1769aa;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        button:hover {
            background: #104e80;
        }

        .resultado {
            margin-top: 22px;
            padding: 14px 16px;
            border-radius: 7px;
            line-height: 1.5;
        }

        .exito {
            background: #e8f4ea;
            color: #205b2b;
            border: 1px solid #c6e6cc;
        }

        .error {
            background: #fdeaea;
            color: #922b21;
            border: 1px solid #f5c6c6;
        }

        .panel {
            margin-top: 22px;
            padding: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
        }

        .panel h3 {
            margin-bottom: 12px;
        }

        pre {
            margin: 0;
            padding: 14px;
            background: #eef2f6;
            border: 1px solid #dbe3ec;
            border-radius: 7px;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            font-family: Consolas, monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .pie {
            margin-top: 25px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
            text-align: center;
        }

        @media (max-width: 500px) {
            .contenedor {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
<div class="contenedor">

    <div class="encabezado">
        <span class="etiqueta">CRIPTOGRAFÍA SIMÉTRICA</span>
        <h2>Cifrado y autenticación con Sodium</h2>
        <p class="subtitulo">
            Introduce un mensaje para cifrarlo y comprobar su autenticidad
            mediante las funciones criptográficas de Sodium.
        </p>
    </div>

    <form method="POST" action="">
        <label for="mensaje">Mensaje original</label>

        <textarea
            id="mensaje"
            name="mensaje"
            placeholder="Escribe aquí el mensaje que deseas cifrar..."
            required
        ><?= htmlspecialchars($mensajeOriginal, ENT_QUOTES, "UTF-8") ?></textarea>

        <button type="submit">Cifrar y verificar mensaje</button>
    </form>

    <?php if ($resultado !== ""): ?>
        <div class="resultado <?= htmlspecialchars($tipoResultado, ENT_QUOTES, "UTF-8") ?>">
            <strong>Resultado:</strong>
            <?= htmlspecialchars($resultado, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>

    <?php if ($mensajeCifrado !== ""): ?>
        <div class="panel">
            <h3>Mensaje cifrado y autenticado</h3>
            <p class="subtitulo">
                Representación Base64 del nonce y del contenido cifrado.
            </p>
            <pre><?= htmlspecialchars($mensajeCifrado, ENT_QUOTES, "UTF-8") ?></pre>
        </div>
    <?php endif; ?>

    <?php if ($mensajeDescifrado !== ""): ?>
        <div class="panel">
            <h3>Mensaje descifrado</h3>
            <pre><?= htmlspecialchars($mensajeDescifrado, ENT_QUOTES, "UTF-8") ?></pre>
        </div>
    <?php endif; ?>

    <div class="pie">
        Laboratorio de criptografía · PHP y Libsodium · Secretbox
    </div>

</div>
</body>
</html>