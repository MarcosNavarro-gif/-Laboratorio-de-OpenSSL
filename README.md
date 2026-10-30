# Laboratorio – Implementación de Criptografía con PHP, OpenSSL y Sodium

> **Universidad Tecnológica de Panamá**  
> Facultad de Ingeniería de Sistemas Computacionales  
> Campus Víctor Levi Sasso  
> **Curso:** [Nombre de la asignatura]  
> **Unidad:** Criptografía y seguridad informática

---

## 1. Introducción

En este laboratorio se desarrollaron diferentes ejercicios prácticos relacionados con la criptografía, utilizando PHP y las librerías OpenSSL y Libsodium (Sodium).

El propósito de la práctica fue comprender cómo funcionan los mecanismos de cifrado y descifrado, la generación de claves criptográficas, la creación de certificados digitales y los procesos de firma y verificación de mensajes.

Durante el desarrollo se implementaron formularios web que permiten interactuar con las funciones criptográficas, introduciendo mensajes y claves para observar los resultados de las operaciones.

Se trabajó con dos enfoques principales:

- **OpenSSL:** para implementar cifrado simétrico AES, generación de certificados X.509, creación de claves RSA y firmas digitales.
- **Sodium:** para explorar una alternativa moderna de criptografía que permite realizar operaciones de cifrado autenticado y otras funciones criptográficas.

---

## 2. Objetivos

- Comprender los fundamentos de la criptografía simétrica y asimétrica.
- Implementar cifrado y descifrado de mensajes mediante OpenSSL.
- Generar claves públicas y privadas utilizando RSA.
- Crear certificados digitales autofirmados X.509.
- Implementar mecanismos de firma y verificación de mensajes.
- Comprender el funcionamiento de Libsodium y sus principales ventajas.
- Desarrollar formularios web en PHP para interactuar con las operaciones criptográficas.
- Comprobar la integridad, autenticidad y confidencialidad de los datos mediante pruebas prácticas.

---

## 3. Requisitos previos y tecnologías

| Tecnología | Versión / uso |
|---|---|
| Sistema operativo | Windows |
| PHP | [Indicar versión instalada] |
| WampServer | Entorno de desarrollo local |
| Apache | Servidor web local |
| OpenSSL | Generación de claves, certificados y operaciones criptográficas |
| Libsodium | Funciones criptográficas modernas |
| HTML5 | Estructura de los formularios |
| CSS3 | Diseño e interfaz de los ejercicios |
| Editor de código | Visual Studio Code |

### Extensiones necesarias de PHP

Es necesario comprobar que las siguientes extensiones estén habilitadas en PHP:

- `openssl`
- `sodium`

Se pueden verificar ejecutando:

```bash
php -m
```

También se puede consultar la información de PHP mediante:

```php
<?php
phpinfo();
?>
```

---

## 4. Estructura del proyecto

El proyecto está organizado en diferentes archivos PHP, cada uno encargado de una operación criptográfica.

```text
SeguridadOpenSSL/
│
├── Encriptacion.php
│
├── Sodium.php
│
├── FirmaOtra.php
│
├── FirmarMesaje.php
│
├── Firma7.php
│
├── keysCert/
│   ├── certout.pem
│   ├── privkey.pem
│   ├── solicitud.csr
│   └── firma_mensaje.txt
│
└── keys/
    ├── private_key.pem
    ├── public_key.pem
    └── signature.dat
```

**Descripción de los archivos:**

| Archivo | Función |
|---|---|
| `Encriptacion.php` | Cifrado y descifrado de mensajes mediante AES-128-CBC. |
| `Sodium.php` | Implementación de operaciones criptográficas utilizando Libsodium. |
| `FirmaOtra.php` | Generación de claves RSA, solicitud CSR y certificado autofirmado X.509. |
| `FirmarMesaje.php` | Firma y verificación de mensajes utilizando el certificado generado. |
| `Firma7.php` | Generación independiente de claves RSA, firma y verificación de datos. |
| `keysCert/` | Almacena el certificado y la clave privada utilizados por FirmaOtra y FirmarMesaje. |
| `keys/` | Almacena las claves y la firma generadas por Firma7. |

> Nota: Los nombres de los archivos deben coincidir con los que se encuentren realmente en el repositorio.

---

## 5. Implementación de los ejercicios

### 5.1. Cifrado y descifrado con OpenSSL – Encriptacion.php

En este ejercicio se implementó el algoritmo AES-128-CBC mediante las funciones `openssl_encrypt()` y `openssl_decrypt()`.

El programa permite introducir un mensaje y una clave desde un formulario.

La clave debe tener exactamente 16 bytes para utilizar AES-128. También se genera un vector de inicialización (IV) aleatorio para cada operación de cifrado.

**Funciones utilizadas:**

- `openssl_cipher_iv_length()`: obtiene la longitud necesaria del IV.
- `openssl_random_pseudo_bytes()`: genera el IV.
- `openssl_encrypt()`: cifra el mensaje.
- `openssl_decrypt()`: recupera el mensaje original.
- `htmlspecialchars()`: protege la salida HTML al mostrar los resultados.

**Funcionamiento:**

1. El usuario introduce un mensaje.
2. Introduce una clave de 16 bytes.
3. El sistema genera un IV aleatorio.
4. Se cifra el mensaje mediante AES-128-CBC.
5. Se descifra utilizando la misma clave y el mismo IV.
6. Se muestran el texto cifrado y el mensaje recuperado.

**Resultado esperado:** El mensaje descifrado debe coincidir con el mensaje original.

### 5.2. Cifrado con Libsodium – Sodium.php

Este ejercicio permite conocer e implementar funciones criptográficas mediante la librería Libsodium.

Sodium proporciona herramientas para realizar operaciones de cifrado, autenticación, generación de claves, hashing y firmas digitales.

Entre sus principales características se encuentran:

- Implementación de algoritmos criptográficos modernos.
- Funciones diseñadas para reducir errores comunes de implementación.
- Cifrado autenticado, que permite comprobar la integridad de los datos.
- Operaciones criptográficas optimizadas.
- Compatibilidad con diferentes lenguajes de programación, incluyendo PHP.

**Principales algoritmos y mecanismos de Libsodium:**

| Algoritmo o mecanismo | Aplicación |
|---|---|
| XChaCha20-Poly1305 | Cifrado autenticado de datos. |
| XSalsa20-Poly1305 | Cifrado autenticado mediante `crypto_secretbox`. |
| Curve25519 | Intercambio de claves. |
| Ed25519 | Firma digital y verificación. |
| BLAKE2b | Funciones hash. |
| Argon2id | Derivación de claves a partir de contraseñas. |

**Funcionamiento del ejercicio:**

1. El usuario introduce los datos solicitados en el formulario.
2. El programa utiliza las funciones de Sodium para realizar la operación criptográfica.
3. Se genera el resultado cifrado.
4. Se realiza la operación inversa para recuperar los datos, cuando corresponde.
5. Se muestran los resultados en pantalla.

**Resultado esperado:** Comprobar el funcionamiento de Libsodium y observar cómo permite realizar operaciones criptográficas mediante funciones especializadas.

> La función y el algoritmo concretos utilizados deben corresponder a la implementación final de `Sodium.php`.

### 5.3. Generación de certificados digitales – FirmaOtra.php

Este ejercicio permite generar un par de claves RSA, crear una solicitud de firma de certificado (CSR) y producir un certificado digital autofirmado X.509.

Se implementó un formulario para introducir los datos que formarán parte de la información del certificado:

- País.
- Localidad.
- Organización.
- Nombre común (Common Name).
- Correo electrónico.

**Funciones utilizadas:**

- `openssl_pkey_new()`: genera el par de claves RSA.
- `openssl_pkey_get_details()`: obtiene los detalles de la clave pública.
- `openssl_pkey_export()`: exporta la clave privada.
- `openssl_csr_new()`: crea la solicitud de certificado.
- `openssl_csr_sign()`: firma la solicitud y genera el certificado autofirmado.
- `openssl_x509_export()`: exporta el certificado.
- `openssl_csr_export()`: exporta la solicitud CSR.
- `file_put_contents()`: guarda los archivos generados.

**Funcionamiento:**

1. El usuario completa el formulario.
2. El programa valida los datos ingresados.
3. Se generan las claves RSA de 2048 bits.
4. Se exporta la clave privada.
5. Se crea la solicitud CSR.
6. Se genera un certificado autofirmado con una validez de 365 días.
7. Se guardan los archivos dentro de la carpeta `keysCert`.

**Archivos generados:**

| Archivo | Descripción |
|---|---|
| `certout.pem` | Certificado digital X.509. |
| `privkey.pem` | Clave privada RSA. |
| `solicitud.csr` | Solicitud de firma del certificado. |

**Importante:** El certificado es autofirmado, por lo que sirve para esta práctica, pero no equivale a un certificado emitido y validado por una autoridad certificadora de confianza.

### 5.4. Firma y verificación de mensajes – FirmarMesaje.php

Este ejercicio utiliza los archivos generados anteriormente en `FirmaOtra.php`.

Su propósito es implementar un mecanismo de firma digital que permita comprobar la autenticidad e integridad de un mensaje.

El formulario permite introducir:

- El mensaje que se desea firmar.
- La firma digital en formato Base64, cuando se desea verificar un mensaje.

También dispone de dos acciones:

- **Firmar mensaje:** genera una firma digital utilizando la clave privada.
- **Verificar firma:** comprueba si la firma corresponde al mensaje introducido mediante la clave pública del certificado.

**Funciones utilizadas:**

- `file_get_contents()`: obtiene el contenido de los archivos.
- `openssl_pkey_get_private()`: carga la clave privada.
- `openssl_pkey_get_public()`: obtiene la clave pública del certificado.
- `openssl_sign()`: genera la firma digital.
- `base64_encode()`: convierte la firma binaria a Base64.
- `base64_decode()`: recupera la firma binaria.
- `openssl_verify()`: verifica la firma digital.

**Funcionamiento de la firma:**

1. Se introduce el mensaje.
2. Se carga la clave privada generada por `FirmaOtra.php`.
3. Se firma el mensaje mediante SHA-256.
4. La firma se convierte a Base64.
5. Se muestra la firma generada.

**Funcionamiento de la verificación:**

1. Se introduce el mensaje original.
2. Se pega la firma Base64 correspondiente.
3. El programa carga el certificado y obtiene su clave pública.
4. Se decodifica la firma.
5. Se ejecuta `openssl_verify()`.
6. Se muestra si la firma es válida o inválida.

**Resultados posibles:**

- Firma válida: el mensaje coincide con el contenido firmado y la firma corresponde a la clave pública.
- Firma inválida: el mensaje pudo haber sido modificado o la firma no corresponde.
- Error de verificación: ocurrió un problema al cargar las claves o ejecutar la operación.

**Dependencia:** Es necesario ejecutar primero `FirmaOtra.php` para generar el certificado y la clave privada.

### 5.5. Generación de claves y firma independiente – Firma7.php

Este ejercicio implementa otro procedimiento de firma digital utilizando RSA y SHA-256.

A diferencia de `FirmarMesaje.php`, este programa genera su propio par de claves y no necesita utilizar el certificado generado por `FirmaOtra.php`.

**Funciones utilizadas:**

- `openssl_pkey_new()`: genera las claves RSA.
- `openssl_pkey_get_details()`: obtiene la clave pública.
- `openssl_pkey_export()`: exporta la clave privada.
- `file_put_contents()`: guarda las claves y la firma.
- `file_get_contents()`: recupera la clave privada.
- `openssl_get_privatekey()`: carga la clave privada.
- `openssl_sign()`: firma los datos.
- `openssl_verify()`: verifica la firma.

**Funcionamiento:**

1. Se establece el texto que se desea firmar.
2. Se genera un par de claves RSA.
3. Se guardan la clave pública y privada.
4. Se carga nuevamente la clave privada desde el archivo.
5. Se genera la firma digital utilizando SHA-256.
6. Se guarda la firma en formato binario.
7. Se verifica la firma utilizando la clave pública.

**Archivos generados:**

| Archivo | Descripción |
|---|---|
| `private_key.pem` | Clave privada RSA. |
| `public_key.pem` | Clave pública RSA. |
| `signature.dat` | Firma digital en formato binario. |

**Resultado esperado:** El programa debe mostrar un mensaje indicando si la firma es válida o si los datos pudieron haber sido alterados.

---

## 6. Documentación técnica de funciones (D.3.1)

A continuación se presenta un resumen de las principales funciones criptográficas utilizadas durante el laboratorio.

| Función | Propósito | Parámetros principales | Valor devuelto |
|---|---|---|---|
| `openssl_encrypt()` | Cifrar información. | Datos, algoritmo, clave, opciones e IV. | Texto cifrado o `false`. |
| `openssl_decrypt()` | Descifrar información. | Datos cifrados, algoritmo, clave, opciones e IV. | Texto original o `false`. |
| `openssl_pkey_new()` | Generar un par de claves. | Configuración criptográfica. | Objeto de clave o `false`. |
| `openssl_pkey_export()` | Exportar una clave privada. | Clave, variable de salida y configuración. | `true` o `false`. |
| `openssl_pkey_get_details()` | Obtener información de una clave. | Clave criptográfica. | Array con información de la clave o `false`. |
| `openssl_csr_new()` | Crear una solicitud CSR. | Datos del certificado, clave privada y configuración. | Solicitud CSR o `false`. |
| `openssl_csr_sign()` | Firmar una solicitud y generar un certificado. | CSR, certificado emisor, clave privada y días de validez. | Certificado o `false`. |
| `openssl_x509_export()` | Exportar un certificado. | Certificado y variable de salida. | `true` o `false`. |
| `openssl_sign()` | Firmar datos digitalmente. | Datos, firma de salida, clave privada y algoritmo. | `true` o `false`. |
| `openssl_verify()` | Verificar una firma digital. | Datos, firma, clave pública y algoritmo. | `1`, `0` o `-1`/`false`, según la versión y el resultado. |
| `base64_encode()` | Convertir datos binarios a Base64. | Cadena de datos. | Cadena codificada. |
| `base64_decode()` | Decodificar información Base64. | Cadena codificada y modo estricto opcional. | Datos binarios o `false`. |

### 6.1. Diferencia entre cifrado simétrico y asimétrico

**Cifrado simétrico:**

Utiliza una misma clave para cifrar y descifrar información. En este laboratorio se utilizó AES-128-CBC.

**Cifrado asimétrico:**

Utiliza un par de claves relacionadas matemáticamente: una pública y otra privada. En la práctica se utilizó RSA para generar claves y realizar firmas digitales.

**Firma digital:**

Permite comprobar la integridad de los datos y verificar que la firma corresponde a una clave pública determinada. Se utilizó SHA-256 como algoritmo de resumen para las firmas RSA.

---

## 7. Instalación y ejecución del proyecto

### 7.1. Preparación del entorno

1. Instalar WampServer.
2. Comprobar que Apache se encuentre funcionando.
3. Habilitar las extensiones `openssl` y `sodium` en PHP.
4. Descargar o clonar este repositorio.

### 7.2. Ubicación del proyecto

Colocar la carpeta del proyecto dentro del directorio:

```text
C:\wamp64\www\
```

Ejemplo:

```text
C:\wamp64\www\SeguridadOpenSSL\
```

### 7.3. Configuración de OpenSSL

En `FirmaOtra.php` se debe comprobar que la ruta de configuración de OpenSSL corresponda a la instalación local.

Ejemplo:

```php
define(
    "OPEN_SSL_CONF_PATH",
    "C:\\wamp64\\bin\\apache\\apache2.4.65\\conf\\openssl.cnf"
);
```

La ruta puede variar dependiendo de la versión instalada de WampServer y OpenSSL.

### 7.4. Ejecución

Abrir el navegador e ingresar a:

```text
http://localhost/SeguridadOpenSSL/
```

Los archivos pueden ejecutarse individualmente desde el navegador:

```text
http://localhost/SeguridadOpenSSL/Encriptacion.php
http://localhost/SeguridadOpenSSL/Sodium.php
http://localhost/SeguridadOpenSSL/FirmaOtra.php
http://localhost/SeguridadOpenSSL/FirmarMesaje.php
http://localhost/SeguridadOpenSSL/Firma7.php
```

**Orden recomendado para las pruebas:**

1. Ejecutar `Encriptacion.php`.
2. Ejecutar `Sodium.php`.
3. Ejecutar `FirmaOtra.php` para generar el certificado y las claves.
4. Ejecutar `FirmarMesaje.php` para realizar las pruebas de firma y verificación.
5. Ejecutar `Firma7.php` para comprobar el procedimiento independiente de firma digital.

---

## 8. Pruebas y resultados

### 8.1. Prueba de cifrado AES

**Captura 1 – Formulario de cifrado y descifrado AES**

[AGREGAR CAPTURA]

Descripción: Se introduce un mensaje y una clave de 16 bytes. El sistema cifra el contenido y posteriormente lo descifra utilizando la misma clave y el IV generado.

### 8.2. Prueba de Libsodium

**Captura 2 – Formulario de operaciones con Sodium**

[AGREGAR CAPTURA]

Descripción: Se comprueba la ejecución de las funciones criptográficas de Sodium y se visualiza el resultado de la operación implementada.

### 8.3. Generación del certificado digital

**Captura 3 – Formulario de FirmaOtra.php**

[AGREGAR CAPTURA]

Descripción: Se completan los datos solicitados para generar el certificado X.509 y el par de claves RSA.

**Captura 4 – Certificado generado correctamente**

[AGREGAR CAPTURA]

Descripción: Se muestra el resultado de la generación del certificado y la creación de los archivos correspondientes.

### 8.4. Firma y verificación de mensajes

**Captura 5 – Formulario de FirmarMesaje.php**

[AGREGAR CAPTURA]

Descripción: Se introduce un mensaje para generar su firma digital.

**Captura 6 – Verificación de firma válida**

[AGREGAR CAPTURA]

Descripción: Se introduce el mensaje original y la firma Base64 correspondiente. El sistema confirma que la firma es válida.

**Captura 7 – Verificación de firma inválida**

[AGREGAR CAPTURA]

Descripción: Se modifica el mensaje original y se comprueba que la verificación falla al no coincidir con el contenido firmado.

### 8.5. Firma digital independiente

**Captura 8 – Ejecución de Firma7.php**

[AGREGAR CAPTURA]

Descripción: Se generan las claves RSA, se firma el texto y se verifica la firma utilizando la clave pública.

---

## 9. Dificultades encontradas y soluciones

### Dificultad 1: Configuración de la ruta de OpenSSL

**Problema:** PHP no encontraba el archivo `openssl.cnf` al generar las claves y certificados.

**Solución:** Se revisó la ruta de configuración y se adaptó al directorio de instalación de WampServer.

### Dificultad 2: Generación y almacenamiento de certificados

**Problema:** Era necesario guardar los archivos generados para utilizarlos posteriormente en otros ejercicios.

**Solución:** Se implementó la creación de la carpeta `keysCert` y el almacenamiento del certificado, la solicitud CSR y la clave privada.

### Dificultad 3: Dependencia entre FirmaOtra y FirmarMesaje

**Problema:** El archivo de firma necesitaba una clave privada y un certificado previamente generados.

**Solución:** Se estableció que `FirmaOtra.php` debe ejecutarse primero y que sus archivos generados sean utilizados por `FirmarMesaje.php`.

### Dificultad 4: Verificación de firmas digitales

**Problema:** Era necesario proporcionar tanto el mensaje original como su firma para comprobar su autenticidad.

**Solución:** Se modificó el formulario para permitir introducir manualmente el mensaje y la firma en Base64, incorporando botones independientes para firmar y verificar.

### Dificultad 5: Funciones obsoletas de OpenSSL

**Problema:** PHP mostró advertencias relacionadas con el uso de `openssl_free_key()`.

**Solución:** Se retiraron las llamadas innecesarias a esa función obsoleta, evitando las advertencias durante la ejecución.

---

## 10. Consideraciones de seguridad

Durante el desarrollo de los ejercicios se deben tener presentes las siguientes consideraciones:

- Las claves privadas deben mantenerse protegidas y no deben publicarse en repositorios públicos.
- Los certificados y claves generados para este laboratorio son de prueba.
- AES-128-CBC requiere gestionar correctamente el IV y no proporciona autenticación por sí solo.
- La clave utilizada para AES-128 debe tener una longitud de 16 bytes.
- Las firmas digitales permiten comprobar integridad y autenticidad, pero no cifran el contenido del mensaje.
- Los archivos de claves deben almacenarse con permisos adecuados.
- Las contraseñas y claves reales no deben incluirse directamente en el código fuente.

---

## 11. Conclusiones personales

El desarrollo de este laboratorio permitió comprender de manera práctica cómo se aplican diferentes mecanismos criptográficos mediante PHP.

Se logró diferenciar el funcionamiento del cifrado simétrico y asimétrico, además de comprender la importancia de las claves públicas y privadas en los procesos de firma digital.

Uno de los aspectos más importantes fue la implementación de certificados digitales y su utilización para firmar y verificar mensajes. También se comprobó cómo una modificación en el contenido puede provocar que una firma deje de ser válida.

Por otra parte, la utilización de Sodium permitió conocer una alternativa moderna para implementar operaciones criptográficas, destacando la importancia del cifrado autenticado y de utilizar bibliotecas especializadas.

Finalmente, el laboratorio permitió reforzar conocimientos de PHP, OpenSSL, seguridad informática y desarrollo web, además de comprender que la criptografía no consiste únicamente en ocultar información, sino también en garantizar su integridad y autenticidad.

---

## 12. Referencias

1. PHP Documentation. (s. f.). *OpenSSL Functions*. https://www.php.net/manual/en/book.openssl.php
2. PHP Documentation. (s. f.). *Sodium Functions*. https://www.php.net/manual/en/book.sodium.php
3. Libsodium. (s. f.). *Libsodium Documentation*. https://doc.libsodium.org/
4. OpenSSL. (s. f.). *OpenSSL Documentation*. https://docs.openssl.org/
5. NIST. (s. f.). *Advanced Encryption Standard (AES)*. https://csrc.nist.gov/pubs/fips/197/final

---

## 13. Autor

Este laboratorio ha sido desarrollado por el estudiante de la Universidad Tecnológica de Panamá:

- **Nombre:** [Nombre del estudiante]
- **Cédula:** [Número de cédula]
- **Correo:** [Correo electrónico]
- **Curso:** [Nombre de la asignatura]
- **Instructor:** Ing. Irina Fong
- **Fecha:** [Fecha de entrega]

