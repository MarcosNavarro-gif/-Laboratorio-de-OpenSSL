<?php
$resultado = "";
$tipoResultado = "";

$mensaje = $_POST["mensaje"] ?? "";
$firma = $_POST["firma"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $certPath = __DIR__ . "/keysCert/certout.pem";
    $keyPath = __DIR__ . "/keysCert/privkey.pem";

    if (trim($mensaje) === "") {
        $resultado = "Escribe el mensaje que deseas firmar o verificar.";
        $tipoResultado = "error";
    } elseif (
        isset($_POST["verificar"]) &&
        trim($firma) === ""
    ) {
        $resultado = "Introduce la firma en Base64 para verificar el mensaje.";
        $tipoResultado = "error";
    } elseif (!is_file($certPath) || !is_file($keyPath)) {
        $resultado = "Primero ejecuta FirmaOtra.php para generar el certificado y la clave privada.";
        $tipoResultado = "error";
    } else {
        $privateKey = openssl_pkey_get_private(
            file_get_contents($keyPath)
        );

        $cert = file_get_contents($certPath);
        $publicKey = openssl_pkey_get_public($cert);

        if (!$privateKey || !$publicKey) {
            $resultado = "No se pudieron cargar las claves o el certificado.";
            $tipoResultado = "error";
        } elseif (isset($_POST["verificar"])) {
            // Convertir la firma Base64 a formato binario.
            $firmaBinaria = base64_decode(trim($firma), true);

            if ($firmaBinaria === false) {
                $resultado = "La firma debe estar en un formato Base64 válido.";
                $tipoResultado = "error";
            } else {
                // Comprobar la firma con el mensaje y la clave pública.
                $verificacion = openssl_verify(
                    $mensaje,
                    $firmaBinaria,
                    $publicKey,
                    OPENSSL_ALGO_SHA256
                );

                if ($verificacion === 1) {
                    $resultado = "Firma válida: el mensaje coincide con la firma.";
                    $tipoResultado = "exito";
                } elseif ($verificacion === 0) {
                    $resultado = "Firma inválida: el mensaje o la firma no coinciden.";
                    $tipoResultado = "error";
                } else {
                    $resultado = "Ocurrió un error al verificar la firma: "
                        . (openssl_error_string() ?: "error de OpenSSL no especificado.");
                    $tipoResultado = "error";
                }
            }
        } elseif (isset($_POST["firmar"])) {
            // Crear la firma digital con la clave privada.
            if (
                openssl_sign(
                    $mensaje,
                    $firmaBinaria,
                    $privateKey,
                    OPENSSL_ALGO_SHA256
                )
            ) {
                $firma = base64_encode($firmaBinaria);

                // Guardar la firma generada para utilizarla después.
                file_put_contents(
                    __DIR__ . "/keysCert/firma_mensaje.txt",
                    $firma
                );

                $resultado = "Firma generada correctamente:\n\n" . $firma;
                $tipoResultado = "exito";
            } else {
                $resultado = "No se pudo generar la firma: "
                    . (openssl_error_string() ?: "error de OpenSSL no especificado.");
                $tipoResultado = "error";
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
    <title>Firma y verificación de mensajes</title>

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
            margin-bottom: 10px;
            color: #243b53;
            font-size: 28px;
        }

        .descripcion {
            color: #526579;
            margin-bottom: 25px;
            line-height: 1.5;
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

        textarea {
            display: block;
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-family: Arial, sans-serif;
            font-size: 15px;
            resize: vertical;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        textarea:focus {
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
            transition: background 0.2s;
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
            white-space: pre-wrap;
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

        .requisito {
            margin-top: 25px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            color: #526579;
            font-size: 14px;
            line-height: 1.5;
        }

        a {
            color: #1769aa;
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
        <span class="etiqueta">FIRMA DIGITAL</span>

        <h1>Firma y verificación de mensajes</h1>

        <p class="descripcion">
            Genera una firma digital para un mensaje o comprueba si una firma
            corresponde al contenido introducido.
        </p>

        <form method="post">
            <label for="mensaje">Mensaje</label>
            <textarea
                id="mensaje"
                name="mensaje"
                rows="5"
                required
                placeholder="Escribe aquí el mensaje que deseas firmar o verificar..."
            ><?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?></textarea>

            <label for="firma">Firma Base64</label>
            <textarea
                id="firma"
                name="firma"
                rows="4"
                placeholder="Pega aquí la firma generada anteriormente..."
            ><?= htmlspecialchars($firma, ENT_QUOTES, "UTF-8") ?></textarea>

            <span class="ayuda">
                Para verificar, introduce tanto el mensaje original como su firma.
                Para firmar, solo necesitas escribir el mensaje.
            </span>

            <div class="botones">
                <button type="submit" name="firmar" value="1">
                    Firmar mensaje
                </button>

                <button
                    type="submit"
                    name="verificar"
                    value="1"
                    class="secundario"
                >
                    Verificar firma
                </button>
            </div>
        </form>

        <?php if ($resultado !== ""): ?>
            <section
                class="resultado <?= htmlspecialchars($tipoResultado, ENT_QUOTES, "UTF-8") ?>"
                aria-live="polite"
            >
                <strong>Resultado</strong>

                <p><?= htmlspecialchars($resultado, ENT_QUOTES, "UTF-8") ?></p>
            </section>
        <?php endif; ?>

        <p class="requisito">
            <strong>Requisito:</strong>
            genera primero el certificado y la clave desde
            <a href="FirmaOtra.php">FirmaOtra.php</a>.
        </p>
    </main>
</body>
</html>