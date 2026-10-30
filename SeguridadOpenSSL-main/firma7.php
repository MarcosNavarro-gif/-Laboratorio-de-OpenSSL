<?php

// Datos que se quieren firmar:
$datos = "";
$resultado = "";
$firmaBase64 = "";
$tipoResultado = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $datos = trim($_POST["datos"] ?? "");

    if ($datos === "") {

        $resultado = "Error: debes escribir los datos que deseas firmar.";
        $tipoResultado = "error";

    } else {

        // Se deben crear dos claves aparejadas, una clave pública y otra privada
        // A continuación el array de configuración para la creación del juego de claves

        $configArgs = array(
            'config' => "C:\wamp64\bin\apache\apache2.4.65\conf\openssl.cnf", // <-- esta ruta es necesaria si trabajas con XAMPP
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        );

        // Verificar que la ruta de configuración exista
        if (!file_exists($configArgs['config'])) {

            $resultado = "Error: verifica la ruta del archivo openssl.cnf.";
            $tipoResultado = "error";

        } else {

            // Crear la carpeta para guardar las claves y la firma
            $directorio = __DIR__ . "/keys";

            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Generar el par de claves
            $resourceNewKeyPair = openssl_pkey_new($configArgs);

            if (!$resourceNewKeyPair) {

                $resultado = 'Puede que tengas problemas con la ruta indicada en el array de configuración "$configArgs": '
                    . (openssl_error_string() ?: "Error desconocido");
                $tipoResultado = "error";

            } else {

                // Obtengo del recurso $resourceNewKeyPair la clave pública como un string
                $details = openssl_pkey_get_details($resourceNewKeyPair);

                if ($details === false) {

                    $resultado = "Error al obtener la clave pública.";
                    $tipoResultado = "error";

                } else {

                    $publicKeyPem = $details['key'];

                    // Obtengo la clave privada como string dentro de la variable
                    // $privateKeyPem (la cual es pasada por referencia)
                    if (!openssl_pkey_export(
                        $resourceNewKeyPair,
                        $privateKeyPem,
                        null,
                        $configArgs
                    )) {

                        $resultado = "Error al exportar la clave privada: "
                            . (openssl_error_string() ?: "Error desconocido");
                        $tipoResultado = "error";

                    } else {

                        // Guardo la clave pública y privada en disco:
                        $guardadoClaves =
                            file_put_contents(
                                $directorio . "/private_key.pem",
                                $privateKeyPem
                            ) !== false &&
                            file_put_contents(
                                $directorio . "/public_key.pem",
                                $publicKeyPem
                            ) !== false;

                        if (!$guardadoClaves) {

                            $resultado = "Error al guardar las claves. Revisa los permisos de la carpeta.";
                            $tipoResultado = "error";

                        } else {

                            // Si bien ya tengo cargado el string de la clave privada,
                            // la voy a buscar a disco para verificar que el archivo
                            // private_key.pem haya sido correctamente generado:
                            $privateKeyPem = file_get_contents(
                                $directorio . "/private_key.pem"
                            );

                            // Obtengo la clave privada como recurso desde el string
                            $resourcePrivateKey = openssl_pkey_get_private($privateKeyPem);

                            if ($resourcePrivateKey === false) {

                                $resultado = "Error al cargar la clave privada guardada.";
                                $tipoResultado = "error";

                            } else {

                                // Crear la firma dentro de la variable $firma
                                // (la cual es pasada por referencia)
                                if (!openssl_sign(
                                    $datos,
                                    $firma,
                                    $resourcePrivateKey,
                                    OPENSSL_ALGO_SHA256
                                )) {

                                    $resultado = "Error al firmar los datos: "
                                        . (openssl_error_string() ?: "Error desconocido");
                                    $tipoResultado = "error";

                                } else {

                                    // Guardar la firma en disco:
                                    $guardadoFirma = file_put_contents(
                                        $directorio . "/signature.dat",
                                        $firma
                                    );

                                    if ($guardadoFirma === false) {

                                        $resultado = "Error al guardar la firma.";
                                        $tipoResultado = "error";

                                    } else {

                                        // Convertir la firma a Base64 para mostrarla
                                        $firmaBase64 = base64_encode($firma);

                                        // Comprobar la firma utilizando la clave pública
                                        $resultadoVerificacion = openssl_verify(
                                            $datos,
                                            $firma,
                                            $publicKeyPem,
                                            OPENSSL_ALGO_SHA256
                                        );

                                        if ($resultadoVerificacion === 1) {

                                            $resultado = "La firma es válida y los datos coinciden con los firmados.";
                                            $tipoResultado = "exito";

                                        } elseif ($resultadoVerificacion === 0) {

                                            $resultado = "La firma es inválida y/o los datos fueron alterados.";
                                            $tipoResultado = "error";

                                        } else {

                                            $resultado = "Error al verificar la firma: "
                                                . (openssl_error_string() ?: "Error desconocido");
                                            $tipoResultado = "error";
                                        }
                                    }
                                }

                                
                            }
                        }
                    }
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
    <title>Firma digital con RSA</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            margin: 0;
            padding: 30px 15px;
        }

        .contenedor {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #ccc;
        }

        h2, h3 {
            color: #243b53;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
        }

        textarea {
            width: 100%;
            min-height: 120px;
            padding: 10px;
            margin-top: 8px;
            box-sizing: border-box;
            border: 1px solid #bbb;
            border-radius: 5px;
            font-family: Arial, sans-serif;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 18px;
            background: #1769aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #104e80;
        }

        .resultado {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            overflow-wrap: anywhere;
        }

        .exito {
            background: #e8f4ea;
            color: #205b2b;
        }

        .error {
            background: #fdeaea;
            color: #922b21;
        }

        pre {
            background: #eee;
            padding: 12px;
            border-radius: 5px;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h2>Firma digital con RSA</h2>

    <p>
        Escribe el texto que deseas firmar. El sistema generará las claves,
        creará la firma digital y comprobará su validez.
    </p>

    <form method="POST" action="">

        <label for="datos">Datos que se quieren firmar:</label>

        <textarea
            id="datos"
            name="datos"
            required
        ><?= htmlspecialchars($datos, ENT_QUOTES, "UTF-8") ?></textarea>

        <button type="submit">Firmar y verificar</button>

    </form>

    <?php if ($resultado !== ""): ?>

        <div class="resultado <?= htmlspecialchars($tipoResultado) ?>">
            <strong>Resultado:</strong>
            <?= htmlspecialchars($resultado, ENT_QUOTES, "UTF-8") ?>
        </div>

    <?php endif; ?>

    <?php if ($firmaBase64 !== ""): ?>

        <h3>Firma digital (Base64)</h3>

        <pre><?= htmlspecialchars($firmaBase64, ENT_QUOTES, "UTF-8") ?></pre>

        <h3>Archivos generados</h3>

        <ul>
            <li><code>keys/private_key.pem</code>: clave privada.</li>
            <li><code>keys/public_key.pem</code>: clave pública.</li>
            <li><code>keys/signature.dat</code>: firma binaria.</li>
        </ul>

    <?php endif; ?>

</div>

</body>
</html>