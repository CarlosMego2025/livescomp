<?php
require_once 'includes/config.php';
require_once 'classes/Producto.php';
require_once 'classes/Categoria.php';

// Configurar título de página
$tituloPagina = "Nombre de la página";

// Obtener datos necesarios (ej. categorías para el menú)
$categoriaModel = new Categoria();
$categorias = $categoriaModel->getAll();

// Incluir header
include 'includes/header.php';
