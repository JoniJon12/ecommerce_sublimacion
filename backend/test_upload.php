<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['imagen'])) {
    $nombre = uniqid() . "_" . basename($_FILES['imagen']['name']);
    $ruta = __DIR__ . "/uploads/" . $nombre;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
        echo "Imagen subida con éxito: $nombre";
    } else {
        echo "Error al subir la imagen";
    }
} else {
?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="imagen">
        <button type="submit">Probar subida</button>
    </form>
<?php } ?>
