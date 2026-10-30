<?php
//✅ ¿Qué puedes hacer con ese certificado?
/*certificados X.509 están orientados a HTTPS o servidores web,
pero también se usan en otras aplicaciones, como la firma y
verificación de archivos o datos.
Ese bloque que te generó es un certificado X.509 en formato PEM, 
y todo indica que la creación y firma del certificado fue exitosa
*/


define("OPEN_SSL_CONF_PATH", "C:\\wamp64\\bin\\apache\\apache2.4.65\\conf\\openssl.cnf");
define("OPEN_SSL_CERT_DAYS_VALID", 365);

$mensaje = "";
$certout = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Obtener los datos del formulario
    $dn = array(
        "countryName" => strtoupper(trim($_POST["pais"] ?? "")),
        "localityName" => trim($_POST["localidad"] ?? ""),
        "organizationName" => trim($_POST["organizacion"] ?? ""),
        "commonName" => trim($_POST["nombre"] ?? ""),
        "emailAddress" => trim($_POST["correo"] ?? "")
    );

    // Validar los datos
    if (
        strlen($dn["countryName"]) !== 2 ||
        $dn["localityName"] === "" ||
        $dn["organizationName"] === "" ||
        $dn["commonName"] === "" ||
        !filter_var($dn["emailAddress"], FILTER_VALIDATE_EMAIL)
    ) {
        $mensaje = "Error: verifica los datos ingresados. El país debe tener dos letras y el correo debe ser válido.";
    } elseif (!file_exists(OPEN_SSL_CONF_PATH)) {

        $mensaje = "Error: no se encontró el archivo openssl.cnf. Revisa la ruta de OpenSSL.";

    } else {

        $configArgs = array(
            "config" => OPEN_SSL_CONF_PATH,
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA
        );

        // 1. Generar las claves RSA
        $keyPair = openssl_pkey_new($configArgs);

        if (!$keyPair) {
            $mensaje = "Error al generar las claves: " .
                (openssl_error_string() ?: "Error desconocido");
        } else {

            // 2. Exportar la clave privada
            if (!openssl_pkey_export($keyPair, $privkey, null, $configArgs)) {

                $mensaje = "Error al exportar la clave privada.";

            } else {

                // 3. Crear la solicitud CSR
                $csr = openssl_csr_new($dn, $keyPair, $configArgs);

                if (!$csr) {

                    $mensaje = "Error al crear la CSR: " .
                        (openssl_error_string() ?: "Error desconocido");

                } else {

                    // 4. Crear el certificado autofirmado
                    $cert = openssl_csr_sign(
                        $csr,
                        null,
                        $keyPair,
                        OPEN_SSL_CERT_DAYS_VALID,
                        $configArgs
                    );

                    if (!$cert) {

                        $mensaje = "Error al firmar el certificado: " .
                            (openssl_error_string() ?: "Error desconocido");

                    } elseif (!openssl_x509_export($cert, $certout)) {

                        $mensaje = "Error al exportar el certificado.";

                    } else {

                        // 5. Exportar la solicitud CSR
                        if (!openssl_csr_export($csr, $csrout)) {

                            $mensaje = "Error al exportar la CSR.";

                        } else {

                            // 6. Crear la carpeta para guardar los archivos
                            $directorio = __DIR__ . "/keysCert";

                            if (!is_dir($directorio)) {
                                mkdir($directorio, 0755, true);
                            }

                            // 7. Guardar los archivos
                            $guardado =
                                is_dir($directorio) &&
                                file_put_contents(
                                    $directorio . "/certout.pem",
                                    $certout
                                ) !== false &&
                                file_put_contents(
                                    $directorio . "/privkey.pem",
                                    $privkey
                                ) !== false &&
                                file_put_contents(
                                    $directorio . "/solicitud.csr",
                                    $csrout
                                ) !== false;

                            if ($guardado) {
                                $mensaje = "Certificado generado y archivos guardados correctamente.";
                            } else {
                                $mensaje = "Error al guardar los archivos. Revisa los permisos de la carpeta.";
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
    <title>Generador de certificados X.509</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            margin: 0;
            padding: 30px 15px;
        }

        .contenedor {
            max-width: 650px;
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
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            box-sizing: border-box;
            border: 1px solid #bbb;
            border-radius: 5px;
        }

        button {
            width: 100%;
            margin-top: 22px;
            padding: 12px;
            background: #1769aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #104e80;
        }

        .mensaje {
            margin-top: 20px;
            padding: 12px;
            background: #e8f4ea;
            border-radius: 5px;
            overflow-wrap: anywhere;
        }

        pre {
            padding: 12px;
            background: #eee;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h2>Generador de certificados X.509</h2>

    <p>Completa los datos para generar un certificado autofirmado.</p>

    <form method="POST" action="">

        <label for="pais">País (dos letras)</label>
        <input
            type="text"
            id="pais"
            name="pais"
            maxlength="2"
            value="<?= htmlspecialchars($_POST["pais"] ?? "PA") ?>"
            required
        >

        <label for="localidad">Localidad</label>
        <input
            type="text"
            id="localidad"
            name="localidad"
            value="<?= htmlspecialchars($_POST["localidad"] ?? "") ?>"
            required
        >

        <label for="organizacion">Organización</label>
        <input
            type="text"
            id="organizacion"
            name="organizacion"
            value="<?= htmlspecialchars($_POST["organizacion"] ?? "") ?>"
            required
        >

        <label for="nombre">Nombre común (Common Name)</label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            value="<?= htmlspecialchars($_POST["nombre"] ?? "") ?>"
            required
        >

        <label for="correo">Correo electrónico</label>
        <input
            type="email"
            id="correo"
            name="correo"
            value="<?= htmlspecialchars($_POST["correo"] ?? "") ?>"
            required
        >

        <button type="submit">Generar certificado</button>

    </form>

    <?php if ($mensaje !== ""): ?>
        <div class="mensaje">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if ($certout !== ""): ?>
        <h3>Certificado generado (PEM)</h3>
        <pre><?= htmlspecialchars($certout) ?></pre>
    <?php endif; ?>

</div>

</body>
</html>