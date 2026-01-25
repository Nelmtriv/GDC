<?php
// =========================
// CONTROLO DE SESSÃO GLOBAL
// =========================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   BLOQUEAR CACHE (ANTI VOLTAR)
========================= */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

/* =========================
   VALIDAR SESSÃO
========================= */
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo'])) {
    header("Location: /GDC/src/View/Auth/login.php");
    exit();
}