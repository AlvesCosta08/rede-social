@extends('layouts.app')

@section('title', 'Cartão - ' . $membro->nome)
@section('page-title', 'Cartão de Membro')
@section('page-subtitle', 'Apresente este cartão para validar sua identidade')

@section('styles')
<style>
    /* ============================================================
       CONTAINER
       ============================================================ */
    .cartao-container {
        max-width: 520px;
        margin: 0 auto;
        padding: 10px;
    }

    .cartao-wrapper {
        perspective: 1500px;
        width: 100%;
        max-width: 440px;
        margin: 0 auto;
        aspect-ratio: 1.58 / 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cartao {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.8s cubic-bezier(0.4, 0.2, 0.2, 1);
        border-radius: 24px;
        transform-origin: center center;
    }

    .cartao.virado {
        transform: rotateY(180deg);
    }

    .frente,
    .verso {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .frente {
        background: linear-gradient(145deg, #0f0c29, #302b63, #24243e);
        color: #f0ece3;
        z-index: 2;
        border: 1px solid rgba(212, 175, 55, 0.3);
        position: relative;
    }

    .frente::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 80%;
        height: 200%;
        background: radial-gradient(circle, rgba(212, 175, 55, 0.06) 0%, transparent 70%);
        transform: rotate(25deg);
        pointer-events: none;
    }

    .frente::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -20%;
        width: 60%;
        height: 60%;
        background: radial-gradient(circle, rgba(212, 175, 55, 0.04) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .frente .conteudo {
        position: relative;
        z-index: 1;
        height: 100%;
        padding: 20px 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .frente .header-cartao {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2px;
    }

    .logo-igreja {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo-igreja .cross-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        overflow: hidden;
        border-radius: 8px;
        background: transparent;
    }

    .logo-igreja .cross-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    .logo-igreja .cross-icon .logo-fallback {
        display: none;
        width: 100%;
        height: 100%;
    }

    .logo-igreja .cross-icon .logo-fallback svg {
        width: 100%;
        height: 100%;
    }

    .logo-igreja .logo-texto {
        line-height: 1;
    }

    .logo-igreja .logo-texto .logo-nome {
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 1.5px;
        background: linear-gradient(180deg, #f2d680, #d4af37, #b8960f);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .logo-igreja .logo-texto .logo-sub {
        font-size: 0.4rem;
        font-weight: 600;
        letter-spacing: 2px;
        color: rgba(255, 255, 255, 0.4);
        text-transform: uppercase;
    }

    .frente .chip {
        background: linear-gradient(145deg, #d4af37, #f2d680, #c9a84c);
        width: 40px;
        height: 30px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: #8b7536;
        box-shadow: inset 0 2px 6px rgba(255, 255, 255, 0.4), 0 2px 8px rgba(0, 0, 0, 0.3);
        flex-shrink: 0;
    }

    .frente .chip i {
        transform: rotate(90deg);
    }

    .status-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 2px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 16px;
        font-size: 0.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(76, 175, 80, 0.3);
        background: rgba(76, 175, 80, 0.15);
        color: #4caf50;
    }

    .status-badge.inativo {
        border-color: rgba(244, 67, 54, 0.3);
        background: rgba(244, 67, 54, 0.15);
        color: #f44336;
    }

    .status-badge i {
        font-size: 0.35rem;
    }

    .foto-membro {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
        position: relative;
    }

    .foto-membro .avatar {
        width: 48px;
        height: 48px;
        min-width: 48px;
        min-height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d4af37, #c9a84c);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        font-weight: 700;
        color: #1a1a2e;
        border: 2px solid rgba(212, 175, 55, 0.4);
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
    }

    .foto-membro .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .foto-membro .avatar .avatar-placeholder {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .foto-membro .info-nome {
        flex: 1;
        min-width: 0;
    }

    .foto-membro .info-nome .nome {
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        word-break: break-word;
        line-height: 1.2;
    }

    .foto-membro .info-nome .funcao {
        font-size: 0.6rem;
        opacity: 0.6;
        letter-spacing: 0.5px;
        word-break: break-word;
    }

    .btn-trocar-foto {
        position: absolute;
        bottom: -4px;
        right: -4px;
        background: rgba(108, 60, 225, 0.9);
        color: white;
        border: 2px solid white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        min-width: 20px;
        min-height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        z-index: 10;
        text-decoration: none;
        padding: 0;
    }

    .btn-trocar-foto:hover {
        transform: scale(1.15);
        background: #6c3ce1;
    }

    .btn-trocar-foto i {
        font-size: 0.5rem;
    }

    .btn-trocar-foto input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        top: 0;
        left: 0;
    }

    .frente .body-cartao {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 4px;
        padding: 2px 0;
    }

    .frente .numero-cartao {
        display: flex;
        gap: 6px;
        font-size: 0.75rem;
        letter-spacing: 2px;
        font-family: 'Courier New', monospace;
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        flex-wrap: wrap;
        justify-content: center;
    }

    .frente .numero-cartao span {
        background: rgba(255, 255, 255, 0.06);
        padding: 2px 8px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        font-size: 0.7rem;
    }

    .frente .numero-cartao .matricula-destaque {
        background: rgba(212, 175, 55, 0.15);
        border: 1px solid rgba(212, 175, 55, 0.2);
        color: #d4af37;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 2px 12px;
    }

    .frente .info-cartao {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 2px;
        padding: 4px 0;
        border-top: 1px solid rgba(212, 175, 55, 0.08);
        border-bottom: 1px solid rgba(212, 175, 55, 0.08);
    }

    .frente .info-cartao .info-item {
        display: flex;
        flex-direction: column;
        gap: 1px;
        flex: 1;
        min-width: 40px;
        text-align: center;
    }

    .frente .info-cartao .info-item label {
        font-size: 0.4rem;
        letter-spacing: 1px;
        opacity: 0.4;
        text-transform: uppercase;
        font-weight: 600;
    }

    .frente .info-cartao .info-item p {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        word-break: break-word;
        color: #ffffff;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        line-height: 1.2;
    }

    .frente .info-cartao .info-item .destaque {
        color: #d4af37;
        font-size: 0.75rem;
    }

    .frente .footer-cartao {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 4px;
        border-top: 1px solid rgba(212, 175, 55, 0.12);
        flex-wrap: wrap;
        gap: 3px;
    }

    .frente .footer-cartao .cargo-membro {
        font-size: 0.55rem;
        opacity: 0.6;
        display: flex;
        align-items: center;
        gap: 4px;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .frente .footer-cartao .cargo-membro i {
        color: #d4af37;
        font-size: 0.5rem;
    }

    .frente .footer-cartao .congregacao {
        font-size: 0.5rem;
        opacity: 0.4;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .frente .footer-cartao .congregacao i {
        color: #d4af37;
        font-size: 0.45rem;
    }

    .verso {
        background: #1a1a2a;
        transform: rotateY(180deg);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .verso .verso-conteudo {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .verso .tarja-magnetica {
        background: linear-gradient(180deg, #0a0a0a, #1a1a1a);
        height: 30px;
        margin-top: 15px;
        margin-bottom: 8px;
        width: 100%;
        box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.8), 0 4px 20px rgba(0, 0, 0, 0.5);
    }

    .verso .verso-info {
        padding: 0 18px 12px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .verso .assinatura-area {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
    }

    .verso .linha-assinatura {
        flex: 1;
        height: 24px;
        background: linear-gradient(180deg, #d5cdc5 0%, #e8e0d8 30%, #f0ece3 50%, #e8e0d8 70%, #d5cdc5 100%);
        border-radius: 4px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .verso .linha-assinatura::after {
        content: 'Assinatura do titular';
        position: absolute;
        bottom: -14px;
        left: 6px;
        font-size: 0.35rem;
        color: rgba(255, 255, 255, 0.2);
        letter-spacing: 0.5px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .verso .codigo-seguranca {
        display: flex;
        align-items: center;
        gap: 4px;
        color: #aaa;
        font-size: 0.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        flex-shrink: 0;
    }

    .verso .codigo-seguranca span {
        background: #0a0a0a;
        padding: 3px 10px;
        border-radius: 4px;
        color: #e0e0e0;
        font-family: 'Courier New', monospace;
        letter-spacing: 1.5px;
        font-size: 0.7rem;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .verso .verso-detalhes {
        display: flex;
        gap: 10px;
        margin-top: 4px;
        padding-top: 6px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .verso .verso-detalhes .qr-code {
        width: 65px;
        height: 65px;
        background: white;
        padding: 4px;
        border-radius: 8px;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .verso .verso-detalhes .qr-code img,
    .verso .verso-detalhes .qr-code canvas {
        width: 100% !important;
        height: 100% !important;
        border-radius: 4px;
        object-fit: contain;
    }

    .verso .verso-texto {
        flex: 1;
        font-size: 0.5rem;
        color: rgba(255, 255, 255, 0.6);
        line-height: 1.4;
        min-width: 0;
    }

    .verso .verso-texto .igreja-nome {
        color: #d4af37;
        font-weight: 700;
        font-size: 0.6rem;
        letter-spacing: 1.5px;
        display: block;
        margin-bottom: 1px;
    }

    .verso .verso-texto .info-linha {
        display: flex;
        align-items: center;
        gap: 3px;
        font-size: 0.45rem;
        opacity: 0.7;
        padding: 1px 0;
        flex-wrap: wrap;
    }

    .verso .verso-texto .info-linha i {
        margin-right: 1px;
        color: #d4af37;
        width: 10px;
        text-align: center;
        font-size: 0.4rem;
        flex-shrink: 0;
    }

    .verso .verso-texto .info-linha .label {
        color: rgba(255, 255, 255, 0.35);
        font-weight: 600;
        min-width: 40px;
        flex-shrink: 0;
    }

    .verso .verso-texto .info-linha .value {
        color: rgba(255, 255, 255, 0.8);
        word-break: break-word;
    }

    .verso .verso-texto .info-consagracao {
        display: flex;
        align-items: center;
        gap: 3px;
        font-size: 0.45rem;
        opacity: 0.7;
        padding: 1px 0;
        color: #d4af37;
        flex-wrap: wrap;
    }

    .verso .verso-texto .info-consagracao i {
        color: #d4af37;
        width: 10px;
        text-align: center;
        font-size: 0.4rem;
        flex-shrink: 0;
    }

    .verso .verso-texto .info-consagracao .label {
        color: rgba(255, 255, 255, 0.35);
        font-weight: 600;
        min-width: 40px;
        flex-shrink: 0;
    }

    .verso .verso-texto .info-consagracao .value {
        color: #d4af37;
        font-weight: 600;
    }

    .verso .verso-texto .info-extra {
        margin-top: 2px;
        padding-top: 2px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        font-size: 0.4rem;
        opacity: 0.3;
        letter-spacing: 0.3px;
        word-break: break-word;
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
    }

    .verso .verso-texto .info-extra span {
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }

    /* ===== BOTÕES DO CARTÃO ===== */
    .botoes-cartao {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 20px;
        max-width: 440px;
        margin-left: auto;
        margin-right: auto;
    }

    .botoes-cartao .btn {
        padding: 8px 14px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.7rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        flex: 1;
        justify-content: center;
        min-width: 60px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
        color: white;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }

    .botoes-cartao .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
        transition: left 0.5s ease;
    }

    .botoes-cartao .btn:hover::before {
        left: 100%;
    }

    .botoes-cartao .btn:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        filter: brightness(1.1);
    }

    .botoes-cartao .btn:active {
        transform: scale(0.95);
    }

    .botoes-cartao .btn i {
        font-size: 0.8rem;
        transition: transform 0.3s ease;
    }

    .botoes-cartao .btn:hover i {
        transform: scale(1.1);
    }

    .btn-voltar {
        background: linear-gradient(145deg, #e74c3c, #c0392b);
        color: white;
    }
    .btn-voltar:hover {
        background: linear-gradient(145deg, #f05a4a, #d44637);
    }

    .btn-girar {
        background: linear-gradient(145deg, #2c3e50, #34495e);
        color: white;
    }
    .btn-girar:hover {
        background: linear-gradient(145deg, #34495e, #3d566e);
    }

    .btn-whatsapp {
        background: linear-gradient(145deg, #25d366, #128C7E);
        color: white;
    }
    .btn-whatsapp:hover {
        background: linear-gradient(145deg, #2de072, #15a085);
    }

    .btn-imprimir {
        background: linear-gradient(145deg, #3498db, #2980b9);
        color: white;
    }
    .btn-imprimir:hover {
        background: linear-gradient(145deg, #3da8e3, #2e8fc4);
    }

    .btn-copiar {
        background: linear-gradient(145deg, #8e44ad, #6c3483);
        color: white;
    }
    .btn-copiar:hover {
        background: linear-gradient(145deg, #9b59b6, #7d3c98);
    }

    .btn-baixar {
        background: linear-gradient(145deg, #00b894, #00a381);
        color: white;
    }
    .btn-baixar:hover {
        background: linear-gradient(145deg, #00c9a0, #00b894);
    }

    .btn-cartao-completo {
        background: linear-gradient(145deg, #6c5ce7, #5a4bd1);
        color: white;
    }
    .btn-cartao-completo:hover {
        background: linear-gradient(145deg, #7d6ff0, #6c5ce7);
    }

    /* ===== TOASTS ===== */
    .toast-copiado {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.85);
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.8rem;
        z-index: 9999;
        backdrop-filter: blur(10px);
        animation: fadeUp 0.4s ease;
        display: none;
        border: 1px solid rgba(212, 175, 55, 0.2);
    }

    .toast-copiado i { color: #4caf50; margin-right: 6px; }

    .toast-upload {
        position: fixed;
        bottom: 80px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.85);
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.8rem;
        z-index: 9999;
        backdrop-filter: blur(10px);
        animation: fadeUp 0.4s ease;
        display: none;
        border: 1px solid rgba(76, 175, 80, 0.3);
    }

    .toast-upload i { margin-right: 6px; }
    .toast-upload.error { border-color: #f44336; }
    .toast-upload .icon-success { color: #4caf50; }
    .toast-upload .icon-error { color: #f44336; }

    /* ===== LOADING OVERLAY ===== */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(10px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        flex-direction: column;
        gap: 20px;
    }

    .loading-overlay.active {
        display: flex;
    }

    .loading-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid rgba(212, 175, 55, 0.1);
        border-top: 4px solid #d4af37;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .loading-text {
        color: white;
        font-size: 1.2rem;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .loading-progress {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateX(-50%) translateY(20px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    /* ===== RESPONSIVIDADE ===== */
    @media (max-width: 768px) {
        .cartao-container { padding: 5px; max-width: 100%; }
        .cartao-wrapper { max-width: 100%; width: 100%; padding: 0 5px; }
        .frente .conteudo { padding: 16px 14px; }
        .frente .numero-cartao { font-size: 0.7rem; gap: 4px; }
        .frente .numero-cartao span { padding: 2px 6px; }
        .botoes-cartao { max-width: 100%; }
        .botoes-cartao .btn { font-size: 0.65rem; padding: 6px 10px; min-width: 45px; }
        .verso .verso-detalhes .qr-code { width: 60px; height: 60px; }
        .verso .verso-texto { font-size: 0.45rem; }
        .verso .verso-texto .info-linha { font-size: 0.4rem; }
        .verso .verso-texto .info-linha .label { min-width: 35px; }
    }

    @media (max-width: 480px) {
        .cartao-container { padding: 0; max-width: 100%; width: 100%; }
        .cartao-wrapper { max-width: 100%; width: 100%; margin: 0; padding: 0; }
        .cartao { border-radius: 0; }
        .frente, .verso { border-radius: 0; }
        .frente .conteudo { padding: 12px 10px; }
        .frente .numero-cartao { font-size: 0.6rem; gap: 3px; }
        .frente .numero-cartao span { padding: 1px 4px; font-size: 0.55rem; }
        .frente .numero-cartao .matricula-destaque { font-size: 0.65rem; padding: 1px 8px; }
        .frente .chip { width: 32px; height: 24px; font-size: 0.8rem; }
        
        .logo-igreja .cross-icon {
            width: 40px;
            height: 40px;
        }
        .logo-igreja .cross-icon img {
            width: 40px;
            height: 40px;
        }
        .logo-igreja .cross-icon svg {
            width: 40px;
            height: 40px;
        }
        .logo-igreja .logo-texto .logo-nome { 
            font-size: 0.6rem;
        }
        .logo-igreja .logo-texto .logo-sub { 
            font-size: 0.32rem;
        }
        .logo-igreja {
            gap: 8px;
        }
        
        .status-badge { font-size: 0.4rem; padding: 2px 8px; }
        .foto-membro .avatar { width: 40px; height: 40px; min-width: 40px; min-height: 40px; font-size: 1.1rem; }
        .foto-membro .info-nome .nome { font-size: 0.8rem; }
        .foto-membro .info-nome .funcao { font-size: 0.5rem; }
        .btn-trocar-foto { width: 16px; height: 16px; min-width: 16px; min-height: 16px; font-size: 0.4rem; }
        .btn-trocar-foto i { font-size: 0.4rem; }
        .frente .info-cartao .info-item p { font-size: 0.6rem; }
        .frente .info-cartao .info-item label { font-size: 0.35rem; }
        .frente .footer-cartao .cargo-membro { font-size: 0.45rem; }
        .frente .footer-cartao .congregacao { font-size: 0.4rem; }
        .verso .verso-detalhes { flex-direction: column; align-items: center; text-align: center; }
        .verso .verso-detalhes .qr-code { width: 60px; height: 60px; }
        .verso .verso-texto { font-size: 0.45rem; text-align: center; }
        .verso .verso-texto .info-linha { justify-content: center; font-size: 0.4rem; flex-wrap: wrap; }
        .verso .verso-texto .info-linha .label { min-width: 35px; }
        .verso .verso-texto .info-consagracao { justify-content: center; font-size: 0.4rem; flex-wrap: wrap; }
        .verso .verso-texto .info-extra { justify-content: center; }
        .verso .verso-info { padding: 0 10px 10px; }
        .verso .tarja-magnetica { height: 28px; margin-top: 10px; margin-bottom: 6px; }
        .botoes-cartao { gap: 5px; margin-top: 10px; padding: 0 5px; max-width: 100%; }
        .botoes-cartao .btn { font-size: 0.55rem; padding: 6px 8px; min-width: 35px; border-radius: 8px; }
        .botoes-cartao .btn i { font-size: 0.65rem; }
        .botoes-cartao .btn span { display: none; }
    }

    @media (max-width: 360px) {
        .frente .conteudo { padding: 8px 6px; }
        .frente .numero-cartao { font-size: 0.5rem; gap: 2px; }
        .frente .numero-cartao span { padding: 1px 3px; font-size: 0.45rem; }
        .frente .numero-cartao .matricula-destaque { font-size: 0.55rem; padding: 1px 6px; }
        .frente .chip { width: 26px; height: 20px; font-size: 0.65rem; }
        
        .logo-igreja .cross-icon {
            width: 34px;
            height: 34px;
        }
        .logo-igreja .cross-icon img {
            width: 34px;
            height: 34px;
        }
        .logo-igreja .cross-icon svg {
            width: 34px;
            height: 34px;
        }
        .logo-igreja .logo-texto .logo-nome { 
            font-size: 0.5rem;
        }
        .logo-igreja .logo-texto .logo-sub { 
            font-size: 0.28rem;
        }
        .logo-igreja {
            gap: 6px;
        }
        
        .foto-membro .avatar { width: 34px; height: 34px; min-width: 34px; min-height: 34px; font-size: 0.9rem; }
        .foto-membro .info-nome .nome { font-size: 0.7rem; }
        .foto-membro .info-nome .funcao { font-size: 0.45rem; }
        .frente .info-cartao .info-item p { font-size: 0.5rem; }
        .frente .info-cartao .info-item label { font-size: 0.3rem; }
        .frente .footer-cartao .cargo-membro { font-size: 0.4rem; }
        .frente .footer-cartao .congregacao { font-size: 0.35rem; }
        .botoes-cartao { flex-direction: column; padding: 0 5px; gap: 4px; }
        .botoes-cartao .btn { width: 100%; font-size: 0.5rem; padding: 8px 10px; justify-content: center; }
        .botoes-cartao .btn span { display: inline; }
        .status-badge { font-size: 0.35rem; padding: 1px 6px; }
        .verso .verso-detalhes .qr-code { width: 50px; height: 50px; }
        .verso .verso-texto { font-size: 0.4rem; }
        .verso .verso-texto .info-linha { font-size: 0.35rem; }
        .verso .verso-texto .info-linha .label { min-width: 30px; }
        .verso .verso-texto .info-consagracao { font-size: 0.35rem; }
        .verso .linha-assinatura { height: 20px; }
        .verso .codigo-seguranca span { font-size: 0.6rem; padding: 2px 8px; }
    }

    @media (min-width: 1400px) {
        .cartao-wrapper { max-width: 520px; }
        .frente .conteudo { padding: 30px 28px; }
        .frente .numero-cartao { font-size: 1rem; gap: 12px; }
        .frente .numero-cartao span { padding: 4px 14px; }
        .frente .numero-cartao .matricula-destaque { font-size: 1.1rem; padding: 4px 18px; }
        .frente .info-cartao .info-item p { font-size: 0.9rem; }
        .foto-membro .avatar { width: 60px; height: 60px; min-width: 60px; min-height: 60px; font-size: 1.8rem; }
        .foto-membro .info-nome .nome { font-size: 1.2rem; }
        
        .logo-igreja .cross-icon {
            width: 64px;
            height: 64px;
        }
        .logo-igreja .cross-icon img {
            width: 64px;
            height: 64px;
        }
        .logo-igreja .cross-icon svg {
            width: 64px;
            height: 64px;
        }
        .logo-igreja .logo-texto .logo-nome { 
            font-size: 1.1rem;
        }
        .logo-igreja .logo-texto .logo-sub { 
            font-size: 0.5rem;
        }
        .logo-igreja {
            gap: 12px;
        }
        
        .botoes-cartao .btn { font-size: 0.85rem; padding: 12px 20px; }
        .verso .verso-texto { font-size: 0.6rem; }
        .verso .verso-texto .info-linha { font-size: 0.55rem; }
        .verso .verso-texto .info-consagracao { font-size: 0.55rem; }
        .verso .verso-detalhes .qr-code { width: 90px; height: 90px; }
    }

    @media (max-height: 500px) and (orientation: landscape) {
        .cartao-container { max-width: 100%; padding: 0; }
        .cartao-wrapper { max-width: 100%; max-height: 90vh; padding: 3px; }
        .frente .conteudo { padding: 8px 12px; }
        .frente .numero-cartao { font-size: 0.6rem; gap: 3px; }
        .frente .numero-cartao span { padding: 1px 4px; }
        .frente .numero-cartao .matricula-destaque { font-size: 0.65rem; padding: 1px 8px; }
        .frente .info-cartao .info-item p { font-size: 0.55rem; }
        .frente .info-cartao .info-item label { font-size: 0.35rem; }
        .frente .chip { width: 28px; height: 20px; font-size: 0.7rem; }
        
        .logo-igreja .cross-icon {
            width: 38px;
            height: 38px;
        }
        .logo-igreja .cross-icon img {
            width: 38px;
            height: 38px;
        }
        .logo-igreja .cross-icon svg {
            width: 38px;
            height: 38px;
        }
        .logo-igreja .logo-texto .logo-nome { 
            font-size: 0.6rem;
        }
        .logo-igreja .logo-texto .logo-sub { 
            font-size: 0.28rem;
        }
        .logo-igreja {
            gap: 6px;
        }
        
        .foto-membro .avatar { width: 34px; height: 34px; min-width: 34px; min-height: 34px; font-size: 0.9rem; }
        .foto-membro .info-nome .nome { font-size: 0.7rem; }
        .foto-membro .info-nome .funcao { font-size: 0.45rem; }
        .botoes-cartao .btn { font-size: 0.5rem; padding: 4px 8px; min-width: 35px; }
        .botoes-cartao .btn span { display: none; }
        .verso .verso-detalhes .qr-code { width: 45px; height: 45px; }
        .verso .verso-texto { font-size: 0.4rem; }
        .verso .verso-texto .info-linha { font-size: 0.35rem; }
        .verso .verso-texto .info-linha .label { min-width: 30px; }
        .verso .verso-texto .info-consagracao { font-size: 0.35rem; }
        .verso .verso-info { padding: 0 8px 8px; }
        .verso .tarja-magnetica { height: 20px; margin-top: 8px; margin-bottom: 4px; }
        .status-badge { font-size: 0.35rem; padding: 1px 6px; }
        .btn-trocar-foto { width: 14px; height: 14px; min-width: 14px; min-height: 14px; font-size: 0.35rem; }
        .btn-trocar-foto i { font-size: 0.35rem; }
        .botoes-cartao { margin-top: 6px; gap: 4px; }
    }

    @media print {
        .sidebar, .top-bar, .botoes-cartao, .toast-copiado, .btn-trocar-foto {
            display: none !important;
        }
        .cartao-container { max-width: 100%; padding: 0; margin: 0; }
        .cartao-wrapper { perspective: none; margin: 0 auto; aspect-ratio: 1.58 / 1; width: 100%; max-width: 100%; max-height: none; }
        .cartao { transform: none !important; height: 100%; width: 100%; }
        .frente, .verso { position: absolute; backface-visibility: visible; border-radius: 16px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); }
        .verso { transform: none !important; display: block; position: absolute; top: 0; left: 0; opacity: 0; pointer-events: none; }
        .cartao.virado .frente { opacity: 0; }
        .cartao.virado .verso { opacity: 1; transform: none !important; position: absolute; top: 0; left: 0; }
        body { background: white; padding: 10px; }
        .main-content { margin: 0; padding: 0; }
        .status-badge { background: rgba(76, 175, 80, 0.1) !important; border: 1px solid #4caf50 !important; color: #4caf50 !important; }
        .verso .verso-texto .info-linha .value { color: #333 !important; }
        .verso .verso-texto .info-linha .label { color: #666 !important; }
        .verso .verso-texto .info-consagracao .value { color: #b8960f !important; }
        .verso .verso-texto .info-consagracao .label { color: #666 !important; }
    }
</style>
@endsection

@section('content')
<div class="cartao-container">
    <!-- TOASTS -->
    <div class="toast-copiado" id="toastCopiado">
        <i class="fas fa-check-circle"></i> Dados copiados para a área de transferência!
    </div>

    <div class="toast-upload" id="toastUpload">
        <i class="fas fa-check-circle icon-success"></i>
        <span id="toastUploadMessage">Foto atualizada com sucesso!</span>
    </div>

    <!-- OVERLAY DE CARREGAMENTO -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Gerando imagem...</div>
        <div class="loading-progress" id="loadingProgress">Preparando cartão</div>
    </div>

    <!-- ===== CARTÃO 3D ===== -->
    <div class="cartao-wrapper">
        <div class="cartao" id="cartao">

            <!-- ===== FRENTE ===== -->
            <div class="frente">
                <div class="conteudo">
                    <!-- HEADER COM LOGO -->
                    <div class="header-cartao">
                        <div class="logo-igreja">
                            <div class="cross-icon">
                                @php
                                    $logoPath = public_path('imagens/logo-branco.png');
                                    $logoUrl = file_exists($logoPath) ? asset('imagens/logo-branco.png') : null;
                                @endphp
                                
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" 
                                         alt="Logo ADTC2" 
                                         loading="lazy"
                                         style="width: 100%; height: 100%; object-fit: contain;"
                                         onerror="this.style.display='none'; this.parentElement.querySelector('.logo-fallback').style.display='flex';">
                                @endif
                                
                                <div class="logo-fallback" style="{{ $logoUrl ? 'display: none;' : 'display: flex;' }} width: 100%; height: 100%;">
                                    <svg viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: 100%;">
                                        <rect x="17" y="2" width="16" height="46" rx="3" fill="url(#goldGrad)"/>
                                        <rect x="2" y="17" width="46" height="16" rx="3" fill="url(#goldGrad)"/>
                                        <defs>
                                            <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#f2d680"/>
                                                <stop offset="50%" stop-color="#d4af37"/>
                                                <stop offset="100%" stop-color="#b8960f"/>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </div>
                            </div>
                            <div class="logo-texto">
                                <div class="logo-nome">ADTC2</div>
                                <div class="logo-sub">Ministério Templo Central</div>
                            </div>
                        </div>
                        <div class="chip">
                            <i class="fas fa-microchip"></i>
                        </div>
                    </div>

                    <!-- STATUS -->
                    <div class="status-container">
                        <span class="status-badge {{ strtolower($membro->status) == 'ativo' ? '' : 'inativo' }}">
                            <i class="fas fa-circle"></i>
                            {{ strtoupper($membro->status) }}
                        </span>
                    </div>

                    <!-- FOTO + NOME -->
                    <div class="foto-membro">
                        <div class="avatar" id="avatarContainer">
                            @php
                                $fotoNome = $membro->foto ?? null;
                                $fotoUrl = $fotoNome ? route('imagem.foto', ['filename' => $fotoNome]) : null;
                            @endphp
                            
                            @if($fotoUrl)
                                <img src="{{ $fotoUrl }}" 
                                     alt="Foto de {{ $membro->nome }}" 
                                     id="fotoPerfil"
                                     onerror="this.style.display='none'; document.getElementById('fotoPlaceholder').style.display='flex';">
                                <span class="avatar-placeholder" id="fotoPlaceholder" style="display: none;">{{ substr($membro->nome, 0, 1) }}</span>
                            @else
                                <span class="avatar-placeholder" id="fotoPlaceholder">{{ substr($membro->nome, 0, 1) }}</span>
                            @endif
                            
                            @php
                                $usuarioLogado = Auth::user();
                                $isDono = $usuarioLogado && $usuarioLogado->matricula == $membro->matricula;
                            @endphp
                            
                            @if($isDono)
                                <button class="btn-trocar-foto" id="btnTrocarFoto" title="Trocar foto">
                                    <i class="fas fa-camera"></i>
                                    <input type="file" id="inputFoto" accept="image/*">
                                </button>
                            @endif
                        </div>
                        <div class="info-nome">
                            <div class="nome">{{ $membro->nome }}</div>
                            <div class="funcao">{{ $membro->matricula }} • {{ $membro->funcao_formatada }}</div>
                        </div>
                    </div>

                    <!-- BODY -->
                    <div class="body-cartao">
                        <div class="numero-cartao">
                            <span>****</span>
                            <span>****</span>
                            <span>****</span>
                            <span class="matricula-destaque">{{ str_pad($membro->matricula, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="info-cartao">
                            <div class="info-item">
                                <label>MATRÍCULA</label>
                                <p class="destaque">#{{ $membro->matricula }}</p>
                            </div>
                            <div class="info-item">
                                <label>NOME</label>
                                <p>{{ $membro->nome }}</p>
                            </div>
                            <div class="info-item validade">
                                <label>VÁLIDO</label>
                                <p>
                                    @php
                                        $validade = $membro->datCadastro ? date('m/y', strtotime('+5 years', strtotime($membro->datCadastro))) : '12/28';
                                    @endphp
                                    {{ $validade }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="footer-cartao">
                        <div class="cargo-membro">
                            <i class="fas fa-user-tie"></i>
                            <span>{{ $membro->funcao ?: 'MEMBRO' }}</span>
                        </div>
                        <div class="congregacao">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $membro->congregacao ?: 'NOVO MARANGUAPE 3' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== VERSO ===== -->
            <div class="verso">
                <div class="verso-conteudo">
                    <div class="tarja-magnetica"></div>

                    <div class="verso-info">
                        <div class="assinatura-area">
                            <div class="linha-assinatura"></div>
                            <div class="codigo-seguranca">
                                <label>CVV</label>
                                <span>{{ substr(str_pad($membro->matricula, 6, '0'), -3) }}</span>
                            </div>
                        </div>

                        <div class="verso-detalhes">
                            <div class="qr-code" id="qrCode"></div>

                            <div class="verso-texto">
                                <span class="igreja-nome">
                                    <i class="fas fa-cross" style="font-size: 0.6rem;"></i> IGREJA CENTRAL
                                </span>

                                <div class="info-linha">
                                    <i class="fas fa-user"></i>
                                    <span class="label">Nome:</span>
                                    <span class="value">{{ $membro->nome }}</span>
                                </div>

                                @if($membro->documento)
                                <div class="info-linha">
                                    <i class="fas fa-id-card"></i>
                                    <span class="label">Documento:</span>
                                    <span class="value">{{ $membro->documento }}</span>
                                </div>
                                @endif

                                @if($membro->dataNascimento)
                                <div class="info-linha">
                                    <i class="fas fa-birthday-cake"></i>
                                    <span class="label">Nascimento:</span>
                                    <span class="value">{{ date('d/m/Y', strtotime($membro->dataNascimento)) }}</span>
                                </div>
                                @endif

                                @if($membro->dataBatismo)
                                <div class="info-linha">
                                    <i class="fas fa-water"></i>
                                    <span class="label">Batismo:</span>
                                    <span class="value">{{ date('d/m/Y', strtotime($membro->dataBatismo)) }}</span>
                                </div>
                                @endif

                                @php
                                    $funcoesMinisteriais = ['Auxiliar', 'Obreiro', 'Diacono', 'Diácono', 'Presbitero', 'Presbítero', 'Evangelista', 'Pastor', 'Pastora', 'Pastor-Presidente', 'Vice-Presidente', 'Missionário', 'Missionária'];
                                    $mostrarConsagracao = in_array($membro->funcao, $funcoesMinisteriais);
                                @endphp

                                @if($mostrarConsagracao && $membro->data_Consagracao)
                                <div class="info-consagracao">
                                    <i class="fas fa-hands-praying"></i>
                                    <span class="label">Consagração:</span>
                                    <span class="value">{{ date('d/m/Y', strtotime($membro->data_Consagracao)) }}</span>
                                </div>
                                @endif

                                @if($membro->mae)
                                <div class="info-linha">
                                    <i class="fas fa-female"></i>
                                    <span class="label">Mãe:</span>
                                    <span class="value">{{ $membro->mae }}</span>
                                </div>
                                @endif

                                @if($membro->pai)
                                <div class="info-linha">
                                    <i class="fas fa-male"></i>
                                    <span class="label">Pai:</span>
                                    <span class="value">{{ $membro->pai }}</span>
                                </div>
                                @endif

                                @if($membro->congregacao)
                                <div class="info-linha">
                                    <i class="fas fa-church"></i>
                                    <span class="label">Congregação:</span>
                                    <span class="value">{{ $membro->congregacao }}</span>
                                </div>
                                @endif

                                @if($membro->telefone)
                                <div class="info-linha">
                                    <i class="fas fa-phone"></i>
                                    <span class="label">Telefone:</span>
                                    <span class="value">{{ $membro->telefone }}</span>
                                </div>
                                @endif

                                <div class="info-extra">
                                    <span><i class="fas fa-id-card"></i> Mat: {{ $membro->matricula }}</span>
                                    <span><i class="fas fa-calendar-alt"></i> Cad: {{ date('d/m/Y', strtotime($membro->datCadastro)) }}</span>
                                    @if($membro->cidade && $membro->uf)
                                    <span><i class="fas fa-map-marker-alt"></i> {{ $membro->cidade }}/{{ $membro->uf }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== BOTÕES ===== -->
    <div class="botoes-cartao">
        <a href="{{ route('membros.index') }}" class="btn btn-voltar" title="Voltar">
            <i class="fas fa-arrow-left"></i> 
            <span>Voltar</span>
        </a>

        <button class="btn btn-girar" id="virarCartao" title="Virar cartão">
            <i class="fas fa-sync-alt"></i> 
            <span id="btnVirarTexto">Virar</span>
        </button>

        <button class="btn btn-whatsapp" id="compartilharWhatsApp" title="Compartilhar no WhatsApp">
            <i class="fab fa-whatsapp"></i> 
            <span>WhatsApp</span>
        </button>

        <button class="btn btn-imprimir" onclick="window.print()" title="Imprimir">
            <i class="fas fa-print"></i> 
            <span>Imprimir</span>
        </button>

        <button class="btn btn-copiar" id="copiarDados" title="Copiar dados">
            <i class="fas fa-copy"></i> 
            <span>Copiar</span>
        </button>

        <button class="btn btn-baixar" id="baixarCartao" title="Baixar imagem do cartão">
            <i class="fas fa-download"></i> 
            <span>Baixar</span>
        </button>

        <button class="btn btn-cartao-completo" id="baixarCartaoCompleto" title="Baixar frente e verso juntos">
            <i class="fas fa-images"></i> 
            <span>Completo</span>
        </button>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // DADOS DO MEMBRO
    // ============================================================
    const membro = {
        matricula: "{{ $membro->matricula }}",
        nome: "{{ $membro->nome }}",
        funcao: "{{ $membro->funcao }}",
        status: "{{ strtoupper($membro->status) }}",
        email: "{{ $membro->email }}",
        telefone: "{{ $membro->telefone }}",
        documento: "{{ $membro->documento }}",
        mae: "{{ $membro->mae }}",
        pai: "{{ $membro->pai }}",
        congregacao: "{{ $membro->congregacao }}",
        cidade: "{{ $membro->cidade }}",
        uf: "{{ $membro->uf }}",
        dataNascimento: "{{ $membro->dataNascimento ? date('d/m/Y', strtotime($membro->dataNascimento)) : 'Não informado' }}",
        dataBatismo: "{{ $membro->dataBatismo ? date('d/m/Y', strtotime($membro->dataBatismo)) : 'Não informado' }}",
        dataConsagracao: "{{ $membro->data_Consagracao ? date('d/m/Y', strtotime($membro->data_Consagracao)) : 'Não informado' }}",
        datCadastro: "{{ date('d/m/Y', strtotime($membro->datCadastro)) }}",
        validade: "{{ $validade ?? '12/28' }}"
    };

    // ============================================================
    // QR CODE
    // ============================================================
    const qrContainer = document.getElementById('qrCode');
    if (qrContainer) {
        qrContainer.innerHTML = '';
        new QRCode(qrContainer, {
            text: `ID:${membro.matricula}|${membro.nome}`,
            width: 90,
            height: 90,
            colorDark: '#1a1a2e',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.L
        });
    }

    // ============================================================
    // FUNÇÕES DE UTILIDADE
    // ============================================================
    const loadingOverlay = document.getElementById('loadingOverlay');
    const loadingProgress = document.getElementById('loadingProgress');
    const toastDownload = document.getElementById('toastDownload');
    const toastDownloadMessage = document.getElementById('toastDownloadMessage');

    function mostrarLoading(texto = 'Preparando cartão...') {
        loadingOverlay.classList.add('active');
        loadingProgress.textContent = texto;
    }

    function esconderLoading() {
        loadingOverlay.classList.remove('active');
    }

    function mostrarToast(mensagem, erro = false) {
        // Cria o toast dinamicamente se não existir
        let toast = document.getElementById('toastDownload');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toastDownload';
            toast.className = 'toast-download';
            toast.innerHTML = '<i class="fas fa-check-circle"></i><span id="toastDownloadMessage"></span>';
            document.body.appendChild(toast);
        }
        
        const messageEl = document.getElementById('toastDownloadMessage');
        if (messageEl) messageEl.textContent = mensagem;
        
        toast.className = 'toast-download';
        if (erro) {
            toast.classList.add('error');
            toast.querySelector('i').className = 'fas fa-times-circle';
        } else {
            toast.querySelector('i').className = 'fas fa-check-circle';
        }
        toast.style.display = 'flex';
        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.style.display = 'none';
        }, 4000);
    }

    // ============================================================
    // CAPTURAR E BAIXAR IMAGEM DO CARTÃO
    // ============================================================
    async function capturarCartao(lado = 'frente', escala = 2) {
        const cartaoWrapper = document.querySelector('.cartao-wrapper');
        const cartao = document.getElementById('cartao');
        
        const estavaVirado = cartao.classList.contains('virado');
        
        if (lado === 'frente' && estavaVirado) {
            cartao.classList.remove('virado');
        } else if (lado === 'verso' && !estavaVirado) {
            cartao.classList.add('virado');
        }
        
        await new Promise(resolve => setTimeout(resolve, 100));
        
        const canvas = await html2canvas(cartaoWrapper, {
            scale: escala,
            useCORS: true,
            allowTaint: true,
            backgroundColor: null,
            logging: false,
            width: cartaoWrapper.scrollWidth,
            height: cartaoWrapper.scrollHeight
        });
        
        if (estavaVirado) {
            cartao.classList.add('virado');
        } else {
            cartao.classList.remove('virado');
        }
        
        return canvas;
    }

    async function baixarCartao(lado = 'frente') {
        try {
            mostrarLoading(`Gerando ${lado === 'frente' ? 'frente' : 'verso'} do cartão...`);
            
            const canvas = await capturarCartao(lado, 3);
            
            const link = document.createElement('a');
            link.download = `cartao_${lado}_${membro.nome.replace(/\s/g, '_')}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
            
            esconderLoading();
            mostrarToast(`Cartão ${lado === 'frente' ? 'frente' : 'verso'} baixado com sucesso! ✅`);
            
        } catch (error) {
            console.error('Erro ao gerar imagem:', error);
            esconderLoading();
            mostrarToast('Erro ao gerar imagem. Tente novamente.', true);
        }
    }

    async function baixarCartaoCompleto() {
        try {
            mostrarLoading('Gerando imagem completa...');
            loadingProgress.textContent = 'Capturando frente...';
            
            const frenteCanvas = await capturarCartao('frente', 2);
            loadingProgress.textContent = 'Capturando verso...';
            
            const versoCanvas = await capturarCartao('verso', 2);
            
            loadingProgress.textContent = 'Montando imagem final...';
            
            const largura = frenteCanvas.width;
            const altura = frenteCanvas.height;
            
            const canvasFinal = document.createElement('canvas');
            canvasFinal.width = largura * 2 + 40;
            canvasFinal.height = altura + 20;
            
            const ctx = canvasFinal.getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, canvasFinal.width, canvasFinal.height);
            gradient.addColorStop(0, '#0f0c29');
            gradient.addColorStop(0.5, '#302b63');
            gradient.addColorStop(1, '#24243e');
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, canvasFinal.width, canvasFinal.height);
            
            ctx.strokeStyle = 'rgba(212, 175, 55, 0.3)';
            ctx.lineWidth = 2;
            ctx.setLineDash([10, 10]);
            ctx.strokeRect(10, 10, canvasFinal.width - 20, canvasFinal.height - 20);
            ctx.setLineDash([]);
            
            ctx.fillStyle = 'rgba(212, 175, 55, 0.6)';
            ctx.font = 'bold 16px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('FRENTE', largura / 2, 25);
            ctx.fillText('VERSO', largura + 40 + largura / 2, 25);
            
            ctx.drawImage(frenteCanvas, 10, 30);
            ctx.drawImage(versoCanvas, largura + 30, 30);
            
            ctx.strokeStyle = 'rgba(212, 175, 55, 0.2)';
            ctx.lineWidth = 2;
            ctx.strokeRect(10, 30, largura, altura);
            ctx.strokeRect(largura + 30, 30, largura, altura);
            
            ctx.fillStyle = 'rgba(212, 175, 55, 0.3)';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            const dataGeracao = new Date().toLocaleDateString('pt-BR');
            ctx.fillText(`Gerado em ${dataGeracao} • ADTC2 - Ministério Templo Central`, canvasFinal.width / 2, canvasFinal.height - 8);
            
            const link = document.createElement('a');
            link.download = `cartao_completo_${membro.nome.replace(/\s/g, '_')}.png`;
            link.href = canvasFinal.toDataURL('image/png');
            link.click();
            
            esconderLoading();
            mostrarToast('Cartão completo baixado com sucesso! 🎉');
            
        } catch (error) {
            console.error('Erro ao gerar imagem completa:', error);
            esconderLoading();
            mostrarToast('Erro ao gerar imagem completa. Tente novamente.', true);
        }
    }

    // ============================================================
    // EVENTOS DOS BOTÕES
    // ============================================================
    
    document.getElementById('baixarCartao').addEventListener('click', function() {
        const cartao = document.getElementById('cartao');
        const lado = cartao.classList.contains('virado') ? 'verso' : 'frente';
        baixarCartao(lado);
    });

    document.getElementById('baixarCartaoCompleto').addEventListener('click', function() {
        baixarCartaoCompleto();
    });

    // ============================================================
    // VIRAR CARTÃO
    // ============================================================
    const cartao = document.getElementById('cartao');
    const btnVirar = document.getElementById('virarCartao');
    const btnVirarTexto = document.getElementById('btnVirarTexto');
    let virado = false;

    function virarCartao() {
        virado = !virado;
        cartao.classList.toggle('virado');
        const icone = btnVirar.querySelector('i');
        if (virado) {
            icone.className = 'fas fa-undo-alt';
            btnVirarTexto.textContent = 'Verso';
        } else {
            icone.className = 'fas fa-sync-alt';
            btnVirarTexto.textContent = 'Virar';
        }
    }

    btnVirar.addEventListener('click', function(e) {
        e.stopPropagation();
        virarCartao();
    });

    cartao.addEventListener('click', function(e) {
        if (e.target.closest('.qr-code') || e.target.closest('.btn') || e.target.closest('.btn-trocar-foto')) {
            return;
        }
        virarCartao();
    });

    // ============================================================
    // COMPARTILHAR WHATSAPP
    // ============================================================
    document.getElementById('compartilharWhatsApp').addEventListener('click', function() {
        const mensagem =
            `🕊️ *CARTÃO DE MEMBRO DIGITAL*\n\n` +
            `🏛️ ADTC2 - Ministério Templo Central\n` +
            `━━━━━━━━━━━━━━━━━━━\n` +
            `👤 Nome: ${membro.nome}\n` +
            `📌 Função: ${membro.funcao}\n` +
            `🆔 Matrícula: ${membro.matricula}\n` +
            `✅ Status: ${membro.status}\n` +
            `🏠 Congregação: ${membro.congregacao || 'Templo Central'}\n` +
            `📅 Cadastro: ${membro.datCadastro}\n` +
            `━━━━━━━━━━━━━━━━━━━\n` +
            `📱 Apresente este cartão para validação.`;

        window.open(`https://wa.me/?text=${encodeURIComponent(mensagem)}`, '_blank');
    });

    // ============================================================
    // COPIAR DADOS
    // ============================================================
    document.getElementById('copiarDados').addEventListener('click', function() {
        const dados =
            `CARTÃO DE MEMBRO - ADTC2\n` +
            `━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n` +
            `Nome: ${membro.nome}\n` +
            `Matrícula: ${membro.matricula}\n` +
            `Função: ${membro.funcao}\n` +
            `Status: ${membro.status}\n` +
            `Documento: ${membro.documento || 'Não informado'}\n` +
            `Nascimento: ${membro.dataNascimento}\n` +
            `Batismo: ${membro.dataBatismo}\n` +
            `Consagração: ${membro.dataConsagracao}\n` +
            `Mãe: ${membro.mae || 'Não informado'}\n` +
            `Pai: ${membro.pai || 'Não informado'}\n` +
            `Congregação: ${membro.congregacao || 'Templo Central'}\n` +
            `Telefone: ${membro.telefone || 'Não informado'}\n` +
            `Email: ${membro.email || 'Não informado'}\n` +
            `Cidade/UF: ${membro.cidade}/${membro.uf}\n` +
            `Data Cadastro: ${membro.datCadastro}\n` +
            `━━━━━━━━━━━━━━━━━━━━━━━━━━━━`;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(dados).then(() => mostrarToastCopiado())
                .catch(() => fallbackCopiar(dados));
        } else {
            fallbackCopiar(dados);
        }
    });

    function fallbackCopiar(texto) {
        const textarea = document.createElement('textarea');
        textarea.value = texto;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        textarea.remove();
        mostrarToastCopiado();
    }

    function mostrarToastCopiado() {
        const toast = document.getElementById('toastCopiado');
        toast.style.display = 'block';
        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }

    // ============================================================
    // UPLOAD DE FOTO
    // ============================================================
    const isDono = {{ $isDono ?? false ? 'true' : 'false' }};

    @if($isDono ?? false)
    const inputFoto = document.getElementById('inputFoto');
    const toastUpload = document.getElementById('toastUpload');
    const toastUploadMessage = document.getElementById('toastUploadMessage');
    const avatarContainer = document.getElementById('avatarContainer');

    function atualizarFoto(url) {
        const fotoPerfil = document.getElementById('fotoPerfil');
        const fotoPlaceholder = document.getElementById('fotoPlaceholder');
        
        const timestamp = new Date().getTime();
        const urlComTimestamp = url + '?t=' + timestamp;
        
        if (fotoPerfil) {
            fotoPerfil.src = urlComTimestamp;
            fotoPerfil.style.display = 'block';
            fotoPerfil.onerror = function() {
                this.style.display = 'none';
                if (fotoPlaceholder) {
                    fotoPlaceholder.style.display = 'flex';
                }
            };
        } else {
            const img = document.createElement('img');
            img.id = 'fotoPerfil';
            img.src = urlComTimestamp;
            img.alt = 'Foto de perfil';
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            img.onerror = function() {
                this.style.display = 'none';
                if (fotoPlaceholder) {
                    fotoPlaceholder.style.display = 'flex';
                }
            };
            avatarContainer.prepend(img);
        }
        
        if (fotoPlaceholder) {
            fotoPlaceholder.style.display = 'none';
        }
    }

    function mostrarUploadToast(message, type) {
        const toast = document.getElementById('toastUpload');
        const messageEl = document.getElementById('toastUploadMessage');
        const icon = toast.querySelector('i');
        
        messageEl.textContent = message;
        toast.className = 'toast-upload';
        
        if (type === 'error') {
            toast.classList.add('error');
            icon.className = 'fas fa-times-circle icon-error';
        } else {
            icon.className = 'fas fa-check-circle icon-success';
        }
        
        toast.style.display = 'block';
        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.style.display = 'none';
        }, 4000);
    }

    inputFoto.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            mostrarUploadToast('A imagem deve ter no máximo 2MB.', 'error');
            this.value = '';
            return;
        }

        const tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!tiposPermitidos.includes(file.type)) {
            mostrarUploadToast('Formato não permitido. Use JPEG, PNG, JPG, GIF ou WEBP.', 'error');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('fotoPerfil');
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            const placeholder = document.getElementById('fotoPlaceholder');
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('foto', file);

        const url = '{{ route("perfil.foto.upload") }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarUploadToast('Foto atualizada com sucesso! ✅', 'success');
                if (data.foto_url) {
                    atualizarFoto(data.foto_url);
                    if (window.FotoEvent) {
                        window.FotoEvent.atualizada(data.foto_url);
                    }
                }
                inputFoto.value = '';
            } else {
                mostrarUploadToast(data.message || 'Erro ao atualizar foto.', 'error');
                inputFoto.value = '';
            }
        })
        .catch(error => {
            console.error('Erro no upload:', error);
            mostrarUploadToast('Erro ao fazer upload. Tente novamente.', 'error');
            inputFoto.value = '';
        });
    });

    if (avatarContainer) {
        avatarContainer.addEventListener('dblclick', function(e) {
            if (e.target.closest('.btn-trocar-foto') || e.target.closest('#inputFoto')) return;
            
            if (!confirm('Deseja remover sua foto de perfil?')) return;
            
            fetch('{{ route("perfil.foto.remover") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const img = document.getElementById('fotoPerfil');
                    if (img) img.remove();
                    
                    let placeholder = document.getElementById('fotoPlaceholder');
                    if (!placeholder) {
                        const span = document.createElement('span');
                        span.id = 'fotoPlaceholder';
                        span.className = 'avatar-placeholder';
                        span.textContent = membro.nome.charAt(0);
                        avatarContainer.prepend(span);
                        placeholder = span;
                    } else {
                        placeholder.style.display = 'flex';
                    }
                    
                    if (window.FotoEvent) {
                        window.FotoEvent.removida();
                    }
                    
                    mostrarUploadToast('Foto removida com sucesso!', 'success');
                } else {
                    mostrarUploadToast(data.message || 'Erro ao remover foto.', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarUploadToast('Erro ao remover foto.', 'error');
            });
        });
    }
    @endif

    // ============================================================
    // IMPRESSÃO
    // ============================================================
    window.addEventListener('beforeprint', function() {
        if (virado) {
            virarCartao();
        }
    });

    // ============================================================
    // LOG
    // ============================================================
    console.log('🕊️ Cartão de Membro - ADTC2');
    console.log(`👤 ${membro.nome} (${membro.matricula})`);
    console.log('📸 Clique em "Baixar" para salvar a imagem do cartão');
    console.log('🖼️ Clique em "Completo" para baixar frente e verso juntos');

});
</script>
@endpush
@endsection