<?php
$resultado = "";
$tipoResultado = "";
$mensaje = "";
$mensajeCifrado = "";
$mensajeDescifrado = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mensaje = $_POST["mensaje"] ?? "";
    $clave = $_POST["clave"] ?? "";

    if (trim($mensaje) === "") {
        $resultado = "Escribe un mensaje o pega el texto cifrado.";
        $tipoResultado = "error";
    } elseif (trim($clave) === "") {
        $resultado = "Introduce una clave para continuar.";
        $tipoResultado = "error";
    } else {
        // Transformar la clave introducida en una clave de 16 bytes.
        $claveHash = hash("sha256", $clave, true);
        $claveAES = substr($claveHash, 0, 16);

        if (isset($_POST["cifrar"])) {
            // Obtener la longitud del IV requerido por AES-128-CBC.
            $longitudIV = openssl_cipher_iv_length("AES-128-CBC");

            // Generar un IV aleatorio para el cifrado.
            $iv = random_bytes($longitudIV);

            // Cifrar el mensaje.
            $cifrado = openssl_encrypt(
                $mensaje,
                "AES-128-CBC",
                $claveAES,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($cifrado === false) {
                $resultado = "No fue posible cifrar el mensaje.";
                $tipoResultado = "error";
            } else {
                // Unir el IV y el contenido cifrado y codificar en Base64.
                $mensajeCifrado = base64_encode($iv . $cifrado);

                $resultado = "Mensaje cifrado correctamente.";
                $tipoResultado = "exito";
            }
        } elseif (isset($_POST["descifrar"])) {
            // Convertir el texto Base64 a datos binarios.
            $datos = base64_decode(trim($mensaje), true);

            $longitudIV = openssl_cipher_iv_length("AES-128-CBC");

            if (
                $datos === false ||
                strlen($datos) <= $longitudIV
            ) {
                $resultado = "El texto cifrado no tiene un formato válido.";
                $tipoResultado = "error";
            } else {
                // Recuperar el IV y el contenido cifrado.
                $iv = substr($datos, 0, $longitudIV);
                $cifrado = substr($datos, $longitudIV);

                // Descifrar usando la misma clave y el IV recuperado.
                $descifrado = openssl_decrypt(
                    $cifrado,
                    "AES-128-CBC",
                    $claveAES,
                    OPENSSL_RAW_DATA,
                    $iv
                );

                if ($descifrado === false) {
                    $resultado = "No se pudo descifrar el mensaje. Comprueba la clave y el texto cifrado.";
                    $tipoResultado = "error";
                } else {
                    $mensajeDescifrado = $descifrado;

                    $resultado = "Mensaje descifrado correctamente.";
                    $tipoResultado = "exito";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cifrado y descifrado AES</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            margin: 0;
            padding: 30px 15px;
            color: #243b53;
        }

        .contenedor {
            max-width: 760px;
            margin: auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-top: 0;
            color: #243b53;
            font-size: 28px;
        }

        .descripcion {
            color: #526579;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .etiqueta {
            display: inline-block;
            background: #e8f1fb;
            color: #1769aa;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 8px;
        }

        textarea,
        input {
            display: block;
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-family: Arial, sans-serif;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
        }

        textarea:focus,
        input:focus {
            outline: none;
            border-color: #1769aa;
            box-shadow: 0 0 0 3px rgba(23, 105, 170, 0.12);
        }

        .ayuda {
            display: block;
            color: #64748b;
            font-size: 13px;
            margin-top: 6px;
            line-height: 1.4;
        }

        .botones {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        button {
            border: none;
            background: #1769aa;
            color: white;
            padding: 12px 18px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #104e80;
        }

        button.secundario {
            background: #526579;
        }

        button.secundario:hover {
            background: #35495e;
        }

        .resultado {
            margin-top: 24px;
            padding: 16px;
            border-radius: 6px;
            overflow-wrap: anywhere;
            line-height: 1.5;
        }

        .exito {
            background: #e8f7ed;
            border: 1px solid #a7dfb8;
            color: #176534;
        }

        .error {
            background: #fdecec;
            border: 1px solid #f2b8b8;
            color: #a12626;
        }

        .panel {
            margin-top: 18px;
            padding: 15px;
            border: 1px solid #dbe3ec;
            border-radius: 6px;
            background: #f8fafc;
        }

        .panel h3 {
            margin-top: 0;
            font-size: 16px;
        }

        .contenido {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            color: #334155;
        }

        .requisito {
            margin-top: 25px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            color: #526579;
            font-size: 14px;
            line-height: 1.5;
        }

        @media (max-width: 500px) {
            .contenedor {
                padding: 18px;
            }

            h1 {
                font-size: 24px;
            }

            .botones {
                flex-direction: column;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <main class="contenedor">
        <span class="etiqueta">CRIPTOGRAFÍA SIMÉTRICA</span>

        <h1>Cifrado y descifrado de mensajes</h1>

        <p class="descripcion">
            Utiliza AES-128-CBC para cifrar un mensaje y recuperarlo
            posteriormente con la misma clave.
        </p>

        <form method="post">
            <label for="mensaje">Mensaje o texto cifrado (Base64)</label>

            <textarea
                id="mensaje"
                name="mensaje"
                rows="5"
                required
                placeholder="Escribe el mensaje que deseas cifrar o pega el texto cifrado..."
            ><?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?></textarea>

            <label for="clave">Clave de cifrado</label>

            <input
                type="password"
                id="clave"
                name="clave"
                required
                autocomplete="off"
                placeholder="Introduce tu clave"
            >

            <span class="ayuda">
                Puedes utilizar una clave corta para esta práctica.
                Recuerda introducir exactamente la misma clave al descifrar.
            </span>

            <div class="botones">
                <button type="submit" name="cifrar" value="1">
                    Cifrar mensaje
                </button>

                <button
                    type="submit"
                    name="descifrar"
                    value="1"
                    class="secundario"
                >
                    Descifrar mensaje
                </button>
            </div>
        </form>

        <?php if ($resultado !== ""): ?>
            <section class="resultado <?= htmlspecialchars($tipoResultado, ENT_QUOTES, "UTF-8") ?>">
                <strong>Resultado</strong>
                <p><?= htmlspecialchars($resultado, ENT_QUOTES, "UTF-8") ?></p>
            </section>
        <?php endif; ?>

        <?php if ($mensajeCifrado !== ""): ?>
            <section class="panel">
                <h3>Mensaje cifrado (Base64)</h3>
                <div class="contenido"><?= htmlspecialchars($mensajeCifrado, ENT_QUOTES, "UTF-8") ?></div>
            </section>
        <?php endif; ?>

        <?php if ($mensajeDescifrado !== ""): ?>
            <section class="panel">
                <h3>Mensaje original recuperado</h3>
                <div class="contenido"><?= htmlspecialchars($mensajeDescifrado, ENT_QUOTES, "UTF-8") ?></div>
            </section>
        <?php endif; ?>

        <p class="requisito">
            <strong>Importante:</strong>
            conserva el texto cifrado y recuerda la clave utilizada.
            La clave no se guarda después de enviar el formulario.
        </p>
    </main>
</body>
</html>

